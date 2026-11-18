-- =====================================================================
-- SIPEKA - Migrasi 2: BALASAN ULASAN (mahasiswa & unit saling menjawab)
-- Jalankan SETELAH migrasi_ulasan_notifikasi.sql (sekali saja, aman diulang):
--   psql -U postgres -d db_sipekabaru -f DB/migrasi_balasan_ulasan.sql
-- =====================================================================
BEGIN;

CREATE TABLE IF NOT EXISTS ulasan_balasan (
    id_balasan  BIGSERIAL PRIMARY KEY,
    id_ulasan   BIGINT      NOT NULL REFERENCES ulasan(id_ulasan) ON DELETE CASCADE,
    id_user     BIGINT      NOT NULL REFERENCES users(id_user)    ON DELETE CASCADE,
    isi_balasan TEXT        NOT NULL,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT ck_balasan_isi CHECK (char_length(btrim(isi_balasan)) BETWEEN 2 AND 300)
);
CREATE INDEX IF NOT EXISTS idx_balasan_ulasan ON ulasan_balasan (id_ulasan, id_balasan);

-- Jenis notifikasi baru: 'balasan_ulasan'
ALTER TABLE notifikasi DROP CONSTRAINT IF EXISTS ck_notifikasi_jenis;
ALTER TABLE notifikasi ADD CONSTRAINT ck_notifikasi_jenis CHECK (jenis IN (
    'laporan_baru', 'laporan_divalidasi', 'laporan_ditolak',
    'tanggapan_baru', 'laporan_selesai', 'balasan_ulasan'
));

-- Balasan baru -> pemilik ulasan + peserta lain di ulasan itu (kecuali penulis balasan)
CREATE OR REPLACE FUNCTION fn_notif_balasan_ulasan() RETURNS trigger
LANGUAGE plpgsql AS $$
BEGIN
    INSERT INTO notifikasi (id_user, id_laporan, jenis, judul, pesan)
    SELECT p.id_user, NULL, 'balasan_ulasan', 'Balasan baru pada ulasan', left(NEW.isi_balasan, 120)
    FROM (
        SELECT ul.id_user FROM ulasan ul WHERE ul.id_ulasan = NEW.id_ulasan
        UNION
        SELECT b.id_user FROM ulasan_balasan b WHERE b.id_ulasan = NEW.id_ulasan
    ) p
    WHERE p.id_user <> NEW.id_user;
    RETURN NEW;
END $$;

DROP TRIGGER IF EXISTS trg_notif_balasan_ulasan ON ulasan_balasan;
CREATE TRIGGER trg_notif_balasan_ulasan
    AFTER INSERT ON ulasan_balasan
    FOR EACH ROW EXECUTE FUNCTION fn_notif_balasan_ulasan();

COMMIT;
