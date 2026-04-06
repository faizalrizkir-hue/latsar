# OPS Monitoring Baseline

## 1) Health Check Command

Command:

```bash
php artisan ops:health
```

Mode JSON (untuk log/collector):

```bash
php artisan ops:health --json
```

Status keluaran:

- `ok`: semua check lolos.
- `warn`: ada kondisi perlu perhatian.
- `fail`: ada kegagalan kritis (exit code non-zero).

Parameter ambang via `.env`:

- `OPS_MAX_FAILED_JOBS`
- `OPS_MAX_PENDING_JOBS`
- `OPS_HEALTH_LOG_FILE`

## 2) Scheduler

Command `ops:health` sudah dijadwalkan setiap 5 menit lewat Laravel scheduler.

Pastikan cron aktif di server:

```cron
* * * * * cd /var/www/latsar-laravel && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

## 3) Dashboard Query Profiling (Opsional)

Tujuan: mendeteksi query lambat di route dashboard tanpa mengubah behavior fitur.

Env:

- `DASHBOARD_QUERY_PROFILE_ENABLED` (`true/false`)
- `DASHBOARD_SLOW_QUERY_MS`
- `DASHBOARD_TOTAL_QUERY_BUDGET_MS`
- `DASHBOARD_PROFILE_MAX_LOGGED_QUERIES`

Saat threshold terlampaui, log warning akan muncul di log Laravel default.

## 4) Schema Metadata Cache

Cache metadata schema (`hasTable`, `hasColumn`, `columnListing`) digunakan untuk mengurangi query berulang ke `information_schema`.

Env:

- `SCHEMA_METADATA_TTL_SECONDS`

Invalidasi manual:

```bash
php artisan ops:schema-cache:bump
```

Script deploy optimize akan menjalankan command ini otomatis (best effort).

## 5) Runtime Services

Gunakan template hardened systemd di:

- `scripts/deploy/systemd/latsar-queue.service`
- `scripts/deploy/systemd/latsar-reverb.service`

## 6) Review Rutin

Rutin cek:

- `storage/logs/ops-health.log`
- `php artisan queue:failed`
- status systemd `latsar-queue` dan `latsar-reverb`
