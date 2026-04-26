<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * VectorTileController
 *
 * Serves Mapbox Vector Tiles (MVT / .pbf) melalui endpoint:
 *   GET /api/v1/tiles/bidang/{z}/{x}/{y}
 *   GET /api/v1/tiles/wilayah/{z}/{x}/{y}
 *
 * Implementasi:
 *   - ST_AsMVT (PostGIS ≥ 2.4) untuk encode protobuf langsung di DB
 *   - ST_Simplify / ST_SimplifyPreserveTopology berdasarkan zoom level
 *   - ST_TileEnvelope (PostGIS ≥ 3.0) atau perhitungan manual untuk bbox tile
 *   - Cache per tile (1 jam default, configurable via env TILE_CACHE_TTL)
 *   - Filter layer: kategori, penggunaan, jenis_hak, jenis_hak_adat, status_kesesuaian
 *
 * Query params (semua opsional):
 *   kategori_ids[]          int[]
 *   penggunaan_ids[]        int[]
 *   jenis_hak_ids[]         int[]
 *   jenis_hak_adat_ids[]    int[]
 *   status_kesesuaian_ids[] int[]
 *   all                     boolean  – sertakan semua bidang tanpa filter layer
 */
class VectorTileController extends Controller
{
    /**
     * Toleransi simplifikasi (dalam satuan derajat WGS84) per zoom level.
     * Zoom < 10 → kasar, Zoom ≥ 16 → tidak disederhanakan.
     */
    private const SIMPLIFY_TOLERANCE = [
        6  => 0.01,
        7  => 0.008,
        8  => 0.005,
        9  => 0.003,
        10 => 0.001,
        11 => 0.0005,
        12 => 0.0002,
        13 => 0.0001,
        14 => 0.00005,
        15 => 0.00001,
        16 => 0,
    ];

    /** Minimum zoom level untuk menampilkan bidang tanah */
    private const MIN_ZOOM_BIDANG = 10;

    /** TTL cache tile dalam detik (default: 1 jam) */
    private int $cacheTtl;

