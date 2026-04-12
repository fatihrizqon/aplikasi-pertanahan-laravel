ALTER TABLE provinsi  ALTER COLUMN kode TYPE varchar(16);
ALTER TABLE kabupaten ALTER COLUMN kode TYPE varchar(16);
ALTER TABLE kecamatan ALTER COLUMN kode TYPE varchar(16);
ALTER TABLE kelurahan ALTER COLUMN kode TYPE varchar(16);

-- ── KABUPATEN ──
UPDATE kabupaten SET kode = '34.01' WHERE nama ILIKE '%kulon progo%';
UPDATE kabupaten SET kode = '34.02' WHERE nama ILIKE '%bantul%';
UPDATE kabupaten SET kode = '34.03' WHERE nama ILIKE '%gunungkidul%';
UPDATE kabupaten SET kode = '34.04' WHERE nama ILIKE '%sleman%';
UPDATE kabupaten SET kode = '34.71' WHERE nama ILIKE '%yogyakarta%';

-- ── KECAMATAN KULON PROGO (34.01) ──
UPDATE kecamatan SET kode = '34.01.01' WHERE nama ILIKE '%temon%';
UPDATE kecamatan SET kode = '34.01.02' WHERE nama ILIKE '%wates%';
UPDATE kecamatan SET kode = '34.01.03' WHERE nama ILIKE '%panjatan%';
UPDATE kecamatan SET kode = '34.01.04' WHERE nama ILIKE '%galur%';
UPDATE kecamatan SET kode = '34.01.05' WHERE nama ILIKE '%lendah%';
UPDATE kecamatan SET kode = '34.01.06' WHERE nama ILIKE '%sentolo%';
UPDATE kecamatan SET kode = '34.01.07' WHERE nama ILIKE '%pengasih%';
UPDATE kecamatan SET kode = '34.01.08' WHERE nama ILIKE '%kokap%';
UPDATE kecamatan SET kode = '34.01.09' WHERE nama ILIKE '%girimulyo%';
UPDATE kecamatan SET kode = '34.01.10' WHERE nama ILIKE '%nanggulan%';
UPDATE kecamatan SET kode = '34.01.11' WHERE nama ILIKE '%kalibawang%';
UPDATE kecamatan SET kode = '34.01.12' WHERE nama ILIKE '%samigaluh%';

-- ── KECAMATAN BANTUL (34.02) ──
UPDATE kecamatan SET kode = '34.02.01' WHERE nama ILIKE '%srandakan%';
UPDATE kecamatan SET kode = '34.02.02' WHERE nama ILIKE '%sanden%';
UPDATE kecamatan SET kode = '34.02.03' WHERE nama ILIKE '%kretek%';
UPDATE kecamatan SET kode = '34.02.04' WHERE nama ILIKE '%pundong%';
UPDATE kecamatan SET kode = '34.02.05' WHERE nama ILIKE '%bambanglipuro%';
UPDATE kecamatan SET kode = '34.02.06' WHERE nama ILIKE '%pandak%';
UPDATE kecamatan SET kode = '34.02.07' WHERE nama ILIKE '%bantul%';
UPDATE kecamatan SET kode = '34.02.08' WHERE nama ILIKE '%jetis%';
UPDATE kecamatan SET kode = '34.02.09' WHERE nama ILIKE '%imogiri%';
UPDATE kecamatan SET kode = '34.02.10' WHERE nama ILIKE '%dlingo%';
UPDATE kecamatan SET kode = '34.02.11' WHERE nama ILIKE '%pleret%';
UPDATE kecamatan SET kode = '34.02.12' WHERE nama ILIKE '%piyungan%';
UPDATE kecamatan SET kode = '34.02.13' WHERE nama ILIKE '%banguntapan%';
UPDATE kecamatan SET kode = '34.02.14' WHERE nama ILIKE '%sewon%';
UPDATE kecamatan SET kode = '34.02.15' WHERE nama ILIKE '%kasihan%';
UPDATE kecamatan SET kode = '34.02.16' WHERE nama ILIKE '%pajangan%';
UPDATE kecamatan SET kode = '34.02.17' WHERE nama ILIKE '%sedayu%';

