--
-- PostgreSQL database dump
--

-- Dumped from database version 17.4
-- Dumped by pg_dump version 17.4

-- Started on 2026-04-13 08:20:24

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 6 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

-- *not* creating schema, since initdb creates it


ALTER SCHEMA public OWNER TO postgres;

--
-- TOC entry 2 (class 3079 OID 18526)
-- Name: postgis; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA public;


--
-- TOC entry 6472 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION postgis; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis IS 'PostGIS geometry, geography, and raster spatial types and functions';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 223 (class 1259 OID 19606)
-- Name: bidang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.bidang (
    id integer NOT NULL,
    id_jenis_hak smallint,
    id_jenis_uupa smallint,
    no_surat_uupa character varying(64),
    no_bidang character varying(64) NOT NULL,
    id_pengelola smallint,
    no_kekancingan character varying(64),
    luas numeric(10,2),
    id_penggunaan smallint,
    tgl_mulai date,
    tgl_selesai date,
    keterangan character varying(512),
    id_status_kesesuaian smallint,
    no_sertifikat character varying(128),
    id_file integer,
    id_status_sertifikat smallint,
    geom public.geometry(MultiPolygon,4326),
    id_persil integer,
    id_kesesuaian_rdtr smallint,
    id_peta integer,
    id_sg_pag_lama character varying(256),
    last_updated character varying(500) DEFAULT NULL::character varying
);


ALTER TABLE public.bidang OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 19612)
-- Name: bidang_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.bidang_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bidang_id_seq OWNER TO postgres;

--
-- TOC entry 6473 (class 0 OID 0)
-- Dependencies: 224
-- Name: bidang_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.bidang_id_seq OWNED BY public.bidang.id;


--
-- TOC entry 225 (class 1259 OID 19613)
-- Name: peta_kecamatan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.peta_kecamatan (
    gid integer NOT NULL,
    kabupaten character varying(50),
    kecamatan character varying(50),
    geom public.geometry(MultiPolygon,4326)
);


ALTER TABLE public.peta_kecamatan OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 19618)
-- Name: bmap_kec_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.bmap_kec_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bmap_kec_gid_seq OWNER TO postgres;

--
-- TOC entry 6474 (class 0 OID 0)
-- Dependencies: 226
-- Name: bmap_kec_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.bmap_kec_gid_seq OWNED BY public.peta_kecamatan.gid;


--
-- TOC entry 227 (class 1259 OID 19619)
-- Name: config; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.config (
    id smallint NOT NULL,
    nama character varying(32) NOT NULL,
    isi character varying(64) NOT NULL
);


ALTER TABLE public.config OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 19622)
-- Name: file; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.file (
    id integer NOT NULL,
    nama character varying(256)
);


ALTER TABLE public.file OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 19625)
-- Name: file_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.file_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.file_id_seq OWNER TO postgres;

--
-- TOC entry 6475 (class 0 OID 0)
-- Dependencies: 229
-- Name: file_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.file_id_seq OWNED BY public.file.id;


--
-- TOC entry 230 (class 1259 OID 19626)
-- Name: galeri_bidang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.galeri_bidang (
    id integer NOT NULL,
    id_bidang integer,
    id_file integer,
    nama character varying(128)
);


ALTER TABLE public.galeri_bidang OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 19629)
-- Name: galeri_bidang_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.galeri_bidang_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.galeri_bidang_id_seq OWNER TO postgres;

--
-- TOC entry 6476 (class 0 OID 0)
-- Dependencies: 231
-- Name: galeri_bidang_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.galeri_bidang_id_seq OWNED BY public.galeri_bidang.id;


--
-- TOC entry 232 (class 1259 OID 19630)
-- Name: galeri_sub_persil_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.galeri_sub_persil_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.galeri_sub_persil_id_seq OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 19631)
-- Name: galeri_sub_persil; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.galeri_sub_persil (
    id integer DEFAULT nextval('public.galeri_sub_persil_id_seq'::regclass) NOT NULL,
    id_file integer,
    id_sub_persil integer,
    nama character varying(255)
);


ALTER TABLE public.galeri_sub_persil OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 19635)
-- Name: idmc_kawasan_strategis; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.idmc_kawasan_strategis (
    gid integer NOT NULL,
    kabupaten character varying(50),
    kecamatan character varying(50),
    desa character varying(50),
    koridor character varying(50),
    wp character varying(50),
    wadmkc character varying(50),
    wadmkk character varying(50),
    keterangan character varying(50),
    luas_ha double precision,
    nama character varying(30),
    ket character varying(50),
    keterang_1 character varying(50),
    keta character varying(75),
    wadmkc_1 character varying(50),
    wadmkk_1 character varying(50),
    ket_1 character varying(50),
    geom public.geometry(MultiPolygonZM,32749),
    id_jenis integer
);


ALTER TABLE public.idmc_kawasan_strategis OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 19640)
-- Name: idmc_kawasan_strategis_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.idmc_kawasan_strategis_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.idmc_kawasan_strategis_gid_seq OWNER TO postgres;

--
-- TOC entry 6477 (class 0 OID 0)
-- Dependencies: 235
-- Name: idmc_kawasan_strategis_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.idmc_kawasan_strategis_gid_seq OWNED BY public.idmc_kawasan_strategis.gid;


--
-- TOC entry 236 (class 1259 OID 19641)
-- Name: idmc_kawasan_strategis_jenis; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.idmc_kawasan_strategis_jenis (
    id integer NOT NULL,
    nama character varying(100),
    warna character varying(7)
);


ALTER TABLE public.idmc_kawasan_strategis_jenis OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 19644)
-- Name: idmc_kawasan_strategis_kasultanan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.idmc_kawasan_strategis_kasultanan (
    gid integer NOT NULL,
    objectid numeric(10,0),
    satuan_rua character varying(254),
    geom public.geometry(Point,32749)
);


ALTER TABLE public.idmc_kawasan_strategis_kasultanan OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 19649)
-- Name: idmc_kawasan_strategis_kasultanan_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.idmc_kawasan_strategis_kasultanan_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.idmc_kawasan_strategis_kasultanan_gid_seq OWNER TO postgres;

--
-- TOC entry 6478 (class 0 OID 0)
-- Dependencies: 238
-- Name: idmc_kawasan_strategis_kasultanan_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.idmc_kawasan_strategis_kasultanan_gid_seq OWNED BY public.idmc_kawasan_strategis_kasultanan.gid;


--
-- TOC entry 239 (class 1259 OID 19650)
-- Name: idmc_pola_ruang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.idmc_pola_ruang (
    gid integer NOT NULL,
    pola_iv character varying(50),
    pola_iii character varying(50),
    pola_ii character varying(50),
    pola_i character varying(50),
    nama_kwsn character varying(50),
    wadmkc character varying(50),
    wadmkk character varying(50),
    wadmpr character varying(50),
    kecamata_1 character varying(50),
    kabupaten character varying(30),
    wilayah character varying(50),
    ket_kra character varying(50),
    sumber_kra character varying(50),
    nama_kbak character varying(50),
    geom public.geometry(MultiPolygonZM,32749),
    id_jenis integer
);


ALTER TABLE public.idmc_pola_ruang OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 19655)
-- Name: idmc_pola_ruang_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.idmc_pola_ruang_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.idmc_pola_ruang_gid_seq OWNER TO postgres;

--
-- TOC entry 6479 (class 0 OID 0)
-- Dependencies: 240
-- Name: idmc_pola_ruang_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.idmc_pola_ruang_gid_seq OWNED BY public.idmc_pola_ruang.gid;


--
-- TOC entry 241 (class 1259 OID 19656)
-- Name: idmc_pola_ruang_jenis; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.idmc_pola_ruang_jenis (
    id integer NOT NULL,
    nama character varying(100),
    warna character varying(7)
);


ALTER TABLE public.idmc_pola_ruang_jenis OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 19659)
-- Name: idmc_struktur_ruang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.idmc_struktur_ruang (
    gid integer NOT NULL,
    objectid numeric(10,0),
    ket_resapa character varying(50),
    smbr_resap character varying(50),
    wa_1 character varying(40),
    ta_1 character varying(15),
    kabupaten character varying(15),
    kewenangan character varying(15),
    nama character varying(50),
    sumber_cat character varying(30),
    nama_cat character varying(30),
    ket_cat character varying(30),
    wadmkc character varying(50),
    wadmkk character varying(50),
    nama_das_1 character varying(50),
    sumber_das character varying(50),
    nama_waduk character varying(30),
    ket_waduk character varying(20),
    shape_leng numeric,
    shape_area numeric,
    geom public.geometry(MultiPolygonZM,32749),
    id_jenis integer
);


ALTER TABLE public.idmc_struktur_ruang OWNER TO postgres;

--
-- TOC entry 243 (class 1259 OID 19664)
-- Name: idmc_struktur_ruang_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.idmc_struktur_ruang_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.idmc_struktur_ruang_gid_seq OWNER TO postgres;

--
-- TOC entry 6480 (class 0 OID 0)
-- Dependencies: 243
-- Name: idmc_struktur_ruang_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.idmc_struktur_ruang_gid_seq OWNED BY public.idmc_struktur_ruang.gid;


--
-- TOC entry 244 (class 1259 OID 19665)
-- Name: idmc_struktur_ruang_jaringan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.idmc_struktur_ruang_jaringan (
    gid integer NOT NULL,
    objectid numeric(10,0),
    nama character varying(50),
    keterangan character varying(50),
    ruas_jalur character varying(50),
    panjang_km numeric,
    kewenangan character varying(50),
    fungsi character varying(50),
    rencana character varying(50),
    air character varying(50),
    jenis character varying(50),
    sumber character varying(50),
    kondisi character varying(50),
    handle character varying(16),
    geom public.geometry(MultiLineStringZM,32749)
);


ALTER TABLE public.idmc_struktur_ruang_jaringan OWNER TO postgres;

--
-- TOC entry 245 (class 1259 OID 19670)
-- Name: idmc_struktur_ruang_jaringan_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.idmc_struktur_ruang_jaringan_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.idmc_struktur_ruang_jaringan_gid_seq OWNER TO postgres;

--
-- TOC entry 6481 (class 0 OID 0)
-- Dependencies: 245
-- Name: idmc_struktur_ruang_jaringan_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.idmc_struktur_ruang_jaringan_gid_seq OWNED BY public.idmc_struktur_ruang_jaringan.gid;


--
-- TOC entry 246 (class 1259 OID 19671)
-- Name: idmc_struktur_ruang_jenis; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.idmc_struktur_ruang_jenis (
    id integer NOT NULL,
    nama character varying(100),
    warna character varying(7)
);


ALTER TABLE public.idmc_struktur_ruang_jenis OWNER TO postgres;

--
-- TOC entry 247 (class 1259 OID 19674)
-- Name: idmc_struktur_ruang_point; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.idmc_struktur_ruang_point (
    gid integer NOT NULL,
    nama character varying(50),
    lokasi character varying(50),
    keterangan character varying(50),
    jenis character varying(50),
    hirarki character varying(50),
    kondisi character varying(50),
    status_1 character varying(50),
    geom public.geometry(PointZM,32749)
);


ALTER TABLE public.idmc_struktur_ruang_point OWNER TO postgres;

--
-- TOC entry 248 (class 1259 OID 19679)
-- Name: idmc_struktur_ruang_point_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.idmc_struktur_ruang_point_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.idmc_struktur_ruang_point_gid_seq OWNER TO postgres;

--
-- TOC entry 6482 (class 0 OID 0)
-- Dependencies: 248
-- Name: idmc_struktur_ruang_point_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.idmc_struktur_ruang_point_gid_seq OWNED BY public.idmc_struktur_ruang_point.gid;


--
-- TOC entry 249 (class 1259 OID 19680)
-- Name: jenis_hak; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jenis_hak (
    id integer NOT NULL,
    kode character varying(16) NOT NULL,
    nama character varying(32) NOT NULL,
    keterangan character varying(512),
    warna character varying(15),
    ontop smallint DEFAULT 0
);


ALTER TABLE public.jenis_hak OWNER TO postgres;

--
-- TOC entry 250 (class 1259 OID 19686)
-- Name: jenis_hak_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jenis_hak_id_seq
    START WITH 15
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jenis_hak_id_seq OWNER TO postgres;

--
-- TOC entry 251 (class 1259 OID 19687)
-- Name: jenis_hak_id_seq1; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jenis_hak_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jenis_hak_id_seq1 OWNER TO postgres;

--
-- TOC entry 6483 (class 0 OID 0)
-- Dependencies: 251
-- Name: jenis_hak_id_seq1; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jenis_hak_id_seq1 OWNED BY public.jenis_hak.id;


--
-- TOC entry 252 (class 1259 OID 19688)
-- Name: jenis_monitoring; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jenis_monitoring (
    id smallint NOT NULL,
    nama character varying(32) NOT NULL
);


ALTER TABLE public.jenis_monitoring OWNER TO postgres;

--
-- TOC entry 253 (class 1259 OID 19691)
-- Name: jenis_pengajuan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jenis_pengajuan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jenis_pengajuan_id_seq OWNER TO postgres;

--
-- TOC entry 254 (class 1259 OID 19692)
-- Name: jenis_pengajuan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jenis_pengajuan (
    id smallint DEFAULT nextval('public.jenis_pengajuan_id_seq'::regclass) NOT NULL,
    nama character varying(50) NOT NULL
);


ALTER TABLE public.jenis_pengajuan OWNER TO postgres;

--
-- TOC entry 255 (class 1259 OID 19696)
-- Name: jenis_permohonan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jenis_permohonan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jenis_permohonan_id_seq OWNER TO postgres;

--
-- TOC entry 256 (class 1259 OID 19697)
-- Name: jenis_permohonan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jenis_permohonan (
    id smallint DEFAULT nextval('public.jenis_permohonan_id_seq'::regclass) NOT NULL,
    nama character varying(50) NOT NULL
);


ALTER TABLE public.jenis_permohonan OWNER TO postgres;

--
-- TOC entry 257 (class 1259 OID 19701)
-- Name: jenis_uupa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jenis_uupa (
    id smallint NOT NULL,
    nama character varying(32) NOT NULL,
    warna character varying(15),
    ontop smallint
);


ALTER TABLE public.jenis_uupa OWNER TO postgres;

--
-- TOC entry 258 (class 1259 OID 19704)
-- Name: kabupaten; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kabupaten (
    id smallint NOT NULL,
    id_provinsi smallint NOT NULL,
    kode character varying(8),
    nama character varying(64) NOT NULL,
    geom public.geometry(MultiPolygon,4326),
    id_kabupaten smallint,
    kode_surat character varying(8) DEFAULT NULL::character varying
);


