<?php
// GET /backend/api/ulasan/list.php?limit=9&offset=0
// Ulasan publik untuk halaman depan (TANPA login). Hanya ulasan yang tampil = TRUE.
// Nama mahasiswa disamarkan (nama depan + inisial belakang); petugas unit tampil sebagai nama unitnya.
//
// Jawaban:
// { "ringkasan": { "total": 12, "rata_rata": 4.6 },
//   "ulasan": [ { "id_ulasan", "nama", "peran", "rating", "isi", "waktu" }, ... ] }

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../config/laporan-helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_out(['message' => 'Method tidak diizinkan, gunakan GET'], 405);
}

$limit = max(1, min(30, (int)($_GET['limit'] ?? 9)));
$offset = max(0, (int)($_GET['offset'] ?? 0));

$row = $pdo->query('SELECT COUNT(*) AS total, ROUND(AVG(rating)::numeric, 1) AS rata_rata FROM v_ulasan_publik')->fetch();

$st = $pdo->prepare(
    'SELECT id_ulasan, nama, peran, nama_unit, rating, isi_ulasan, created_at
       FROM v_ulasan_publik
      ORDER BY created_at DESC, id_ulasan DESC
      LIMIT :lim OFFSET :off'
);
$st->bindValue(':lim', $limit, PDO::PARAM_INT);
$st->bindValue(':off', $offset, PDO::PARAM_INT);
$st->execute();

// "Budi Santoso Putra" -> "Budi P."
function samarkan_nama(string $nama): string
{
    $bagian = preg_split('/\s+/u', trim($nama)) ?: [];
    if (count($bagian) <= 1) return $nama;
    $akhir = end($bagian);
    $inisial = function_exists('mb_substr') ? mb_substr($akhir, 0, 1, 'UTF-8') : substr($akhir, 0, 1);
    return $bagian[0] . ' ' . strtoupper($inisial) . '.';
}

$ulasan = array_map(function ($r) {
    $unit = $r['peran'] === 'unit';
    return [
        'id_ulasan' => (int)$r['id_ulasan'],
        'nama' => $unit ? ($r['nama_unit'] ?: 'Petugas Unit') : samarkan_nama((string)$r['nama']),
        'peran' => $unit ? 'Petugas Unit' : 'Mahasiswa',
        'rating' => (int)$r['rating'],
        'isi' => $r['isi_ulasan'],
        'waktu' => iso_waktu($r['created_at']),
    ];
}, $st->fetchAll());

json_out([
    'ringkasan' => [
        'total' => (int)$row['total'],
        'rata_rata' => $row['rata_rata'] !== null ? (float)$row['rata_rata'] : null,
    ],
    'ulasan' => $ulasan,
]);