-- ── KECAMATAN GUNUNGKIDUL (34.03) ──
UPDATE kecamatan SET kode = '34.03.01' WHERE nama ILIKE '%wonosari%';
UPDATE kecamatan SET kode = '34.03.02' WHERE nama ILIKE '%nglipar%';
UPDATE kecamatan SET kode = '34.03.03' WHERE nama ILIKE '%ngawen%';
UPDATE kecamatan SET kode = '34.03.04' WHERE nama ILIKE '%semin%';
UPDATE kecamatan SET kode = '34.03.05' WHERE nama ILIKE '%ponjong%';
UPDATE kecamatan SET kode = '34.03.06' WHERE nama ILIKE '%karangmojo%';
UPDATE kecamatan SET kode = '34.03.07' WHERE nama ILIKE '%semanu%';
UPDATE kecamatan SET kode = '34.03.08' WHERE nama ILIKE '%paliyan%';
UPDATE kecamatan SET kode = '34.03.09' WHERE nama ILIKE '%saptosari%';
UPDATE kecamatan SET kode = '34.03.10' WHERE nama ILIKE '%tepus%';
UPDATE kecamatan SET kode = '34.03.11' WHERE nama ILIKE '%tanjungsari%';
UPDATE kecamatan SET kode = '34.03.12' WHERE nama ILIKE '%rongkop%';
UPDATE kecamatan SET kode = '34.03.13' WHERE nama ILIKE '%girisubo%';
UPDATE kecamatan SET kode = '34.03.14' WHERE nama ILIKE '%purwosari%';
UPDATE kecamatan SET kode = '34.03.15' WHERE nama ILIKE '%panggang%';
UPDATE kecamatan SET kode = '34.03.16' WHERE nama ILIKE '%patuk%';
UPDATE kecamatan SET kode = '34.03.17' WHERE nama ILIKE '%gedangsari%';
UPDATE kecamatan SET kode = '34.03.18' WHERE nama ILIKE '%playen%';

-- ── KECAMATAN SLEMAN (34.04) ──
UPDATE kecamatan SET kode = '34.04.01' WHERE nama ILIKE '%moyudan%';
UPDATE kecamatan SET kode = '34.04.02' WHERE nama ILIKE '%minggir%';
UPDATE kecamatan SET kode = '34.04.03' WHERE nama ILIKE '%seyegan%';
UPDATE kecamatan SET kode = '34.04.04' WHERE nama ILIKE '%godean%';
UPDATE kecamatan SET kode = '34.04.05' WHERE nama ILIKE '%gamping%';
UPDATE kecamatan SET kode = '34.04.06' WHERE nama ILIKE '%mlati%';
UPDATE kecamatan SET kode = '34.04.07' WHERE nama ILIKE '%depok%';
UPDATE kecamatan SET kode = '34.04.08' WHERE nama ILIKE '%berbah%';
UPDATE kecamatan SET kode = '34.04.09' WHERE nama ILIKE '%prambanan%';
UPDATE kecamatan SET kode = '34.04.10' WHERE nama ILIKE '%kalasan%';
UPDATE kecamatan SET kode = '34.04.11' WHERE nama ILIKE '%ngemplak%';
UPDATE kecamatan SET kode = '34.04.12' WHERE nama ILIKE '%ngaglik%';
UPDATE kecamatan SET kode = '34.04.13' WHERE nama ILIKE '%sleman%';
UPDATE kecamatan SET kode = '34.04.14' WHERE nama ILIKE '%tempel%';
UPDATE kecamatan SET kode = '34.04.15' WHERE nama ILIKE '%turi%';
UPDATE kecamatan SET kode = '34.04.16' WHERE nama ILIKE '%pakem%';
UPDATE kecamatan SET kode = '34.04.17' WHERE nama ILIKE '%cangkringan%';

-- ── KECAMATAN KOTA YOGYAKARTA (34.71) ──
UPDATE kecamatan SET kode = '34.71.01' WHERE nama ILIKE '%mantrijeron%';
UPDATE kecamatan SET kode = '34.71.02' WHERE nama ILIKE '%kraton%';
UPDATE kecamatan SET kode = '34.71.03' WHERE nama ILIKE '%mergangsan%';
UPDATE kecamatan SET kode = '34.71.04' WHERE nama ILIKE '%umbulharjo%';
UPDATE kecamatan SET kode = '34.71.05' WHERE nama ILIKE '%kotagede%';
UPDATE kecamatan SET kode = '34.71.06' WHERE nama ILIKE '%gondokusuman%';
UPDATE kecamatan SET kode = '34.71.07' WHERE nama ILIKE '%danurejan%';
UPDATE kecamatan SET kode = '34.71.08' WHERE nama ILIKE '%pakualaman%';
UPDATE kecamatan SET kode = '34.71.09' WHERE nama ILIKE '%gondomanan%';
UPDATE kecamatan SET kode = '34.71.10' WHERE nama ILIKE '%ngampilan%';
UPDATE kecamatan SET kode = '34.71.11' WHERE nama ILIKE '%wirobrajan%';
UPDATE kecamatan SET kode = '34.71.12' WHERE nama ILIKE '%gedongtengen%';
UPDATE kecamatan SET kode = '34.71.13' WHERE nama ILIKE '%jetis%';
UPDATE kecamatan SET kode = '34.71.14' WHERE nama ILIKE '%tegalrejo%';

WITH urutan AS (
    SELECT
        kel.id,
        kec.kode || '.' || LPAD(ROW_NUMBER() OVER (
            PARTITION BY kel.id_kecamatan ORDER BY kel.nama
        )::TEXT, 3, '0') AS kode_baru
    FROM kelurahan kel
    JOIN kecamatan kec ON kel.id_kecamatan = kec.id
)
UPDATE kelurahan
SET kode = urutan.kode_baru
FROM urutan
WHERE kelurahan.id = urutan.id;
