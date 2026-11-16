/**
 * Halaman Ulasan (mahasiswa & semua unit memakai skrip yang sama)
 * Halaman : content/mahasiswa/ulasan.php, content/unit/{unit}/ulasan.php
 *           (isi halaman ada di includes/ulasan-content.php)
 * Fungsi  : initUlasanPage()
 * Data    : GET/POST backend/api/ulasan/saya.php, GET backend/api/ulasan/list.php
 * Satu akun = satu ulasan; boleh diedit atau dihapus kapan saja.
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

    const LABEL = ['Pilih bintang', 'Sangat buruk', 'Kurang baik', 'Cukup', 'Baik', 'Sangat baik'];
    let rating = 0;
    let sudahAda = false;

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

    // ---- ulasan milik saya ----
    async function muatSaya() {
        try {
            const { ulasan } = await window.sipekaAPI.getUlasanSaya();
            sudahAda = !!ulasan;
            if (ulasan) {
                rating = ulasan.rating;
                isi.value = ulasan.isi;
                status.textContent = 'Anda sudah menulis ulasan. Ubah lalu simpan untuk memperbaruinya.';
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

    // ---- ulasan publik terbaru ----
    async function muatDaftar() {
        try {
            const data = await window.sipekaAPI.getUlasanPublik(6);
            const total = data.ringkasan?.total ?? 0;
            const rata = data.ringkasan?.rata_rata;
            ringkasan.textContent = total ? `Rata-rata ${rata.toFixed(1).replace('.', ',')} dari 5 · ${total} ulasan` : '';
            daftar.innerHTML = total === 0
                ? '<p class="review-empty">Belum ada ulasan.</p>'
                : data.ulasan.map(kartuUlasanHtml).join('');
        } catch (err) {
            daftar.innerHTML = '<p class="review-empty">Daftar ulasan belum dapat dimuat.</p>';
        }
    }

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
        if (!window.confirm('Hapus ulasan Anda dari halaman depan?')) return;
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
