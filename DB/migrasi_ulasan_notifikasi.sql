-- =====================================================================
-- SIPEKA - Migrasi tambahan: ULASAN + NOTIFIKASI
--
-- Cara pakai (sekali saja, aman dijalankan ulang):
--   psql -U postgres -d db_sipekabaru -f DB/migrasi_ulasan_notifikasi.sql
--
-- Memakai tabel yang sudah dipakai kode backend:
--   users, mahasiswa, unit, kategori, laporan, tanggapan, riwayat_status
-- Isi tabel baru:
--   1. notifikasi  : satu baris per penerima (mahasiswa atau petugas unit)
--   2. ulasan      : satu ulasan per akun (mahasiswa atau unit), bisa diedit
--   3. trigger     : notifikasi dibuat OTOMATIS oleh database, jadi file PHP
--                    yang sudah ada (create.php, validasi.php, tanggapan.php,
--                    selesai.php) tidak perlu diubah.
--   4. view        : v_ulasan_publik untuk halaman depan
-- =====================================================================

BEGIN;

-- ---------------------------------------------------------------------
-- 1. NOTIFIKASI
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifikasi (
    id_notifikasi BIGSERIAL PRIMARY KEY,
    id_user       BIGINT       NOT NULL REFERENCES users(id_user)      ON DELETE CASCADE,
    id_laporan    BIGINT                REFERENCES laporan(id_laporan) ON DELETE CASCADE,
    jenis         VARCHAR(30)  NOT NULL,
    judul         VARCHAR(150) NOT NULL,
    pesan         TEXT         NOT NULL,
    sudah_dibaca  BOOLEAN      NOT NULL DEFAULT FALSE,
    created_at    TIMESTAMPTZ  NOT NULL DEFAULT now(),
    dibaca_at     TIMESTAMPTZ,
    CONSTRAINT ck_notifikasi_jenis CHECK (jenis IN (
        'laporan_baru',        -- ke petugas unit: ada laporan masuk
        'laporan_divalidasi',  -- ke mahasiswa: laporan diterima, masuk penanganan
        'laporan_ditolak',     -- ke mahasiswa: laporan ditolak
        'tanggapan_baru',      -- ke mahasiswa: unit menulis tanggapan
        'laporan_selesai'      -- ke mahasiswa: laporan selesai ditangani
    ))
);

-- daftar notifikasi terbaru per pengguna + hitung yang belum dibaca
CREATE INDEX IF NOT EXISTS idx_notifikasi_user_terbaru
    ON notifikasi (id_user, id_notifikasi DESC);
CREATE INDEX IF NOT EXISTS idx_notifikasi_belum_dibaca
    ON notifikasi (id_user) WHERE NOT sudah_dibaca;

-- ---------------------------------------------------------------------
-- 2. ULASAN  (satu akun = satu ulasan; mahasiswa maupun petugas unit)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ulasan (
    id_ulasan  BIGSERIAL PRIMARY KEY,
    id_user    BIGINT      NOT NULL REFERENCES users(id_user) ON DELETE CASCADE,
    rating     SMALLINT    NOT NULL,
    isi_ulasan TEXT        NOT NULL,
    tampil     BOOLEAN     NOT NULL DEFAULT TRUE,   -- FALSE = disembunyikan dari halaman depan
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT uq_ulasan_user  UNIQUE (id_user),
    CONSTRAINT ck_ulasan_rating CHECK (rating BETWEEN 1 AND 5),
    CONSTRAINT ck_ulasan_isi    CHECK (char_length(btrim(isi_ulasan)) BETWEEN 10 AND 500)
);

CREATE INDEX IF NOT EXISTS idx_ulasan_tampil_terbaru
    ON ulasan (created_at DESC) WHERE tampil;

-- Tampilan publik: dipakai halaman depan (tanpa email / data pribadi lain)
CREATE OR REPLACE VIEW v_ulasan_publik AS
SELECT ul.id_ulasan,
       u.nama,
       u.role::text AS peran,
       un.nama_unit,
       ul.rating,
       ul.isi_ulasan,
       ul.created_at,
       ul.updated_at
FROM ulasan ul
JOIN users u        ON u.id_user = ul.id_user
LEFT JOIN unit un   ON un.id_unit = u.id_unit
WHERE ul.tampil;

-- ---------------------------------------------------------------------
-- 3. TRIGGER NOTIFIKASI
-- ---------------------------------------------------------------------

-- 3a. Laporan baru  ->  semua petugas unit penanggung jawab kategori tsb
CREATE OR REPLACE FUNCTION fn_notif_laporan_baru() RETURNS trigger
LANGUAGE plpgsql AS $$
DECLARE
    v_tiket TEXT := 'SPK-' || to_char(NEW.tanggal_lapor, 'YYYY') || '-' || lpad(NEW.id_laporan::text, 5, '0');
