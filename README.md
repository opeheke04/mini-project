# Minimarket POS

A Laravel point-of-sale and inventory application for a small minimarket. It includes product management, role-protected admin pages, and a cashier workflow for recording sales and updating stock.

## Requirements

- PHP 8.2 or newer with the extensions required by Laravel
- Composer
- Node.js and npm
- A Laravel-supported database. The default local setup uses SQLite and does not require a separate database server.

## Local setup

From the project directory:

```powershell
composer install
if (!(Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
npm install
```

The default `.env.example` uses SQLite, file-based sessions/cache, and a synchronous local queue, so the app can run without starting MySQL. Ensure `database\database.sqlite` exists (the repository includes it); if it does not, create an empty file before migrating:

```powershell
New-Item -ItemType File database\database.sqlite
```

To use XAMPP MySQL instead, start MySQL in the XAMPP Control Panel, create a database, then set `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE` to that database, and your local MySQL username/password in `.env`.

Before creating initial user accounts, set these values in `.env`:

- `INITIAL_ADMIN_EMAIL` and `INITIAL_ADMIN_PASSWORD`
- `INITIAL_CASHIER_EMAIL` and `INITIAL_CASHIER_PASSWORD`

Use different, unique passwords of at least 16 characters. The initial account credentials are intentionally not provided by default. The seeder updates these two accounts to the configured values when it runs, so remove the password values from `.env` after bootstrapping accounts if you do not want future seed runs to reset them. Never commit `.env` or share these values.

Run the database migrations and seed initial accounts/products:

```powershell
php artisan migrate
php artisan db:seed
```

The seeder warns and skips initial account creation if the initial email/password values are missing. It still seeds sample products. Existing databases may still contain the old demo accounts; change or remove those passwords before deploying.

Build frontend assets and start the application:

```powershell
npm run build
php artisan serve
```

Open the URL shown by Artisan (normally `http://127.0.0.1:8000`). For frontend development with Vite hot reload, run `npm run dev` in a second terminal alongside `php artisan serve`; use `npm run build` for production assets.

## Roles and main routes

- Admins can access `/admin/dashboard` and `/products`.
- Products can have an optional unique barcode. On the cashier screen, search by barcode or scan it with a keyboard-style barcode scanner; pressing Enter adds the matching product to the cart.
- Admins can access `/stock-restocks` to record incoming inventory and review its history.
- Admins can access `/sales` for searchable transaction history, transaction details, and daily/monthly sales summaries.
- Admins and cashiers can access `/cashier` and record transactions.
- The `admin` and `cek.role` middleware enforce the role restrictions.
- Public registration is disabled. Admins can create cashier accounts from the **Akun Kasir** menu; new accounts require a password of at least 16 characters containing letters and numbers.

## Restock and mobile app

Admins can record incoming stock from **Restock** in the web navigation or at `/stock-restocks`. Each entry stores the product name, quantity, received date, supplier, notes, and admin who recorded it. Stock updates and the history entry are written in one database transaction. The responsive page is available on desktop and mobile browsers.

The `mobile` Flutter project provides an Android/iOS admin app for viewing stock, recording restocks, and reviewing recent restock history. Its Sanctum API is restricted to admin accounts:

| Method | Endpoint | Description |
| --- | --- | --- |
| `POST` | `/api/mobile/login` | Sign in and receive a scoped mobile token |
| `GET` | `/api/mobile/me` | Read the signed-in admin profile |
| `POST` | `/api/mobile/logout` | Revoke the current mobile token |
| `GET` | `/api/mobile/products` | Read products and the configured low-stock threshold |
| `GET` | `/api/mobile/restocks` | Read paginated restock history |
| `POST` | `/api/mobile/restocks` | Record a restock and atomically update its product stock |

After running the database migrations and starting Laravel, run the app on an Android emulator with:

```powershell
cd mobile
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/mobile
```

