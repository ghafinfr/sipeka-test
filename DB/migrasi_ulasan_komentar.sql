-- =====================================================================
-- SIPEKA - Migrasi 3: ULASAN = KOMENTAR (tanpa bintang, tidak dibatasi 1 per akun)
-- Jalankan SETELAH migrasi_ulasan_notifikasi.sql dan migrasi_balasan_ulasan.sql:
--   psql -U postgres -d db_sipekabaru -f DB/migrasi_ulasan_komentar.sql
-- Aman diulang. Data ulasan lama tetap ada (nilai bintang lama diabaikan).
-- =====================================================================
BEGIN;

-- 1 akun boleh menulis banyak ulasan
ALTER TABLE ulasan DROP CONSTRAINT IF EXISTS uq_ulasan_user;

-- bintang tidak dipakai lagi (kolom dipertahankan agar data lama aman)
ALTER TABLE ulasan ALTER COLUMN rating DROP NOT NULL;

-- komentar boleh lebih singkat: 3-500 karakter
ALTER TABLE ulasan DROP CONSTRAINT IF EXISTS ck_ulasan_isi;
ALTER TABLE ulasan ADD CONSTRAINT ck_ulasan_isi
    CHECK (char_length(btrim(isi_ulasan)) BETWEEN 3 AND 500);

-- ulasan per akun (daftar milik sendiri) dan urutan terbaru
CREATE INDEX IF NOT EXISTS idx_ulasan_user ON ulasan (id_user, id_ulasan DESC);
CREATE INDEX IF NOT EXISTS idx_ulasan_waktu ON ulasan (created_at DESC);

COMMIT;
