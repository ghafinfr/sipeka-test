<?php
// Menulis / menghapus ULASAN milik akun yang sedang login (mahasiswa ATAU unit).
// Ulasan berupa komentar saja (tanpa bintang) dan satu akun boleh menulis BANYAK ulasan.
//
// POST /backend/api/ulasan/saya.php  Body JSON: { "isi": "3-500 karakter" }
//      -> 201 { "message", "id_ulasan" }
// POST /backend/api/ulasan/saya.php  Body JSON: { "hapus": true, "id_ulasan": 7 }
//      -> hapus ulasan MILIK SENDIRI (balasan pada ulasan itu ikut terhapus)
//
// Daftar ulasan dibaca lewat ulasan/list.php.

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../config/laporan-helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['message' => 'Method tidak diizinkan, gunakan POST'], 405);
}

$user = butuh_login();
$in = json_in();

// ---------- hapus ----------
if (!empty($in['hapus'])) {
    $id = (int)($in['id_ulasan'] ?? 0);
    if ($id <= 0) {
        json_out(['message' => 'id_ulasan wajib diisi'], 400);
    }
    $st = $pdo->prepare('DELETE FROM ulasan WHERE id_ulasan = :id AND id_user = :u');
    $st->execute([':id' => $id, ':u' => $user['id_user']]);
    if ($st->rowCount() === 0) {
        json_out(['message' => 'Ulasan tidak ditemukan atau bukan milik Anda'], 404);
    }
    json_out(['message' => 'Ulasan dihapus.']);
}

// ---------- tulis ----------
$isi = trim((string)($in['isi'] ?? ''));
if (panjang_teks($isi) < 3 || panjang_teks($isi) > 500) {
    json_out(['message' => 'Ulasan harus 3-500 karakter'], 400);
}

$st = $pdo->prepare('INSERT INTO ulasan (id_user, isi_ulasan) VALUES (:u, :isi) RETURNING id_ulasan');
$st->execute([':u' => $user['id_user'], ':isi' => $isi]);

json_out(['message' => 'Ulasan Anda terkirim.', 'id_ulasan' => (int)$st->fetchColumn()], 201);
