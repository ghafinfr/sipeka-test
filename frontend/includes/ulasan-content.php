<?php
/**
 * includes/ulasan-content.php
 * ISI halaman Ulasan (Tailwind), dipakai mahasiswa dan semua unit.
 * Dipanggil dari content/mahasiswa/ulasan.php dan content/unit/{unit}/ulasan.php.
 * Logikanya ada di assets/js/pages/ulasan.js (ID elemen di bawah dipakai file itu).
 */
?>
<div class="content-container">
  <div class="page-header-row">
    <div class="page-title">
      <h2>Ulasan</h2>
      <p>Bagikan pengalaman Anda memakai SIPEKA. Ulasan tampil di halaman depan.</p>
    </div>
  </div>

  <div class="grid gap-6 lg:grid-cols-5">
    <!-- Formulir -->
    <form id="ulasanForm" novalidate
      class="anim-fade-up rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
      <h3 class="mb-1 text-base font-semibold text-navy">Ulasan Anda</h3>
      <p id="ulasanStatus" class="mb-4 text-xs text-slate-500">Memuat...</p>

      <div class="mb-1 text-sm font-medium text-slate-700">Penilaian</div>
      <div class="star-rating mb-1" role="radiogroup" aria-label="Penilaian 1 sampai 5 bintang">
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <button type="button" class="star-btn" role="radio" aria-checked="false" aria-label="<?= $i ?> bintang">
            <svg viewBox="0 0 24 24">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
          </button>
        <?php endfor; ?>
      </div>
      <p id="ratingLabel" class="mb-4 text-xs font-medium text-amber-600">Pilih bintang</p>

      <label for="ulasanIsi" class="mb-1 block text-sm font-medium text-slate-700">Ulasan</label>
      <textarea id="ulasanIsi" rows="5" maxlength="500" placeholder="Ceritakan pengalaman Anda (minimal 10 karakter)..."
        class="w-full resize-y rounded-lg border border-slate-200 p-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-blue-200"></textarea>
      <div class="mt-1 text-right text-xs text-slate-400"><span id="ulasanHitung">0/500</span></div>

      <div class="mt-4 flex flex-wrap items-center gap-2">
        <button type="submit" id="btnKirimUlasan" disabled
          class="btn-primary btn-inline disabled:cursor-not-allowed disabled:opacity-50">Kirim Ulasan</button>
        <button type="button" id="btnHapusUlasan"
          class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
      </div>
    </form>

    <!-- Ulasan terbaru -->
    <section class="lg:col-span-3">
      <div class="mb-3 flex items-baseline justify-between">
        <h3 class="text-base font-semibold text-navy">Ulasan Terbaru</h3>
        <span id="ulasanRingkasan" class="text-xs text-slate-500"></span>
      </div>
      <div id="ulasanDaftar" class="grid gap-4 sm:grid-cols-2">
        <p class="review-empty">Memuat ulasan...</p>
      </div>
    </section>
  </div>
</div>