BEGIN
    INSERT INTO notifikasi (id_user, id_laporan, jenis, judul, pesan)
    SELECT u.id_user, NEW.id_laporan, 'laporan_baru', 'Laporan baru masuk',
           v_tiket || ' - ' || NEW.judul_laporan
    FROM kategori k
    JOIN users u ON u.id_unit = k.id_unit
    WHERE k.id_kategori = NEW.id_kategori;
    RETURN NEW;
END $$;

DROP TRIGGER IF EXISTS trg_notif_laporan_baru ON laporan;
CREATE TRIGGER trg_notif_laporan_baru
    AFTER INSERT ON laporan
    FOR EACH ROW EXECUTE FUNCTION fn_notif_laporan_baru();

-- 3b. Status berubah (dicatat di riwayat_status oleh sp_ubah_status) -> mahasiswa pelapor
CREATE OR REPLACE FUNCTION fn_notif_status_berubah() RETURNS trigger
LANGUAGE plpgsql AS $$
DECLARE
    v_user   BIGINT;
    v_judul  TEXT;
    v_tiket  TEXT;
    v_jenis  TEXT;
    v_title  TEXT;
    v_pesan  TEXT;
BEGIN
    -- status awal 'menunggu' dibuat trigger laporan; tidak perlu diberitahukan
    IF NEW.status::text NOT IN ('dalam_penanganan', 'ditolak', 'selesai') THEN
        RETURN NEW;
    END IF;

    SELECT m.id_user, l.judul_laporan,
           'SPK-' || to_char(l.tanggal_lapor, 'YYYY') || '-' || lpad(l.id_laporan::text, 5, '0')
      INTO v_user, v_judul, v_tiket
      FROM laporan l
      JOIN mahasiswa m ON m.id_mahasiswa = l.id_mahasiswa
     WHERE l.id_laporan = NEW.id_laporan;

    IF v_user IS NULL THEN
        RETURN NEW;
    END IF;

    IF NEW.status::text = 'dalam_penanganan' THEN
        v_jenis := 'laporan_divalidasi'; v_title := 'Laporan Anda diterima';
        v_pesan := v_tiket || ' telah divalidasi dan sedang ditangani.';
    ELSIF NEW.status::text = 'ditolak' THEN
        v_jenis := 'laporan_ditolak';    v_title := 'Laporan Anda ditolak';
        v_pesan := v_tiket || ' ditolak oleh unit. Buka detail untuk melihat alasannya.';
    ELSE
        v_jenis := 'laporan_selesai';    v_title := 'Laporan Anda selesai ditangani';
        v_pesan := v_tiket || ' - ' || v_judul;
    END IF;

    INSERT INTO notifikasi (id_user, id_laporan, jenis, judul, pesan)
    VALUES (v_user, NEW.id_laporan, v_jenis, v_title, v_pesan);
    RETURN NEW;
END $$;

DROP TRIGGER IF EXISTS trg_notif_status_berubah ON riwayat_status;
CREATE TRIGGER trg_notif_status_berubah
    AFTER INSERT ON riwayat_status
    FOR EACH ROW EXECUTE FUNCTION fn_notif_status_berubah();

-- 3c. Tanggapan unit -> mahasiswa pelapor
CREATE OR REPLACE FUNCTION fn_notif_tanggapan_baru() RETURNS trigger
LANGUAGE plpgsql AS $$
DECLARE
    v_user  BIGINT;
    v_tiket TEXT;
BEGIN
    SELECT m.id_user,
           'SPK-' || to_char(l.tanggal_lapor, 'YYYY') || '-' || lpad(l.id_laporan::text, 5, '0')
      INTO v_user, v_tiket
      FROM laporan l
      JOIN mahasiswa m ON m.id_mahasiswa = l.id_mahasiswa
     WHERE l.id_laporan = NEW.id_laporan;

    IF v_user IS NOT NULL THEN
        INSERT INTO notifikasi (id_user, id_laporan, jenis, judul, pesan)
        VALUES (v_user, NEW.id_laporan, 'tanggapan_baru', 'Tanggapan baru dari unit',
                v_tiket || ' - ' || left(NEW.isi_tanggapan, 120));
    END IF;
    RETURN NEW;
END $$;

DROP TRIGGER IF EXISTS trg_notif_tanggapan_baru ON tanggapan;
CREATE TRIGGER trg_notif_tanggapan_baru
    AFTER INSERT ON tanggapan
    FOR EACH ROW EXECUTE FUNCTION fn_notif_tanggapan_baru();

COMMIT;

-- ---------------------------------------------------------------------
-- (Opsional) Uji cepat setelah migrasi:
--   SELECT * FROM notifikasi ORDER BY id_notifikasi DESC LIMIT 5;
--   -- buat 1 laporan dari akun mahasiswa -> petugas unit terkait mendapat notifikasi
--   -- validasi / tolak / tanggapi / selesaikan dari akun unit -> mahasiswa mendapat notifikasi
--   -- sembunyikan ulasan tertentu dari halaman depan:
--   --   UPDATE ulasan SET tampil = FALSE WHERE id_ulasan = 1;
-- ---------------------------------------------------------------------
