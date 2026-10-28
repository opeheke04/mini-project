# Minimarket POS Mobile

Flutter companion app for the Laravel minimarket POS. Admins can view product stock, record incoming stock, and review recent restock history.

## Run on Android

Start the Laravel app from the repository root. Android emulators can reach the host computer through `10.0.2.2`:

```powershell
php artisan migrate
php artisan serve --host 0.0.0.0
cd mobile
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/mobile
```

For a physical Android device, use the computer's LAN IP address in `API_BASE_URL` and allow port 8000 through the local firewall. The debug Android manifest permits HTTP only for local testing.

## Run on iOS

iOS builds require macOS and Xcode. Use an HTTPS API URL for a physical device or a development URL reachable from the simulator:

```sh
flutter run --dart-define=API_BASE_URL=https://your-domain.example/api/mobile
```

## API and release builds

The app authenticates with an admin account using the Sanctum endpoints documented in the repository [README](../README.md#restock-and-mobile-app). API tokens are stored with `flutter_secure_storage`, scoped to inventory access, expire after 30 days, and are revoked when signing out.

Release signing is intentionally not configured in this project. Configure a private signing key and use an HTTPS API endpoint before distributing Android or iOS builds.
