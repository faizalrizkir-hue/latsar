# Public Deployment Readiness (No Feature Change)

Dokumen ini hanya untuk persiapan deploy ke hosting + domain publik.
Tidak mengubah alur fitur inti aplikasi.

## 1) Rekomendasi Hosting

Paling aman gunakan VPS (Ubuntu/Nginx/PHP-FPM/MySQL/Redis optional), bukan shared hosting biasa.
Alasan:

- Aplikasi menggunakan queue database dan Reverb (websocket) yang butuh proses background.
- Anda perlu kontrol process manager (`systemd` atau `supervisor`).

Jika tetap memakai shared hosting:

- Pastikan provider mendukung long-running process untuk queue worker dan websocket server.
- Jika tidak didukung, fitur realtime akan terbatas.

## 2) Checklist Go-Live

- [ ] Domain sudah mengarah ke server (`A/AAAA` record).
- [ ] SSL aktif (Let's Encrypt atau managed cert).
- [ ] `APP_ENV=production`, `APP_DEBUG=false`.
- [ ] `APP_URL` menggunakan `https://domain-anda`.
- [ ] `APP_KEY` terisi valid.
- [ ] DB production terpisah dari lokal/dev.
- [ ] Folder upload persistent dan sudah `php artisan storage:link`.
- [ ] Queue worker aktif otomatis saat server restart.
- [ ] Reverb aktif otomatis saat server restart.
- [ ] Backup database + upload berjalan terjadwal.
- [ ] Logs terpantau dan rotasi log aktif.

## 3) Template Environment Production

Gunakan file `.env.production.example` sebagai baseline.

Langkah:

1. Copy ke `.env` di server production.
2. Isi semua secret (`APP_KEY`, DB, SMTP, Reverb keys, Recaptcha).
3. Validasi cepat:

```bash
bash scripts/deploy/preflight.sh .env
```

```powershell
.\scripts\deploy\preflight.ps1 -EnvFile .env
```

Untuk hosting tanpa websocket (realtime dibatasi), gunakan mode optional:

```bash
bash scripts/deploy/preflight.sh .env --skip-realtime
```

```powershell
.\scripts\deploy\preflight.ps1 -EnvFile .env -SkipRealtime
```

## 4) First Deploy (Server)

Jalankan dari root project:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan optimize:clear
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

Catatan:

- `npm` bisa dipindah ke CI/CD. Server hanya menerima artifact build.
- Jalankan `php artisan down` sebelum migrasi jika ada perubahan schema yang berisiko.

Optimasi runtime ulang setelah deploy update rutin (tanpa migrate) bisa pakai script:

```bash
bash scripts/deploy/optimize-runtime.sh --clear-first
```

```powershell
.\scripts\deploy\optimize-runtime.ps1 -PhpBin php -ClearFirst
```

## 5) Process Manager (Contoh systemd)

### Queue Worker

`/etc/systemd/system/latsar-queue.service`

```ini
[Unit]
Description=Latsar Laravel Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/latsar-laravel
ExecStart=/usr/bin/php artisan queue:work --queue=default --sleep=3 --tries=3 --timeout=120
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

### Reverb

`/etc/systemd/system/latsar-reverb.service`

```ini
[Unit]
Description=Latsar Laravel Reverb Server
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/latsar-laravel
ExecStart=/usr/bin/php artisan reverb:start --host=0.0.0.0 --port=8080
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

Aktifkan:

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now latsar-queue
sudo systemctl enable --now latsar-reverb
```

## 6) Nginx Reverse Proxy Ringkas

Tambahkan proxy websocket ke Reverb:

```nginx
location /app {
    proxy_http_version 1.1;
    proxy_set_header Host $host;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_read_timeout 60s;
    proxy_pass http://127.0.0.1:8080;
}
```

Set environment:

- `REVERB_HOST=domain-anda`
- `REVERB_PORT=443`
- `REVERB_SCHEME=https`
- `REVERB_SERVER_HOST=0.0.0.0`
- `REVERB_SERVER_PORT=8080`

## 7) Cache Asset Statis + Kompresi

Tujuan: mempercepat load ulang halaman tanpa mengubah perilaku fitur.

### Apache

`public/.htaccess` sudah disiapkan untuk:

- cache aset statis (`css/js/font/image`) dengan `Cache-Control`.
- kompresi teks (`DEFLATE`) untuk HTML/CSS/JS/JSON/SVG.

### Nginx

Jika menggunakan Nginx, tambahkan kebijakan ekuivalen:

```nginx
location ~* \.(css|js|mjs|map|jpg|jpeg|png|gif|webp|avif|svg|ico|ttf|otf|woff|woff2)$ {
    expires 1d;
    add_header Cache-Control "public, max-age=86400";
    access_log off;
}

gzip on;
gzip_types text/plain text/css application/javascript application/json application/xml image/svg+xml;
gzip_min_length 1024;
```

## 8) Post Deploy Smoke Check

```bash
php artisan about
php artisan migrate:status
php artisan route:list --except-vendor > /dev/null
php artisan queue:failed
```

Uji manual minimal:

- Login/logout
- Buka dashboard
- Buka DMS list/filter
- Upload dokumen kecil
- Cek notifikasi realtime

## 9) Rollback Cepat

1. Kembalikan ke commit stabil sebelumnya.
2. Jalankan `composer install --no-dev --prefer-dist --optimize-autoloader`.
3. Jalankan `php artisan optimize`.
4. Restart service queue + reverb.

Jika migrasi tidak backward-compatible, siapkan backup DB sebelum deploy dan gunakan prosedur restore DB.

## 10) Untuk Subdomain Pemerintah

Jika deploy ke subdomain pemerintah (`*.jakarta.go.id`), pakai checklist khusus:

- `docs/GOV_HOSTING_SUBDOMAIN_CHECKLIST.md`
