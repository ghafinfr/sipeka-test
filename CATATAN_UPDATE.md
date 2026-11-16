# SIPEKA — Update: Ulasan, Notifikasi, Tampilan Interaktif, Dashboard Tailwind

## Cara pasang (urutan penting)
1. **Salin** isi folder ini ke root project (struktur folder sama; file lama yang berubah akan tertimpa).
2. **Jalankan migrasi database** (sekali saja, aman diulang):
   ```bash
   psql -U postgres -d db_sipekabaru -f DB/migrasi_ulasan_notifikasi.sql
   ```
3. Buka aplikasi, login sebagai mahasiswa dan sebagai unit, lalu cek daftar uji di bawah.

## Yang ditambahkan
| Permintaan | Hasil |
|---|---|
| **Ulasan** | Tabel `ulasan` + view `v_ulasan_publik`; API `ulasan/list.php` (publik) & `ulasan/saya.php`; halaman **Ulasan** di sidebar mahasiswa dan 4 unit (bintang 1–5, teks 10–500 karakter, bisa diedit/dihapus, 1 akun = 1 ulasan); bagian `#ulasan` di halaman depan kini terisi otomatis |
| **Notifikasi** | Tabel `notifikasi` + 3 trigger database; API `notifikasi/list.php` & `baca.php`; bel di header (desktop) dan topbar (HP), badge angka, tandai dibaca, klik membuka tiket terkait, cek ulang tiap 30 detik, toast saat ada yang baru |
| **Tampilan interaktif** | Angka statistik naik perlahan, baris tabel muncul bertahap, skeleton saat memuat, ripple tombol, kartu statistik **bisa diklik** untuk memfilter tabel, titik merah berkedip untuk laporan menunggu > 3 hari, tombol hapus-pencarian & muat-ulang, tabel unit otomatis refresh saat laporan baru masuk |
| **Dashboard pakai Tailwind** | Isi dashboard mahasiswa dan 4 unit memakai kelas Tailwind (+ halaman Ulasan baru) |
| **Pakai ulang CSS/JS lama** | Sidebar, header, modal, tabel (`custom-table`), badge, tombol, `getStatusBadge`, `showToast`, `escapeHtml`, `sipekaAPI` tetap dipakai; kode baru hanya menambah |

### Notifikasi dibuat oleh database, bukan PHP
Trigger membuat notifikasi otomatis, jadi `create.php`, `validasi.php`, `tanggapan.php`, `selesai.php` **tidak diubah**:
- laporan baru → semua petugas unit penanggung jawab kategori
- status jadi *dalam penanganan / ditolak / selesai* → mahasiswa pelapor
- tanggapan unit → mahasiswa pelapor

## Di mana mengubah sesuatu
| Ingin mengubah | Edit file |
|---|---|
| Isi dashboard **semua unit** sekaligus | `frontend/includes/unit-dashboard-content.php` (teks per unit ada di array `$dash` di tiap `dashboard.php`) |
| Isi halaman Ulasan | `frontend/includes/ulasan-content.php` |
| Konfigurasi/warna Tailwind | `frontend/includes/tailwind.php` (warna `brand` mengikuti `themes.css`) |
| Animasi, bel, bintang | `frontend/assets/css/interaktif.css`, `assets/js/core/interaktif.js`, `core/notifikasi.js` |
| Interval cek notifikasi | `NOTIF_INTERVAL_MS` di `core/notifikasi.js` |
| Sembunyikan ulasan dari halaman depan | `UPDATE ulasan SET tampil = FALSE WHERE id_ulasan = ...;` |

## Daftar uji
- [ ] Mahasiswa buat laporan → login unit terkait: bel menampilkan angka 1 dan tabel dashboard ikut terisi
- [ ] Unit validasi / tolak / tanggapi / selesaikan → mahasiswa: bel bertambah; klik notifikasi membuka riwayat tiket tsb
- [ ] "Tandai semua dibaca" mengosongkan badge; muat ulang halaman, badge tetap kosong
- [ ] Dashboard unit: klik kartu "Selesai" → tabel terfilter, klik lagi → kembali semua
- [ ] Dashboard mahasiswa: klik kartu "Ditolak" → Riwayat terbuka dengan filter Ditolak
- [ ] Kirim ulasan dari mahasiswa dan dari unit → muncul di bagian Ulasan halaman depan (nama mahasiswa disamarkan, mis. "Budi P.")
- [ ] Layar HP: bel ada di topbar, panel notifikasi lebar penuh

## Catatan & batasan jujur
- **Belum dijalankan terhadap PHP dan PostgreSQL asli** (lingkungan saya tidak punya keduanya). JS sudah dicek sintaksnya dan fungsi pembantunya diuji; PHP dan SQL ditulis mengikuti pola kode Anda. Skema tabel lama **tidak ada di repo**, jadi nama tabel/kolom saya simpulkan dari query di backend (`users`, `mahasiswa`, `kategori`, `laporan`, `tanggapan`, `riwayat_status`, `unit`). Jika migrasi gagal di baris tertentu, kirim pesan errornya dan saya sesuaikan.
- **Tailwind lewat CDN** (versi dikunci 3.4.17) cocok untuk pengembangan/demo dan butuh internet. Untuk produksi, build CSS sendiri dengan Tailwind CLI dan ganti isi `includes/tailwind.php` dengan satu `<link>`.
- `preflight` Tailwind sengaja dimatikan agar tampilan sidebar/header/modal lama tidak berubah. Konsekuensinya, Tailwind dipakai untuk **isi halaman**; kerangka halaman tetap CSS lama.
- Notifikasi **tidak mundur ke data lama**: laporan yang sudah ada sebelum migrasi tidak punya notifikasi.
- Hapus ulasan memakai `window.confirm` bawaan browser (belum ada modal konfirmasi khusus).
- Halaman Ulasan bersifat umum (bukan per laporan). Jika ingin ulasan per tiket selesai, strukturnya perlu kolom `id_laporan` — bisa saya tambahkan.
