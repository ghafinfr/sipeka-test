<?php
/**
 * includes/tailwind.php
 * Tailwind CSS untuk halaman dashboard & ulasan. Dipakai SATU baris di <head>, setelah styles.php:
 *   <?php include __DIR__ . '/../../includes/tailwind.php'; ?>        (content/mahasiswa)
 *   <?php include __DIR__ . '/../../../includes/tailwind.php'; ?>     (content/unit/{unit})
 *
 * - preflight DIMATIKAN supaya CSS lama (sidebar, header, modal, tabel) tidak berubah tampilannya.
 *   Tailwind hanya dipakai untuk kelas utilitas di isi halaman.
 * - Warna "brand" mengikuti body[data-unit] di themes.css, jadi tiap unit tetap punya warnanya sendiri.
 * - Versi CDN dikunci (3.4.17). CDN cocok untuk pengembangan/demo; untuk produksi sebaiknya
 *   build CSS sendiri dengan Tailwind CLI (lihat CATATAN_UPDATE.md).
 */
?>
<script src="https://cdn.tailwindcss.com/3.4.17"></script>
<script>
  tailwind.config = {
    corePlugins: { preflight: false },
    theme: {
      extend: {
        colors: {
          navy: '#0f2744',
          brand: 'var(--unit-brand, var(--accent-blue, #2563eb))',
          'brand-soft': 'var(--unit-soft-bg, #dbeafe)',
          'brand-ink': 'var(--unit-soft-text, #1d4ed8)',
        },
      },
    },
  };
</script>