<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Ketua Jurusan - SIPEKA Polinema</title>
  <?php include __DIR__ . '/../../../includes/styles.php'; ?>
  <?php include __DIR__ . '/../../../includes/tailwind.php'; ?>
</head>

<body data-unit="kajur">
  <!-- Mobile Topbar -->
  <div class="mobile-topbar topbar--unit">
    <div class="topbar-brand">
      <div class="logo-badge logo-badge--sm"><img src="../../../assets/images/logo/Jti.webp" alt="Logo SIPEKA"
          class="logo-JTI"></div>
      <span class="topbar-title">SIPEKA Kajur</span>
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
    <aside class="app-sidebar sidebar--unit">
      <div>
        <div class="sidebar-logo">
          <div class="logo-badge"><img src="../../../assets/images/logo/Jti.webp" alt="Logo SIPEKA" class="logo-JTI">
          </div>
          <div class="logo-text">
            <h1>SIPEKA Kajur</h1>
            <p>Ketua Jurusan TI Polinema</p>
          </div>
        </div>

        <ul class="sidebar-menu">
          <li class="active">
            <a href="dashboard.php">
              <svg class="icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2"
                fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1 2-2h2" />
                <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
              </svg>
              <span>Daftar Laporan Masuk</span>
            </a>
          </li>
          <li>
            <a href="ulasan.php">
              <svg class="icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2"
                fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path
                  d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
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
              <span>Profil Unit</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="sidebar-footer">
        <a href="../../auth/login.php" data-logout>
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

    <main class="app-main">
      <header class="app-header">
        <div class="header-breadcrumb">
          <span class="breadcrumb-text">Portal Penanganan Unit Resmi &rsaquo; <strong>Ketua Jurusan
              (Kajur)</strong></span>
        </div>
        <div class="user-profile user-profile--clickable" onclick="window.location.href='profil.php'">
          <div class="avatar avatar--unit"> </div>
          <div class="user-info">
            <div class="user-name"> </div>
            <div class="user-role"> </div>
          </div>
        </div>
      </header>

      <?php
      $dash = [
        'judul' => 'Dashboard Ketua Jurusan (Kajur)',
        'badge' => 'Etika Dosen &amp; Pungli',
        'deskripsi' => 'Pengawasan langsung pengaduan kode etik tenaga pengajar/staf, transparansi perkuliahan, serta pencegahan pungutan liar di lingkungan jurusan.',
        'placeholder' => 'Cari laporan kode etik atau pungli berdasarkan nomor tiket, judul, atau pelapor...',
        'kolom' => 'Pengaduan Etika / Pungli',
      ];
      include __DIR__ . '/../../../includes/unit-dashboard-content.php';
      ?>
    </main>
  </div>

  <!-- Validation Modal (Approve / Reject) -->
  <div class="modal" id="validationModal">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog">
      <div class="modal-header">
        <div>
          <h3>Verifikasi Pengaduan Masuk</h3>
          <p class="modal-subtitle">Tiket #<span id="valTiket"></span></p>
        </div>
        <button class="modal-close-btn" onclick="closeModal('validationModal')">✕</button>
      </div>

      <table class="summary-table summary-table--tight">
        <tr>
          <td class="label">Pelapor</td>
          <td id="valPelapor"></td>
        </tr>
        <tr>
          <td class="label">Judul Kasus</td>
          <td id="valJudul"></td>
        </tr>
        <tr>
          <td class="label">Lokasi Kejadian</td>
          <td id="valLokasi"></td>
        </tr>
        <tr>
          <td class="label">Kronologi Lengkap</td>
          <td id="valKronologi"></td>
        </tr>
      </table>

      <div class="info-box">
        <p class="info-box__title">Keputusan Validasi Ketua Jurusan:</p>
        <p class="info-box__text">Laporan yang divalidasi akan masuk status <strong>Dalam Penanganan</strong> untuk
          klarifikasi pimpinan atau pemanggilan pihak terkait.</p>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn-primary btn-danger btn-inline" id="btnReject">✕ Tolak (Tidak Valid)</button>
        <button type="button" class="btn-primary btn-success btn-inline" id="btnApprove">✓ Setujui (Mulai
          Penanganan)</button>
      </div>
    </div>
  </div>

  <!-- Resolution Modal (Record action & mark Selesai) -->
  <div class="modal" id="resolutionModal">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog">
      <div class="modal-header">
        <div>
          <h3>Pencatatan Tindak Lanjut &amp; Penyelesaian</h3>
          <p class="modal-subtitle">Tiket #<span id="resTiket"></span></p>
        </div>
        <button class="modal-close-btn" onclick="closeModal('resolutionModal')">✕</button>
      </div>

      <table class="summary-table summary-table--tight">
        <tr>
          <td class="label">Kasus</td>
          <td id="resJudul"></td>
        </tr>
        <tr>
          <td class="label">Lokasi</td>
          <td id="resLokasi"></td>
        </tr>
      </table>

      <form id="resolutionForm">
        <div class="form-group">
          <label for="resTindakan">Keterangan Keputusan / Tindak Lanjut Pimpinan <span
              class="required-mark">*</span></label>
          <textarea id="resTindakan" class="form-input"
            placeholder="Tuliskan hasil mediasi, keputusan rapat pimpinan, sanksi, atau solusi yang telah diambil..."
            required></textarea>
        </div>

        <div class="form-group">
          <label>Unggah Berita Acara / Surat Keputusan</label>
          <input type="file" class="form-input" accept="image/*,.pdf">
        </div>

        <div class="modal-actions mt-lg">
          <button type="button" class="btn-detail" onclick="closeModal('resolutionModal')">Batal</button>
          <button type="submit" class="btn-primary btn-success btn-inline">✓ Simpan & Tandai SELESAI</button>
        </div>
      </form>
    </div>
  </div>

  <?php include __DIR__ . '/../../../includes/scripts.php'; ?>
</body>

</html>