ALTER TABLE public.kabupaten OWNER TO postgres;

--
-- TOC entry 259 (class 1259 OID 19710)
-- Name: kategori; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kategori (
    id smallint NOT NULL,
    nama character varying(32) NOT NULL,
    warna character varying(12)
);


ALTER TABLE public.kategori OWNER TO postgres;

--
-- TOC entry 260 (class 1259 OID 19713)
-- Name: kategori_rencana_tata_ruang_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kategori_rencana_tata_ruang_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kategori_rencana_tata_ruang_id_seq OWNER TO postgres;

--
-- TOC entry 261 (class 1259 OID 19714)
-- Name: kategori_rencana_tata_ruang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kategori_rencana_tata_ruang (
    id integer DEFAULT nextval('public.kategori_rencana_tata_ruang_id_seq'::regclass) NOT NULL,
    nama character varying(256),
    warna character varying(15),
    ontop smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.kategori_rencana_tata_ruang OWNER TO postgres;

--
-- TOC entry 262 (class 1259 OID 19719)
-- Name: kategori_sarana_prasarana_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kategori_sarana_prasarana_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kategori_sarana_prasarana_id_seq OWNER TO postgres;

--
-- TOC entry 263 (class 1259 OID 19720)
-- Name: kategori_sarana_prasarana; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kategori_sarana_prasarana (
    id integer DEFAULT nextval('public.kategori_sarana_prasarana_id_seq'::regclass) NOT NULL,
    nama character varying(256),
    warna character varying(15),
    ontop smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.kategori_sarana_prasarana OWNER TO postgres;

--
-- TOC entry 264 (class 1259 OID 19725)
-- Name: kategori_tanah_desa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kategori_tanah_desa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kategori_tanah_desa_id_seq OWNER TO postgres;

--
-- TOC entry 265 (class 1259 OID 19726)
-- Name: kategori_tanah_desa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kategori_tanah_desa (
    id smallint DEFAULT nextval('public.kategori_tanah_desa_id_seq'::regclass) NOT NULL,
    nama character varying(32) NOT NULL
);


ALTER TABLE public.kategori_tanah_desa OWNER TO postgres;

--
-- TOC entry 266 (class 1259 OID 19730)
-- Name: kategori_tanah_desa_detail; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kategori_tanah_desa_detail (
    id smallint NOT NULL,
    nama character varying(32) NOT NULL
);


ALTER TABLE public.kategori_tanah_desa_detail OWNER TO postgres;

--
-- TOC entry 267 (class 1259 OID 19733)
-- Name: kecamatan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kecamatan (
    id smallint NOT NULL,
    id_kabupaten smallint NOT NULL,
    kode character varying(8),
    nama character varying(64) NOT NULL,
    geom public.geometry(MultiPolygon,4326),
    id_kecamatan smallint
);


ALTER TABLE public.kecamatan OWNER TO postgres;

--
-- TOC entry 268 (class 1259 OID 19738)
-- Name: kelurahan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kelurahan (
    id smallint NOT NULL,
    id_kecamatan smallint NOT NULL,
    kode character varying(8) NOT NULL,
    nama character varying(64) NOT NULL,
    geom public.geometry(MultiPolygon,4326)
);


ALTER TABLE public.kelurahan OWNER TO postgres;

--
-- TOC entry 269 (class 1259 OID 19743)
-- Name: kelurahan_temp; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kelurahan_temp (
    gid integer NOT NULL,
    id integer,
    id_kecamat integer,
    kode character varying(254),
    nama character varying(254),
    geom public.geometry(MultiPolygon,4326)
);


ALTER TABLE public.kelurahan_temp OWNER TO postgres;

--
-- TOC entry 270 (class 1259 OID 19748)
-- Name: kelurahan_temp_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kelurahan_temp_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kelurahan_temp_gid_seq OWNER TO postgres;

--
-- TOC entry 6484 (class 0 OID 0)
-- Dependencies: 270
-- Name: kelurahan_temp_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kelurahan_temp_gid_seq OWNED BY public.kelurahan_temp.gid;


--
-- TOC entry 271 (class 1259 OID 19749)
-- Name: kepemilikan_tanah_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kepemilikan_tanah_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kepemilikan_tanah_id_seq OWNER TO postgres;

--
-- TOC entry 272 (class 1259 OID 19750)
-- Name: kepemilikan_tanah; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kepemilikan_tanah (
    id smallint DEFAULT nextval('public.kepemilikan_tanah_id_seq'::regclass) NOT NULL,
    nama character varying(50) NOT NULL,
    jenis smallint
);


ALTER TABLE public.kepemilikan_tanah OWNER TO postgres;

--
-- TOC entry 273 (class 1259 OID 19754)
-- Name: kondisi_lahan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kondisi_lahan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kondisi_lahan_id_seq OWNER TO postgres;

--
-- TOC entry 274 (class 1259 OID 19755)
-- Name: kondisi_lahan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kondisi_lahan (
    id smallint DEFAULT nextval('public.kondisi_lahan_id_seq'::regclass) NOT NULL,
    nama character varying(50) NOT NULL
);


ALTER TABLE public.kondisi_lahan OWNER TO postgres;

--
-- TOC entry 275 (class 1259 OID 19759)
-- Name: kontak_id_seq1; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kontak_id_seq1
    START WITH 7
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kontak_id_seq1 OWNER TO postgres;

--
-- TOC entry 276 (class 1259 OID 19760)
-- Name: kontak; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kontak (
    id integer DEFAULT nextval('public.kontak_id_seq1'::regclass) NOT NULL,
    tanggal date,
    nama character varying(128) NOT NULL,
    email character varying(32) NOT NULL,
    subyek character varying(32) NOT NULL,
    pesan character varying(255),
    balasan character varying(255),
    status smallint DEFAULT 0
);


ALTER TABLE public.kontak OWNER TO postgres;

--
-- TOC entry 277 (class 1259 OID 19767)
-- Name: kontak_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kontak_id_seq
    START WITH 7
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kontak_id_seq OWNER TO postgres;

--
-- TOC entry 278 (class 1259 OID 19768)
-- Name: lampiran_jenis_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.lampiran_jenis_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lampiran_jenis_id_seq OWNER TO postgres;

--
-- TOC entry 279 (class 1259 OID 19769)
-- Name: lampiran_jenis; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lampiran_jenis (
    id smallint DEFAULT nextval('public.lampiran_jenis_id_seq'::regclass) NOT NULL,
    id_lampiran_kategori smallint NOT NULL,
    nama character varying(100) NOT NULL,
    hint character varying(100) DEFAULT NULL::character varying
);


ALTER TABLE public.lampiran_jenis OWNER TO postgres;

--
-- TOC entry 280 (class 1259 OID 19774)
-- Name: lampiran_jenis_tanah_desa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.lampiran_jenis_tanah_desa_id_seq
    START WITH 90
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lampiran_jenis_tanah_desa_id_seq OWNER TO postgres;

--
-- TOC entry 281 (class 1259 OID 19775)
-- Name: lampiran_jenis_tanah_desa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lampiran_jenis_tanah_desa (
    id smallint DEFAULT nextval('public.lampiran_jenis_tanah_desa_id_seq'::regclass) NOT NULL,
    id_lampiran_jenis smallint NOT NULL,
    id_tujuan_permohonan smallint NOT NULL
);


ALTER TABLE public.lampiran_jenis_tanah_desa OWNER TO postgres;

--
-- TOC entry 282 (class 1259 OID 19779)
-- Name: lampiran_kategori_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.lampiran_kategori_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lampiran_kategori_id_seq OWNER TO postgres;

--
-- TOC entry 283 (class 1259 OID 19780)
-- Name: lampiran_kategori; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lampiran_kategori (
    id smallint DEFAULT nextval('public.lampiran_kategori_id_seq'::regclass) NOT NULL,
    nama character varying(50) NOT NULL
);


ALTER TABLE public.lampiran_kategori OWNER TO postgres;

--
-- TOC entry 284 (class 1259 OID 19784)
-- Name: lampiran_pengajuan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.lampiran_pengajuan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lampiran_pengajuan_id_seq OWNER TO postgres;

--
-- TOC entry 285 (class 1259 OID 19785)
-- Name: lampiran_pengajuan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lampiran_pengajuan (
    id smallint DEFAULT nextval('public.lampiran_pengajuan_id_seq'::regclass) NOT NULL,
    id_lampiran_jenis smallint NOT NULL,
    id_pengajuan smallint NOT NULL,
    id_file integer
);


ALTER TABLE public.lampiran_pengajuan OWNER TO postgres;

--
-- TOC entry 286 (class 1259 OID 19789)
-- Name: lampiran_pengajuan_tanah_desa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.lampiran_pengajuan_tanah_desa_id_seq
    START WITH 10
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lampiran_pengajuan_tanah_desa_id_seq OWNER TO postgres;

--
-- TOC entry 287 (class 1259 OID 19790)
-- Name: lampiran_pengajuan_tanah_desa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lampiran_pengajuan_tanah_desa (
    id smallint DEFAULT nextval('public.lampiran_pengajuan_tanah_desa_id_seq'::regclass) NOT NULL,
    id_lampiran_jenis smallint NOT NULL,
    id_pengajuan_tanah_desa smallint NOT NULL,
    id_file integer
);


ALTER TABLE public.lampiran_pengajuan_tanah_desa OWNER TO postgres;

--
-- TOC entry 288 (class 1259 OID 19794)
-- Name: masa_berlaku_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.masa_berlaku_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.masa_berlaku_id_seq OWNER TO postgres;

--
-- TOC entry 289 (class 1259 OID 19795)
-- Name: masa_berlaku; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.masa_berlaku (
    id smallint DEFAULT nextval('public.masa_berlaku_id_seq'::regclass) NOT NULL,
    nama character varying(50) NOT NULL
);


ALTER TABLE public.masa_berlaku OWNER TO postgres;

--
-- TOC entry 290 (class 1259 OID 19799)
-- Name: monitoring; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.monitoring (
    id integer NOT NULL,
    id_jenis_monitoring integer,
    hasil text,
    id_file integer,
    id_file_pendukung integer,
    tanggal date,
    id_persil integer
);


ALTER TABLE public.monitoring OWNER TO postgres;

--
-- TOC entry 291 (class 1259 OID 19804)
-- Name: monitoring_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.monitoring_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.monitoring_id_seq OWNER TO postgres;

--
-- TOC entry 6485 (class 0 OID 0)
-- Dependencies: 291
-- Name: monitoring_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.monitoring_id_seq OWNED BY public.monitoring.id;


--
-- TOC entry 292 (class 1259 OID 19805)
-- Name: pengajuan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pengajuan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pengajuan_id_seq OWNER TO postgres;

--
-- TOC entry 293 (class 1259 OID 19806)
-- Name: pengajuan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pengajuan (
    id integer DEFAULT nextval('public.pengajuan_id_seq'::regclass) NOT NULL,
    nomor character varying(20) NOT NULL,
    tgl_masuk date,
    nama character varying(100),
    nama_instansi character varying(100),
    alamat text,
    id_jenis_permohonan smallint,
    id_kepemilikan_tanah smallint,
    lokasi character varying(150),
    id_kelurahan smallint,
    persil character varying(64),
    bidang character varying(20),
    sub_persil character varying(20),
    luas numeric(10,2),
    id_penggunaan integer,
    diwakilkan smallint,
    nama_wakil character varying(20),
    alamat_wakil text,
    tgl_mulai date,
    tgl_selesai date,
    id_status_pengajuan smallint,
    id_jenis_pengajuan smallint,
    no_kekancingan character varying(64),
    keterangan character varying(512)
);


ALTER TABLE public.pengajuan OWNER TO postgres;

--
-- TOC entry 294 (class 1259 OID 19812)
-- Name: pengajuan_tanah_desa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pengajuan_tanah_desa_id_seq
    START WITH 3
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pengajuan_tanah_desa_id_seq OWNER TO postgres;

--
-- TOC entry 295 (class 1259 OID 19813)
-- Name: pengajuan_tanah_desa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pengajuan_tanah_desa (
    id integer DEFAULT nextval('public.pengajuan_tanah_desa_id_seq'::regclass) NOT NULL,
    nomor character varying(20) NOT NULL,
    tgl_masuk date,
    nama character varying(100),
    nama_instansi character varying(100),
    alamat text,
    id_tujuan_permohonan smallint,
    id_jenis_permohonan smallint,
    id_kondisi_lahan smallint,
    id_masa_berlaku smallint,
    id_kepemilikan_tanah smallint,
    lokasi character varying(150),
    id_kelurahan smallint,
    persil character varying(64),
    bidang character varying(20),
    sub_persil character varying(20),
    luas numeric(10,2),
    id_penggunaan integer,
    keterangan text,
    tgl_mulai date,
    tgl_selesai date,
    longitude character varying(255),
    latitude character varying(255),
    diwakilkan smallint,
    nama_wakil character varying(20),
    alamat_wakil text,
    id_jenis_pengajuan smallint,
    id_status_pengajuan smallint
);


ALTER TABLE public.pengajuan_tanah_desa OWNER TO postgres;

--
-- TOC entry 296 (class 1259 OID 19819)
-- Name: pengelola; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pengelola (
    id integer NOT NULL,
    nama character varying(255) NOT NULL,
    keterangan character varying(512),
    kontak character varying(64),
    no_telepon character varying(18),
    email character varying(64),
    alamat character varying(255)
);


ALTER TABLE public.pengelola OWNER TO postgres;

--
-- TOC entry 297 (class 1259 OID 19824)
-- Name: pengelola_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pengelola_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pengelola_id_seq OWNER TO postgres;

--
-- TOC entry 6486 (class 0 OID 0)
-- Dependencies: 297
-- Name: pengelola_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pengelola_id_seq OWNED BY public.pengelola.id;


--
-- TOC entry 298 (class 1259 OID 19825)
-- Name: penggunaan_rtr_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.penggunaan_rtr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.penggunaan_rtr_id_seq OWNER TO postgres;

--
-- TOC entry 299 (class 1259 OID 19826)
-- Name: penggunaan_rtr; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.penggunaan_rtr (
    id integer DEFAULT nextval('public.penggunaan_rtr_id_seq'::regclass) NOT NULL,
    nama character varying(64) NOT NULL,
    nama_file character varying(256),
    warna character varying(15),
    ontop smallint DEFAULT 0
);


ALTER TABLE public.penggunaan_rtr OWNER TO postgres;

--
-- TOC entry 300 (class 1259 OID 19831)
-- Name: penggunaan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.penggunaan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.penggunaan_id_seq OWNER TO postgres;

--
-- TOC entry 6487 (class 0 OID 0)
-- Dependencies: 300
-- Name: penggunaan_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.penggunaan_id_seq OWNED BY public.penggunaan_rtr.id;


--
-- TOC entry 301 (class 1259 OID 19832)
-- Name: penggunaan_sg; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.penggunaan_sg (
    id integer NOT NULL,
    id_penggunaan smallint NOT NULL,
    nama character varying(255) NOT NULL
);


ALTER TABLE public.penggunaan_sg OWNER TO postgres;

--
-- TOC entry 302 (class 1259 OID 19835)
-- Name: penggunaan_sg_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.penggunaan_sg_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.penggunaan_sg_id_seq OWNER TO postgres;

--
-- TOC entry 6488 (class 0 OID 0)
-- Dependencies: 302
-- Name: penggunaan_sg_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.penggunaan_sg_id_seq OWNED BY public.penggunaan_sg.id;


--
-- TOC entry 303 (class 1259 OID 19836)
-- Name: penggunaan_tanah_desa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.penggunaan_tanah_desa_id_seq
    START WITH 5
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.penggunaan_tanah_desa_id_seq OWNER TO postgres;

--
-- TOC entry 304 (class 1259 OID 19837)
-- Name: penggunaan_tanah_desa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.penggunaan_tanah_desa (
    id integer DEFAULT nextval('public.penggunaan_tanah_desa_id_seq'::regclass) NOT NULL,
    nama character varying(64) NOT NULL,
    nama_file character varying(256),
    warna character varying(15),
    ontop smallint
);


ALTER TABLE public.penggunaan_tanah_desa OWNER TO postgres;

--
-- TOC entry 305 (class 1259 OID 19841)
-- Name: persetujuan_kadipaten_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.persetujuan_kadipaten_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.persetujuan_kadipaten_id_seq OWNER TO postgres;

--
-- TOC entry 306 (class 1259 OID 19842)
-- Name: persetujuan_kadipaten; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.persetujuan_kadipaten (
    id smallint DEFAULT nextval('public.persetujuan_kadipaten_id_seq'::regclass) NOT NULL,
    id_pengajuan integer NOT NULL,
    status smallint NOT NULL,
    no_surat character varying(50),
    tgl_mulai date,
    tgl_selesai date,
    keterangan character varying(225),
    id_file integer
);


ALTER TABLE public.persetujuan_kadipaten OWNER TO postgres;

--
-- TOC entry 307 (class 1259 OID 19846)
-- Name: persetujuan_kadipaten_tanah_desa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.persetujuan_kadipaten_tanah_desa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.persetujuan_kadipaten_tanah_desa_id_seq OWNER TO postgres;

--
-- TOC entry 308 (class 1259 OID 19847)
-- Name: persetujuan_kadipaten_tanah_desa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.persetujuan_kadipaten_tanah_desa (
    id smallint DEFAULT nextval('public.persetujuan_kadipaten_tanah_desa_id_seq'::regclass) NOT NULL,
    id_pengajuan_tanah_desa integer NOT NULL,
    status smallint NOT NULL,
    no_surat character varying(50),
    tgl_mulai date,
    tgl_selesai date,
    keterangan character varying(225),
    id_file integer
);


ALTER TABLE public.persetujuan_kadipaten_tanah_desa OWNER TO postgres;

--
-- TOC entry 309 (class 1259 OID 19851)
-- Name: persil; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.persil (
    id integer NOT NULL,
    id_kategori smallint,
    id_kelurahan smallint,
    jalan character varying(128),
    no_persil character varying(32) NOT NULL,
    no_sertifikat character varying(64),
    luas numeric(10,2),
    batas_utara character varying(256),
    batas_selatan character varying(256),
    batas_timur character varying(256),
    batas_barat character varying(256),
    geom public.geometry(MultiPolygon,4326),
    no_surat_ukur character varying(16),
    id_kategori_tanah_desa smallint,
    last_updated character varying(500) DEFAULT NULL::character varying,
    status_verifikasi smallint,
    id_user_verifikasi integer,
    id_kategori_tanah_desa_detail smallint
);


ALTER TABLE public.persil OWNER TO postgres;

--
-- TOC entry 310 (class 1259 OID 19857)
-- Name: persil_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.persil_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.persil_id_seq OWNER TO postgres;

--
-- TOC entry 6489 (class 0 OID 0)
-- Dependencies: 310
-- Name: persil_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.persil_id_seq OWNED BY public.persil.id;


--
-- TOC entry 311 (class 1259 OID 19858)
-- Name: persil_tanah_desa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.persil_tanah_desa_id_seq
    START WITH 9
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.persil_tanah_desa_id_seq OWNER TO postgres;

--
-- TOC entry 312 (class 1259 OID 19859)
-- Name: persil_tanah_desa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.persil_tanah_desa (
    id smallint DEFAULT nextval('public.persil_tanah_desa_id_seq'::regclass) NOT NULL,
    id_pengajuan_tanah_desa integer NOT NULL,
    persil character varying(64),
    bidang character varying(20),
    sub_persil character varying(20)
);


ALTER TABLE public.persil_tanah_desa OWNER TO postgres;

--
-- TOC entry 313 (class 1259 OID 19863)
-- Name: peta_bidang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.peta_bidang (
    gid integer NOT NULL,
    objectid numeric,
    id numeric,
    shape_area numeric,
    no_persil character varying(21),
    penggunaan character varying(49),
    no_bidang character varying(30),
    kecamatan character varying(12),
    kelurahan character varying(12),
    sertifikat character varying(20),
    hak_adat character varying(14),
    luas numeric,
    the_geom public.geometry,
    CONSTRAINT enforce_dims_the_geom CHECK ((public.st_ndims(the_geom) = 2)),
    CONSTRAINT enforce_geotype_the_geom CHECK (((public.geometrytype(the_geom) = 'MULTIPOLYGON'::text) OR (the_geom IS NULL))),
    CONSTRAINT enforce_srid_the_geom CHECK ((public.st_srid(the_geom) = 4326))
);


ALTER TABLE public.peta_bidang OWNER TO postgres;

--
-- TOC entry 314 (class 1259 OID 19871)
-- Name: peta_bidang_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.peta_bidang_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.peta_bidang_gid_seq OWNER TO postgres;

--
-- TOC entry 6490 (class 0 OID 0)
-- Dependencies: 314
-- Name: peta_bidang_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.peta_bidang_gid_seq OWNED BY public.peta_bidang.gid;


--
-- TOC entry 315 (class 1259 OID 19872)
-- Name: peta_persil; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.peta_persil (
    gid integer NOT NULL,
    objectid numeric,
    id numeric,
    shape_area numeric,
    no_persil character varying(21),
    penggunaan character varying(49),
    no_bidang character varying(30),
    kecamatan character varying(12),
    kelurahan character varying(12),
    sertifikat character varying(20),
    hak_adat character varying(15),
    luas numeric,
    the_geom public.geometry,
    CONSTRAINT enforce_dims_the_geom CHECK ((public.st_ndims(the_geom) = 2)),
    CONSTRAINT enforce_geotype_the_geom CHECK (((public.geometrytype(the_geom) = 'MULTIPOLYGON'::text) OR (the_geom IS NULL))),
    CONSTRAINT enforce_srid_the_geom CHECK ((public.st_srid(the_geom) = 4326))
);


ALTER TABLE public.peta_persil OWNER TO postgres;

--
-- TOC entry 316 (class 1259 OID 19880)
-- Name: peta_persil_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.peta_persil_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.peta_persil_gid_seq OWNER TO postgres;

--
-- TOC entry 6491 (class 0 OID 0)
-- Dependencies: 316
-- Name: peta_persil_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.peta_persil_gid_seq OWNED BY public.peta_persil.gid;


--
-- TOC entry 317 (class 1259 OID 19881)
-- Name: polaruang_bantul; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.polaruang_bantul (
    gid integer NOT NULL,
    fid_polaru integer,
    id double precision,
    kawasan character varying(50),
    guna_lahan character varying(50),
    fid_insteb integer,
    provinsi character varying(30),
    kecamatan character varying(18),
    kode_kec integer,
    shape_le_1 numeric,
    shape_area numeric,
    keterangan character varying(50),
    geom public.geometry(MultiPolygon,32749),
    id_kawasan integer,
    nama character varying(32)
);


ALTER TABLE public.polaruang_bantul OWNER TO postgres;

--
-- TOC entry 318 (class 1259 OID 19886)
-- Name: polaruang_bantul_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.polaruang_bantul_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.polaruang_bantul_gid_seq OWNER TO postgres;

--
-- TOC entry 6492 (class 0 OID 0)
-- Dependencies: 318
-- Name: polaruang_bantul_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.polaruang_bantul_gid_seq OWNED BY public.polaruang_bantul.gid;


--
-- TOC entry 319 (class 1259 OID 19887)
-- Name: polaruang_bantul_kawasan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.polaruang_bantul_kawasan (
    id integer NOT NULL,
    warna character(7),
    nama character varying(32)
);


ALTER TABLE public.polaruang_bantul_kawasan OWNER TO postgres;

--
-- TOC entry 320 (class 1259 OID 19890)
-- Name: polaruang_gk; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.polaruang_gk (
    gid integer NOT NULL,
    pola_ruang character varying(254),
    geom public.geometry(MultiPolygon,32749),
    id_kawasan integer
);


ALTER TABLE public.polaruang_gk OWNER TO postgres;

--
-- TOC entry 321 (class 1259 OID 19895)
-- Name: polaruang_gk_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.polaruang_gk_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.polaruang_gk_gid_seq OWNER TO postgres;

--
-- TOC entry 6493 (class 0 OID 0)
-- Dependencies: 321
-- Name: polaruang_gk_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.polaruang_gk_gid_seq OWNED BY public.polaruang_gk.gid;


--
-- TOC entry 322 (class 1259 OID 19896)
-- Name: polaruang_gk_kawasan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.polaruang_gk_kawasan (
    id integer NOT NULL,
    warna character(7),
    nama character varying(32)
);


ALTER TABLE public.polaruang_gk_kawasan OWNER TO postgres;

--
-- TOC entry 323 (class 1259 OID 19899)
-- Name: polaruang_kp; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.polaruang_kp (
    gid integer NOT NULL,
    pola_ruang character varying(254),
    fungsi character varying(200),
    k_budidaya character varying(200),
    k_lindung character varying(200),
    kws_genera character varying(50),
    geom public.geometry(MultiPolygon,32749),
    id_kawasan integer
);


ALTER TABLE public.polaruang_kp OWNER TO postgres;

--
-- TOC entry 324 (class 1259 OID 19904)
-- Name: polaruang_kp_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.polaruang_kp_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.polaruang_kp_gid_seq OWNER TO postgres;

--
-- TOC entry 6494 (class 0 OID 0)
-- Dependencies: 324
-- Name: polaruang_kp_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.polaruang_kp_gid_seq OWNED BY public.polaruang_kp.gid;


--
-- TOC entry 325 (class 1259 OID 19905)
-- Name: polaruang_kp_kawasan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.polaruang_kp_kawasan (
    id integer NOT NULL,
    warna character(7),
    nama character varying(32)
);


ALTER TABLE public.polaruang_kp_kawasan OWNER TO postgres;

--
-- TOC entry 326 (class 1259 OID 19908)
-- Name: polaruang_sleman; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.polaruang_sleman (
    gid integer NOT NULL,
    keterangan character varying(40),
    geom public.geometry(MultiPolygon,32749),
    id_kawasan integer
);


ALTER TABLE public.polaruang_sleman OWNER TO postgres;

--
-- TOC entry 327 (class 1259 OID 19913)
-- Name: polaruang_sleman_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.polaruang_sleman_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.polaruang_sleman_gid_seq OWNER TO postgres;

--
-- TOC entry 328 (class 1259 OID 19914)
-- Name: polaruang_sleman_gid_seq1; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.polaruang_sleman_gid_seq1
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.polaruang_sleman_gid_seq1 OWNER TO postgres;

--
-- TOC entry 6495 (class 0 OID 0)
-- Dependencies: 328
-- Name: polaruang_sleman_gid_seq1; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.polaruang_sleman_gid_seq1 OWNED BY public.polaruang_sleman.gid;


--
-- TOC entry 329 (class 1259 OID 19915)
-- Name: polaruang_sleman_kawasan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.polaruang_sleman_kawasan (
    id integer NOT NULL,
    warna character(7),
    nama character varying(32)
);


ALTER TABLE public.polaruang_sleman_kawasan OWNER TO postgres;

--
-- TOC entry 330 (class 1259 OID 19918)
-- Name: provinsi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.provinsi (
    id smallint NOT NULL,
    kode character varying(8) NOT NULL,
    nama character varying(64) NOT NULL,
    geom public.geometry(MultiPolygon,4326)
);


ALTER TABLE public.provinsi OWNER TO postgres;

--
-- TOC entry 331 (class 1259 OID 19923)
-- Name: rdtr_bantul; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rdtr_bantul (
    gid integer NOT NULL,
    objectid numeric(10,0),
    pola_ruang character varying(50),
    ha numeric,
    desa character varying(30),
    kecamatan character varying(15),
    geom public.geometry(MultiPolygon,32749),
    id_kawasan integer
);


ALTER TABLE public.rdtr_bantul OWNER TO postgres;

--
-- TOC entry 332 (class 1259 OID 19928)
-- Name: rdtr_bantul_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rdtr_bantul_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rdtr_bantul_gid_seq OWNER TO postgres;

--
-- TOC entry 6496 (class 0 OID 0)
-- Dependencies: 332
-- Name: rdtr_bantul_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rdtr_bantul_gid_seq OWNED BY public.rdtr_bantul.gid;


--
-- TOC entry 333 (class 1259 OID 19929)
-- Name: rdtr_bantul_kawasan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rdtr_bantul_kawasan (
    id integer NOT NULL,
    nama character varying(100) NOT NULL,
    warna character(7) NOT NULL
);


ALTER TABLE public.rdtr_bantul_kawasan OWNER TO postgres;

--
-- TOC entry 334 (class 1259 OID 19932)
-- Name: rdtr_diy; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rdtr_diy (
    gid integer NOT NULL,
    keterangan character varying(50),
    peruntukan character varying(100),
    geom public.geometry(MultiPolygonZM,32749)
);


ALTER TABLE public.rdtr_diy OWNER TO postgres;

--
-- TOC entry 335 (class 1259 OID 19937)
-- Name: rdtr_diy_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rdtr_diy_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rdtr_diy_gid_seq OWNER TO postgres;

--
-- TOC entry 6497 (class 0 OID 0)
-- Dependencies: 335
-- Name: rdtr_diy_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rdtr_diy_gid_seq OWNED BY public.rdtr_diy.gid;


--
-- TOC entry 336 (class 1259 OID 19938)
-- Name: rdtr_kota; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rdtr_kota (
    gid integer NOT NULL,
    symbolid numeric(10,0),
    sub_zona character varying(50),
    geom public.geometry(MultiPolygonZM,32749),
    id_kawasan integer
);


ALTER TABLE public.rdtr_kota OWNER TO postgres;

--
-- TOC entry 337 (class 1259 OID 19943)
-- Name: rdtr_kota_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rdtr_kota_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rdtr_kota_gid_seq OWNER TO postgres;

--
-- TOC entry 6498 (class 0 OID 0)
-- Dependencies: 337
-- Name: rdtr_kota_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rdtr_kota_gid_seq OWNED BY public.rdtr_kota.gid;


--
-- TOC entry 338 (class 1259 OID 19944)
-- Name: rdtr_kota_kawasan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rdtr_kota_kawasan (
    id integer NOT NULL,
    nama character varying(100) NOT NULL,
    warna character(7) NOT NULL
);


ALTER TABLE public.rdtr_kota_kawasan OWNER TO postgres;

--
-- TOC entry 339 (class 1259 OID 19947)
-- Name: rdtr_sleman; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rdtr_sleman (
    gid integer NOT NULL,
    kecamatan character varying(50),
    peruntukan character varying(75),
    geom public.geometry(MultiPolygon,32749),
    id_kawasan integer
);


ALTER TABLE public.rdtr_sleman OWNER TO postgres;

--
-- TOC entry 340 (class 1259 OID 19952)
-- Name: rdtr_sleman_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rdtr_sleman_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rdtr_sleman_gid_seq OWNER TO postgres;

--
-- TOC entry 6499 (class 0 OID 0)
-- Dependencies: 340
-- Name: rdtr_sleman_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rdtr_sleman_gid_seq OWNED BY public.rdtr_sleman.gid;


--
-- TOC entry 341 (class 1259 OID 19953)
-- Name: rdtr_sleman_kawasan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rdtr_sleman_kawasan (
    id integer NOT NULL,
    nama character varying(100) NOT NULL,
    warna character(7) NOT NULL
);


ALTER TABLE public.rdtr_sleman_kawasan OWNER TO postgres;

--
-- TOC entry 342 (class 1259 OID 19956)
-- Name: rekomendasi_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rekomendasi_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rekomendasi_id_seq OWNER TO postgres;

--
-- TOC entry 343 (class 1259 OID 19957)
-- Name: rekomendasi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rekomendasi (
    id smallint DEFAULT nextval('public.rekomendasi_id_seq'::regclass) NOT NULL,
    id_pengajuan integer NOT NULL,
    no_surat character varying(50) NOT NULL,
    keterangan character varying(225) NOT NULL,
    id_file integer
);


ALTER TABLE public.rekomendasi OWNER TO postgres;

--
-- TOC entry 344 (class 1259 OID 19961)
-- Name: rekomendasi_tanah_desa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rekomendasi_tanah_desa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rekomendasi_tanah_desa_id_seq OWNER TO postgres;

--
-- TOC entry 345 (class 1259 OID 19962)
-- Name: rekomendasi_tanah_desa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rekomendasi_tanah_desa (
    id smallint DEFAULT nextval('public.rekomendasi_tanah_desa_id_seq'::regclass) NOT NULL,
    id_pengajuan_tanah_desa integer NOT NULL,
    no_surat character varying(50) NOT NULL,
    keterangan character varying(225) NOT NULL,
    id_file integer
);


ALTER TABLE public.rekomendasi_tanah_desa OWNER TO postgres;

--
-- TOC entry 346 (class 1259 OID 19966)
-- Name: rencana_tata_ruang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rencana_tata_ruang (
    gid integer NOT NULL,
    ____gid integer,
    fid_pola_r integer,
    fid_batas_ integer,
    id integer,
    fid_edit_1 integer,
    kws character varying(254),
    simbol character varying(254),
    luas numeric,
    fid_kecama integer,
    id_1 integer,
    kecamatan character varying(254),
    id_kategori_rencana_tata_ruang integer,
    id_kabupaten integer,
    geom public.geometry(MultiPolygon,4326)
);


ALTER TABLE public.rencana_tata_ruang OWNER TO postgres;

--
-- TOC entry 347 (class 1259 OID 19971)
-- Name: rencana_tata_ruang2_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rencana_tata_ruang2_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rencana_tata_ruang2_gid_seq OWNER TO postgres;

--
-- TOC entry 6500 (class 0 OID 0)
-- Dependencies: 347
-- Name: rencana_tata_ruang2_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rencana_tata_ruang2_gid_seq OWNED BY public.rencana_tata_ruang.gid;


--
-- TOC entry 348 (class 1259 OID 19972)
-- Name: rencana_tata_ruang_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rencana_tata_ruang_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rencana_tata_ruang_gid_seq OWNER TO postgres;

--
-- TOC entry 349 (class 1259 OID 19973)
-- Name: rtrw_diy; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rtrw_diy (
    gid integer NOT NULL,
    pola_iv character varying(50),
    pola_iii character varying(50),
    pola_ii character varying(50),
    pola_i character varying(50),
    luas_ha double precision,
    nama_kwsn character varying(50),
    geom public.geometry(MultiPolygonZM,32749),
    id_kawasan integer
);


ALTER TABLE public.rtrw_diy OWNER TO postgres;

--
-- TOC entry 350 (class 1259 OID 19978)
-- Name: rtrw_diy_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rtrw_diy_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rtrw_diy_gid_seq OWNER TO postgres;

--
-- TOC entry 6501 (class 0 OID 0)
-- Dependencies: 350
-- Name: rtrw_diy_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rtrw_diy_gid_seq OWNED BY public.rtrw_diy.gid;


--
-- TOC entry 351 (class 1259 OID 19979)
-- Name: rtrw_diy_kawasan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rtrw_diy_kawasan (
    id integer NOT NULL,
    warna character(7),
    nama character varying(32)
);


ALTER TABLE public.rtrw_diy_kawasan OWNER TO postgres;

--
-- TOC entry 352 (class 1259 OID 19982)
-- Name: sarana_prasarana; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sarana_prasarana (
    gid integer NOT NULL,
    nama_fasum character varying(254),
    jenis character varying(254),
    kategori character varying(254),
    nama_foto character varying(254),
    link_foto character varying(254),
    keterangan character varying(254),
    id character varying(254),
    id_kategori_sarana_prasarana integer,
    id_kabupaten integer,
    geom public.geometry(Point,4326)
);


ALTER TABLE public.sarana_prasarana OWNER TO postgres;

--
-- TOC entry 353 (class 1259 OID 19987)
-- Name: sarana_prasarana_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sarana_prasarana_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sarana_prasarana_gid_seq OWNER TO postgres;

--
-- TOC entry 6502 (class 0 OID 0)
-- Dependencies: 353
-- Name: sarana_prasarana_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sarana_prasarana_gid_seq OWNED BY public.sarana_prasarana.gid;


--
-- TOC entry 354 (class 1259 OID 19988)
-- Name: sk_gubernur_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sk_gubernur_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sk_gubernur_id_seq OWNER TO postgres;

--
-- TOC entry 355 (class 1259 OID 19989)
-- Name: sk_gubernur; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sk_gubernur (
    id smallint DEFAULT nextval('public.sk_gubernur_id_seq'::regclass) NOT NULL,
    id_pengajuan integer NOT NULL,
    no_sk character varying(50),
    id_file integer
);


ALTER TABLE public.sk_gubernur OWNER TO postgres;

--
-- TOC entry 356 (class 1259 OID 19993)
-- Name: sk_gubernur_tanah_desa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sk_gubernur_tanah_desa_id_seq
    START WITH 2
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sk_gubernur_tanah_desa_id_seq OWNER TO postgres;

--
-- TOC entry 357 (class 1259 OID 19994)
-- Name: sk_gubernur_tanah_desa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sk_gubernur_tanah_desa (
    id smallint DEFAULT nextval('public.sk_gubernur_tanah_desa_id_seq'::regclass) NOT NULL,
    id_pengajuan_tanah_desa integer NOT NULL,
    no_sk character varying(50),
    id_file integer
);


ALTER TABLE public.sk_gubernur_tanah_desa OWNER TO postgres;

--
-- TOC entry 358 (class 1259 OID 19998)
-- Name: status_kesesuaian; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.status_kesesuaian (
    id integer NOT NULL,
    nama character varying(256),
    warna character varying(15),
    ontop smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.status_kesesuaian OWNER TO postgres;

--
-- TOC entry 359 (class 1259 OID 20002)
-- Name: status_kesesuaian_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.status_kesesuaian_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.status_kesesuaian_id_seq OWNER TO postgres;

--
-- TOC entry 6503 (class 0 OID 0)
-- Dependencies: 359
-- Name: status_kesesuaian_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.status_kesesuaian_id_seq OWNED BY public.status_kesesuaian.id;


--
-- TOC entry 360 (class 1259 OID 20003)
-- Name: status_pengajuan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.status_pengajuan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.status_pengajuan_id_seq OWNER TO postgres;

--
-- TOC entry 361 (class 1259 OID 20004)
-- Name: status_pengajuan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.status_pengajuan (
    id smallint DEFAULT nextval('public.status_pengajuan_id_seq'::regclass) NOT NULL,
    nama character varying(100) NOT NULL,
    warna character varying(10)
);


ALTER TABLE public.status_pengajuan OWNER TO postgres;

--
-- TOC entry 362 (class 1259 OID 20008)
-- Name: status_sertifikat; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.status_sertifikat (
    id integer NOT NULL,
    nama character varying(256),
    warna character varying(15),
    ontop smallint DEFAULT 0
);


ALTER TABLE public.status_sertifikat OWNER TO postgres;

--
-- TOC entry 363 (class 1259 OID 20012)
-- Name: status_sertifikat_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.status_sertifikat_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.status_sertifikat_id_seq OWNER TO postgres;

--
-- TOC entry 6504 (class 0 OID 0)
-- Dependencies: 363
-- Name: status_sertifikat_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.status_sertifikat_id_seq OWNED BY public.status_sertifikat.id;


--
-- TOC entry 364 (class 1259 OID 20013)
-- Name: sub_persil_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sub_persil_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sub_persil_id_seq OWNER TO postgres;

--
-- TOC entry 365 (class 1259 OID 20014)
-- Name: sub_persil; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sub_persil (
    id integer DEFAULT nextval('public.sub_persil_id_seq'::regclass) NOT NULL,
    id_bidang integer NOT NULL,
    no_sub_persil character varying(64),
    no_serat_kekancingan character varying(64),
    tgl_mulai date,
    tgl_selesai date,
    luas numeric(10,2),
    id_penggunaan smallint,
    id_pengelola smallint,
    keterangan character varying(512),
    id_file integer,
    last_updated character varying(500) DEFAULT NULL::character varying
);


ALTER TABLE public.sub_persil OWNER TO postgres;

--
-- TOC entry 366 (class 1259 OID 20021)
-- Name: tanggal_notifikasi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tanggal_notifikasi (
    id integer NOT NULL,
    tanggal date,
    keterangan character varying(25)
);


ALTER TABLE public.tanggal_notifikasi OWNER TO postgres;

--
-- TOC entry 367 (class 1259 OID 20024)
-- Name: tanggal_notifikasi_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tanggal_notifikasi_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tanggal_notifikasi_id_seq OWNER TO postgres;

--
-- TOC entry 6505 (class 0 OID 0)
-- Dependencies: 367
-- Name: tanggal_notifikasi_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tanggal_notifikasi_id_seq OWNED BY public.tanggal_notifikasi.id;


--
-- TOC entry 368 (class 1259 OID 20025)
-- Name: temp_bidang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.temp_bidang (
    id character varying(255),
    no_persil character varying(255),
    id_jenis_hak character varying(255),
    id_jenis_uupa character varying(255),
    no_surat_uupa character varying(255),
    no_bidang character varying(255),
    id_pengelola character varying(255),
    no_kekancingan character varying(255),
    luas character varying(255),
    id_penggunaan character varying(255),
    tgl_mulai character varying(255),
    tgl_selesai character varying(255),
    keterangan character varying(255),
    id_status_kesesuaian character varying(255),
    no_sertifikat character varying(255),
    id_file character varying(255),
    id_status_sertifikat character varying(255)
);


ALTER TABLE public.temp_bidang OWNER TO postgres;

--
-- TOC entry 369 (class 1259 OID 20030)
-- Name: tujuan_permohonan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tujuan_permohonan_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tujuan_permohonan_id_seq OWNER TO postgres;

--
-- TOC entry 370 (class 1259 OID 20031)
-- Name: tujuan_permohonan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tujuan_permohonan (
    id smallint DEFAULT nextval('public.tujuan_permohonan_id_seq'::regclass) NOT NULL,
    nama character varying(50) NOT NULL
);


ALTER TABLE public.tujuan_permohonan OWNER TO postgres;

--
-- TOC entry 371 (class 1259 OID 20035)
-- Name: user_grup; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.user_grup (
    id smallint NOT NULL,
    nama character varying(20)
);


ALTER TABLE public.user_grup OWNER TO postgres;

--
-- TOC entry 372 (class 1259 OID 20038)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    username character varying(50) NOT NULL,
    password_hash character varying(255) NOT NULL,
    nama character varying(100) NOT NULL,
    id_grup smallint NOT NULL,
    password_reset_token character varying(255),
    auth_key character varying(32),
    created_at integer,
    updated_at integer,
    id_kabupaten smallint,
    email character varying(255)
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 373 (class 1259 OID 20043)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 6506 (class 0 OID 0)
-- Dependencies: 373
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 374 (class 1259 OID 20044)
-- Name: v_persil; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_persil AS
 SELECT a.id,
    a.id_kategori,
    b.nama AS katgori_nama,
    a.id_kelurahan,
    e.nama AS kelurahan,
    e.id_kecamatan,
    f.nama AS kecamatan,
    f.id_kabupaten,
    g.nama AS kabupaten,
    a.jalan,
    a.no_persil,
    a.no_sertifikat,
    a.luas,
    a.batas_utara,
    a.batas_selatan,
    a.batas_timur,
    a.batas_barat,
    a.no_surat_ukur,
    a.id_kategori_tanah_desa,
    c.nama AS kategori_tanah_desa_nama,
    a.last_updated,
    a.status_verifikasi,
    a.id_user_verifikasi,
    d.username,
    a.id_kategori_tanah_desa_detail,
    a.geom
   FROM ((((((public.persil a
     LEFT JOIN public.kategori b ON ((a.id_kategori = b.id)))
     LEFT JOIN public.kategori_tanah_desa c ON ((a.id_kategori_tanah_desa = c.id)))
     LEFT JOIN public.users d ON ((a.id_user_verifikasi = d.id)))
     LEFT JOIN public.kelurahan e ON ((a.id_kelurahan = e.id)))
     JOIN public.kecamatan f ON ((e.id_kecamatan = f.id)))
     JOIN public.kabupaten g ON ((f.id_kabupaten = g.id)));


ALTER VIEW public.v_persil OWNER TO postgres;

--
-- TOC entry 375 (class 1259 OID 20049)
-- Name: v_bidang; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.v_bidang AS
 SELECT a.id,
    a.id_jenis_hak,
    b.nama AS jenis_hak_nama,
    a.id_jenis_uupa,
    c.nama AS jenis_uupa_nama,
    a.no_surat_uupa,
    a.no_bidang,
    a.id_pengelola,
    e.nama AS pengelola_nama,
    a.no_kekancingan,
    a.luas,
    a.id_penggunaan,
    f.nama AS penggunaan_nama,
    a.tgl_mulai,
    a.tgl_selesai,
    a.keterangan,
    a.id_status_kesesuaian,
    i.nama AS status_kesesuaian_nama,
    a.no_sertifikat,
    a.id_file,
    a.id_status_sertifikat,
    g.nama AS status_sertifikat_nama,
    a.id_persil,
    h.no_persil,
    h.id_kelurahan,
    h.kelurahan,
    h.id_kecamatan,
    h.kecamatan,
    h.id_kabupaten,
    h.kabupaten,
    a.id_kesesuaian_rdtr,
    d.nama AS kesesuaian_rdtr_nama,
    a.id_peta,
    a.id_sg_pag_lama,
    a.last_updated,
    a.geom
   FROM ((((((((public.bidang a
     LEFT JOIN public.jenis_hak b ON ((a.id_jenis_hak = b.id)))
     LEFT JOIN public.jenis_uupa c ON ((a.id_jenis_uupa = c.id)))
     LEFT JOIN public.status_kesesuaian d ON ((a.id_kesesuaian_rdtr = d.id)))
     LEFT JOIN public.pengelola e ON ((a.id_pengelola = e.id)))
     LEFT JOIN public.penggunaan_rtr f ON ((a.id_penggunaan = f.id)))
     LEFT JOIN public.status_sertifikat g ON ((a.id_status_sertifikat = g.id)))
     LEFT JOIN public.v_persil h ON ((a.id_persil = h.id)))
     LEFT JOIN public.status_kesesuaian i ON ((a.id_status_kesesuaian = i.id)));


ALTER VIEW public.v_bidang OWNER TO postgres;

--
-- TOC entry 376 (class 1259 OID 20054)
-- Name: verifikasi_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.verifikasi_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.verifikasi_id_seq OWNER TO postgres;

--
-- TOC entry 377 (class 1259 OID 20055)
-- Name: verifikasi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.verifikasi (
    id smallint DEFAULT nextval('public.verifikasi_id_seq'::regclass) NOT NULL,
    id_pengajuan integer NOT NULL,
    id_user integer,
    status smallint NOT NULL,
    alasan character varying(225) NOT NULL
);


ALTER TABLE public.verifikasi OWNER TO postgres;

--
-- TOC entry 378 (class 1259 OID 20059)
-- Name: verifikasi_tanah_desa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.verifikasi_tanah_desa_id_seq
    START WITH 2
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.verifikasi_tanah_desa_id_seq OWNER TO postgres;

--
-- TOC entry 379 (class 1259 OID 20060)
-- Name: verifikasi_tanah_desa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.verifikasi_tanah_desa (
    id smallint DEFAULT nextval('public.verifikasi_tanah_desa_id_seq'::regclass) NOT NULL,
    id_pengajuan_tanah_desa integer NOT NULL,
    id_user integer,
    status smallint NOT NULL,
    alasan character varying(225) NOT NULL
);


ALTER TABLE public.verifikasi_tanah_desa OWNER TO postgres;

--
-- TOC entry 5969 (class 2604 OID 20064)
-- Name: bidang id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bidang ALTER COLUMN id SET DEFAULT nextval('public.bidang_id_seq'::regclass);


--
-- TOC entry 5972 (class 2604 OID 20065)
-- Name: file id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.file ALTER COLUMN id SET DEFAULT nextval('public.file_id_seq'::regclass);


--
-- TOC entry 5973 (class 2604 OID 20066)
-- Name: galeri_bidang id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri_bidang ALTER COLUMN id SET DEFAULT nextval('public.galeri_bidang_id_seq'::regclass);


--
-- TOC entry 5975 (class 2604 OID 20067)
-- Name: idmc_kawasan_strategis gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_kawasan_strategis ALTER COLUMN gid SET DEFAULT nextval('public.idmc_kawasan_strategis_gid_seq'::regclass);


--
-- TOC entry 5976 (class 2604 OID 20068)
-- Name: idmc_kawasan_strategis_kasultanan gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_kawasan_strategis_kasultanan ALTER COLUMN gid SET DEFAULT nextval('public.idmc_kawasan_strategis_kasultanan_gid_seq'::regclass);


--
-- TOC entry 5977 (class 2604 OID 20069)
-- Name: idmc_pola_ruang gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_pola_ruang ALTER COLUMN gid SET DEFAULT nextval('public.idmc_pola_ruang_gid_seq'::regclass);


--
-- TOC entry 5978 (class 2604 OID 20070)
-- Name: idmc_struktur_ruang gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_struktur_ruang ALTER COLUMN gid SET DEFAULT nextval('public.idmc_struktur_ruang_gid_seq'::regclass);


--
-- TOC entry 5979 (class 2604 OID 20071)
-- Name: idmc_struktur_ruang_jaringan gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_struktur_ruang_jaringan ALTER COLUMN gid SET DEFAULT nextval('public.idmc_struktur_ruang_jaringan_gid_seq'::regclass);


--
-- TOC entry 5980 (class 2604 OID 20072)
-- Name: idmc_struktur_ruang_point gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_struktur_ruang_point ALTER COLUMN gid SET DEFAULT nextval('public.idmc_struktur_ruang_point_gid_seq'::regclass);


--
-- TOC entry 5981 (class 2604 OID 20073)
-- Name: jenis_hak id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis_hak ALTER COLUMN id SET DEFAULT nextval('public.jenis_hak_id_seq1'::regclass);


--
-- TOC entry 5991 (class 2604 OID 20074)
-- Name: kelurahan_temp gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kelurahan_temp ALTER COLUMN gid SET DEFAULT nextval('public.kelurahan_temp_gid_seq'::regclass);


--
-- TOC entry 6003 (class 2604 OID 20075)
-- Name: monitoring id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.monitoring ALTER COLUMN id SET DEFAULT nextval('public.monitoring_id_seq'::regclass);


--
-- TOC entry 6006 (class 2604 OID 20076)
-- Name: pengelola id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengelola ALTER COLUMN id SET DEFAULT nextval('public.pengelola_id_seq'::regclass);


--
-- TOC entry 6009 (class 2604 OID 20077)
-- Name: penggunaan_sg id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penggunaan_sg ALTER COLUMN id SET DEFAULT nextval('public.penggunaan_sg_id_seq'::regclass);


--
-- TOC entry 6013 (class 2604 OID 20078)
-- Name: persil id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persil ALTER COLUMN id SET DEFAULT nextval('public.persil_id_seq'::regclass);


--
-- TOC entry 6016 (class 2604 OID 20079)
-- Name: peta_bidang gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.peta_bidang ALTER COLUMN gid SET DEFAULT nextval('public.peta_bidang_gid_seq'::regclass);


--
-- TOC entry 5971 (class 2604 OID 20080)
-- Name: peta_kecamatan gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.peta_kecamatan ALTER COLUMN gid SET DEFAULT nextval('public.bmap_kec_gid_seq'::regclass);


--
-- TOC entry 6017 (class 2604 OID 20081)
-- Name: peta_persil gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.peta_persil ALTER COLUMN gid SET DEFAULT nextval('public.peta_persil_gid_seq'::regclass);


--
-- TOC entry 6018 (class 2604 OID 20082)
-- Name: polaruang_bantul gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.polaruang_bantul ALTER COLUMN gid SET DEFAULT nextval('public.polaruang_bantul_gid_seq'::regclass);


--
-- TOC entry 6019 (class 2604 OID 20083)
-- Name: polaruang_gk gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.polaruang_gk ALTER COLUMN gid SET DEFAULT nextval('public.polaruang_gk_gid_seq'::regclass);


--
-- TOC entry 6020 (class 2604 OID 20084)
-- Name: polaruang_kp gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.polaruang_kp ALTER COLUMN gid SET DEFAULT nextval('public.polaruang_kp_gid_seq'::regclass);


--
-- TOC entry 6021 (class 2604 OID 20085)
-- Name: polaruang_sleman gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.polaruang_sleman ALTER COLUMN gid SET DEFAULT nextval('public.polaruang_sleman_gid_seq1'::regclass);


--
-- TOC entry 6022 (class 2604 OID 20086)
-- Name: rdtr_bantul gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rdtr_bantul ALTER COLUMN gid SET DEFAULT nextval('public.rdtr_bantul_gid_seq'::regclass);


--
-- TOC entry 6023 (class 2604 OID 20087)
-- Name: rdtr_diy gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rdtr_diy ALTER COLUMN gid SET DEFAULT nextval('public.rdtr_diy_gid_seq'::regclass);


--
-- TOC entry 6024 (class 2604 OID 20088)
-- Name: rdtr_kota gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rdtr_kota ALTER COLUMN gid SET DEFAULT nextval('public.rdtr_kota_gid_seq'::regclass);


--
-- TOC entry 6025 (class 2604 OID 20089)
-- Name: rdtr_sleman gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rdtr_sleman ALTER COLUMN gid SET DEFAULT nextval('public.rdtr_sleman_gid_seq'::regclass);


--
-- TOC entry 6028 (class 2604 OID 20090)
-- Name: rencana_tata_ruang gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rencana_tata_ruang ALTER COLUMN gid SET DEFAULT nextval('public.rencana_tata_ruang2_gid_seq'::regclass);


--
-- TOC entry 6029 (class 2604 OID 20091)
-- Name: rtrw_diy gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rtrw_diy ALTER COLUMN gid SET DEFAULT nextval('public.rtrw_diy_gid_seq'::regclass);


--
-- TOC entry 6030 (class 2604 OID 20092)
-- Name: sarana_prasarana gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sarana_prasarana ALTER COLUMN gid SET DEFAULT nextval('public.sarana_prasarana_gid_seq'::regclass);


--
-- TOC entry 6033 (class 2604 OID 20093)
-- Name: status_kesesuaian id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.status_kesesuaian ALTER COLUMN id SET DEFAULT nextval('public.status_kesesuaian_id_seq'::regclass);


--
-- TOC entry 6036 (class 2604 OID 20094)
-- Name: status_sertifikat id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.status_sertifikat ALTER COLUMN id SET DEFAULT nextval('public.status_sertifikat_id_seq'::regclass);


--
-- TOC entry 6040 (class 2604 OID 20095)
-- Name: tanggal_notifikasi id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tanggal_notifikasi ALTER COLUMN id SET DEFAULT nextval('public.tanggal_notifikasi_id_seq'::regclass);


--
-- TOC entry 6042 (class 2604 OID 20096)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 6055 (class 2606 OID 41679)
-- Name: bidang bidang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bidang
    ADD CONSTRAINT bidang_pkey PRIMARY KEY (id);


--
-- TOC entry 6058 (class 2606 OID 41681)
-- Name: peta_kecamatan bmap_kec_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.peta_kecamatan
    ADD CONSTRAINT bmap_kec_pkey PRIMARY KEY (gid);


--
-- TOC entry 6060 (class 2606 OID 41683)
-- Name: config config_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.config
    ADD CONSTRAINT config_pkey PRIMARY KEY (id);


--
-- TOC entry 6062 (class 2606 OID 41685)
-- Name: file file_id_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.file
    ADD CONSTRAINT file_id_pkey PRIMARY KEY (id);


--
-- TOC entry 6066 (class 2606 OID 41687)
-- Name: galeri_bidang galeri_bidang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri_bidang
    ADD CONSTRAINT galeri_bidang_pkey PRIMARY KEY (id);


--
-- TOC entry 6068 (class 2606 OID 41689)
-- Name: galeri_sub_persil galeri_sub_persil_id_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri_sub_persil
    ADD CONSTRAINT galeri_sub_persil_id_pkey PRIMARY KEY (id);


--
-- TOC entry 6072 (class 2606 OID 41691)
-- Name: idmc_kawasan_strategis_jenis idmc_kawasan_strategis_jenis_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_kawasan_strategis_jenis
    ADD CONSTRAINT idmc_kawasan_strategis_jenis_pkey PRIMARY KEY (id);


--
-- TOC entry 6074 (class 2606 OID 41693)
-- Name: idmc_kawasan_strategis_kasultanan idmc_kawasan_strategis_kasultanan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_kawasan_strategis_kasultanan
    ADD CONSTRAINT idmc_kawasan_strategis_kasultanan_pkey PRIMARY KEY (gid);


--
-- TOC entry 6070 (class 2606 OID 41695)
-- Name: idmc_kawasan_strategis idmc_kawasan_strategis_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_kawasan_strategis
    ADD CONSTRAINT idmc_kawasan_strategis_pkey PRIMARY KEY (gid);


--
-- TOC entry 6078 (class 2606 OID 41697)
-- Name: idmc_pola_ruang_jenis idmc_pola_ruang_jenis_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_pola_ruang_jenis
    ADD CONSTRAINT idmc_pola_ruang_jenis_pkey PRIMARY KEY (id);


--
-- TOC entry 6076 (class 2606 OID 41699)
-- Name: idmc_pola_ruang idmc_pola_ruang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_pola_ruang
    ADD CONSTRAINT idmc_pola_ruang_pkey PRIMARY KEY (gid);


--
-- TOC entry 6082 (class 2606 OID 41701)
-- Name: idmc_struktur_ruang_jaringan idmc_struktur_ruang_jaringan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_struktur_ruang_jaringan
    ADD CONSTRAINT idmc_struktur_ruang_jaringan_pkey PRIMARY KEY (gid);


--
-- TOC entry 6084 (class 2606 OID 41703)
-- Name: idmc_struktur_ruang_jenis idmc_struktur_ruang_jenis_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_struktur_ruang_jenis
    ADD CONSTRAINT idmc_struktur_ruang_jenis_pkey PRIMARY KEY (id);


--
-- TOC entry 6080 (class 2606 OID 41705)
-- Name: idmc_struktur_ruang idmc_struktur_ruang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_struktur_ruang
    ADD CONSTRAINT idmc_struktur_ruang_pkey PRIMARY KEY (gid);


--
-- TOC entry 6086 (class 2606 OID 41707)
-- Name: idmc_struktur_ruang_point idmc_struktur_ruang_point_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.idmc_struktur_ruang_point
    ADD CONSTRAINT idmc_struktur_ruang_point_pkey PRIMARY KEY (gid);


--
-- TOC entry 6088 (class 2606 OID 41709)
-- Name: jenis_hak jenis_hak_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis_hak
    ADD CONSTRAINT jenis_hak_pkey PRIMARY KEY (id);


--
-- TOC entry 6090 (class 2606 OID 41711)
-- Name: jenis_monitoring jenis_monitoring_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis_monitoring
    ADD CONSTRAINT jenis_monitoring_pkey PRIMARY KEY (id);


--
-- TOC entry 6092 (class 2606 OID 41713)
-- Name: jenis_pengajuan jenis_pengajuan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis_pengajuan
    ADD CONSTRAINT jenis_pengajuan_pkey PRIMARY KEY (id);


--
-- TOC entry 6094 (class 2606 OID 41715)
-- Name: jenis_permohonan jenis_permohonan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis_permohonan
    ADD CONSTRAINT jenis_permohonan_pkey PRIMARY KEY (id);


--
-- TOC entry 6096 (class 2606 OID 41717)
-- Name: jenis_uupa jenis_uupa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis_uupa
    ADD CONSTRAINT jenis_uupa_pkey PRIMARY KEY (id);


--
-- TOC entry 6098 (class 2606 OID 41719)
-- Name: kabupaten kabupaten_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kabupaten
    ADD CONSTRAINT kabupaten_pkey PRIMARY KEY (id);


--
-- TOC entry 6100 (class 2606 OID 41721)
-- Name: kategori kategori_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori
    ADD CONSTRAINT kategori_pkey PRIMARY KEY (id);


--
-- TOC entry 6102 (class 2606 OID 41723)
-- Name: kategori_rencana_tata_ruang kategori_rencana_tata_ruang_id_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_rencana_tata_ruang
    ADD CONSTRAINT kategori_rencana_tata_ruang_id_pkey PRIMARY KEY (id);


--
-- TOC entry 6104 (class 2606 OID 41725)
-- Name: kategori_sarana_prasarana kategori_sarana_prasarana_id_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_sarana_prasarana
    ADD CONSTRAINT kategori_sarana_prasarana_id_pkey PRIMARY KEY (id);


--
-- TOC entry 6108 (class 2606 OID 41727)
-- Name: kategori_tanah_desa_detail kategori_tanah_desa_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_tanah_desa_detail
    ADD CONSTRAINT kategori_tanah_desa_detail_pkey PRIMARY KEY (id);


--
-- TOC entry 6106 (class 2606 OID 41729)
-- Name: kategori_tanah_desa kategori_tanah_desa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kategori_tanah_desa
    ADD CONSTRAINT kategori_tanah_desa_pkey PRIMARY KEY (id);


--
-- TOC entry 6110 (class 2606 OID 41731)
-- Name: kecamatan kecamatan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kecamatan
    ADD CONSTRAINT kecamatan_pkey PRIMARY KEY (id);


--
-- TOC entry 6112 (class 2606 OID 41733)
-- Name: kelurahan kelurahan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kelurahan
    ADD CONSTRAINT kelurahan_pkey PRIMARY KEY (id);


--
-- TOC entry 6115 (class 2606 OID 41735)
-- Name: kelurahan_temp kelurahan_temp_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kelurahan_temp
    ADD CONSTRAINT kelurahan_temp_pkey PRIMARY KEY (gid);


--
-- TOC entry 6117 (class 2606 OID 41737)
-- Name: kepemilikan_tanah kepemilikan_tanah_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kepemilikan_tanah
    ADD CONSTRAINT kepemilikan_tanah_pkey PRIMARY KEY (id);


--
-- TOC entry 6119 (class 2606 OID 41739)
-- Name: kondisi_lahan kondisi_lahan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kondisi_lahan
    ADD CONSTRAINT kondisi_lahan_pkey PRIMARY KEY (id);


--
-- TOC entry 6121 (class 2606 OID 41741)
-- Name: kontak kontak_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kontak
    ADD CONSTRAINT kontak_pkey PRIMARY KEY (id);


--
-- TOC entry 6125 (class 2606 OID 41743)
-- Name: lampiran_jenis_tanah_desa lampiran_jenis_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_jenis_tanah_desa
    ADD CONSTRAINT lampiran_jenis_copy1_pkey PRIMARY KEY (id);


--
-- TOC entry 6123 (class 2606 OID 41745)
-- Name: lampiran_jenis lampiran_jenis_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_jenis
    ADD CONSTRAINT lampiran_jenis_pkey PRIMARY KEY (id);


--
-- TOC entry 6127 (class 2606 OID 41747)
-- Name: lampiran_kategori lampiran_kategori_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_kategori
    ADD CONSTRAINT lampiran_kategori_pkey PRIMARY KEY (id);


--
-- TOC entry 6131 (class 2606 OID 41749)
-- Name: lampiran_pengajuan_tanah_desa lampiran_pengajuan_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_pengajuan_tanah_desa
    ADD CONSTRAINT lampiran_pengajuan_copy1_pkey PRIMARY KEY (id);


--
-- TOC entry 6129 (class 2606 OID 41751)
-- Name: lampiran_pengajuan lampiran_pengajuan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_pengajuan
    ADD CONSTRAINT lampiran_pengajuan_pkey PRIMARY KEY (id);


--
-- TOC entry 6133 (class 2606 OID 41753)
-- Name: masa_berlaku masa_berlaku_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.masa_berlaku
    ADD CONSTRAINT masa_berlaku_pkey PRIMARY KEY (id);


--
-- TOC entry 6138 (class 2606 OID 41755)
-- Name: monitoring monitoring_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.monitoring
    ADD CONSTRAINT monitoring_pkey PRIMARY KEY (id);


--
-- TOC entry 6140 (class 2606 OID 41757)
-- Name: pengajuan pengajuan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan
    ADD CONSTRAINT pengajuan_pkey PRIMARY KEY (id);


--
-- TOC entry 6142 (class 2606 OID 41759)
-- Name: pengajuan_tanah_desa pengajuan_tanah_desa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan_tanah_desa
    ADD CONSTRAINT pengajuan_tanah_desa_pkey PRIMARY KEY (id);


--
-- TOC entry 6144 (class 2606 OID 41761)
-- Name: pengelola pengelola_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengelola
    ADD CONSTRAINT pengelola_pkey PRIMARY KEY (id);


--
-- TOC entry 6146 (class 2606 OID 41763)
-- Name: penggunaan_rtr penggunaan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penggunaan_rtr
    ADD CONSTRAINT penggunaan_pkey PRIMARY KEY (id);


--
-- TOC entry 6148 (class 2606 OID 41765)
-- Name: penggunaan_sg penggunaan_sg_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penggunaan_sg
    ADD CONSTRAINT penggunaan_sg_pkey PRIMARY KEY (id);


--
-- TOC entry 6150 (class 2606 OID 41767)
-- Name: penggunaan_tanah_desa penggunaan_tanah_desa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penggunaan_tanah_desa
    ADD CONSTRAINT penggunaan_tanah_desa_pkey PRIMARY KEY (id);


--
-- TOC entry 6154 (class 2606 OID 41769)
-- Name: persetujuan_kadipaten_tanah_desa persetujuan_kadipaten_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persetujuan_kadipaten_tanah_desa
    ADD CONSTRAINT persetujuan_kadipaten_copy1_pkey PRIMARY KEY (id);


--
-- TOC entry 6152 (class 2606 OID 41771)
-- Name: persetujuan_kadipaten persetujuan_kadipaten_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persetujuan_kadipaten
    ADD CONSTRAINT persetujuan_kadipaten_pkey PRIMARY KEY (id);


--
-- TOC entry 6158 (class 2606 OID 41773)
-- Name: persil persil_no_persil_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persil
    ADD CONSTRAINT persil_no_persil_key UNIQUE (no_persil);


--
-- TOC entry 6160 (class 2606 OID 41775)
-- Name: persil persil_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persil
    ADD CONSTRAINT persil_pkey PRIMARY KEY (id);


--
-- TOC entry 6162 (class 2606 OID 41777)
-- Name: persil_tanah_desa persil_tanah_desa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persil_tanah_desa
    ADD CONSTRAINT persil_tanah_desa_pkey PRIMARY KEY (id);


--
-- TOC entry 6164 (class 2606 OID 41779)
-- Name: peta_bidang peta_bidang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.peta_bidang
    ADD CONSTRAINT peta_bidang_pkey PRIMARY KEY (gid);


--
-- TOC entry 6166 (class 2606 OID 41781)
-- Name: peta_persil peta_persil_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.peta_persil
    ADD CONSTRAINT peta_persil_pkey PRIMARY KEY (gid);


--
-- TOC entry 6182 (class 2606 OID 41783)
-- Name: polaruang_sleman_kawasan polaruang_bantul_kawasan_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.polaruang_sleman_kawasan
    ADD CONSTRAINT polaruang_bantul_kawasan_copy1_pkey PRIMARY KEY (id);


--
-- TOC entry 6174 (class 2606 OID 41785)
-- Name: polaruang_gk_kawasan polaruang_bantul_kawasan_copy_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.polaruang_gk_kawasan
    ADD CONSTRAINT polaruang_bantul_kawasan_copy_pkey PRIMARY KEY (id);


--
-- TOC entry 6170 (class 2606 OID 41787)
-- Name: polaruang_bantul_kawasan polaruang_bantul_kawasan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.polaruang_bantul_kawasan
    ADD CONSTRAINT polaruang_bantul_kawasan_pkey PRIMARY KEY (id);


--
-- TOC entry 6168 (class 2606 OID 41789)
-- Name: polaruang_bantul polaruang_bantul_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.polaruang_bantul
    ADD CONSTRAINT polaruang_bantul_pkey PRIMARY KEY (gid);


--
-- TOC entry 6178 (class 2606 OID 41791)
-- Name: polaruang_kp_kawasan polaruang_gk_kawasan_copy_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.polaruang_kp_kawasan
    ADD CONSTRAINT polaruang_gk_kawasan_copy_pkey PRIMARY KEY (id);


--
-- TOC entry 6172 (class 2606 OID 41793)
-- Name: polaruang_gk polaruang_gk_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.polaruang_gk
    ADD CONSTRAINT polaruang_gk_pkey PRIMARY KEY (gid);


--
-- TOC entry 6209 (class 2606 OID 41795)
-- Name: rtrw_diy_kawasan polaruang_kp_kawasan_copy_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rtrw_diy_kawasan
    ADD CONSTRAINT polaruang_kp_kawasan_copy_pkey PRIMARY KEY (id);


--
-- TOC entry 6176 (class 2606 OID 41797)
-- Name: polaruang_kp polaruang_kp_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.polaruang_kp
    ADD CONSTRAINT polaruang_kp_pkey PRIMARY KEY (gid);


--
-- TOC entry 6180 (class 2606 OID 41799)
-- Name: polaruang_sleman polaruang_sleman_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.polaruang_sleman
    ADD CONSTRAINT polaruang_sleman_pkey PRIMARY KEY (gid);


--
-- TOC entry 6184 (class 2606 OID 41801)
-- Name: provinsi provinsi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.provinsi
    ADD CONSTRAINT provinsi_pkey PRIMARY KEY (id);


--
-- TOC entry 6188 (class 2606 OID 41803)
-- Name: rdtr_bantul_kawasan rdtr_bantul_kawasan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rdtr_bantul_kawasan
    ADD CONSTRAINT rdtr_bantul_kawasan_pkey PRIMARY KEY (id);


--
-- TOC entry 6186 (class 2606 OID 41805)
-- Name: rdtr_bantul rdtr_bantul_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rdtr_bantul
    ADD CONSTRAINT rdtr_bantul_pkey PRIMARY KEY (gid);


--
-- TOC entry 6190 (class 2606 OID 41807)
-- Name: rdtr_diy rdtr_diy_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rdtr_diy
    ADD CONSTRAINT rdtr_diy_pkey PRIMARY KEY (gid);


--
-- TOC entry 6194 (class 2606 OID 41809)
-- Name: rdtr_kota_kawasan rdtr_kota_kawasan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rdtr_kota_kawasan
    ADD CONSTRAINT rdtr_kota_kawasan_pkey PRIMARY KEY (id);


--
-- TOC entry 6192 (class 2606 OID 41811)
-- Name: rdtr_kota rdtr_kota_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rdtr_kota
    ADD CONSTRAINT rdtr_kota_pkey PRIMARY KEY (gid);


--
-- TOC entry 6198 (class 2606 OID 41813)
-- Name: rdtr_sleman_kawasan rdtr_sleman_kawasan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rdtr_sleman_kawasan
    ADD CONSTRAINT rdtr_sleman_kawasan_pkey PRIMARY KEY (id);


--
-- TOC entry 6196 (class 2606 OID 41815)
-- Name: rdtr_sleman rdtr_sleman_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rdtr_sleman
    ADD CONSTRAINT rdtr_sleman_pkey PRIMARY KEY (gid);


--
-- TOC entry 6202 (class 2606 OID 41817)
-- Name: rekomendasi_tanah_desa rekomendasi_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rekomendasi_tanah_desa
    ADD CONSTRAINT rekomendasi_copy1_pkey PRIMARY KEY (id);


--
-- TOC entry 6200 (class 2606 OID 41819)
-- Name: rekomendasi rekomendasi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rekomendasi
    ADD CONSTRAINT rekomendasi_pkey PRIMARY KEY (id);


--
-- TOC entry 6205 (class 2606 OID 41821)
-- Name: rencana_tata_ruang rencana_tata_ruang2_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rencana_tata_ruang
    ADD CONSTRAINT rencana_tata_ruang2_pkey PRIMARY KEY (gid);


--
-- TOC entry 6207 (class 2606 OID 41823)
-- Name: rtrw_diy rtrw_diy_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rtrw_diy
    ADD CONSTRAINT rtrw_diy_pkey PRIMARY KEY (gid);


--
-- TOC entry 6212 (class 2606 OID 41825)
-- Name: sarana_prasarana sarana_prasarana_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sarana_prasarana
    ADD CONSTRAINT sarana_prasarana_pkey PRIMARY KEY (gid);


--
-- TOC entry 6216 (class 2606 OID 41827)
-- Name: sk_gubernur_tanah_desa sk_gubernur_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sk_gubernur_tanah_desa
    ADD CONSTRAINT sk_gubernur_copy1_pkey PRIMARY KEY (id);


--
-- TOC entry 6214 (class 2606 OID 41829)
-- Name: sk_gubernur sk_gubernur_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sk_gubernur
    ADD CONSTRAINT sk_gubernur_pkey PRIMARY KEY (id);


--
-- TOC entry 6218 (class 2606 OID 41831)
-- Name: status_kesesuaian status_kesesuaian_id_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.status_kesesuaian
    ADD CONSTRAINT status_kesesuaian_id_pkey PRIMARY KEY (id);


--
-- TOC entry 6220 (class 2606 OID 41833)
-- Name: status_pengajuan status_pengajuan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.status_pengajuan
    ADD CONSTRAINT status_pengajuan_pkey PRIMARY KEY (id);


--
-- TOC entry 6222 (class 2606 OID 41835)
-- Name: status_sertifikat status_sertifikat_id_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.status_sertifikat
    ADD CONSTRAINT status_sertifikat_id_pkey PRIMARY KEY (id);


--
-- TOC entry 6224 (class 2606 OID 41837)
-- Name: sub_persil sub_persil_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sub_persil
    ADD CONSTRAINT sub_persil_pkey PRIMARY KEY (id);


--
-- TOC entry 6226 (class 2606 OID 41839)
-- Name: tanggal_notifikasi tanggal_notifikasi_id_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tanggal_notifikasi
    ADD CONSTRAINT tanggal_notifikasi_id_pkey PRIMARY KEY (id);


--
-- TOC entry 6228 (class 2606 OID 41841)
-- Name: tujuan_permohonan tujuan_permohonan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tujuan_permohonan
    ADD CONSTRAINT tujuan_permohonan_pkey PRIMARY KEY (id);


--
-- TOC entry 6230 (class 2606 OID 41843)
-- Name: user_grup user_grup_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_grup
    ADD CONSTRAINT user_grup_pkey PRIMARY KEY (id);


--
-- TOC entry 6232 (class 2606 OID 41845)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 6234 (class 2606 OID 41847)
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- TOC entry 6238 (class 2606 OID 41849)
-- Name: verifikasi_tanah_desa verifikasi_copy1_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.verifikasi_tanah_desa
    ADD CONSTRAINT verifikasi_copy1_pkey PRIMARY KEY (id);


--
-- TOC entry 6236 (class 2606 OID 41851)
-- Name: verifikasi verifikasi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.verifikasi
    ADD CONSTRAINT verifikasi_pkey PRIMARY KEY (id);


--
-- TOC entry 6056 (class 1259 OID 41852)
-- Name: fki_bidang_id_file_fkey; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_bidang_id_file_fkey ON public.bidang USING btree (id_file);


--
-- TOC entry 6063 (class 1259 OID 41853)
-- Name: fki_galeri_bidang_id_bidang; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_galeri_bidang_id_bidang ON public.galeri_bidang USING btree (id_bidang);


--
-- TOC entry 6064 (class 1259 OID 41854)
-- Name: fki_galeri_bidang_id_file; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_galeri_bidang_id_file ON public.galeri_bidang USING btree (id_file);


--
-- TOC entry 6134 (class 1259 OID 41855)
-- Name: fki_moitoring_id_file_pendukung; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_moitoring_id_file_pendukung ON public.monitoring USING btree (id_file_pendukung);


--
-- TOC entry 6135 (class 1259 OID 41856)
-- Name: fki_moitoring_id_persil; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_moitoring_id_persil ON public.monitoring USING btree (id_persil);


--
-- TOC entry 6136 (class 1259 OID 41857)
-- Name: fki_montoring_id_file; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_montoring_id_file ON public.monitoring USING btree (id_file);


--
-- TOC entry 6155 (class 1259 OID 41858)
-- Name: fki_persil_id_kategori_fkey; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_persil_id_kategori_fkey ON public.persil USING btree (id_kategori);


--
-- TOC entry 6156 (class 1259 OID 41859)
-- Name: fki_persil_id_kelurahan_fkey; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_persil_id_kelurahan_fkey ON public.persil USING btree (id_kelurahan);


--
-- TOC entry 6113 (class 1259 OID 41860)
-- Name: kelurahan_temp_geom_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX kelurahan_temp_geom_idx ON public.kelurahan_temp USING gist (geom);


--
-- TOC entry 6203 (class 1259 OID 41861)
-- Name: rencana_tata_ruang2_geom_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX rencana_tata_ruang2_geom_idx ON public.rencana_tata_ruang USING gist (geom);


--
-- TOC entry 6210 (class 1259 OID 41862)
-- Name: sarana_prasarana_geom_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sarana_prasarana_geom_idx ON public.sarana_prasarana USING gist (geom);


--
-- TOC entry 6239 (class 2606 OID 41863)
-- Name: bidang bidang_id_file_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bidang
    ADD CONSTRAINT bidang_id_file_fkey FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6240 (class 2606 OID 41868)
-- Name: bidang bidang_id_jenis_hak_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bidang
    ADD CONSTRAINT bidang_id_jenis_hak_fkey FOREIGN KEY (id_jenis_hak) REFERENCES public.jenis_hak(id) ON UPDATE CASCADE;


--
-- TOC entry 6241 (class 2606 OID 41873)
-- Name: bidang bidang_id_jenis_uupa_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bidang
    ADD CONSTRAINT bidang_id_jenis_uupa_fkey FOREIGN KEY (id_jenis_uupa) REFERENCES public.jenis_uupa(id) ON UPDATE CASCADE;


--
-- TOC entry 6242 (class 2606 OID 41878)
-- Name: bidang bidang_id_kesesuaian_rdtr; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bidang
    ADD CONSTRAINT bidang_id_kesesuaian_rdtr FOREIGN KEY (id_kesesuaian_rdtr) REFERENCES public.status_kesesuaian(id) ON UPDATE CASCADE;


--
-- TOC entry 6243 (class 2606 OID 41883)
-- Name: bidang bidang_id_pengelola_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bidang
    ADD CONSTRAINT bidang_id_pengelola_fkey FOREIGN KEY (id_pengelola) REFERENCES public.pengelola(id) ON UPDATE CASCADE;


--
-- TOC entry 6244 (class 2606 OID 41888)
-- Name: bidang bidang_id_penggunaan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bidang
    ADD CONSTRAINT bidang_id_penggunaan_fkey FOREIGN KEY (id_penggunaan) REFERENCES public.penggunaan_rtr(id) ON UPDATE CASCADE;


--
-- TOC entry 6245 (class 2606 OID 41893)
-- Name: bidang bidang_id_persil; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bidang
    ADD CONSTRAINT bidang_id_persil FOREIGN KEY (id_persil) REFERENCES public.persil(id) ON UPDATE CASCADE;


--
-- TOC entry 6246 (class 2606 OID 41898)
-- Name: bidang bidang_id_peta_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bidang
    ADD CONSTRAINT bidang_id_peta_fkey FOREIGN KEY (id_peta) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6247 (class 2606 OID 41903)
-- Name: bidang bidang_id_status_kesesuaian; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bidang
    ADD CONSTRAINT bidang_id_status_kesesuaian FOREIGN KEY (id_status_kesesuaian) REFERENCES public.status_kesesuaian(id) ON UPDATE CASCADE;


--
-- TOC entry 6248 (class 2606 OID 41908)
-- Name: bidang bidang_id_status_sertifikat; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bidang
    ADD CONSTRAINT bidang_id_status_sertifikat FOREIGN KEY (id_status_sertifikat) REFERENCES public.status_sertifikat(id) ON UPDATE CASCADE;


--
-- TOC entry 6249 (class 2606 OID 41913)
-- Name: galeri_bidang galeri_bidang_id_bidang; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri_bidang
    ADD CONSTRAINT galeri_bidang_id_bidang FOREIGN KEY (id_bidang) REFERENCES public.bidang(id);


--
-- TOC entry 6250 (class 2606 OID 41918)
-- Name: galeri_bidang galeri_bidang_id_file; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri_bidang
    ADD CONSTRAINT galeri_bidang_id_file FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6251 (class 2606 OID 41923)
-- Name: galeri_sub_persil id_file; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri_sub_persil
    ADD CONSTRAINT id_file FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6252 (class 2606 OID 41928)
-- Name: galeri_sub_persil id_sub_persil; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galeri_sub_persil
    ADD CONSTRAINT id_sub_persil FOREIGN KEY (id_sub_persil) REFERENCES public.sub_persil(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6253 (class 2606 OID 41933)
-- Name: kabupaten kabupaten_id_provinsi_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kabupaten
    ADD CONSTRAINT kabupaten_id_provinsi_fkey FOREIGN KEY (id_provinsi) REFERENCES public.provinsi(id);


--
-- TOC entry 6254 (class 2606 OID 41938)
-- Name: kecamatan kecamatan_id_kabupaten_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kecamatan
    ADD CONSTRAINT kecamatan_id_kabupaten_fkey FOREIGN KEY (id_kabupaten) REFERENCES public.kabupaten(id);


--
-- TOC entry 6255 (class 2606 OID 41943)
-- Name: kelurahan kelurahan_id_kecamatan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kelurahan
    ADD CONSTRAINT kelurahan_id_kecamatan_fkey FOREIGN KEY (id_kecamatan) REFERENCES public.kecamatan(id);


--
-- TOC entry 6256 (class 2606 OID 41948)
-- Name: lampiran_jenis lampiran_jenis_id_lampiran_kategori_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_jenis
    ADD CONSTRAINT lampiran_jenis_id_lampiran_kategori_fkey FOREIGN KEY (id_lampiran_kategori) REFERENCES public.lampiran_kategori(id) ON UPDATE CASCADE;


--
-- TOC entry 6257 (class 2606 OID 41953)
-- Name: lampiran_jenis_tanah_desa lampiran_jenis_tanah_desa_id_lampiran_jenis_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_jenis_tanah_desa
    ADD CONSTRAINT lampiran_jenis_tanah_desa_id_lampiran_jenis_fkey FOREIGN KEY (id_lampiran_jenis) REFERENCES public.lampiran_jenis(id) ON UPDATE CASCADE;


--
-- TOC entry 6258 (class 2606 OID 41958)
-- Name: lampiran_jenis_tanah_desa lampiran_jenis_tanah_desa_id_tujuan_permohonan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_jenis_tanah_desa
    ADD CONSTRAINT lampiran_jenis_tanah_desa_id_tujuan_permohonan_fkey FOREIGN KEY (id_tujuan_permohonan) REFERENCES public.tujuan_permohonan(id) ON UPDATE CASCADE;


--
-- TOC entry 6259 (class 2606 OID 41963)
-- Name: lampiran_pengajuan lampiran_pengajuan_id_file_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_pengajuan
    ADD CONSTRAINT lampiran_pengajuan_id_file_fkey FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6260 (class 2606 OID 41968)
-- Name: lampiran_pengajuan lampiran_pengajuan_id_lampiran_jenis_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_pengajuan
    ADD CONSTRAINT lampiran_pengajuan_id_lampiran_jenis_fkey FOREIGN KEY (id_lampiran_jenis) REFERENCES public.lampiran_jenis(id) ON UPDATE CASCADE;


--
-- TOC entry 6261 (class 2606 OID 41973)
-- Name: lampiran_pengajuan lampiran_pengajuan_id_pengajuan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_pengajuan
    ADD CONSTRAINT lampiran_pengajuan_id_pengajuan_fkey FOREIGN KEY (id_pengajuan) REFERENCES public.pengajuan(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6262 (class 2606 OID 41978)
-- Name: lampiran_pengajuan_tanah_desa lampiran_pengajuan_tanah_desa_id_file_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_pengajuan_tanah_desa
    ADD CONSTRAINT lampiran_pengajuan_tanah_desa_id_file_fkey FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6263 (class 2606 OID 41983)
-- Name: lampiran_pengajuan_tanah_desa lampiran_pengajuan_tanah_desa_id_lampiran_jenis_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_pengajuan_tanah_desa
    ADD CONSTRAINT lampiran_pengajuan_tanah_desa_id_lampiran_jenis_fkey FOREIGN KEY (id_lampiran_jenis) REFERENCES public.lampiran_jenis(id) ON UPDATE CASCADE;


--
-- TOC entry 6264 (class 2606 OID 41988)
-- Name: lampiran_pengajuan_tanah_desa lampiran_pengajuan_tanah_desa_id_pengajuan_tanah_desa_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lampiran_pengajuan_tanah_desa
    ADD CONSTRAINT lampiran_pengajuan_tanah_desa_id_pengajuan_tanah_desa_fkey FOREIGN KEY (id_pengajuan_tanah_desa) REFERENCES public.pengajuan_tanah_desa(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6265 (class 2606 OID 41993)
-- Name: monitoring monitoring_id_file; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.monitoring
    ADD CONSTRAINT monitoring_id_file FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6266 (class 2606 OID 41998)
-- Name: monitoring monitoring_id_file_pendukung; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.monitoring
    ADD CONSTRAINT monitoring_id_file_pendukung FOREIGN KEY (id_file_pendukung) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6267 (class 2606 OID 42003)
-- Name: monitoring monitoring_id_persil; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.monitoring
    ADD CONSTRAINT monitoring_id_persil FOREIGN KEY (id_persil) REFERENCES public.persil(id);


--
-- TOC entry 6268 (class 2606 OID 42008)
-- Name: pengajuan pengajuan_id_jenis_pengajuan; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan
    ADD CONSTRAINT pengajuan_id_jenis_pengajuan FOREIGN KEY (id_jenis_pengajuan) REFERENCES public.jenis_pengajuan(id) ON UPDATE CASCADE;


--
-- TOC entry 6269 (class 2606 OID 42013)
-- Name: pengajuan pengajuan_id_jenis_permohonan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan
    ADD CONSTRAINT pengajuan_id_jenis_permohonan_fkey FOREIGN KEY (id_jenis_permohonan) REFERENCES public.jenis_permohonan(id) ON UPDATE CASCADE;


--
-- TOC entry 6270 (class 2606 OID 42018)
-- Name: pengajuan pengajuan_id_kelurahan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan
    ADD CONSTRAINT pengajuan_id_kelurahan_fkey FOREIGN KEY (id_kelurahan) REFERENCES public.kelurahan(id) ON UPDATE CASCADE;


--
-- TOC entry 6271 (class 2606 OID 42023)
-- Name: pengajuan pengajuan_id_kepemilikan_tanah_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan
    ADD CONSTRAINT pengajuan_id_kepemilikan_tanah_fkey FOREIGN KEY (id_kepemilikan_tanah) REFERENCES public.kepemilikan_tanah(id) ON UPDATE CASCADE;


--
-- TOC entry 6272 (class 2606 OID 42028)
-- Name: pengajuan pengajuan_id_penggunaan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan
    ADD CONSTRAINT pengajuan_id_penggunaan_fkey FOREIGN KEY (id_penggunaan) REFERENCES public.penggunaan_rtr(id) ON UPDATE CASCADE;


--
-- TOC entry 6273 (class 2606 OID 42033)
-- Name: pengajuan pengajuan_id_status_pengajuan; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan
    ADD CONSTRAINT pengajuan_id_status_pengajuan FOREIGN KEY (id_status_pengajuan) REFERENCES public.status_pengajuan(id) ON UPDATE CASCADE;


--
-- TOC entry 6274 (class 2606 OID 42038)
-- Name: pengajuan_tanah_desa pengajuan_tanah_desa_fk_id_penggunaan; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan_tanah_desa
    ADD CONSTRAINT pengajuan_tanah_desa_fk_id_penggunaan FOREIGN KEY (id_penggunaan) REFERENCES public.penggunaan_tanah_desa(id) ON UPDATE CASCADE NOT VALID;


--
-- TOC entry 6275 (class 2606 OID 42043)
-- Name: pengajuan_tanah_desa pengajuan_tanah_desa_id_jenis_pengajuan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan_tanah_desa
    ADD CONSTRAINT pengajuan_tanah_desa_id_jenis_pengajuan_fkey FOREIGN KEY (id_jenis_pengajuan) REFERENCES public.jenis_pengajuan(id) ON UPDATE CASCADE;


--
-- TOC entry 6276 (class 2606 OID 42048)
-- Name: pengajuan_tanah_desa pengajuan_tanah_desa_id_jenis_permohonan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan_tanah_desa
    ADD CONSTRAINT pengajuan_tanah_desa_id_jenis_permohonan_fkey FOREIGN KEY (id_jenis_permohonan) REFERENCES public.jenis_permohonan(id) ON UPDATE CASCADE;


--
-- TOC entry 6277 (class 2606 OID 42053)
-- Name: pengajuan_tanah_desa pengajuan_tanah_desa_id_kelurahan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan_tanah_desa
    ADD CONSTRAINT pengajuan_tanah_desa_id_kelurahan_fkey FOREIGN KEY (id_kelurahan) REFERENCES public.kelurahan(id) ON UPDATE CASCADE;


--
-- TOC entry 6278 (class 2606 OID 42058)
-- Name: pengajuan_tanah_desa pengajuan_tanah_desa_id_kepemilikan_tanah_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan_tanah_desa
    ADD CONSTRAINT pengajuan_tanah_desa_id_kepemilikan_tanah_fkey FOREIGN KEY (id_kepemilikan_tanah) REFERENCES public.kepemilikan_tanah(id) ON UPDATE CASCADE;


--
-- TOC entry 6279 (class 2606 OID 42063)
-- Name: pengajuan_tanah_desa pengajuan_tanah_desa_id_kondisi_lahan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan_tanah_desa
    ADD CONSTRAINT pengajuan_tanah_desa_id_kondisi_lahan_fkey FOREIGN KEY (id_kondisi_lahan) REFERENCES public.kondisi_lahan(id);


--
-- TOC entry 6280 (class 2606 OID 42068)
-- Name: pengajuan_tanah_desa pengajuan_tanah_desa_id_masa_berlaku_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan_tanah_desa
    ADD CONSTRAINT pengajuan_tanah_desa_id_masa_berlaku_fkey FOREIGN KEY (id_masa_berlaku) REFERENCES public.masa_berlaku(id) ON UPDATE CASCADE;


--
-- TOC entry 6281 (class 2606 OID 42073)
-- Name: pengajuan_tanah_desa pengajuan_tanah_desa_id_status_pengajuan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan_tanah_desa
    ADD CONSTRAINT pengajuan_tanah_desa_id_status_pengajuan_fkey FOREIGN KEY (id_status_pengajuan) REFERENCES public.status_pengajuan(id) ON UPDATE CASCADE;


--
-- TOC entry 6282 (class 2606 OID 42078)
-- Name: pengajuan_tanah_desa pengajuan_tanah_desa_id_tujuan_permohonan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengajuan_tanah_desa
    ADD CONSTRAINT pengajuan_tanah_desa_id_tujuan_permohonan_fkey FOREIGN KEY (id_tujuan_permohonan) REFERENCES public.tujuan_permohonan(id) ON UPDATE CASCADE;


--
-- TOC entry 6283 (class 2606 OID 42083)
-- Name: penggunaan_sg penggunaan_sg_id_penggunaan; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.penggunaan_sg
    ADD CONSTRAINT penggunaan_sg_id_penggunaan FOREIGN KEY (id_penggunaan) REFERENCES public.penggunaan_rtr(id);


--
-- TOC entry 6286 (class 2606 OID 42088)
-- Name: persetujuan_kadipaten_tanah_desa persetujuan_kadipaten_copy1_id_file_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persetujuan_kadipaten_tanah_desa
    ADD CONSTRAINT persetujuan_kadipaten_copy1_id_file_fkey FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6287 (class 2606 OID 42093)
-- Name: persetujuan_kadipaten_tanah_desa persetujuan_kadipaten_copy1_id_pengajuan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persetujuan_kadipaten_tanah_desa
    ADD CONSTRAINT persetujuan_kadipaten_copy1_id_pengajuan_fkey FOREIGN KEY (id_pengajuan_tanah_desa) REFERENCES public.pengajuan_tanah_desa(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6284 (class 2606 OID 42098)
-- Name: persetujuan_kadipaten persetujuan_kadipaten_id_file_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persetujuan_kadipaten
    ADD CONSTRAINT persetujuan_kadipaten_id_file_fkey FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6285 (class 2606 OID 42103)
-- Name: persetujuan_kadipaten persetujuan_kadipaten_id_pengajuan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persetujuan_kadipaten
    ADD CONSTRAINT persetujuan_kadipaten_id_pengajuan_fkey FOREIGN KEY (id_pengajuan) REFERENCES public.pengajuan(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6288 (class 2606 OID 42108)
-- Name: persil persil_id_kategori_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persil
    ADD CONSTRAINT persil_id_kategori_fkey FOREIGN KEY (id_kategori) REFERENCES public.kategori(id);


--
-- TOC entry 6289 (class 2606 OID 42113)
-- Name: persil persil_id_kategori_tanah_desa; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persil
    ADD CONSTRAINT persil_id_kategori_tanah_desa FOREIGN KEY (id_kategori_tanah_desa) REFERENCES public.kategori_tanah_desa(id) ON UPDATE CASCADE;


--
-- TOC entry 6290 (class 2606 OID 42118)
-- Name: persil persil_id_kategori_tanah_desa_detail; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persil
    ADD CONSTRAINT persil_id_kategori_tanah_desa_detail FOREIGN KEY (id_kategori_tanah_desa_detail) REFERENCES public.kategori_tanah_desa_detail(id) ON UPDATE CASCADE;


--
-- TOC entry 6291 (class 2606 OID 42123)
-- Name: persil persil_id_kelurahan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persil
    ADD CONSTRAINT persil_id_kelurahan_fkey FOREIGN KEY (id_kelurahan) REFERENCES public.kelurahan(id);


--
-- TOC entry 6292 (class 2606 OID 42128)
-- Name: persil persil_id_user_verifikasi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persil
    ADD CONSTRAINT persil_id_user_verifikasi FOREIGN KEY (id_user_verifikasi) REFERENCES public.users(id) ON UPDATE CASCADE;


--
-- TOC entry 6293 (class 2606 OID 42133)
-- Name: persil_tanah_desa persil_tanah_desa_id_pengajuan_tanah_desa_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persil_tanah_desa
    ADD CONSTRAINT persil_tanah_desa_id_pengajuan_tanah_desa_fkey FOREIGN KEY (id_pengajuan_tanah_desa) REFERENCES public.pengajuan_tanah_desa(id) NOT VALID;


--
-- TOC entry 6296 (class 2606 OID 42138)
-- Name: rekomendasi_tanah_desa rekomendasi_copy1_id_file_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rekomendasi_tanah_desa
    ADD CONSTRAINT rekomendasi_copy1_id_file_fkey FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6297 (class 2606 OID 42143)
-- Name: rekomendasi_tanah_desa rekomendasi_copy1_id_pengajuan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rekomendasi_tanah_desa
    ADD CONSTRAINT rekomendasi_copy1_id_pengajuan_fkey FOREIGN KEY (id_pengajuan_tanah_desa) REFERENCES public.pengajuan_tanah_desa(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6294 (class 2606 OID 42148)
-- Name: rekomendasi rekomendasi_id_file_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rekomendasi
    ADD CONSTRAINT rekomendasi_id_file_fkey FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6295 (class 2606 OID 42153)
-- Name: rekomendasi rekomendasi_id_pengajuan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rekomendasi
    ADD CONSTRAINT rekomendasi_id_pengajuan_fkey FOREIGN KEY (id_pengajuan) REFERENCES public.pengajuan(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6298 (class 2606 OID 42158)
-- Name: rencana_tata_ruang rencana_tata_ruang2_id_kabupaten_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rencana_tata_ruang
    ADD CONSTRAINT rencana_tata_ruang2_id_kabupaten_fkey FOREIGN KEY (id_kabupaten) REFERENCES public.kabupaten(id) ON UPDATE CASCADE;


--
-- TOC entry 6299 (class 2606 OID 42163)
-- Name: rencana_tata_ruang rencana_tata_ruang2_id_kategori_rencana_tata_ruang_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rencana_tata_ruang
    ADD CONSTRAINT rencana_tata_ruang2_id_kategori_rencana_tata_ruang_fkey FOREIGN KEY (id_kategori_rencana_tata_ruang) REFERENCES public.kategori_rencana_tata_ruang(id) ON UPDATE CASCADE;


--
-- TOC entry 6302 (class 2606 OID 42168)
-- Name: sk_gubernur_tanah_desa sk_gubernur_copy1_id_file_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sk_gubernur_tanah_desa
    ADD CONSTRAINT sk_gubernur_copy1_id_file_fkey FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6303 (class 2606 OID 42173)
-- Name: sk_gubernur_tanah_desa sk_gubernur_copy1_id_pengajuan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sk_gubernur_tanah_desa
    ADD CONSTRAINT sk_gubernur_copy1_id_pengajuan_fkey FOREIGN KEY (id_pengajuan_tanah_desa) REFERENCES public.pengajuan_tanah_desa(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6300 (class 2606 OID 42178)
-- Name: sk_gubernur sk_gubernur_id_file_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sk_gubernur
    ADD CONSTRAINT sk_gubernur_id_file_fkey FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6301 (class 2606 OID 42183)
-- Name: sk_gubernur sk_gubernur_id_pengajuan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sk_gubernur
    ADD CONSTRAINT sk_gubernur_id_pengajuan_fkey FOREIGN KEY (id_pengajuan) REFERENCES public.pengajuan(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6304 (class 2606 OID 42188)
-- Name: sub_persil sub_persil_id_bidang_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sub_persil
    ADD CONSTRAINT sub_persil_id_bidang_fkey FOREIGN KEY (id_bidang) REFERENCES public.bidang(id) ON UPDATE CASCADE;


--
-- TOC entry 6305 (class 2606 OID 42193)
-- Name: sub_persil sub_persil_id_file_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sub_persil
    ADD CONSTRAINT sub_persil_id_file_fkey FOREIGN KEY (id_file) REFERENCES public.file(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 6306 (class 2606 OID 42198)
-- Name: sub_persil sub_persil_id_pengelola_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sub_persil
    ADD CONSTRAINT sub_persil_id_pengelola_fkey FOREIGN KEY (id_pengelola) REFERENCES public.pengelola(id) ON UPDATE CASCADE;


--
-- TOC entry 6307 (class 2606 OID 42203)
-- Name: sub_persil sub_persil_id_penggunaan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sub_persil
    ADD CONSTRAINT sub_persil_id_penggunaan_fkey FOREIGN KEY (id_penggunaan) REFERENCES public.penggunaan_rtr(id) ON UPDATE CASCADE;


--
-- TOC entry 6308 (class 2606 OID 42208)
-- Name: users users_id_grup_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_id_grup_fkey FOREIGN KEY (id_grup) REFERENCES public.user_grup(id);


--
-- TOC entry 6309 (class 2606 OID 42213)
-- Name: users users_id_kabupaten; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_id_kabupaten FOREIGN KEY (id_kabupaten) REFERENCES public.kabupaten(id) ON UPDATE CASCADE;


--
-- TOC entry 6310 (class 2606 OID 42218)
-- Name: verifikasi varifikasi_id_pengajuan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.verifikasi
    ADD CONSTRAINT varifikasi_id_pengajuan_fkey FOREIGN KEY (id_pengajuan) REFERENCES public.pengajuan(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6311 (class 2606 OID 42223)
-- Name: verifikasi varifikasi_id_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.verifikasi
    ADD CONSTRAINT varifikasi_id_user_fkey FOREIGN KEY (id_user) REFERENCES public.users(id) ON UPDATE CASCADE;


--
-- TOC entry 6312 (class 2606 OID 42228)
-- Name: verifikasi_tanah_desa verifikasi_copy1_id_pengajuan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.verifikasi_tanah_desa
    ADD CONSTRAINT verifikasi_copy1_id_pengajuan_fkey FOREIGN KEY (id_pengajuan_tanah_desa) REFERENCES public.pengajuan_tanah_desa(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 6313 (class 2606 OID 42233)
-- Name: verifikasi_tanah_desa verifikasi_copy1_id_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.verifikasi_tanah_desa
    ADD CONSTRAINT verifikasi_copy1_id_user_fkey FOREIGN KEY (id_user) REFERENCES public.users(id) ON UPDATE CASCADE;


--
-- TOC entry 6471 (class 0 OID 0)
-- Dependencies: 6
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE USAGE ON SCHEMA public FROM PUBLIC;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2026-04-13 08:20:25

--
-- PostgreSQL database dump complete
--

