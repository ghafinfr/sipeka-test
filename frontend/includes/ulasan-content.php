<?php
/**
 * includes/ulasan-content.php
 * ISI halaman Ulasan (Tailwind), dipakai mahasiswa dan semua unit.
 * Kiri : kotak menulis ulasan (komentar; boleh menulis berkali-kali).
 * Kanan: filter (harian/bulanan/tahunan/rentang) + SEMUA ulasan beserta balasan.
 * Logikanya ada di assets/js/pages/ulasan.js (ID elemen di bawah dipakai file itu).
 */
?>
<div class="content-container">
  <div class="page-header-row">
    <div class="page-title">
      <h2>Ulasan</h2>
      <p>Tulis ulasan tentang SIPEKA dan balas ulasan mahasiswa maupun unit lain. Anda boleh menulis lebih dari satu
        kali.</p>
    </div>
  </div>

  <div class="grid gap-6 lg:grid-cols-5">
    <!-- Tulis ulasan -->
    <form id="ulasanForm" novalidate
      class="anim-fade-up self-start rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:sticky lg:top-4 lg:col-span-2">
      <h3 class="mb-3 text-base font-semibold text-navy">Tulis Ulasan</h3>
      <label for="ulasanLaporan" class="mb-1 block text-sm font-medium text-slate-700">Penanganan (opsional)</label>
      <select id="ulasanLaporan"
        class="mb-1 w-full rounded-lg border border-slate-200 bg-white p-3 text-sm text-slate-700 focus:border-brand focus:outline-none focus:ring-2 focus:ring-blue-200">
        <option value="">Ulasan umum (tidak terkait penanganan tertentu)</option>
      </select>
      <p id="ulasanLaporanInfo" class="mb-3 text-xs text-slate-500">Memuat daftar penanganan selesai...</p>
      <label for="ulasanIsi" class="mb-1 block text-sm font-medium text-slate-700">Ulasan Anda</label>
      <textarea id="ulasanIsi" rows="5" maxlength="500" placeholder="Ceritakan pengalaman atau masukan Anda..."
        class="w-full resize-y rounded-lg border border-slate-200 p-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-blue-200"></textarea>
      <div class="mt-1 text-right text-xs text-slate-400"><span id="ulasanHitung">0/500</span></div>
      <div class="mt-3">
        <button type="submit" id="btnKirimUlasan" disabled
          class="btn-primary btn-inline disabled:cursor-not-allowed disabled:opacity-50">Kirim Ulasan</button>
      </div>
    </form>

    <div id="ulasanCatatanUnit"
      class="hidden self-start rounded-xl border border-blue-100 bg-blue-50 p-5 text-sm text-slate-700 lg:col-span-2">
      <h3 class="mb-1 font-semibold text-navy">Informasi untuk Unit Kerja</h3>
      <p>Mahasiswa dapat menulis ulasan. Petugas Unit Kerja dapat membaca ulasan dan memberikan balasan.</p>
    </div>

    <!-- Filter + semua ulasan -->
    <section class="lg:col-span-3">
      <div id="ulasanFilter"></div>
      <div class="mb-3 flex items-baseline justify-between">
        <h3 class="text-base font-semibold text-navy">Semua Ulasan</h3>
        <span id="ulasanRingkasan" class="text-xs text-slate-500"></span>
      </div>
      <div id="ulasanDaftar" class="grid gap-4">
        <p class="review-empty">Memuat ulasan...</p>
      </div>
      <div class="mt-4 text-center">
        <button type="button" id="ulasanMore" hidden
          class="btn-detail rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:text-brand">Muat
          lebih banyak</button>
      </div>
    </section>
  </div>
</div>