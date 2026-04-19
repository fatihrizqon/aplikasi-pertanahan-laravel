-- Mapping persil
SELECT
    id,
    no_persil AS nomor_persil,
    NULL AS klas,
    luas,
    jalan AS alamat,
    id_kelurahan,
    NULL AS id_kecamatan,
    NULL AS id_kabupaten,
    batas_utara,
    batas_selatan,
    batas_timur,
    batas_barat,
    geom,
    NULL AS koordinat,
    id AS legacy_id,
    NULL AS created_by,
    NULL AS verified_by,
    NULL AS created_at,
    NULL AS updated_at
FROM
    persil;
ORDER BY
    id ASC;

-- Mapping bidang

SELECT
    id,
    NULLIF(id_persil::text, '')::integer AS id_persil,
    NULLIF(id_jenis_uupa::text, '')::integer AS id_jenis_hak,
    NULLIF(id_jenis_hak::text, '')::integer AS id_jenis_hak_adat,
    (
        SELECT id_kategori
        FROM persil
        WHERE id = NULLIF(bidang.id_persil::text, '')::integer
    ) AS id_kategori,
    NULLIF(id_status_kesesuaian::text, '')::integer AS id_status_kesesuaian,
    NULL AS pemilik,
    NULLIF(id_pengelola::text, '')::integer AS id_pengelola,
    NULLIF(id_penggunaan::text, '')::integer AS id_penggunaan,
    no_surat_uupa AS nomor_hak,
    no_kekancingan AS nomor_hak_adat,
    no_bidang AS nomor_bidang,
    luas,
    geom,
    NULL AS koordinat,
    NULL AS id_file,
    keterangan,
    NULL AS created_by,
    NULL AS verified_by,
    NULL AS created_at,
    NULL AS updated_at
FROM
    bidang
ORDER BY
    id ASC;
