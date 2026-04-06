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

## 3) Runtime Services

Gunakan template hardened systemd di:

- `scripts/deploy/systemd/latsar-queue.service`
- `scripts/deploy/systemd/latsar-reverb.service`

## 4) Review Rutin

Rutin cek:

- `storage/logs/ops-health.log`
- `php artisan queue:failed`
- status systemd `latsar-queue` dan `latsar-reverb`
