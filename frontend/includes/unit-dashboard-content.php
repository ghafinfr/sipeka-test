<?php
/**
 * includes/unit-dashboard-content.php
 * ISI dashboard semua unit (Sekjur, Admin Prodi, Kajur, KPS) memakai Tailwind.
 * Dipanggil dari content/unit/{unit}/dashboard.php setelah mengisi $dash:
 *   $dash = [
 *     'judul'       => 'Dashboard ...',      // teks HTML
 *     'badge'       => 'Fasilitas Kampus',
 *     'deskripsi'   => 'Kelola ...',
 *     'placeholder' => 'Cari ...',
 *     'kolom'       => 'Judul Laporan ...',  // judul kolom ke-3 tabel
 *   ];
 * Mengubah tampilan semua dashboard unit = ubah SATU file ini.
 * ID elemen (statusFilterUnit, unitStat*, searchUnitReports, unitReportsBody, ...) dipakai
 * assets/js/pages/unit/unit-dashboard.js — jangan diganti tanpa menyesuaikan file itu.
 */
$kartu = [
  // [nilai filter (sama dgn <option>), id angka, label, warna indikator]
  ['Menunggu Validasi', 'unitStatValidasi', 'Menunggu', 'bg-orange-500'],
  ['Dalam Penanganan', 'unitStatProses', 'Dalam Penanganan', 'bg-blue-500'],
  ['Selesai', 'unitStatSelesai', 'Selesai', 'bg-green-500'],
  ['Ditolak', 'unitStatDitolak', 'Ditolak', 'bg-red-500'],
];
?>
<div class="content-container">
  <!-- Judul + filter status -->
  <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
    <div>
      <div class="mb-1 flex flex-wrap items-center gap-2">
        <h2 class="text-2xl font-bold text-navy"><?= $dash['judul'] ?></h2>
        <span class="badge badge-soft"><?= $dash['badge'] ?></span>
      </div>
      <p class="max-w-2xl text-sm text-slate-500"><?= $dash['deskripsi'] ?></p>
    </div>
    <select id="statusFilterUnit" class="filter-select filter-select--accent" aria-label="Filter status">
      <option value="all">Semua Status</option>
      <option value="Menunggu Validasi">Menunggu Validasi</option>
      <option value="Dalam Penanganan">Dalam Penanganan</option>
      <option value="Selesai">Selesai</option>
      <option value="Ditolak">Ditolak</option>
    </select>
  </div>

  <!-- Kartu ringkasan: klik kartu untuk memfilter tabel, klik lagi untuk melepas -->
  <div class="mb-6 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4" id="unitStatCards">
    <?php foreach ($kartu as $i => [$filter, $id, $label, $warna]): ?>
      <button type="button" data-filter="<?= $filter ?>" style="--i:<?= $i ?>"
        class="stat-tile anim-fade-up flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 text-left shadow-sm hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-brand">
        <span class="h-10 w-1.5 shrink-0 rounded-full <?= $warna ?>"></span>
        <span>
          <span id="<?= $id ?>" class="block text-3xl font-extrabold tabular-nums text-slate-800">0</span>
          <span class="block text-xs font-medium text-slate-500"><?= $label ?></span>
        </span>
      </button>
    <?php endforeach; ?>
  </div>

  <!-- Pencarian + segarkan -->
  <div class="mb-4 flex items-center gap-2">
    <div class="relative flex-1">
      <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24"
        width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"
        stroke-linejoin="round">
        <circle cx="11" cy="11" r="8" />
        <path d="m21 21-4.3-4.3" />
      </svg>
      <input type="text" id="searchUnitReports" placeholder="<?= $dash['placeholder'] ?>"
        class="w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-9 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-blue-200">
      <button type="button" id="clearSearchUnit" hidden aria-label="Hapus pencarian"
        class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full px-1.5 text-slate-400 hover:text-slate-700">✕</button>
    </div>
    <button type="button" id="btnRefreshUnit" aria-label="Muat ulang"
      class="rounded-lg border border-slate-200 bg-white p-2.5 text-slate-500 shadow-sm transition hover:text-brand focus:outline-none focus-visible:ring-2 focus-visible:ring-brand">
      <svg id="iconRefreshUnit" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2"
        fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 12a9 9 0 1 1-3-6.7L21 8" />
        <path d="M21 3v5h-5" />
      </svg>
    </button>
  </div>

  <!-- Tabel laporan -->
  <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
      <h3 class="text-sm font-semibold text-slate-700">Daftar Laporan</h3>
      <span id="unitResultInfo" class="text-xs text-slate-500"></span>
    </div>
    <div class="overflow-x-auto">
      <table class="custom-table w-full">
        <thead>
          <tr>
            <th>No</th>
            <th>Data Pelapor</th>
            <th><?= $dash['kolom'] ?></th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi Tindakan</th>
          </tr>
        </thead>
        <tbody id="unitReportsBody">
          <!-- diisi oleh pages/unit/unit-dashboard.js -->
        </tbody>
      </table>
    </div>
  </div>
</div>