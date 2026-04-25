-- Mapping persil
BEGIN;

-- Tabel sementara untuk mencatat yang gagal
CREATE TEMP TABLE failed_persil (
    id integer,
    error_reason text
);

DO $$
DECLARE
    rec RECORD;
BEGIN
    FOR rec IN
        SELECT
            p.id,
            p.no_persil AS nomor_persil,
            NULL::varchar AS klas,
            p.luas,
            p.jalan AS alamat,
            p.id_kelurahan,
            NULL::bigint AS id_kecamatan,
            NULL::bigint AS id_kabupaten,
            p.batas_utara,
            p.batas_selatan,
            p.batas_timur,
            p.batas_barat,
            p.geom,
            NULL::varchar AS koordinat,
            p.id AS legacy_id,
            NULL::bigint AS created_by,
            NULL::bigint AS verified_by,
            NULL::timestamp AS created_at,
            NULL::timestamp AS updated_at
        FROM dblink(
            'host=127.0.0.1 port=5432 dbname=dev_pertanahan user=postgres password=password',
            'SELECT id, no_persil, luas, jalan, id_kelurahan,
                    batas_utara, batas_selatan, batas_timur, batas_barat, geom
             FROM persil
             WHERE id > 0
             ORDER BY id ASC'
        ) AS p(
            id integer,
            no_persil varchar,
            luas numeric,
            jalan varchar,
            id_kelurahan integer,
            batas_utara varchar,
            batas_selatan varchar,
            batas_timur varchar,
            batas_barat varchar,
            geom geometry
        )
    LOOP
        BEGIN
            INSERT INTO persil (
                id, nomor_persil, klas, luas, alamat,
                id_kelurahan, id_kecamatan, id_kabupaten,
                batas_utara, batas_selatan, batas_timur, batas_barat,
                geom, koordinat, legacy_id,
                created_by, verified_by, created_at, updated_at
            ) VALUES (
                rec.id,
                rec.nomor_persil,
                rec.klas,
                rec.luas,
                rec.alamat,
                rec.id_kelurahan,
                rec.id_kecamatan,
                rec.id_kabupaten,
                rec.batas_utara,
                rec.batas_selatan,
                rec.batas_timur,
                rec.batas_barat,
                rec.geom,
                rec.koordinat,
                rec.legacy_id,
                rec.created_by,
                rec.verified_by,
                rec.created_at,
                rec.updated_at
            );
        EXCEPTION
            WHEN OTHERS THEN
                INSERT INTO failed_persil (id, error_reason)
                VALUES (rec.id, SQLERRM);
        END;
    END LOOP;
END;
$$;

-- Cek hasil
SELECT COUNT(*) AS total_berhasil FROM persil;
SELECT COUNT(*) AS total_gagal FROM failed_persil;
SELECT * FROM failed_persil ORDER BY id;

COMMIT;

-- Mapping bidang

BEGIN;

CREATE TEMP TABLE failed_bidang (
    id integer,
    id_persil integer,
    error_reason text
);

DO $$
DECLARE
    rec RECORD;
BEGIN
    FOR rec IN
        SELECT
            b.id,
            NULLIF(b.id_persil::text, '')::integer AS id_persil,
            NULLIF(b.id_jenis_uupa::text, '')::bigint AS id_jenis_hak,
            NULLIF(b.id_jenis_hak::text, '')::bigint AS id_jenis_hak_adat,
            (SELECT p.id_kategori
             FROM dblink('host=127.0.0.1 port=5432 dbname=dev_pertanahan user=postgres password=password',
                'SELECT id, id_kategori FROM persil') AS p(id integer, id_kategori bigint)
             WHERE p.id = NULLIF(b.id_persil::text, '')::integer) AS id_kategori,
            (SELECT p.id_kelurahan
             FROM dblink('host=127.0.0.1 port=5432 dbname=dev_pertanahan user=postgres password=password',
                'SELECT id, id_kelurahan FROM persil') AS p(id integer, id_kelurahan bigint)
             WHERE p.id = NULLIF(b.id_persil::text, '')::integer) AS id_kelurahan,
            NULLIF(b.id_status_kesesuaian::text, '')::bigint AS id_status_kesesuaian,
            NULLIF(b.id_pengelola::text, '')::bigint AS id_pengelola,
            NULLIF(b.id_penggunaan::text, '')::bigint AS id_penggunaan,
            b.no_surat_uupa,
            b.no_kekancingan,
            b.no_bidang,
            b.luas,
            NULLIF(b.geom::text, '')::geometry AS geom,
            NULLIF(b.id_file::text, '')::bigint AS id_file,
            b.keterangan
        FROM dblink(
            'host=127.0.0.1 port=5432 dbname=dev_pertanahan user=postgres password=password',
            'SELECT id, id_persil, id_jenis_uupa, id_jenis_hak, id_status_kesesuaian,
                    id_pengelola, id_penggunaan, no_surat_uupa, no_kekancingan,
                    no_bidang, luas, geom, id_file, keterangan
             FROM bidang
             WHERE id_persil > 0
             ORDER BY id ASC'
        ) AS b(
            id integer,
            id_persil integer,
            id_jenis_uupa smallint,
            id_jenis_hak smallint,
            id_status_kesesuaian smallint,
            id_pengelola smallint,
            id_penggunaan smallint,
            no_surat_uupa varchar,
            no_kekancingan varchar,
            no_bidang varchar,
            luas numeric,
            geom geometry,
            id_file integer,
            keterangan varchar
        )
    LOOP
        BEGIN
            INSERT INTO bidang (
                id, id_persil, id_jenis_hak, id_jenis_hak_adat,
                id_kategori, id_status_kesesuaian, pemilik,
                id_pengelola, id_penggunaan, id_kelurahan, nomor_hak, nomor_hak_adat,
                nomor_bidang, luas, geom, koordinat, id_file, keterangan,
                created_by, verified_by, created_at, updated_at
            ) VALUES (
                rec.id,
                rec.id_persil,
                rec.id_jenis_hak,
                rec.id_jenis_hak_adat,
                rec.id_kategori,
                rec.id_status_kesesuaian,
                NULL::varchar,
                rec.id_pengelola,
                rec.id_penggunaan,
                rec.id_kelurahan,
                rec.no_surat_uupa,
                rec.no_kekancingan,
                rec.no_bidang,
                rec.luas,
                rec.geom,
                NULL::varchar,
                rec.id_file,
                rec.keterangan,
                NULL::bigint,
                NULL::bigint,
                NULL::timestamp,
                NULL::timestamp
            );
        EXCEPTION
            WHEN OTHERS THEN
                INSERT INTO failed_bidang (id, id_persil, error_reason)
                VALUES (rec.id, rec.id_persil, SQLERRM);
        END;
    END LOOP;
END;
$$;

-- Cek hasil
SELECT COUNT(*) AS total_berhasil FROM bidang;
SELECT COUNT(*) AS total_gagal FROM failed_bidang;
SELECT * FROM failed_bidang ORDER BY id;

COMMIT;
