BEGIN;

ALTER TABLE notifikasi
DROP CONSTRAINT ck_notifikasi_jenis;

ALTER table notifikasi
ADD CONSTRAINT ck_notifikasi_jenis
CHECK (
    jenis::text IN (
        'laporan_baru',
        'laporan_divalidasi',
        'laporan_ditolak',
        'tanggapan_baru',
        'laporan_selesai',
        'balasan_ulasan',
        'ulasan_baru'
    )
);

COMMIT;