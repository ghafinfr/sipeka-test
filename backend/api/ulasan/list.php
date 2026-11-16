<?php
// GET /backend/api/ulasan/list.php?limit=9&offset=0
// Semua ulasan (hanya yang tampil = TRUE) beserta balasannya. Dipakai:
//   - halaman depan (tanpa login, hanya dibaca)
//   - halaman Ulasan mahasiswa & unit (login -> "milik_saya" terisi untuk ulasan/balasan sendiri)
// Nama mahasiswa disamarkan (mis. "Budi P."); petugas unit tampil sebagai nama unitnya.
//
// Jawaban:
// { "ringkasan": { "total", "rata_rata", "distribusi": {"5":n,"4":n,"3":n,"2":n,"1":n} },
//   "ada_lagi": true|false,
//   "ulasan": [ { "id_ulasan", "nama", "peran", "rating", "isi", "waktu", "milik_saya",
//                 "jumlah_balasan", "balasan": [ {id_balasan, nama, peran, isi, waktu, milik_saya} ] } ] }

require __DIR__ . '/../../config/bootstrap.php';
require __DIR__ . '/../../config/laporan-helper.php';
require __DIR__ . '/../../config/ulasan-helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_out(['message' => 'Method tidak diizinkan, gunakan GET'], 405);
}

$limit = max(1, min(50, (int)($_GET['limit'] ?? 9)));
$offset = max(0, (int)($_GET['offset'] ?? 0));
$saya = isset($_SESSION['user']['id_user']) ? (int)$_SESSION['user']['id_user'] : null;

$row = $pdo->query('SELECT COUNT(*) AS total, ROUND(AVG(rating)::numeric, 1) AS rata_rata FROM ulasan WHERE tampil')->fetch();
$total = (int)$row['total'];

$distribusi = ['5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0];
foreach ($pdo->query('SELECT rating, COUNT(*) AS n FROM ulasan WHERE tampil GROUP BY rating')->fetchAll() as $d) {
    $distribusi[(string)(int)$d['rating']] = (int)$d['n'];
}

$st = $pdo->prepare(
    'SELECT ul.id_ulasan, ul.id_user, ul.rating, ul.isi_ulasan, ul.created_at,
            u.nama, u.role::text AS peran, un.nama_unit
       FROM ulasan ul
       JOIN users u      ON u.id_user = ul.id_user
       LEFT JOIN unit un ON un.id_unit = u.id_unit
      WHERE ul.tampil
      ORDER BY ul.created_at DESC, ul.id_ulasan DESC
      LIMIT :lim OFFSET :off'
);
$st->bindValue(':lim', $limit, PDO::PARAM_INT);
$st->bindValue(':off', $offset, PDO::PARAM_INT);
$st->execute();
$baris = $st->fetchAll();

$balasan = muat_balasan($pdo, array_map(fn($r) => (int)$r['id_ulasan'], $baris), $saya);

$ulasan = array_map(function ($r) use ($balasan, $saya) {
    $id = (int)$r['id_ulasan'];
    $b = $balasan[$id] ?? [];
    return [
        'id_ulasan' => $id,
        'nama' => nama_tampil($r),
        'peran' => peran_tampil($r['peran']),
        'rating' => (int)$r['rating'],
        'isi' => $r['isi_ulasan'],
        'waktu' => iso_waktu($r['created_at']),
        'milik_saya' => $saya !== null && (int)$r['id_user'] === $saya,
        'jumlah_balasan' => count($b),
        'balasan' => $b,
    ];
}, $baris);

json_out([
    'ringkasan' => [
        'total' => $total,
        'rata_rata' => $row['rata_rata'] !== null ? (float)$row['rata_rata'] : null,
        'distribusi' => $distribusi,
    ],
    'ada_lagi' => ($offset + count($ulasan)) < $total,
    'ulasan' => $ulasan,
]);