For a physical Android device, start Laravel with `php artisan serve --host 0.0.0.0` and set `API_BASE_URL` to the computer's LAN address, for example `http://192.168.1.20:8000/api/mobile`; allow the port through the local firewall as needed. iOS builds and simulator/device runs require macOS and Xcode. Cleartext HTTP is enabled only in the Android debug manifest. Mobile API tokens are scoped to inventory access, expire after 30 days, and are revoked when the user signs out.

```powershell
flutter build apk --debug --dart-define=API_BASE_URL=http://192.168.1.20:8000/api/mobile
```

The generated Android project is ready for debug testing; release signing is not configured. Before distributing an app, configure a private release keystore, build with an HTTPS API URL, and keep signing credentials out of source control.

## Laravel Books API (Acara 25-26)

The API practical exercise demonstrates Laravel's MVC flow, JSON responses, API Resources, and REST CRUD operations for books. Run migrations and start the application first:

```powershell
php artisan migrate
php artisan serve
```

All endpoints are under `/api/books` and accept JSON. Send `Accept: application/json` so validation and not-found errors are returned as JSON.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/books` | List books |
| `POST` | `/api/books` | Create a book (`title` required; `description` and non-negative integer `status` optional) |
| `GET` | `/api/books/{id}` | Show a book |
| `PUT` | `/api/books/{id}` | Replace a book (`title`, `description`, and `status` required; nullable fields may be `null`) |
| `PATCH` | `/api/books/{id}` | Partially update a book |
| `DELETE` | `/api/books/{id}` | Delete a book |

Book data is returned through `BookResource` under a `data` key; validation errors use HTTP `422`, missing books use `404`, and successful creation uses `201`.

For Acara 26, import [Acara-25-26-Laravel-Books-API.postman_collection.json](./postman/collections/Acara-25-26-Laravel-Books-API.postman_collection.json) into Postman and run the requests in order with Collection Runner. The create request saves the new ID for the show, update, and delete requests. The collection defaults to `http://127.0.0.1:8000/api`.

## Low-stock alerts

The dashboard, product list, and cashier screen flag active products with stock at or below `LOW_STOCK_THRESHOLD`. The default is 5 units. Set that environment variable to another non-negative whole number and clear the config cache after changing it:

```powershell
php artisan config:clear
```

The product list warning opens a filtered view of low-stock products.

## Production checklist

- Set `APP_ENV=production` and `APP_DEBUG=false` in the deployment environment. `APP_DEBUG` defaults to false; do not enable it on a public server.
- Use a fresh production-only `APP_KEY`, unique admin/cashier email addresses and long, different passwords. Do not deploy the local `.env`, demo accounts, or passwords.
- Configure a least-privilege database user, HTTPS, secure session cookies (`SESSION_SECURE_COOKIE=true`), and a real mail transport before enabling password reset.
- Keep `.env` private, restrict access to database backups, and regularly test restoring a backup before relying on it.
- After setting production environment values, run `php artisan config:cache`, `php artisan route:cache`, and `php artisan view:cache`. Run `php artisan optimize:clear` before changing those values.

### Back up XAMPP MySQL/MariaDB on Windows

From the project root, run the included PowerShell script. It creates a timestamped SQL dump under the private `storage\app\private\database-backups` directory (ignored by Git). `mysqldump` prompts for the database password. For the default local XAMPP root account with no password, pass `-NoPassword`.

```powershell
.\scripts\backup-database.ps1 -DatabaseName minimarket -DatabaseUser root -NoPassword
```

Pass `-MysqldumpPath`, `-DatabaseHost`, or `-DatabasePort` if your XAMPP/MySQL installation uses different values. Copy backups to encrypted off-site storage and apply a retention policy; keeping the only backup on the database machine does not protect against disk loss.

## Tests

Run the test suite with:

```powershell
php artisan test
```

The test environment uses an in-memory SQLite database and does not require access to the local MySQL database.
