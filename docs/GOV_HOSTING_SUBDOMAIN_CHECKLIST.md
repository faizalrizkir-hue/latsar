# Checklist Hosting Pemerintah (`*.jakarta.go.id`)

Dokumen ini untuk koordinasi dengan tim infrastruktur pemerintah saat go-live.
Fokusnya kesiapan hosting, bukan perubahan fitur aplikasi.

## 1) Data yang Perlu Diminta ke Tim Infra

Kirim daftar ini dan minta jawaban `YA/TIDAK + keterangan`:

1. Bisa menyediakan subdomain final (contoh: `namasistem.jakarta.go.id`).
2. SSL/TLS aktif dan auto-renew.
3. Hosting mendukung PHP `8.3+` dan extension yang dibutuhkan Laravel.
4. Bisa menjalankan process background persisten (`queue:work`).
5. Bisa menjalankan websocket server (`php artisan reverb:start`).
6. Reverse proxy websocket (`Upgrade` / `Connection`) diperbolehkan.
7. Cron job tersedia (untuk backup/maintenance).
8. Akses log aplikasi tersedia (minimal file log).
9. Outbound internet ke Google reCAPTCHA diperbolehkan.
10. Policy firewall mengizinkan koneksi internal app -> DB.
11. Storage upload persisten dan tidak terhapus saat deploy.
12. Batas upload file sesuai kebutuhan aplikasi (minimal 5 MB per file).
13. Dukungan cache header + kompresi HTTP (gzip/brotli) untuk asset statis.
14. Cron scheduler diizinkan (`php artisan schedule:run` tiap menit).

## 2) Pilih Profil Deploy

Gunakan salah satu profil ini berdasarkan jawaban infra:

### Profil A: Full Realtime (direkomendasikan)

Pakai ini jika poin 4, 5, 6 = `YA`.

Set `.env` penting:

- `APP_URL=https://subdomain-anda.jakarta.go.id`
- `SESSION_DOMAIN=subdomain-anda.jakarta.go.id`
- `SESSION_SECURE_COOKIE=true`
- `BROADCAST_CONNECTION=reverb`
- `REVERB_HOST=subdomain-anda.jakarta.go.id`
- `REVERB_PORT=443`
- `REVERB_SCHEME=https`
- `REVERB_SERVER_HOST=0.0.0.0`
- `REVERB_SERVER_PORT=8080`

Validasi:

```bash
bash scripts/deploy/preflight.sh .env
```

```powershell
.\scripts\deploy\preflight.ps1 -EnvFile .env
```

### Profil B: Restricted (tanpa websocket)

Pakai ini jika poin 5 atau 6 = `TIDAK`.

Catatan: fitur notifikasi tetap jalan via polling periodik (bukan push realtime websocket).

Set `.env` penting:

- `APP_URL=https://subdomain-anda.jakarta.go.id`
- `SESSION_DOMAIN=subdomain-anda.jakarta.go.id`
- `SESSION_SECURE_COOKIE=true`
- `BROADCAST_CONNECTION=log`
- `REVERB_APP_ID=`
- `REVERB_APP_KEY=`
- `REVERB_APP_SECRET=`

Validasi:

```bash
bash scripts/deploy/preflight.sh .env --skip-realtime
```

```powershell
.\scripts\deploy\preflight.ps1 -EnvFile .env -SkipRealtime
```

## 3) Uji Terima Minimal (Setelah Deploy)

1. Login/logout berhasil.
2. Dashboard bisa dibuka normal.
3. DMS list/filter/upload berjalan.
4. Link file upload valid pada domain publik (tanpa path lokal).
5. Session tetap aman di HTTPS.
6. reCAPTCHA login berjalan (tidak timeout/firewall blocked).
7. Jika Profil A: notifikasi realtime websocket berjalan.
8. Jika Profil B: notifikasi tetap refresh via polling.
9. Health check command (`php artisan ops:health`) berjalan normal.
10. Backup script + verify backup lulus.

## 4) Risiko Umum di Hosting Pemerintah

1. Websocket diblok reverse proxy/firewall.
2. Proses background dimatikan otomatis oleh policy shared hosting.
3. Outbound internet dibatasi (reCAPTCHA/CDN gagal).
4. `APP_URL`/`SESSION_DOMAIN` salah sehingga cookie/session tidak stabil.

## 5) Mitigasi Ringkas

1. Siapkan fallback Profil B jika websocket tidak disetujui.
2. Hindari hardcoded URL/path lokal di kode.
3. Gunakan preflight script sebelum go-live.
4. Pastikan ada jadwal backup DB + upload dan prosedur rollback.
