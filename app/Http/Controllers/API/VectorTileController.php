<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VectorTileController extends Controller
{
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

    // Diturunkan ke 8 agar tidak ada inkonsistensi "harus zoom dalam dulu"
    private const MIN_ZOOM_BIDANG = 8;

    private int $cacheTtl;

    public function __construct()
    {
        $this->cacheTtl = (int) env('TILE_CACHE_TTL', 3600);
    }

    public function bidang(Request $request, int $z, int $x, int $y): Response
    {
        if (!$this->isValidTile($z, $x, $y)) return $this->emptyTile();
        if ($z < self::MIN_ZOOM_BIDANG) return $this->emptyTile();

        $filters = $this->parseFilters($request);

        // Sidebar adalah satu-satunya pengendali — tidak ada "all" mode
        if ($this->hasNoFilter($filters)) return $this->emptyTile();

        $cacheKey = $this->buildCacheKey('bidang', $z, $x, $y, $filters);

        $tileData = Cache::remember($cacheKey, $this->cacheTtl, function () use ($z, $x, $y, $filters) {
            return $this->generateBidangTile($z, $x, $y, $filters);
        });

        return $this->tileResponse($tileData);
    }

    public function wilayah(Request $request, int $z, int $x, int $y): Response
    {
        if (!$this->isValidTile($z, $x, $y)) return $this->emptyTile();

        $cacheKey = "tile:wilayah:{$z}:{$x}:{$y}";

        $tileData = Cache::remember($cacheKey, $this->cacheTtl * 4, function () use ($z, $x, $y) {
            return $this->generateWilayahTile($z, $x, $y);
        });

        return $this->tileResponse($tileData);
    }

    private function generateBidangTile(int $z, int $x, int $y, array $filters): ?string
    {
        $tolerance = $this->getTolerance($z);
        $envelope  = $this->tileEnvelope($z, $x, $y);

        $params = [
            'xmin'  => $envelope['xmin'], 'ymin'  => $envelope['ymin'],
            'xmax'  => $envelope['xmax'], 'ymax'  => $envelope['ymax'],
            'xmin2' => $envelope['xmin'], 'ymin2' => $envelope['ymin'],
            'xmax2' => $envelope['xmax'], 'ymax2' => $envelope['ymax'],
        ];

        $layerFilterSql = $this->buildLayerFilter($filters, $params);

        if ($tolerance > 0) $params['tolerance'] = $tolerance;

        $simplifyExpr = $tolerance > 0
            ? 'ST_SimplifyPreserveTopology(c.valid_geom, :tolerance)'
            : 'c.valid_geom';

        /*
         * Pipeline 3-CTE:
         *
         * candidates  – filter bbox (GIST index) + ST_MakeValid untuk geometry invalid
         *               (mencegah TopologyException: side location conflict pada GEOS)
         *
         * simplified  – simplifikasi per zoom + ST_Intersection ke tile bounds
         *               (dijalankan hanya pada geometry yang ST_IsValid)
         *
         * mvt_data    – join warna dari tabel referensi + encode ST_AsMVTGeom
         */
        $sql = <<<SQL
            WITH
            bounds AS (
                SELECT
                    ST_MakeEnvelope(:xmin, :ymin, :xmax, :ymax, 4326)::geometry AS geom_4326,
                    ST_Transform(ST_MakeEnvelope(:xmin2, :ymin2, :xmax2, :ymax2, 4326), 3857) AS geom_3857
            ),
            candidates AS (
                SELECT
                    b.id, b.nomor_bidang, b.luas,
                    b.id_kategori, b.id_penggunaan, b.id_jenis_hak,
                    b.id_jenis_hak_adat, b.id_status_kesesuaian, b.id_persil,
                    ST_MakeValid(b.geom) AS valid_geom
                FROM bidang b
                CROSS JOIN bounds
                WHERE b.geom IS NOT NULL
                  AND b.geom && bounds.geom_4326
                  {$layerFilterSql}
            ),
            simplified AS (
                SELECT
                    c.id, c.nomor_bidang, c.luas,
                    c.id_kategori, c.id_penggunaan, c.id_jenis_hak,
                    c.id_jenis_hak_adat, c.id_status_kesesuaian, c.id_persil,
                    ST_Intersection({$simplifyExpr}, bounds.geom_4326) AS clipped_geom
                FROM candidates c
                CROSS JOIN bounds
                WHERE ST_IsValid(c.valid_geom)
                  AND ST_Intersects(c.valid_geom, bounds.geom_4326)
            ),
            mvt_data AS (
                SELECT
                    s.id, s.nomor_bidang, p.nomor_persil, s.luas,
                    s.id_kategori          AS kategori_id,        k.warna,
                    s.id_penggunaan        AS penggunaan_id,      pg.warna  AS penggunaan_warna,
                    s.id_jenis_hak         AS jenis_hak_id,       jh.warna  AS jenis_hak_warna,
                    s.id_jenis_hak_adat    AS jenis_hak_adat_id,  jha.warna AS jenis_hak_adat_warna,
                    s.id_status_kesesuaian AS status_kesesuaian_id, sk.warna AS status_kesesuaian_warna,
                    ST_AsMVTGeom(
                        ST_Transform(s.clipped_geom, 3857),
                        bounds.geom_3857, 4096, 64, true
                    ) AS geom
                FROM simplified s
                CROSS JOIN bounds
                JOIN persil p                ON p.id   = s.id_persil
                LEFT JOIN kategori k         ON k.id   = s.id_kategori
                LEFT JOIN penggunaan pg      ON pg.id  = s.id_penggunaan
                LEFT JOIN jenis_hak jh       ON jh.id  = s.id_jenis_hak
                LEFT JOIN jenis_hak_adat jha ON jha.id = s.id_jenis_hak_adat
                LEFT JOIN status_kesesuaian sk ON sk.id = s.id_status_kesesuaian
                WHERE s.clipped_geom IS NOT NULL
                  AND NOT ST_IsEmpty(s.clipped_geom)
            )
            SELECT ST_AsMVT(mvt_data.*, 'bidang', 4096, 'geom') AS tile
            FROM mvt_data
            WHERE mvt_data.geom IS NOT NULL
        SQL;

        try {
            $result = DB::selectOne($sql, $params);
        } catch (\Exception $e) {
            Log::warning('[VectorTile] bidang tile error', [
                'z' => $z, 'x' => $x, 'y' => $y, 'error' => $e->getMessage(),
            ]);
            return null;
        }

        if (!$result || empty($result->tile)) return null;
        return $this->decodeBytea($result->tile);
    }

    private function generateWilayahTile(int $z, int $x, int $y): ?string
    {
        $envelope      = $this->tileEnvelope($z, $x, $y);
        $tolerance     = $this->getTolerance($z);
        $wilayahLayers = $this->getWilayahLayers($z);

        if (empty($wilayahLayers)) return null;

        $params = [
            'xmin'  => $envelope['xmin'], 'ymin'  => $envelope['ymin'],
            'xmax'  => $envelope['xmax'], 'ymax'  => $envelope['ymax'],
            'xmin2' => $envelope['xmin'], 'ymin2' => $envelope['ymin'],
            'xmax2' => $envelope['xmax'], 'ymax2' => $envelope['ymax'],
        ];

        if ($tolerance > 0) $params['tolerance'] = $tolerance;

        $simplifyExpr = $tolerance > 0
            ? 'ST_SimplifyPreserveTopology(ST_MakeValid(w.geom), :tolerance)'
            : 'ST_MakeValid(w.geom)';

        $unionParts = [];
        foreach ($wilayahLayers as $layer) {
            $unionParts[] = <<<SQL
                SELECT w.id, w.nama, w.kode, '{$layer['name']}' AS level,
                    ST_AsMVTGeom(
                        ST_Transform(
                            ST_Intersection({$simplifyExpr}, bounds.geom_4326),
                            3857
                        ),
                        bounds.geom_3857, 4096, 64, true
                    ) AS geom
                FROM {$layer['table']} w
                CROSS JOIN bounds
                WHERE w.geom IS NOT NULL
                  AND w.geom && bounds.geom_4326
                  AND ST_IsValid(ST_MakeValid(w.geom))
            SQL;
        }

        $sql = <<<SQL
            WITH
            bounds AS (
                SELECT
                    ST_MakeEnvelope(:xmin, :ymin, :xmax, :ymax, 4326)::geometry AS geom_4326,
                    ST_Transform(ST_MakeEnvelope(:xmin2, :ymin2, :xmax2, :ymax2, 4326), 3857) AS geom_3857
            ),
            mvt_data AS ( {$this->implodeUnion($unionParts)} )
            SELECT ST_AsMVT(mvt_data.*, 'wilayah', 4096, 'geom') AS tile
            FROM mvt_data WHERE mvt_data.geom IS NOT NULL
        SQL;

        try {
            $result = DB::selectOne($sql, $params);
        } catch (\Exception $e) {
            Log::warning('[VectorTile] wilayah tile error', [
                'z' => $z, 'x' => $x, 'y' => $y, 'error' => $e->getMessage(),
            ]);
            return null;
        }

        if (!$result || empty($result->tile)) return null;
        return $this->decodeBytea($result->tile);
    }

    // ──────────────────────────────────────────────────────────────────────────

    private function parseFilters(Request $request): array
    {
        $clean = fn($key) => array_values(array_filter(array_map('intval', (array) $request->input($key, []))));
        return [
            'kategori'          => $clean('kategori_ids'),
            'penggunaan'        => $clean('penggunaan_ids'),
            'jenis_hak'         => $clean('jenis_hak_ids'),
            'jenis_hak_adat'    => $clean('jenis_hak_adat_ids'),
            'status_kesesuaian' => $clean('status_kesesuaian_ids'),
        ];
    }

    private function hasNoFilter(array $filters): bool
    {
        foreach ($filters as $ids) {
            if (!empty($ids)) return false;
        }
        return true;
    }

    private function buildLayerFilter(array $filters, array &$params): string
    {
        $conditions = [];
        $map = [
            'kategori'          => ['b.id_kategori',          'kid'],
            'penggunaan'        => ['b.id_penggunaan',        'pid'],
            'jenis_hak'         => ['b.id_jenis_hak',         'jhid'],
            'jenis_hak_adat'    => ['b.id_jenis_hak_adat',    'jhaid'],
            'status_kesesuaian' => ['b.id_status_kesesuaian', 'skid'],
        ];

        foreach ($map as $key => [$col, $prefix]) {
            if (!empty($filters[$key])) {
                $ph = $this->buildPlaceholders($filters[$key], $params, $prefix);
                $conditions[] = "{$col} IN ({$ph})";
            }
        }

        return empty($conditions) ? '' : 'AND (' . implode(' OR ', $conditions) . ')';
    }

    private function tileEnvelope(int $z, int $x, int $y): array
    {
        $n    = pow(2, $z);
        $xmin = $x / $n * 360.0 - 180.0;
        $xmax = ($x + 1) / $n * 360.0 - 180.0;
        $ymin = rad2deg(atan(sinh(M_PI * (1 - 2 * ($y + 1) / $n))));
        $ymax = rad2deg(atan(sinh(M_PI * (1 - 2 * $y / $n))));
        return compact('xmin', 'ymin', 'xmax', 'ymax');
    }

    private function getTolerance(int $z): float
    {
        if ($z >= 16) return 0;
        return self::SIMPLIFY_TOLERANCE[max(6, min(15, $z))] ?? 0.001;
    }

    private function isValidTile(int $z, int $x, int $y): bool
    {
        if ($z < 0 || $z > 22) return false;
        $max = pow(2, $z) - 1;
        return $x >= 0 && $x <= $max && $y >= 0 && $y <= $max;
    }

    private function emptyTile(): Response
    {
        return response('', 204)
            ->header('Content-Type', 'application/x-protobuf')
            ->header('Cache-Control', 'public, max-age=86400')
            ->header('Access-Control-Allow-Origin', '*');
    }

    private function tileResponse(?string $data): Response
    {
        if (empty($data)) return $this->emptyTile();
        return response($data, 200)
            ->header('Content-Type', 'application/x-protobuf')
            ->header('Cache-Control', "public, max-age={$this->cacheTtl}")
            ->header('Access-Control-Allow-Origin', '*')
            ->header('X-Content-Type-Options', 'nosniff');
    }

    private function decodeBytea(mixed $value): string
    {
        if (is_resource($value)) return stream_get_contents($value);
        $str = (string) $value;
        return str_starts_with($str, '\\x') ? hex2bin(substr($str, 2)) : $str;
    }

    private function buildPlaceholders(array $ids, array &$params, string $prefix): string
    {
        $placeholders = [];
        foreach (array_values($ids) as $i => $id) {
            $key = "{$prefix}{$i}";
            $params[$key] = $id;
            $placeholders[] = ":{$key}";
        }
        return implode(', ', $placeholders);
    }

    private function buildCacheKey(string $type, int $z, int $x, int $y, array $filters): string
    {
        return "tile:{$type}:{$z}:{$x}:{$y}:" . md5(json_encode($filters));
    }

    private function implodeUnion(array $parts): string
    {
        return implode("\n UNION ALL \n", $parts);
    }

    private function getWilayahLayers(int $z): array
    {
        if ($z < 8)  return [['table' => 'provinsi',  'name' => 'provinsi']];
        if ($z < 11) return [['table' => 'provinsi',  'name' => 'provinsi'],  ['table' => 'kabupaten', 'name' => 'kabupaten']];
        if ($z < 13) return [['table' => 'kabupaten', 'name' => 'kabupaten'], ['table' => 'kecamatan', 'name' => 'kecamatan']];
        return           [['table' => 'kecamatan', 'name' => 'kecamatan'],  ['table' => 'kelurahan',  'name' => 'kelurahan']];
    }
}
