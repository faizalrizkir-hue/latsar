# Backup & Restore Drill

Dokumen ini untuk memastikan backup bisa dipulihkan, bukan hanya dibuat.

## 1) Buat Backup

### Windows (Laragon)

```powershell
.\scripts\backup\create-backup.ps1 -EnvFile .env -OutputDir backups/runtime -MysqldumpBin "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe"
```

### Linux

```bash
bash scripts/backup/create-backup.sh .env backups/runtime
```

Output backup per run:

- `db.sql`
- `uploads.zip` (Windows) atau `uploads.tar.gz` (Linux) jika folder upload berisi file
- `manifest.json` (Windows) atau `manifest.txt` + `checksums.sha256` (Linux)

## 2) Verifikasi Backup

### Windows

```powershell
.\scripts\backup\verify-backup.ps1 -BackupDir backups/runtime/<timestamp>
```

### Linux

```bash
bash scripts/backup/verify-backup.sh backups/runtime/<timestamp>
```

## 3) Restore Drill (Staging)

Lakukan di database/folder staging, bukan production langsung.

1. Siapkan DB kosong staging.
2. Import `db.sql`.
3. Restore arsip `uploads`.
4. Jalankan smoke test aplikasi:
   - login/logout
   - dashboard
   - DMS list dan akses file
5. Catat durasi restore + kendala untuk perbaikan SOP.

## 4) Frekuensi Rekomendasi

- Backup harian otomatis (minimal 1x/hari).
- Verifikasi backup minimal mingguan.
- Restore drill minimal bulanan.
