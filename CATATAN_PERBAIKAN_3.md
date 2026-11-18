# SIPEKA — Perbaikan (putaran 4): ulasan hanya mahasiswa, pilih penanganan, filter kategori

## Cara pasang
1. Salin isi folder ini ke root project (timpa file yang sama). `koneksi.php` dan `landing.css` Anda tidak disentuh.
2. Pastikan **empat** migrasi sudah dijalankan, berurutan (semuanya aman diulang):
   ```bash
   psql -U postgres -d db_sipekabaru -f DB/migrasi_ulasan_notifikasi.sql
   psql -U postgres -d db_sipekabaru -f DB/migrasi_balasan_ulasan.sql      # tidak ada di repo Anda, saya sertakan lagi
   psql -U postgres -d db_sipekabaru -f DB/migrasi_ulasan_komentar.sql
   psql -U postgres -d db_sipekabaru -f DB/migrasi_ulasan_penanganan.sql   # BARU
   ```
3. Muat ulang browser dengan Ctrl+F5.

## Yang berubah
| # | Permintaan | Hasil |
|---|---|---|
| 1 | Unit hanya membalas; landing hanya ulasan mahasiswa + balasan unit | Hanya **mahasiswa** yang bisa menulis ulasan (dijaga di halaman, di PHP `ulasan/saya.php`, dan di trigger database). Halaman Ulasan unit tidak punya kotak tulis, hanya daftar ulasan mahasiswa dengan tombol **Balas**. Unit hanya bisa membalas ulasan yang ditulis mahasiswa. Halaman depan menampilkan **ulasan mahasiswa dan balasan unit saja** (balasan mahasiswa ke mahasiswa tetap terlihat di halaman Ulasan setelah login). Ulasan lama milik unit tidak dihapus, hanya tidak ditampilkan |
| 2 | Mahasiswa memilih penanganan yang diulas | Di kotak tulis ada pilihan **Penanganan yang diulas**: berisi laporan Anda yang berstatus **selesai** (tiket + judul + kategori, ditandai "sudah diulas n×"), atau biarkan **Ulasan umum**. Satu penanganan boleh diulas lebih dari sekali. Kartu ulasan menampilkan kategori dan unit yang menangani. Unit penanggung jawab mendapat **notifikasi** saat ulasan untuk penanganannya masuk |
| 3 | Filter kategori | Filter **Kategori** ditambahkan di halaman Ulasan (mahasiswa dan 4 unit) dan halaman depan, bersama filter periode dan kata kunci. Filter "Penulis" dihapus karena semua ulasan kini dari mahasiswa |

## Keputusan yang saya ambil (silakan koreksi)
- **Privasi:** judul laporan tidak pernah ditampilkan ke orang lain. Publik dan mahasiswa lain hanya melihat kategori dan unit penanganan; **nomor tiket hanya terlihat oleh pemilik ulasan**.
- Hanya penanganan berstatus **selesai** yang bisa diulas (penanganan yang masih berjalan belum).
- Ulasan tanpa penanganan ("umum") tidak ikut tersaring saat filter kategori dipakai.
- Mahasiswa tetap boleh membalas ulasan (misalnya menanggapi balasan unit); balasan itu hanya tampil di halaman Ulasan, bukan di halaman depan.

## Catatan
- Belum dijalankan terhadap PHP dan PostgreSQL asli. Skema saya cocokkan dengan `DB/sipeka.sql` Anda (id_laporan BIGINT, status enum, role_user). JS diuji dengan DOM tiruan untuk halaman mahasiswa, halaman unit, dan halaman depan. Jika ada error dari API atau migrasi, kirim pesannya.
- Belum ada fitur edit ulasan/balasan dan batas kiriman anti-spam.
