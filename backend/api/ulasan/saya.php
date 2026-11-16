<?php
// Ulasan milik akun yang sedang login (mahasiswa ATAU unit). Satu akun = satu ulasan.
//
// GET  /backend/api/ulasan/saya.php
//      -> { "ulasan": { "rating", "isi", "waktu", "diperbarui" } | null }
// POST /backend/api/ulasan/saya.php   Body JSON: { "rating": 1-5, "isi": "10-500 karakter" }
//      -> simpan (buat baru, atau perbarui jika sudah pernah mengulas)
//      -> 201 jika baru, 200 jika diperbarui: { "message", "baru": true|false }
// POST /backend/api/ulasan/saya.php   Body JSON: { "hapus": true }
//      -> hapus ulasan milik sendiri

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../config/laporan-helper.php';

$user = butuh_login();
$metode = $_SERVER['REQUEST_METHOD'];

if ($metode === 'GET') {
    $st = $pdo->prepare('SELECT rating, isi_ulasan, created_at, updated_at FROM ulasan WHERE id_user = :u');
    $st->execute([':u' => $user['id_user']]);
    $r = $st->fetch();
    json_out(['ulasan' => $r ? [
        'rating' => (int)$r['rating'],
        'isi' => $r['isi_ulasan'],
        'waktu' => iso_waktu($r['created_at']),
        'diperbarui' => iso_waktu($r['updated_at']),
    ] : null]);
}

if ($metode !== 'POST') {
    json_out(['message' => 'Method tidak diizinkan, gunakan GET atau POST'], 405);
}

$in = json_in();

// ---------- hapus ----------
if (!empty($in['hapus'])) {
    $st = $pdo->prepare('DELETE FROM ulasan WHERE id_user = :u');
    $st->execute([':u' => $user['id_user']]);
    json_out(['message' => 'Ulasan Anda sudah dihapus.']);
}

// ---------- simpan ----------
$rating = (int)($in['rating'] ?? 0);
$isi = trim((string)($in['isi'] ?? ''));

if ($rating < 1 || $rating > 5) {
    json_out(['message' => 'Pilih rating 1 sampai 5 bintang'], 400);
}
if (panjang_teks($isi) < 10 || panjang_teks($isi) > 500) {
    json_out(['message' => 'Ulasan harus 10-500 karakter'], 400);
}

try {
    $st = $pdo->prepare(
        'INSERT INTO ulasan (id_user, rating, isi_ulasan)
         VALUES (:u, :rating, :isi)
         ON CONFLICT (id_user) DO UPDATE
            SET rating = EXCLUDED.rating, isi_ulasan = EXCLUDED.isi_ulasan, updated_at = now()
         RETURNING (xmax = 0) AS baru'
    );
    $st->execute([':u' => $user['id_user'], ':rating' => $rating, ':isi' => $isi]);
    $baru = ke_bool($st->fetchColumn());
} catch (PDOException $e) {
    if ($e->getCode() === '23514') {   // pelanggaran CHECK
        json_out(['message' => 'Data ulasan tidak memenuhi aturan validasi'], 400);
    }
    throw $e;
}

json_out([
    'message' => $baru ? 'Terima kasih, ulasan Anda sudah tampil di halaman depan.' : 'Ulasan Anda diperbarui.',
    'baru' => $baru,
], $baru ? 201 : 200);
