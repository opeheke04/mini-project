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
- Admins can access `/sales` for searchable transaction history, transaction details, and daily/monthly sales summaries.
- Admins and cashiers can access `/cashier` and record transactions.
- The `admin` and `cek.role` middleware enforce the role restrictions.
- Public registration is disabled. Admins can create cashier accounts from the **Akun Kasir** menu; new accounts require a password of at least 16 characters containing letters and numbers.

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
