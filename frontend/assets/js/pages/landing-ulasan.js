/**
 * Halaman depan - bagian Ulasan (HANYA MENAMPILKAN HASIL; tidak ada formulir)
 * Halaman : landing-page.php (#reviewSummary, #reviewStats, #reviewGrid, #reviewMore)
 * Fungsi  : initLandingUlasan()
 * Data    : GET backend/api/ulasan/list.php (publik, tanpa login)
 * Juga memuat fungsi bersama yang dipakai pages/ulasan.js: bintangStatis(), chipPeran().
 */

// Bintang statis (tampilan saja)
function bintangStatis(rating) {
    const bintang = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
    return `<span class="stars-static" role="img" aria-label="${rating} dari 5 bintang">` +
        Array.from({ length: 5 }, (_, i) => `<span class="${i < rating ? 'on' : ''}">${bintang}</span>`).join('') +
        '</span>';
}

// Label kecil "Petugas Unit" / "Mahasiswa"
function chipPeran(peran) {
    const unit = peran === 'Petugas Unit';
    return `<span class="review-item__chip ${unit ? 'review-item__chip--unit' : 'review-item__chip--mhs'}">${escapeHtml(peran)}</span>`;
}

// Kartu ulasan untuk halaman depan (hanya baca). Menampilkan maks. 2 balasan pertama.
function kartuUlasanHtml(u, idx = 0) {
    const inisial = (u.nama || '?').trim().charAt(0).toUpperCase();
    const balasan = u.balasan || [];
    const tampil = balasan.slice(0, 2).map((b) => `
        <div class="review-reply">
            <div class="review-reply__head"><strong>${escapeHtml(b.nama)}</strong>${chipPeran(b.peran)}</div>
            <div>${escapeHtml(b.isi)}</div>
        </div>`).join('');
    const sisa = balasan.length - 2;
    return `
        <article class="review-item anim-fade-up" style="--i:${Math.min(idx, 8)}">
            <div class="review-item__head">
                <div class="review-item__avatar">${escapeHtml(inisial)}</div>
                <div>
                    <div class="review-item__name">${escapeHtml(u.nama)}</div>
                    <div class="review-item__role">${chipPeran(u.peran)}</div>
                </div>
            </div>
            <div class="review-item__stars">${bintangStatis(u.rating)}</div>
            <p class="review-item__text">${escapeHtml(u.isi)}</p>
            ${balasan.length ? `<div class="review-replies">${tampil}${sisa > 0 ? `<div class="review-replies-more">+${sisa} balasan lainnya</div>` : ''}</div>` : ''}
            <div class="review-item__date">${escapeHtml(relativeTime(u.waktu))}</div>
        </article>`;
}

// Ringkasan: rata-rata + sebaran bintang
function ringkasanUlasanHtml(r) {
    const rata = r.rata_rata != null ? r.rata_rata.toFixed(1).replace('.', ',') : '-';
    const bar = [5, 4, 3, 2, 1].map((n) => {
        const jml = r.distribusi?.[String(n)] ?? 0;
        const persen = r.total ? Math.round((jml / r.total) * 100) : 0;
        return `<div class="review-bar"><span class="review-bar__label">${n}★</span>
            <span class="review-bar__track"><span class="review-bar__fill" style="width:${persen}%"></span></span>
            <span class="review-bar__n">${jml}</span></div>`;
    }).join('');
    return `<div class="review-stats">
        <div class="review-stats__avg">
            <div class="review-stats__num">${rata}</div>
            ${bintangStatis(Math.round(r.rata_rata || 0))}
            <div class="review-stats__total">${r.total} ulasan</div>
        </div>
        <div class="review-bars">${bar}</div>
    </div>`;
}

async function initLandingUlasan() {
    const grid = document.getElementById('reviewGrid');
    const ringkasan = document.getElementById('reviewSummary');
    const stats = document.getElementById('reviewStats');
    const more = document.getElementById('reviewMore');
    if (!grid) return;

    const HALAMAN = 6;
    let dimuat = 0;

    async function muat(awal) {
        let data;
        try {
            data = await window.sipekaAPI.getUlasanPublik(HALAMAN, dimuat);
        } catch (err) {
            if (awal) grid.innerHTML = '<p class="review-empty">Ulasan belum dapat dimuat. Silakan coba lagi nanti.</p>';
            else showToast(err.message || 'Gagal memuat ulasan.', 'error');
            return;
        }
        const total = data.ringkasan?.total ?? 0;
        if (total === 0) {
            if (ringkasan) ringkasan.textContent = '';
            if (stats) stats.innerHTML = '';
            grid.innerHTML = '<p class="review-empty">Belum ada ulasan. Jadilah yang pertama setelah masuk ke akun Anda.</p>';
            if (more) more.hidden = true;
            return;
        }
        if (awal) {
            if (ringkasan) ringkasan.textContent = 'Penilaian dari mahasiswa dan petugas unit JTI';
            if (stats) stats.innerHTML = ringkasanUlasanHtml(data.ringkasan);
            grid.innerHTML = '';
        }
        grid.insertAdjacentHTML('beforeend', data.ulasan.map((u, i) => kartuUlasanHtml(u, i)).join(''));
        dimuat += data.ulasan.length;
        if (more) more.hidden = !data.ada_lagi;
    }

    if (more) more.addEventListener('click', async () => {
        more.disabled = true;
        await muat(false);
        more.disabled = false;
    });
    await muat(true);
}
