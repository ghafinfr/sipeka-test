<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Mahasiswa - SIPEKA</title>
  <?php include __DIR__ . '/../../includes/styles.php'; ?>
  <?php include __DIR__ . '/../../includes/tailwind.php'; ?>
</head>

<body>
  <!-- Mobile Topbar for Smartphone Screens -->
  <div class="mobile-topbar">
    <div class="topbar-brand">
      <div class="logo-badge logo-badge--sm"><img src="../../assets/images/logo/Jti.webp" alt="Logo SIPEKA"
          class="logo-JTI"></div>
      <span class="topbar-title">SIPEKA</span>
    </div>
    <button class="hamburger-btn" aria-label="Buka Menu">
      <svg class="icon" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"
        stroke-linecap="round" stroke-linejoin="round">
        <line x1="4" x2="20" y1="12" y2="12" />
        <line x1="4" x2="20" y1="6" y2="6" />
        <line x1="4" x2="20" y1="18" y2="18" />
      </svg>
    </button>
  </div>

  <div class="sidebar-backdrop"></div>

  <div class="app-layout">
    <!-- Sidebar -->
    <aside class="app-sidebar">
      <div>
        <div class="sidebar-logo">
          <div class="logo-badge"><img src="../../assets/images/logo/Jti.webp" alt="Logo SIPEKA" class="logo-JTI"></div>

          <div class="logo-text">
            <h1>SIPEKA</h1>
            <p>Sistem Pengaduan Kampus</p>
          </div>
        </div>

        <ul class="sidebar-menu">
          <li class="active">
            <a href="dashboard.php">
              <svg class="icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2"
                fill="none" stroke-linecap="round" stroke-linejoin="round">
                <rect width="7" height="9" x="3" y="3" rx="1" />
                <rect width="7" height="5" x="14" y="3" rx="1" />
                <rect width="7" height="9" x="14" y="12" rx="1" />
                <rect width="7" height="5" x="3" y="16" rx="1" />
              </svg>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="kategori.php">
              <svg class="icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2"
                fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20h9" />
                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
              </svg>
              <span>Buat Pengaduan</span>
            </a>
          </li>
          <li>
            <a href="riwayat.php">
              <svg class="icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2"
                fill="none" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
              <span>Riwayat Pengaduan</span>
            </a>
          </li>
          <li>
            <a href="ulasan.php">
              <svg class="icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2"
                fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
              </svg>
              <span>Ulasan</span>
            </a>
          </li>
          <li>
            <a href="profil.php">
              <svg class="icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2"
                fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
              </svg>
              <span>Profil Saya</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="sidebar-footer">
        <a href="../auth/login.php" data-logout>
          <svg class="icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2"
            fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" x2="9" y1="12" y2="12" />
          </svg>
          <span>Keluar</span>
        </a>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="app-main">
      <header class="app-header">
        <div class="header-breadcrumb">
          <span class="breadcrumb-text">Portal Mahasiswa JTI Polinema &rsaquo; <strong>Dashboard</strong></span>
        </div>
        <div class="user-profile user-profile--clickable" onclick="window.location.href='profil.php'">
          <div class="avatar"> </div>
          <div class="user-info">
            <div class="user-name"> </div>
            <div class="user-role"> </div>
          </div>
        </div>
      </header>

      <div class="content-container">
        <!-- Judul + tombol aksi -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-2xl font-bold text-navy">Dashboard Mahasiswa</h2>
            <p class="text-sm text-slate-500">Pantau seluruh status laporan pengaduan Anda di Jurusan Teknologi Informasi</p>
          </div>
          <a href="kategori.php"
            class="btn-primary btn-inline inline-flex items-center justify-center gap-2 no-underline">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"
              stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14" /></svg>
            Buat Pengaduan
          </a>
        </div>

        <!-- Kartu ringkasan: klik untuk melihat daftar sesuai status di halaman Riwayat -->
        <div class="mb-6 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
          <a href="riwayat.php" style="--i:0"
            class="stat-tile anim-fade-up flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 no-underline shadow-sm hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-brand">
            <span class="h-10 w-1.5 shrink-0 rounded-full bg-blue-500"></span>
            <span>
              <span id="statTotal" class="block text-3xl font-extrabold tabular-nums text-slate-800">0</span>
              <span class="block text-xs font-medium text-slate-500">Total Pengaduan</span>
            </span>
          </a>
          <a href="riwayat.php?status=dalam_penanganan" style="--i:1"
            class="stat-tile anim-fade-up flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 no-underline shadow-sm hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-brand">
            <span class="h-10 w-1.5 shrink-0 rounded-full bg-orange-500"></span>
            <span>
              <span id="statProses" class="block text-3xl font-extrabold tabular-nums text-slate-800">0</span>
              <span class="block text-xs font-medium text-slate-500">Dalam Penanganan</span>
            </span>
          </a>
          <a href="riwayat.php?status=selesai" style="--i:2"
            class="stat-tile anim-fade-up flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 no-underline shadow-sm hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-brand">
            <span class="h-10 w-1.5 shrink-0 rounded-full bg-green-500"></span>
            <span>
              <span id="statSelesai" class="block text-3xl font-extrabold tabular-nums text-slate-800">0</span>
              <span class="block text-xs font-medium text-slate-500">Selesai Ditangani</span>
            </span>
          </a>
          <a href="riwayat.php?status=ditolak" style="--i:3"
            class="stat-tile anim-fade-up flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 no-underline shadow-sm hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-brand">
            <span class="h-10 w-1.5 shrink-0 rounded-full bg-red-500"></span>
            <span>
              <span id="statDitolak" class="block text-3xl font-extrabold tabular-nums text-slate-800">0</span>
              <span class="block text-xs font-medium text-slate-500">Ditolak</span>
            </span>
          </a>
        </div>

        <!-- Ajakan memberi ulasan (halaman Ulasan) -->
        <a href="ulasan.php"
          class="anim-fade-up mb-6 flex items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 no-underline transition hover:shadow-md"
          style="--i:4">
          <span class="text-sm text-amber-900"><strong>Bagaimana pengalaman Anda?</strong> Beri ulasan agar layanan SIPEKA makin baik.</span>
          <span class="shrink-0 text-sm font-semibold text-amber-700">Tulis ulasan &rarr;</span>
        </a>

        <!-- Pengaduan terbaru -->
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
          <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
            <h3 class="text-sm font-semibold text-slate-700">Pengaduan Terbaru</h3>
            <a href="riwayat.php" class="text-xs font-semibold text-brand no-underline hover:underline">Lihat semua</a>
          </div>
          <div class="overflow-x-auto">
            <table class="custom-table w-full">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Kategori</th>
                  <th>Judul Laporan</th>
                  <th>Tanggal Kejadian</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="recentReportsBody">
                <!-- diisi oleh pages/mahasiswa/mahasiswa-dashboard.js -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>

  <?php include __DIR__ . '/../../includes/scripts.php'; ?>
</body>

</html>
