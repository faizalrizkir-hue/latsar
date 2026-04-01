# PROJECT CONTEXT (Local Laragon)

Terakhir diperbarui: 1 April 2026

## Runtime Profile
- Mode aktif: Windows + Laragon (tanpa WSL).
- Root repo: `C:\laragon\www\latsar-laravel`.
- Branch aktif: `main`.
- App stack:
  - Laravel 12
  - PHP 8.3 (Laragon)
  - MySQL/MariaDB (Laragon)
  - Reverb + Pusher JS untuk realtime notifikasi
  - Vite untuk asset dev

## Entry Points
- HTTP routes: `routes/web.php`
- Middleware aliases: `bootstrap/app.php`
- Main shell layout: `resources/views/layouts/dashboard-shell.blade.php`

## Functional Modules
- Auth & session:
  - `app/Http/Controllers/AuthController.php`
  - `app/Http/Middleware/EnsureLoggedIn.php`
  - Session idle timeout pakai `session.idle_timeout`.
- Dashboard scoring:
  - `app/Http/Controllers/DashboardController.php`
  - Menghitung skor mandiri + skor QA per element/subtopik.
- Element scoring form:
  - `app/Http/Controllers/ElementController.php`
  - Source konfigurasi dinamis dari `ElementPreferenceService`.
- Element preference + archive:
  - `app/Http/Controllers/ElementPreferenceController.php`
  - `app/Services/ElementPreferenceService.php`
  - Fitur: update struktur, reset data, archive/load progress.
- Team assignment & access scope:
  - `app/Models/ElementTeamAssignment.php`
  - Dipakai untuk filter akses halaman/notification channel.
- Notification realtime:
  - `app/Models/Notification.php`
  - `app/Http/Controllers/NotificationController.php`
  - Event: `app/Events/NotificationFeedUpdated.php`
  - Read state: `notification_reads` table.
- DMS:
  - `app/Http/Controllers/DmsController.php`
  - `app/Livewire/DmsTable.php`
  - Models: `DmsDocument`, `DmsFile`.
- AoI:
  - `app/Http/Controllers/AoiController.php`
  - Mengambil QA verify note + follow-up recommendation dari tabel subtopik.
- Informasi umum:
  - `app/Http/Controllers/GeneralInformationController.php`
  - Model: `GeneralInformationProfile`.

## Critical Config
- `config/database.php`
  - Ada guard `server_lock` untuk cegah salah konek DB server.
- `config/element_summary_modules.php`
  - Konfigurasi element summary + bobot antar subtopik.
- `config/element_subtopic_modules.php`
  - Konfigurasi subtopik, rows, weights, info levels, model mapping.
- `config/broadcasting.php` + `config/reverb.php`
  - Koneksi realtime notifikasi.

## Important Migrations (Recent)
- `2026_03_27_000001_create_element_progress_archives_table.php`
- `2026_03_31_000001_create_element_progress_archive_load_logs_table.php`
- `2026_03_30_120000_add_scope_columns_to_notifications_table.php`
- `2026_03_30_130000_create_notification_reads_table.php`
- `2026_03_30_130100_add_performance_indexes_to_notifications_table.php`
- `2026_03_25_*` (QA final verification + follow up + level validation state)

## Local Ops Scripts
- DB conflict cleanup (3306):
  - `scripts/fix-db-conflict-laragon.ps1`
- WSL uninstall (arsip maintenance OS, bukan runtime app):
  - `scripts/uninstall-wsl-total.ps1`

## Quick Recovery Commands
```powershell
$php = "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe"
& $php artisan optimize:clear
& $php artisan migrate --force
& $php artisan db:seed --force
& $php artisan route:list --except-vendor
```

## Current Working Tree Situation
- Banyak perubahan belum commit pada controller/view/css/config terkait:
  - Realtime notifikasi
  - Archive progress
  - General information
  - AoI
  - DB lock middleware
- Selalu cek sebelum lanjut task:
  - `git status --short`
  - `git diff --stat`

## Notes
- `README.md` sudah memuat section lokal Laragon.
- Handover operasional utama: `docs/HANDOVER.md`.
