# Backlog Prioritas Substansi Project

Dokumen ini fokus untuk peningkatan **fitur inti aplikasi** (bukan deploy).
Status baseline saat dokumen ini dibuat: smoke test manual 10/10 PASS.

## Tujuan
- Menjaga stabilitas alur utama: login, dashboard, element, DMS, notifikasi.
- Menurunkan risiko regresi saat perubahan fitur berikutnya.
- Meningkatkan kejelasan alur kerja user admin/QA/anggota.

## Cara Prioritasi
- `P0`: kritis, berdampak langsung ke data/flow utama.
- `P1`: penting, meningkatkan kualitas penggunaan harian.
- `P2`: penyempurnaan, nilai tambah jangka menengah.

## P0 - Dikerjakan Dulu

1. Feature test end-to-end alur Element (Mandiri -> Verifikasi -> QA -> Rekap)
- Nilai: mencegah regresi logika scoring/verifikasi.
- Ruang lingkup:
  - test transisi status verifikasi per row.
  - test perhitungan level dan skor tertimbang.
  - test hasil rekap sesuai data sumber.
- Selesai jika:
  - minimal 3 skenario utama lulus otomatis.
  - perubahan pada controller/service terkait tetap hijau di CI lokal.

2. Feature test arsip progress (archive/load/log)
- Nilai: memastikan fitur arsip aman untuk data operasional.
- Ruang lingkup:
  - test simpan arsip.
  - test load arsip mengembalikan data sesuai snapshot.
  - test log pemulihan tercatat.
- Selesai jika:
  - semua endpoint archive/load teruji happy path + gagal validasi.

3. Hardening validasi upload DMS
- Nilai: menurunkan risiko file tidak valid dan abuse upload.
- Ruang lingkup:
  - whitelist MIME + extension yang konsisten.
  - batas ukuran file per upload.
  - sanitasi nama file saat simpan.
- Selesai jika:
  - upload file valid tetap berjalan.
  - file tidak valid ditolak dengan pesan jelas.

## P1 - Setelah P0 Stabil

1. Audit UX notifikasi (isi teks, grouping, read-state)
- Nilai: notifikasi lebih cepat dipahami user.
- Ruang lingkup:
  - standar format pesan notifikasi.
  - konsistensi status belum dibaca/dibaca.
  - evaluasi ringkasan notifikasi di shell.
- Selesai jika:
  - tidak ada format teks ambigu.
  - alur mark-as-read konsisten antar halaman.

2. Optimasi query dashboard berbasis log profiling
- Nilai: menjaga performa saat data tumbuh.
- Ruang lingkup:
  - review log dari `ProfileDashboardQueries`.
  - tambah index baru hanya jika memang dibutuhkan query real.
  - validasi dampak index ke query paling lambat.
- Selesai jika:
  - total waktu query dashboard turun dibanding baseline.

3. Uji akses berbasis role yang lebih lengkap
- Nilai: mencegah akses tidak sesuai hak user.
- Ruang lingkup:
  - tambah test matrix role admin/qa/anggota.
  - cek route kritis: element-preferences, account, update element.
- Selesai jika:
  - route terproteksi sesuai role tanpa false positive.

## P2 - Penyempurnaan

1. Rapikan komponen UI yang padat script inline
- Nilai: maintenance lebih mudah.
- Ruang lingkup:
  - pecah script besar di Blade menjadi modul JS terstruktur.
  - kurangi duplikasi utility JS antar halaman.
- Selesai jika:
  - file Blade lebih ringkas, behavior tetap sama.

2. Peningkatan observability aplikasi harian
- Nilai: debugging lebih cepat saat ada laporan user.
- Ruang lingkup:
  - tambah ringkasan metrik ringan (error rate, slow query trend).
  - SOP review log mingguan.
- Selesai jika:
  - ada baseline metrik yang dipantau rutin.

## Rencana Eksekusi Disarankan

Minggu 1:
- P0.1 dan P0.2

Minggu 2:
- P0.3 dan stabilisasi hasil test

Minggu 3:
- P1.1 dan P1.3

Minggu 4:
- P1.2 (berbasis data profiling)

## Catatan Kerja
- Setiap item selesai harus ditutup dengan:
  - test otomatis terkait (minimal scope item).
  - smoke test manual singkat pada alur terdampak.
  - commit terpisah per item agar rollback mudah.
