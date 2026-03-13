# HANDOVER LATSAR (Codespaces)

Terakhir diperbarui: 13 Maret 2026

## Tujuan
Dokumen ini dipakai saat pindah device/client (Browser Codespace <-> VS Code Desktop) agar tidak tergantung history chat.

## Repo & Branch
- Repo: `https://github.com/faizalrizkir-hue/latsar.git`
- Branch aktif: `main`

## Commit Penting Terbaru
- `7372eae2` auto-start MariaDB saat startup Codespace
- `87513c6e` fallback route untuk static assets + redirect absolut
- `b1f47375` path asset root-relative (`/css/...`, `/static/...`)
- `d26b1899` dukung `ASSET_URL` di `config/app.php`
- `ad3b6e34` auto set `APP_URL` + `ASSET_URL` saat start Codespace

## Gejala Yang Pernah Muncul
- CSS tidak termuat (halaman terlihat polos)
- Error DB: `SQLSTATE[HY000] [2002] Connection refused`
- Error driver sebelumnya: `could not find driver` (sudah ditangani via script setup)

## Recovery Cepat (Jalankan Berurutan di Terminal Codespace)
```bash
cd /workspaces/latsar
git pull origin main

# sinkron URL public + auto start mariadb
bash .devcontainer/post-start.sh

# jika DB belum hidup, setup ulang mysql + extension
mysqladmin -h 127.0.0.1 -u latsar -platsar123 ping || bash scripts/codespaces/setup-mysql.sh

# import data dump (jalankan saat data kosong / habis setelah rebuild)
bash scripts/codespaces/import-mysql-dump.sh ./latsar.sql

# clear cache laravel
php artisan optimize:clear

# start app
pkill -f "artisan serve|php -S" || true
nohup php -d display_errors=0 -d xdebug.mode=off artisan serve --host=0.0.0.0 --port=8000 >/tmp/laravel-serve.log 2>&1 &
sleep 2
```

## Verifikasi Cepat
```bash
curl -I http://127.0.0.1:8000/login
curl -I http://127.0.0.1:8000/css/login.css
curl -I http://127.0.0.1:8000/static/logo-sikap-light.png
mysql -u latsar -platsar123 -D latsar -e "SHOW TABLES LIKE 'sessions'; SELECT COUNT(*) AS accounts FROM accounts;"
```

Expected:
- `curl` return `HTTP/1.1 200 OK`
- tabel `sessions` ada
- `accounts` count > 0

## Jika Masih Error
Kirim output command ini untuk diagnosis:
```bash
mysqladmin -h 127.0.0.1 -u latsar -platsar123 ping
php -m | grep -Ei '^pdo$|pdo_mysql|mysqlnd'
tail -n 120 /tmp/laravel-serve.log
```

## Catatan Operasional
- Untuk akses aplikasi di browser, gunakan URL port forward Codespaces:
  - `https://<codespace-name>-8000.app.github.dev`
- Jangan tambah `:8000` di URL public `app.github.dev`.
- Lebih stabil troubleshooting langsung dari browser Codespace (terminal + preview dalam satu environment).
