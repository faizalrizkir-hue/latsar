# HANDOVER LATSAR (Windows Laragon)

Terakhir diperbarui: 1 April 2026

## Tujuan
Dokumen ini jadi sumber recovery utama saat history chat hilang, dengan asumsi runtime lokal Windows + Laragon (tanpa WSL).

## Status Lingkungan Aktif
- OS: Windows (PowerShell)
- Root project: `C:\laragon\www\latsar-laravel`
- Runtime: Laragon (MySQL + PHP + Nginx/Apache)
- WSL: sudah tidak dipakai

## Repo & Branch
- Repo: `https://github.com/faizalrizkir-hue/latsar.git`
- Branch aktif: `main`

## Baseline Konfigurasi Lokal
Nilai penting yang saat ini aktif:
- `APP_ENV=local`
- `APP_URL=http://latsar-laravel.test`
- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=latsar`
- `DB_DATABASE_LOCK=latsar`
- `DB_SERVER_UUID_LOCK=<uuid Laragon>`
- `DB_SERVER_ID_LOCK=1`
- `BROADCAST_CONNECTION=reverb`

Catatan:
- Middleware `db.lock` aktif, jadi jika server DB berubah, akses aplikasi akan ditolak (HTTP 503) sampai nilai lock cocok.

## Start Harian (Tanpa WSL)
Jalankan berurutan:

1. Start service Laragon (MySQL + web server).
2. Jika port `3306` bentrok, jalankan sebagai Administrator:
   - `.\scripts\fix-db-conflict-laragon.ps1`
3. Buka terminal di root project lalu set PHP binary:

```powershell
$php = "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe"
```

4. Sinkronisasi app:

```powershell
& $php artisan optimize:clear
& $php artisan migrate --force
& $php artisan db:seed --force
```

5. Jalankan proses aplikasi (3 terminal):

```powershell
# Terminal 1
& $php artisan serve --host=127.0.0.1 --port=8000

# Terminal 2
& $php artisan reverb:start --host=127.0.0.1 --port=8080

# Terminal 3
npm run dev
```

## Verifikasi Cepat
```powershell
# Route harus kebaca
& $php artisan route:list --except-vendor

# Login page harus 200
(Invoke-WebRequest "http://127.0.0.1:8000/login" -UseBasicParsing).StatusCode

# Cek DB aktif yang dipakai aplikasi
& $php artisan tinker --execute="echo DB::scalar('SELECT DATABASE()');"
```

Expected:
- `route:list` tampil tanpa error.
- `StatusCode` = `200`.
- nama DB = `latsar`.

## Komponen Fitur Kunci (Konteks Terbaru)
- Dynamic konfigurasi element/subtopik/bobot: `ElementPreferenceService`.
- Arsip progress per tahun + load archive + log restore.
- Realtime notifikasi (Reverb) + read state (`notification_reads`).
- Halaman AoI dari hasil verifikasi final QA.
- Halaman Informasi Umum + daftar dasar hukum dari `/public/uploads/pedoman`.
- Guard koneksi DB server lock (`EnsureDatabaseServerLock`).

## File Kunci Untuk Recovery
- `routes/web.php`
- `bootstrap/app.php`
- `config/database.php`
- `config/element_summary_modules.php`
- `config/element_subtopic_modules.php`
- `app/Services/ElementPreferenceService.php`
- `app/Http/Controllers/ElementController.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/NotificationController.php`
- `resources/views/layouts/dashboard-shell.blade.php`

## Snapshot Diagnostik Saat Task Hilang
```powershell
git status --short
git log --oneline -n 12
git diff --stat
```

## Catatan Operasional
- Script `scripts/uninstall-wsl-total.ps1` dan log WSL tidak dipakai untuk runtime aplikasi; hanya arsip maintenance OS.
- Jika pindah mesin Laragon atau install DB baru, update nilai lock di `.env`:
  - `DB_DATABASE_LOCK`
  - `DB_SERVER_UUID_LOCK`
  - `DB_SERVER_ID_LOCK`
