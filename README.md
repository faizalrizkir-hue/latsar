<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Development in Windows Laragon (No WSL)

Project ini saat ini dijalankan utama di Windows + Laragon, tanpa WSL.

1. Start service Laragon (MySQL + web server).
2. Set PHP binary Laragon:

```powershell
$php = "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe"
```

3. Sinkronisasi project:

```powershell
& $php artisan optimize:clear
& $php artisan migrate --force
& $php artisan db:seed --force
```

4. Jalankan proses aplikasi:

```powershell
# Terminal 1
& $php artisan serve --host=127.0.0.1 --port=8000

# Terminal 2
& $php artisan reverb:start --host=127.0.0.1 --port=8080

# Terminal 3
npm run dev
```

Jika port MySQL bentrok (`3306`), jalankan script ini sebagai Administrator:

```powershell
.\scripts\fix-db-conflict-laragon.ps1
```

Handover operasional lokal: `docs/HANDOVER.md`.
Snapshot konteks teknis: `docs/PROJECT_CONTEXT.md`.

## Development in GitHub Codespaces

You can develop this project from anywhere using GitHub Codespaces.

1. Open repository in GitHub.
2. Click **Code** -> **Codespaces** -> **Create codespace on main**.
3. Wait until container setup is complete (automatic via `.devcontainer/post-create.sh`).
4. Start the app:

```bash
php -d display_errors=0 artisan serve --host=0.0.0.0 --port=8000
npm run dev -- --host 0.0.0.0 --port 5173
```

Default database for Codespaces is SQLite (`database/database.sqlite`).
Do not commit `.env`; store sensitive values using GitHub Codespaces Secrets.
Default seeded login for first run:
- Username: `admin`
- Password: `admin123`

If you see `libcrypto.so.1.1` / `OPENSSL_1_1_1` errors, rebuild the container from VS Code command palette:
`Codespaces: Rebuild Container`.
If needed, run `which php` and ensure it resolves to `/usr/bin/php` or `/usr/local/bin/php`
(not `/home/codespace/.php/current/bin/php`).
If browser URL becomes `...app.github.dev:8000/...`, fix `.env` APP_URL to:
`https://${CODESPACE_NAME}-8000.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}` then run `php artisan optimize:clear`.
If login fails on an existing Codespace, run:
`php artisan migrate --force && php artisan db:seed --force`.
If MySQL driver is still not detected, ensure `/usr/local/etc/php/conf.d/99-pdo-mysql.ini` contains `extension=pdo_mysql`.

### Sync MySQL Data to Codespaces

If your source data is from local MySQL (Laragon), use these scripts:

```bash
bash scripts/codespaces/import-mysql-dump.sh /workspaces/<repo>/latsar.sql
```

What it does:
- Installs and starts MariaDB inside Codespaces (if missing)
- Installs PHP MySQL driver (`pdo_mysql`) if missing
- Enables `pdo_mysql` ini automatically if module exists but is not loaded
- Creates DB/user (`latsar` / `latsar`, password `latsar123`)
- Updates `.env` to MySQL connection
- Imports dump file (and strips `CREATE DATABASE` / `USE` directives to force import into target DB)
- Runs `php artisan migrate --force` and ensures admin seeder

Important:
- A file in `C:\Users\...\Desktop` is not directly readable by Codespaces.
- Upload your dump into repo workspace first (drag-and-drop in VS Code Explorer), then run the script.
- If `apt-get update` fails because of Yarn GPG key in Codespaces, the setup script will disable `yarn.list` automatically and retry.

## Production / Public Deployment

Panduan deploy publik (domain + SSL + queue/reverb + rollback) ada di:

- `docs/DEPLOY_PUBLIC.md`

Template environment production:

- `.env.production.example`

Validasi cepat sebelum go-live:

```bash
bash scripts/deploy/preflight.sh .env
```

```powershell
.\scripts\deploy\preflight.ps1 -EnvFile .env
```

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
