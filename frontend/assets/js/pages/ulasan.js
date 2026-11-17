/**
 * Halaman Ulasan (mahasiswa & semua unit memakai skrip yang sama)
 * Halaman : content/mahasiswa/ulasan.php, content/unit/{unit}/ulasan.php
 *           (isi halaman ada di includes/ulasan-content.php)
 * Fungsi  : initUlasanPage()
 * Data    : GET/POST ulasan/saya.php (ulasan sendiri), GET ulasan/list.php (SEMUA ulasan + balasan),
 *           POST ulasan/balas.php (balas / hapus balasan sendiri)
 * Satu akun = satu ulasan (boleh diedit/dihapus); balasan tidak dibatasi jumlahnya.
 * Memakai bintangStatis() & chipPeran() dari pages/landing-ulasan.js.
 */

async function initUlasanPage() {
    const form = document.getElementById('ulasanForm');
    if (!form) return;

    const bintang = Array.from(form.querySelectorAll('.star-btn'));
    const isi = document.getElementById('ulasanIsi');
    const hitung = document.getElementById('ulasanHitung');
    const labelRating = document.getElementById('ratingLabel');
    const btnKirim = document.getElementById('btnKirimUlasan');
    const btnHapus = document.getElementById('btnHapusUlasan');
    const status = document.getElementById('ulasanStatus');
    const daftar = document.getElementById('ulasanDaftar');
    const ringkasan = document.getElementById('ulasanRingkasan');
    const btnMore = document.getElementById('ulasanMore');

    const LABEL = ['Pilih bintang', 'Sangat buruk', 'Kurang baik', 'Cukup', 'Baik', 'Sangat baik'];
    const HALAMAN = 10;
    let rating = 0;
    let sudahAda = false;
    let dimuat = 0;          // jumlah ulasan yang sedang tampil
    let terbuka = new Set(); // id ulasan yang kotak balasnya sedang dibuka

    // ================= formulir ulasan sendiri =================
    function gambarBintang(nilai) {
        bintang.forEach((b, i) => {
            b.classList.toggle('is-on', i < nilai);
            b.setAttribute('aria-checked', String(i + 1 === rating));
        });
        labelRating.textContent = LABEL[nilai] || LABEL[0];
    }
    function periksa() {
        const panjang = isi.value.trim().length;
        hitung.textContent = `${isi.value.length}/500`;
        hitung.classList.toggle('text-red-500', panjang > 0 && panjang < 10);
        btnKirim.disabled = rating < 1 || panjang < 10;
    }
    bintang.forEach((b, i) => {
        b.addEventListener('mouseenter', () => gambarBintang(i + 1));
        b.addEventListener('focus', () => gambarBintang(i + 1));
        b.addEventListener('click', () => { rating = i + 1; gambarBintang(rating); periksa(); });
        b.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                e.preventDefault();
                const baru = Math.min(5, Math.max(1, (rating || i + 1) + (e.key === 'ArrowRight' ? 1 : -1)));
                rating = baru; gambarBintang(rating); bintang[baru - 1].focus(); periksa();
            }
        });
    });
    form.querySelector('.star-rating').addEventListener('mouseleave', () => gambarBintang(rating));
    isi.addEventListener('input', periksa);

    async function muatSaya() {
        try {
            const { ulasan } = await window.sipekaAPI.getUlasanSaya();
            sudahAda = !!ulasan;
            if (ulasan) {
                rating = ulasan.rating;
                isi.value = ulasan.isi;
                status.textContent = 'Anda sudah menulis ulasan. Ubah lalu simpan untuk memperbarui.';
            } else {
                status.textContent = 'Anda belum menulis ulasan.';
            }
        } catch (err) {
            status.textContent = '';
            showToast(err.message || 'Gagal memuat ulasan Anda.', 'error');
        }
        btnHapus.classList.toggle('hidden', !sudahAda);
        btnKirim.textContent = sudahAda ? 'Perbarui Ulasan' : 'Kirim Ulasan';
        gambarBintang(rating);
        periksa();
    }

    // ================= daftar semua ulasan + balasan =================
    function balasanHtml(b) {
        return `
        <div class="review-reply">
            <div class="review-reply__head">
                <strong>${escapeHtml(b.nama)}</strong>${chipPeran(b.peran)}
                <span class="review-reply__time">· ${escapeHtml(relativeTime(b.waktu))}</span>
                ${b.milik_saya ? `<button type="button" class="review-reply__del" data-hapus-balasan="${b.id_balasan}">Hapus</button>` : ''}
            </div>
            <div>${escapeHtml(b.isi)}</div>
        </div>`;
    }

    function kartuHtml(u, idx) {
        const inisial = (u.nama || '?').trim().charAt(0).toUpperCase();
        const buka = terbuka.has(u.id_ulasan);
        return `
        <article class="review-item anim-fade-up" id="ulasan-${u.id_ulasan}" data-id="${u.id_ulasan}" style="--i:${Math.min(idx, 8)}">
            <div class="review-item__head">
                <div class="review-item__avatar">${escapeHtml(inisial)}</div>
                <div>
                    <div class="review-item__name">${escapeHtml(u.nama)}${u.milik_saya ? '<span class="review-item__chip review-item__chip--saya">Ulasan Anda</span>' : ''}</div>
                    <div class="review-item__role">${chipPeran(u.peran)}</div>
                </div>
            </div>
            <div class="review-item__stars">${bintangStatis(u.rating)}</div>
            <p class="review-item__text">${escapeHtml(u.isi)}</p>
            ${u.balasan.length ? `<div class="review-replies">${u.balasan.map(balasanHtml).join('')}</div>` : ''}
            <div class="mt-2 flex items-center justify-between">
                <span class="review-item__date">${escapeHtml(relativeTime(u.waktu))}</span>
                <button type="button" class="text-xs font-semibold text-brand hover:underline" data-toggle-balas="${u.id_ulasan}">
                    Balas${u.jumlah_balasan ? ` (${u.jumlah_balasan})` : ''}
                </button>
            </div>
            <div class="mt-2 ${buka ? '' : 'hidden'}" data-kotak-balas="${u.id_ulasan}">
                <textarea rows="2" maxlength="300" placeholder="Tulis balasan (2-300 karakter)..."
                    class="w-full resize-y rounded-lg border border-slate-200 p-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-blue-200"></textarea>
                <div class="mt-1 text-right">
                    <button type="button" class="btn-primary btn-inline" data-kirim-balas="${u.id_ulasan}">Kirim Balasan</button>
                </div>
            </div>
        </article>`;
    }

    // Muat ulang dari awal sebanyak yang sudah tampil (supaya balasan baru langsung terlihat)
    async function muatDaftar({ tambah = false } = {}) {
        const awal = tambah ? dimuat : 0;
        const jumlah = tambah ? HALAMAN : Math.max(dimuat, HALAMAN);
        let data;
        try {
            data = await window.sipekaAPI.getUlasanPublik(jumlah, awal);
        } catch (err) {
            if (!tambah) daftar.innerHTML = '<p class="review-empty">Daftar ulasan belum dapat dimuat.</p>';
            else showToast(err.message || 'Gagal memuat ulasan.', 'error');
            return;
        }
        const { total, rata_rata } = data.ringkasan;
        ringkasan.textContent = total ? `Rata-rata ${rata_rata.toFixed(1).replace('.', ',')} dari 5 · ${total} ulasan` : '';
        if (total === 0) {
            daftar.innerHTML = '<p class="review-empty">Belum ada ulasan. Jadilah yang pertama!</p>';
            btnMore.hidden = true;
            dimuat = 0;
            return;
        }
        const html = data.ulasan.map((u, i) => kartuHtml(u, i)).join('');
        if (tambah) { daftar.insertAdjacentHTML('beforeend', html); dimuat += data.ulasan.length; }
        else { daftar.innerHTML = html; dimuat = data.ulasan.length; }
        btnMore.hidden = !data.ada_lagi;
    }

    daftar.addEventListener('click', async (e) => {
        const toggle = e.target.closest('[data-toggle-balas]');
        if (toggle) {
            const id = Number(toggle.dataset.toggleBalas);
            const kotak = daftar.querySelector(`[data-kotak-balas="${id}"]`);
            const buka = kotak.classList.toggle('hidden') === false;
            if (buka) { terbuka.add(id); kotak.querySelector('textarea').focus(); } else { terbuka.delete(id); }
            return;
        }
        const kirim = e.target.closest('[data-kirim-balas]');
        if (kirim) {
            const id = Number(kirim.dataset.kirimBalas);
            const ta = daftar.querySelector(`[data-kotak-balas="${id}"] textarea`);
            const teks = ta.value.trim();
            if (teks.length < 2) { showToast('Balasan minimal 2 karakter.', 'error'); return; }
            kirim.disabled = true;
            try {
                await window.sipekaAPI.balasUlasan(id, teks);
                showToast('Balasan terkirim.', 'success');
                terbuka.delete(id);
                await muatDaftar();
            } catch (err) {
                showToast(err.message || 'Gagal mengirim balasan.', 'error');
                kirim.disabled = false;
            }
            return;
        }
        const hapus = e.target.closest('[data-hapus-balasan]');
        if (hapus) {
            if (!window.confirm('Hapus balasan Anda?')) return;
            try {
                await window.sipekaAPI.hapusBalasan(Number(hapus.dataset.hapusBalasan));
                await muatDaftar();
            } catch (err) {
                showToast(err.message || 'Gagal menghapus balasan.', 'error');
            }
        }
    });

    btnMore.addEventListener('click', async () => {
        btnMore.disabled = true;
        await muatDaftar({ tambah: true });
        btnMore.disabled = false;
    });

    // Ada balasan baru untuk saya (notifikasi) -> daftar diperbarui otomatis
    window.addEventListener('sipeka:notifikasi-baru', (e) => {
        if (e.detail.items.some((n) => n.jenis === 'balasan_ulasan')) muatDaftar();
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        btnKirim.disabled = true;
        try {
            const hasil = await window.sipekaAPI.simpanUlasanSaya(rating, isi.value.trim());
            showToast(hasil.message || 'Ulasan tersimpan.', 'success');
            await Promise.all([muatSaya(), muatDaftar()]);
        } catch (err) {
            showToast(err.message || 'Gagal menyimpan ulasan.', 'error');
            periksa();
        }
    });

    btnHapus.addEventListener('click', async () => {
        if (!window.confirm('Hapus ulasan Anda? Balasan pada ulasan ini ikut terhapus.')) return;
        try {
            await window.sipekaAPI.hapusUlasanSaya();
            rating = 0; isi.value = '';
            showToast('Ulasan Anda dihapus.', 'success');
            await Promise.all([muatSaya(), muatDaftar()]);
        } catch (err) {
            showToast(err.message || 'Gagal menghapus ulasan.', 'error');
        }
    });

    await Promise.all([muatSaya(), muatDaftar()]);
}
