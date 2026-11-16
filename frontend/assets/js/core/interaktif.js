/**
 * SIPEKA - Interaksi umum (dipakai semua halaman)
 * Isi   : animateCount(), relativeTime(), skeletonRows(), staggerRows(), efek ripple tombol.
 * Catatan: hanya fungsi bantu tampilan; tidak memanggil API. Dimuat setelah core/ui.js.
 */

const SIPEKA_KURANGI_GERAK = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Angka naik perlahan dari nilai tampil sekarang ke nilai baru (mis. kartu statistik).
function animateCount(el, ke, durasi = 600) {
    if (!el) return;
    const target = Number(ke) || 0;
    const dari = Number(el.dataset.nilai ?? el.textContent) || 0;
    el.dataset.nilai = String(target);
    if (SIPEKA_KURANGI_GERAK || dari === target) {
        el.textContent = target;
        return;
    }
    const mulai = performance.now();
    const langkah = (now) => {
        const t = Math.min(1, (now - mulai) / durasi);
        const eased = 1 - Math.pow(1 - t, 3);                 // easeOutCubic
        el.textContent = Math.round(dari + (target - dari) * eased);
        if (t < 1) requestAnimationFrame(langkah);
    };
    requestAnimationFrame(langkah);
}

// "2026-11-16T09:30:00+07:00" -> "5 menit lalu" / "kemarin" / "3 hari lalu" / "16 Nov 2026"
function relativeTime(iso) {
    if (!iso) return '';
    const d = new Date(iso);
    if (isNaN(d.getTime())) return '';
    const detik = Math.round((Date.now() - d.getTime()) / 1000);
    if (detik < 45) return 'baru saja';
    if (detik < 3600) return Math.round(detik / 60) + ' menit lalu';
    if (detik < 86400) return Math.round(detik / 3600) + ' jam lalu';
    if (detik < 172800) return 'kemarin';
    if (detik < 7 * 86400) return Math.floor(detik / 86400) + ' hari lalu';
    return typeof formatDate === 'function' ? formatDate(iso) : d.toLocaleDateString('id-ID');
}

// Baris tabel "kerangka" saat data sedang dimuat.
function skeletonRows(kolom = 5, baris = 4) {
    const sel = '<td><span class="skeleton"></span></td>'.repeat(kolom);
    return Array.from({ length: baris }, () => `<tr>${sel}</tr>`).join('');
}

// Beri animasi muncul bertahap pada semua <tr> di dalam tbody (panggil setelah innerHTML diisi).
function staggerRows(tbody) {
    if (!tbody) return;
    tbody.querySelectorAll('tr').forEach((tr, i) => {
        tr.classList.add('row-anim');
        tr.style.setProperty('--i', Math.min(i, 12));
    });
}

// Ripple pada tombol: cukup beri salah satu kelas tombol yang sudah ada.
document.addEventListener('click', (e) => {
    if (SIPEKA_KURANGI_GERAK) return;
    const tombol = e.target.closest ? e.target.closest('.btn-primary, .btn-action, .btn-detail, .btn-inline') : null;
    if (!tombol || tombol.disabled) return;
    const kotak = tombol.getBoundingClientRect();
    const gelombang = document.createElement('span');
    gelombang.className = 'ripple-wave';
    gelombang.style.left = (e.clientX - kotak.left) + 'px';
    gelombang.style.top = (e.clientY - kotak.top) + 'px';
    tombol.appendChild(gelombang);
    setTimeout(() => gelombang.remove(), 600);
});
