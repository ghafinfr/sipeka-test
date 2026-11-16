/**
 * Halaman depan - bagian Ulasan
 * Halaman : landing-page.php (#ulasan: #reviewSummary + #reviewGrid)
 * Fungsi  : initLandingUlasan()
 * Data    : GET backend/api/ulasan/list.php (publik, tanpa login)
 * Memakai kelas yang sudah ada di landing.css (.review-item, .review-item__*).
 */

// Bintang statis. Dipakai juga oleh halaman ulasan (pages/ulasan.js).
function bintangStatis(rating) {
    const bintang = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
    return `<span class="stars-static" role="img" aria-label="${rating} dari 5 bintang">` +
        Array.from({ length: 5 }, (_, i) => `<span class="${i < rating ? 'on' : ''}">${bintang}</span>`).join('') +
        '</span>';
}

function kartuUlasanHtml(u, idx = 0) {
    const inisial = (u.nama || '?').trim().charAt(0).toUpperCase();
    return `
        <article class="review-item anim-fade-up" style="--i:${idx}">
            <div class="review-item__head">
                <div class="review-item__avatar">${escapeHtml(inisial)}</div>
                <div>
                    <div class="review-item__name">${escapeHtml(u.nama)}</div>
                    <div class="review-item__role">${escapeHtml(u.peran)}</div>
                </div>
            </div>
            <div class="review-item__stars">${bintangStatis(u.rating)}</div>
            <p class="review-item__text">${escapeHtml(u.isi)}</p>
            <div class="review-item__date">${escapeHtml(relativeTime(u.waktu))}</div>
        </article>`;
}

async function initLandingUlasan() {
    const grid = document.getElementById('reviewGrid');
    const ringkasan = document.getElementById('reviewSummary');
    if (!grid) return;

    let data;
    try {
        data = await window.sipekaAPI.getUlasanPublik(6);
    } catch (err) {
        grid.innerHTML = '<p class="review-empty">Ulasan belum dapat dimuat. Silakan coba lagi nanti.</p>';
        return;
    }

    const total = data.ringkasan?.total ?? 0;
    if (total === 0) {
        if (ringkasan) ringkasan.textContent = '';
        grid.innerHTML = '<p class="review-empty">Belum ada ulasan. Jadilah yang pertama setelah pengaduan Anda selesai ditangani.</p>';
        return;
    }
    if (ringkasan) {
        const rata = data.ringkasan.rata_rata != null ? data.ringkasan.rata_rata.toFixed(1).replace('.', ',') : '-';
        ringkasan.textContent = `Rata-rata ${rata} dari 5 · ${total} ulasan dari mahasiswa dan petugas unit`;
    }
    grid.innerHTML = data.ulasan.map(kartuUlasanHtml).join('');
}