    public function __construct()
    {
        $this->cacheTtl = (int) env('TILE_CACHE_TTL', 3600);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Bidang Tanah Tiles
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * GET /api/v1/tiles/bidang/{z}/{x}/{y}
     *
     * Mengembalikan MVT binary (application/x-protobuf) berisi polygon bidang tanah.
     *
     * Properties yang disertakan per feature:
     *   id, nomor_bidang, nomor_persil, luas,
     *   kategori_id, warna,
     *   penggunaan_id, penggunaan_warna,
     *   jenis_hak_id, jenis_hak_warna,
     *   jenis_hak_adat_id, jenis_hak_adat_warna,
     *   status_kesesuaian_id, status_kesesuaian_warna
     */
    public function bidang(Request $request, int $z, int $x, int $y): Response
    {
        // Validasi koordinat tile
        if (!$this->isValidTile($z, $x, $y)) {
            return $this->emptyTile();
        }

        // Di bawah min zoom: kembalikan tile kosong (kurangi beban server)
        if ($z < self::MIN_ZOOM_BIDANG) {
            return $this->emptyTile();
        }

        // Buat cache key unik berdasarkan z/x/y + filter params
        $cacheKey = $this->buildCacheKey('bidang', $z, $x, $y, $request);

        $tileData = Cache::remember($cacheKey, $this->cacheTtl, function () use ($z, $x, $y, $request) {
            return $this->generateBidangTile($z, $x, $y, $request);
        });

        return $this->tileResponse($tileData);
    }

    /**
     * GET /api/v1/tiles/wilayah/{z}/{x}/{y}
     *
     * Layer batas wilayah (kabupaten, kecamatan, kelurahan).
     * Otomatis memilih level detail sesuai zoom.
     */
    public function wilayah(Request $request, int $z, int $x, int $y): Response
    {
        if (!$this->isValidTile($z, $x, $y)) {
            return $this->emptyTile();
        }

        $cacheKey = "tile:wilayah:{$z}:{$x}:{$y}";

        $tileData = Cache::remember($cacheKey, $this->cacheTtl * 4, function () use ($z, $x, $y) {
            return $this->generateWilayahTile($z, $x, $y);
        });

        return $this->tileResponse($tileData);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Tile generation
    // ──────────────────────────────────────────────────────────────────────────

    private function generateBidangTile(int $z, int $x, int $y, Request $request): ?string
    {
        $tolerance   = $this->getTolerance($z);
        $envelope    = $this->tileEnvelope($z, $x, $y);
        $loadAll     = $request->boolean('all', false);

        // Filter layer IDs dari query params
        $kategoriIds          = array_filter(array_map('intval', (array) $request->input('kategori_ids', [])));
        $penggunaanIds        = array_filter(array_map('intval', (array) $request->input('penggunaan_ids', [])));
        $jenisHakIds          = array_filter(array_map('intval', (array) $request->input('jenis_hak_ids', [])));
        $jenisHakAdatIds      = array_filter(array_map('intval', (array) $request->input('jenis_hak_adat_ids', [])));
        $statusKesesuaianIds  = array_filter(array_map('intval', (array) $request->input('status_kesesuaian_ids', [])));

        // Jika tidak ada filter aktif dan bukan mode "all", kembalikan tile kosong
        if (
            !$loadAll &&
            empty($kategoriIds) &&
            empty($penggunaanIds) &&
            empty($jenisHakIds) &&
            empty($jenisHakAdatIds) &&
            empty($statusKesesuaianIds)
        ) {
            return null;
        }

        // Buat CTE untuk filter layer (OR logic antar semua tipe)
        $layerFilterSql = '';
        $params = [
            'xmin' => $envelope['xmin'],
            'ymin' => $envelope['ymin'],
            'xmax' => $envelope['xmax'],
            'ymax' => $envelope['ymax'],
            'xmin2' => $envelope['xmin'],
            'ymin2' => $envelope['ymin'],
            'xmax2' => $envelope['xmax'],
            'ymax2' => $envelope['ymax'],
        ];

        if (!$loadAll) {
            $conditions = [];

            if (!empty($kategoriIds)) {
                $ph = $this->buildPlaceholders($kategoriIds, $params, 'kid');
                $conditions[] = "b.id_kategori IN ({$ph})";
            }
            if (!empty($penggunaanIds)) {
                $ph = $this->buildPlaceholders($penggunaanIds, $params, 'pid');
                $conditions[] = "b.id_penggunaan IN ({$ph})";
            }
            if (!empty($jenisHakIds)) {
                $ph = $this->buildPlaceholders($jenisHakIds, $params, 'jhid');
                $conditions[] = "b.id_jenis_hak IN ({$ph})";
            }
            if (!empty($jenisHakAdatIds)) {
                $ph = $this->buildPlaceholders($jenisHakAdatIds, $params, 'jhaid');
                $conditions[] = "b.id_jenis_hak_adat IN ({$ph})";
            }
            if (!empty($statusKesesuaianIds)) {
                $ph = $this->buildPlaceholders($statusKesesuaianIds, $params, 'skid');
                $conditions[] = "b.id_status_kesesuaian IN ({$ph})";
            }

            $layerFilterSql = 'AND (' . implode(' OR ', $conditions) . ')';
        }

        $toleranceSql = $tolerance > 0
            ? "ST_SimplifyPreserveTopology(b.geom, :tolerance)"
            : "b.geom";

        if ($tolerance > 0) {
            $params['tolerance'] = $tolerance;
        }

        /*
         * Pipeline:
         * 1. Pilih bidang yang bbox-nya intersect dengan tile envelope (index hit)
         * 2. Clip geometry ke tile envelope + buffer kecil (kurangi artifact tepi)
         * 3. Simplifikasi sesuai zoom
         * 4. Transform ke koordinat tile (3857 → tile coords)
         * 5. Encode ke MVT dengan ST_AsMVT
         */
        $sql = <<<SQL
            WITH
            bounds AS (
                SELECT ST_MakeEnvelope(:xmin, :ymin, :xmax, :ymax, 4326)::geometry AS geom_4326,
                       ST_Transform(ST_MakeEnvelope(:xmin2, :ymin2, :xmax2, :ymax2, 4326), 3857) AS geom_3857
            ),
            mvt_data AS (
                SELECT
                    b.id,
                    b.nomor_bidang,
                    p.nomor_persil,
                    b.luas,
                    b.id_kategori      AS kategori_id,
                    k.warna            AS warna,
                    b.id_penggunaan    AS penggunaan_id,
                    pg.warna           AS penggunaan_warna,
                    b.id_jenis_hak     AS jenis_hak_id,
                    jh.warna           AS jenis_hak_warna,
                    b.id_jenis_hak_adat AS jenis_hak_adat_id,
                    jha.warna          AS jenis_hak_adat_warna,
                    b.id_status_kesesuaian AS status_kesesuaian_id,
                    sk.warna           AS status_kesesuaian_warna,
                    ST_AsMVTGeom(
                        ST_Transform(
                            ST_Intersection(
                                {$toleranceSql},
                                bounds.geom_4326
                            ),
                            3857
                        ),
                        bounds.geom_3857,
                        4096,
                        64,
                        true
                    ) AS geom
                FROM bidang b
                CROSS JOIN bounds
                JOIN persil p ON p.id = b.id_persil
                LEFT JOIN kategori k      ON k.id   = b.id_kategori
                LEFT JOIN penggunaan pg   ON pg.id  = b.id_penggunaan
                LEFT JOIN jenis_hak jh    ON jh.id  = b.id_jenis_hak
                LEFT JOIN jenis_hak_adat jha ON jha.id = b.id_jenis_hak_adat
                LEFT JOIN status_kesesuaian sk ON sk.id = b.id_status_kesesuaian
                WHERE b.geom IS NOT NULL
                  AND b.geom && bounds.geom_4326
                  AND ST_Intersects(b.geom, bounds.geom_4326)
                  {$layerFilterSql}
            )
            SELECT ST_AsMVT(mvt_data.*, 'bidang', 4096, 'geom') AS tile
            FROM mvt_data
            WHERE mvt_data.geom IS NOT NULL
        SQL;

        $result = DB::selectOne($sql, $params);

        if (!$result || empty($result->tile)) {
            return null;
        }

        // PostgreSQL mengembalikan bytea sebagai hex string dengan prefix \x
        return $this->decodeBytea($result->tile);
    }

    private function generateWilayahTile(int $z, int $x, int $y): ?string
    {
        $envelope  = $this->tileEnvelope($z, $x, $y);
        $tolerance = $this->getTolerance($z);

        // Pilih tabel wilayah berdasarkan zoom
        $wilayahLayers = $this->getWilayahLayers($z);

        if (empty($wilayahLayers)) {
            return null;
        }

        $params = [
            'xmin'  => $envelope['xmin'],
            'ymin'  => $envelope['ymin'],
            'xmax'  => $envelope['xmax'],
            'ymax'  => $envelope['ymax'],
            'xmin2' => $envelope['xmin'],
            'ymin2' => $envelope['ymin'],
            'xmax2' => $envelope['xmax'],
            'ymax2' => $envelope['ymax'],
        ];

        if ($tolerance > 0) {
            $params['tolerance'] = $tolerance;
        }

        $toleranceSql = $tolerance > 0
            ? "ST_SimplifyPreserveTopology(w.geom, :tolerance)"
            : "w.geom";

        $unionParts = [];
        foreach ($wilayahLayers as $layer) {
            $unionParts[] = <<<SQL
                SELECT
                    w.id,
                    w.nama,
                    w.kode,
                    '{$layer['name']}' AS level,
                    ST_AsMVTGeom(
                        ST_Transform(
                            ST_Intersection({$toleranceSql}, bounds.geom_4326),
                            3857
                        ),
                        bounds.geom_3857,
                        4096,
                        64,
                        true
                    ) AS geom
                FROM {$layer['table']} w
                CROSS JOIN bounds
                WHERE w.geom IS NOT NULL
                  AND w.geom && bounds.geom_4326
            SQL;
        }

        $unionSql = implode("\n UNION ALL \n", $unionParts);

        $sql = <<<SQL
            WITH
            bounds AS (
                SELECT ST_MakeEnvelope(:xmin, :ymin, :xmax, :ymax, 4326)::geometry AS geom_4326,
                       ST_Transform(ST_MakeEnvelope(:xmin2, :ymin2, :xmax2, :ymax2, 4326), 3857) AS geom_3857
            ),
            mvt_data AS (
                {$unionSql}
            )
            SELECT ST_AsMVT(mvt_data.*, 'wilayah', 4096, 'geom') AS tile
            FROM mvt_data
            WHERE mvt_data.geom IS NOT NULL
        SQL;

        $result = DB::selectOne($sql, $params);

        if (!$result || empty($result->tile)) {
            return null;
        }

        return $this->decodeBytea($result->tile);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Hitung bounding box WGS84 untuk tile {z}/{x}/{y}.
     * Menggunakan rumus Web Mercator tile-to-lat-lng standard.
     *
     * @return array{xmin: float, ymin: float, xmax: float, ymax: float}
     */
    private function tileEnvelope(int $z, int $x, int $y): array
    {
        $n = pow(2, $z);

        $xmin = $x / $n * 360.0 - 180.0;
        $xmax = ($x + 1) / $n * 360.0 - 180.0;

        $ymin = rad2deg(atan(sinh(M_PI * (1 - 2 * ($y + 1) / $n))));
        $ymax = rad2deg(atan(sinh(M_PI * (1 - 2 * $y / $n))));

        return compact('xmin', 'ymin', 'xmax', 'ymax');
    }

    private function getTolerance(int $z): float
    {
        if ($z >= 16) return 0;

        $clampedZ = max(6, min(15, $z));
        return self::SIMPLIFY_TOLERANCE[$clampedZ] ?? 0.001;
    }

    private function isValidTile(int $z, int $x, int $y): bool
    {
        if ($z < 0 || $z > 22) return false;
        $max = pow(2, $z) - 1;
        return $x >= 0 && $x <= $max && $y >= 0 && $y <= $max;
    }

    private function emptyTile(): Response
    {
        // Empty MVT: header MVT valid tapi tanpa fitur
        return response('', 204)
            ->header('Content-Type', 'application/x-protobuf')
            ->header('Cache-Control', 'public, max-age=86400')
            ->header('Access-Control-Allow-Origin', '*');
    }

    private function tileResponse(?string $data): Response
    {
        if (empty($data)) {
            return $this->emptyTile();
        }

        return response($data, 200)
            ->header('Content-Type', 'application/x-protobuf')
            ->header('Cache-Control', "public, max-age={$this->cacheTtl}")
            ->header('Access-Control-Allow-Origin', '*')
            ->header('X-Content-Type-Options', 'nosniff');
    }

    /**
     * Decode PostgreSQL bytea (hex string dengan prefix \x) ke binary string.
     */
    private function decodeBytea(mixed $value): string
    {
        if (is_resource($value)) {
            return stream_get_contents($value);
        }

        $str = (string) $value;

        // Format PostgreSQL: \x followed by hex chars
        if (str_starts_with($str, '\\x')) {
            return hex2bin(substr($str, 2));
        }

        // Sudah binary atau format lain
        return $str;
    }

    /**
     * Build named placeholders untuk array nilai integer.
     * Menambahkan nilai ke array $params (by reference) dengan prefix unik.
     *
     * @param int[] $ids
     * @param array &$params
     * @param string $prefix
     * @return string  contoh: ":kid0, :kid1, :kid2"
     */
    private function buildPlaceholders(array $ids, array &$params, string $prefix): string
    {
        $placeholders = [];
        foreach (array_values($ids) as $i => $id) {
            $key             = "{$prefix}{$i}";
            $params[$key]    = $id;
            $placeholders[]  = ":{$key}";
        }
        return implode(', ', $placeholders);
    }

    private function buildCacheKey(string $type, int $z, int $x, int $y, Request $request): string
    {
        $filters = [
            'k'  => implode(',', array_map('intval', (array) $request->input('kategori_ids', []))),
            'p'  => implode(',', array_map('intval', (array) $request->input('penggunaan_ids', []))),
            'jh' => implode(',', array_map('intval', (array) $request->input('jenis_hak_ids', []))),
            'ja' => implode(',', array_map('intval', (array) $request->input('jenis_hak_adat_ids', []))),
            'sk' => implode(',', array_map('intval', (array) $request->input('status_kesesuaian_ids', []))),
            'a'  => $request->boolean('all') ? '1' : '0',
        ];

        $filterHash = md5(json_encode($filters));
        return "tile:{$type}:{$z}:{$x}:{$y}:{$filterHash}";
    }

    /**
     * Tentukan layer wilayah yang ditampilkan berdasarkan zoom.
     * Zoom < 8: provinsi, 8-10: kabupaten, 11-13: kecamatan, 14+: kelurahan
     *
     * @return array<array{table: string, name: string}>
     */
    private function getWilayahLayers(int $z): array
    {
        if ($z < 8) {
            return [['table' => 'provinsi', 'name' => 'provinsi']];
        }

        if ($z < 11) {
            return [
                ['table' => 'provinsi',  'name' => 'provinsi'],
                ['table' => 'kabupaten', 'name' => 'kabupaten'],
            ];
        }

        if ($z < 13) {
            return [
                ['table' => 'kabupaten', 'name' => 'kabupaten'],
                ['table' => 'kecamatan', 'name' => 'kecamatan'],
            ];
        }

        return [
            ['table' => 'kecamatan',  'name' => 'kecamatan'],
            ['table' => 'kelurahan',  'name' => 'kelurahan'],
        ];
    }
}
