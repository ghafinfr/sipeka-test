-- =====================================================================
-- SIPEKA - Migrasi 4: ULASAN PER PENANGANAN, HANYA MAHASISWA
--   * ulasan boleh dikaitkan ke satu laporan yang SUDAH SELESAI ditangani
--     (id_laporan, opsional: kosong = ulasan umum)
--   * hanya akun mahasiswa yang bisa menulis ulasan (unit hanya membalas)
--   * unit penanggung jawab mendapat notifikasi saat ulasan penanganannya masuk
-- Jalankan SETELAH migrasi_ulasan_notifikasi, migrasi_balasan_ulasan, migrasi_ulasan_komentar:
--   psql -U postgres -d db_sipekabaru -f DB/migrasi_ulasan_penanganan.sql
-- Aman diulang. Ulasan lama milik unit TIDAK dihapus, hanya tidak ditampilkan lagi.
-- =====================================================================
BEGIN;

-- 1. Kaitan ke penanganan (laporan). Laporan terhapus -> ulasan menjadi ulasan umum.
ALTER TABLE ulasan
    ADD COLUMN IF NOT EXISTS id_laporan BIGINT
    REFERENCES laporan (id_laporan) ON UPDATE CASCADE ON DELETE SET NULL;
CREATE INDEX IF NOT EXISTS idx_ulasan_laporan ON ulasan (id_laporan) WHERE id_laporan IS NOT NULL;

-- 2. Validasi di database (selain di PHP): penulis harus mahasiswa; laporan harus milik
--    penulis dan berstatus selesai.
CREATE OR REPLACE FUNCTION fn_validasi_ulasan() RETURNS trigger
LANGUAGE plpgsql AS $$
DECLARE
    v_role    TEXT;
    v_status  TEXT;
    v_pemilik INT;
BEGIN
    SELECT role::text INTO v_role FROM users WHERE id_user = NEW.id_user;
    IF v_role IS DISTINCT FROM 'mahasiswa' THEN
        RAISE EXCEPTION 'Hanya mahasiswa yang dapat menulis ulasan' USING ERRCODE = '23514';
    END IF;

    IF NEW.id_laporan IS NOT NULL THEN
        SELECT l.status::text, m.id_user INTO v_status, v_pemilik
          FROM laporan l
          JOIN mahasiswa m ON m.id_mahasiswa = l.id_mahasiswa
         WHERE l.id_laporan = NEW.id_laporan;
        IF v_pemilik IS DISTINCT FROM NEW.id_user THEN
            RAISE EXCEPTION 'Penanganan yang diulas bukan laporan Anda' USING ERRCODE = '23514';
        END IF;
        IF v_status <> 'selesai' THEN
            RAISE EXCEPTION 'Hanya penanganan yang sudah selesai yang dapat diulas' USING ERRCODE = '23514';
        END IF;
    END IF;
    RETURN NEW;
END $$;

DROP TRIGGER IF EXISTS trg_validasi_ulasan ON ulasan;
CREATE TRIGGER trg_validasi_ulasan
    BEFORE INSERT ON ulasan
    FOR EACH ROW EXECUTE FUNCTION fn_validasi_ulasan();

-- 3. Jenis notifikasi baru: 'ulasan_baru' (ke petugas unit penanggung jawab laporan itu)
ALTER TABLE notifikasi DROP CONSTRAINT IF EXISTS ck_notifikasi_jenis;
ALTER TABLE notifikasi ADD CONSTRAINT ck_notifikasi_jenis CHECK (jenis IN (
    'laporan_baru', 'laporan_divalidasi', 'laporan_ditolak',
    'tanggapan_baru', 'laporan_selesai', 'balasan_ulasan', 'ulasan_baru'
));

CREATE OR REPLACE FUNCTION fn_notif_ulasan_baru() RETURNS trigger
LANGUAGE plpgsql AS $$
BEGIN
    IF NEW.id_laporan IS NULL THEN
        RETURN NEW;               -- ulasan umum tidak ditujukan ke unit tertentu
    END IF;
    INSERT INTO notifikasi (id_user, id_laporan, jenis, judul, pesan)
    SELECT u.id_user, NEW.id_laporan, 'ulasan_baru', 'Ulasan baru dari mahasiswa',
           left(NEW.isi_ulasan, 120)
      FROM laporan l
      JOIN kategori k ON k.id_kategori = l.id_kategori
      JOIN users u    ON u.id_unit = k.id_unit
     WHERE l.id_laporan = NEW.id_laporan;
    RETURN NEW;
END $$;

DROP TRIGGER IF EXISTS trg_notif_ulasan_baru ON ulasan;
CREATE TRIGGER trg_notif_ulasan_baru
    AFTER INSERT ON ulasan
    FOR EACH ROW EXECUTE FUNCTION fn_notif_ulasan_baru();

COMMIT;
