<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    /**
     * GET /api/v1/peta/bidang
     *
     * Query params:
     *   kategori_ids[]  int[]   wajib — ID kategori/kategori yang aktif
     *   bbox               string  opsional — "minLng,minLat,maxLng,maxLat" (viewport Leaflet)
     *   kabupaten_kode     string  opsional
     *   kecamatan_kode     string  opsional
     *   kelurahan_kode     string  opsional
     *
     * Prioritas filter wilayah: kelurahan > kecamatan > kabupaten > bbox
     */
    public function bidang(Request $request): JsonResponse
    {
        $request->validate([
            'kategori_ids'    => ['nullable', 'array'],
            'kategori_ids.*'  => ['integer'],
            'jenis_hak_ids'   => ['nullable', 'array'],
            'jenis_hak_ids.*' => ['integer'],
            'bbox'              => ['nullable', 'string', 'regex:/^-?\d+\.?\d*,-?\d+\.?\d*,-?\d+\.?\d*,-?\d+\.?\d*$/'],
            'kabupaten_kode'    => ['nullable', 'string'],
            'kecamatan_kode'    => ['nullable', 'string'],
            'kelurahan_kode'    => ['nullable', 'string'],
        ]);

        $kategoriIds  = $request->input('kategori_ids', []);
        $jenisHakIds  = $request->input('jenis_hak_ids', []);

        // Minimal salah satu filter aktif
        if (empty($kategoriIds) && empty($jenisHakIds)) {
            return response()->json(['type' => 'FeatureCollection', 'features' => []]);
        }

        // ── Resolusi filter wilayah ke array kelurahan_id ──────────────────
        // Prioritas: kelurahan > kecamatan > kabupaten > (hanya bbox)
        $kelurahanIds = null;

        if ($request->filled('kelurahan_kode')) {
            $kelurahanIds = Kelurahan::where('kode', $request->kelurahan_kode)
                ->pluck('id')
                ->toArray();

        } elseif ($request->filled('kecamatan_kode')) {
            $kecamatanId = Kecamatan::where('kode', $request->kecamatan_kode)
                ->value('id');

            if ($kecamatanId) {
                $kelurahanIds = Kelurahan::where('id_kecamatan', $kecamatanId)
                    ->pluck('id')
                    ->toArray();
            }

        } elseif ($request->filled('kabupaten_kode')) {
            $kabupatenId = Kabupaten::where('kode', $request->kabupaten_kode)
                ->value('id');

            if ($kabupatenId) {
                $kecamatanIds = Kecamatan::where('id_kabupaten', $kabupatenId)
                    ->pluck('id')
                    ->toArray();

                $kelurahanIds = Kelurahan::whereIn('id_kecamatan', $kecamatanIds)
                    ->pluck('id')
                    ->toArray();
            }
        }

        // ── Build query ────────────────────────────────────────────────────
        $query = Bidang::query()
            ->select([
                'bidang.id',
                'bidang.no_bidang',
                'bidang.luas',
                'bidang.id_persil',
                'bidang.id_jenis_hak',
                'bidang.geom',
                'persil.no_persil',
                'persil.id_kategori',
                'kategori.warna',
                'jenis_hak.warna as jenis_hak_warna',
            ])
            ->join('persil', 'persil.id', '=', 'bidang.id_persil')
            ->join('kategori', 'kategori.id', '=', 'persil.id_kategori')
            ->leftJoin('jenis_hak', 'jenis_hak.id', '=', 'bidang.id_jenis_hak')
            ->whereNotNull('bidang.geom');

        // Filter: kategori dan/atau jenis hak (OR logic — tampilkan bila salah satu match)
        $query->where(function ($q) use ($kategoriIds, $jenisHakIds) {
            if (!empty($kategoriIds)) {
                $q->orWhereIn('persil.id_kategori', $kategoriIds);
            }
            if (!empty($jenisHakIds)) {
                $q->orWhereIn('bidang.id_jenis_hak', $jenisHakIds);
            }
        });

        // Filter wilayah via relasi FK (lebih ringan dari spatial query)
        if ($kelurahanIds !== null) {
            $query->whereIn('persil.id_kelurahan', $kelurahanIds);
        }

        // Filter bbox via PostGIS ST_Intersects — hanya aktif jika tidak ada
        // filter wilayah yang lebih spesifik (kelurahan/kecamatan/kabupaten)
        if ($kelurahanIds === null && $request->filled('bbox')) {
            [$minLng, $minLat, $maxLng, $maxLat] = explode(',', $request->bbox);

            $query->whereRaw(
                'ST_Intersects(bidang.geom, ST_MakeEnvelope(?, ?, ?, ?, 4326))',
                [(float) $minLng, (float) $minLat, (float) $maxLng, (float) $maxLat]
            );
        }

        $bidangs = $query->get();

        // ── Format GeoJSON FeatureCollection ──────────────────────────────
        $features = $bidangs->map(function ($bidang) {
            $geom = $bidang->geom;

            // Magellan Geometry → cast ke array GeoJSON
            $geometry = json_decode(json_encode($geom), true);

            return [
                'type'       => 'Feature',
                'geometry'   => $geometry,
                'properties' => [
                    'id'             => $bidang->id,
                    'no_bidang'      => $bidang->no_bidang,
                    'luas'           => $bidang->luas,
                    'no_persil'      => $bidang->no_persil,
                    'kategori_id'    => $bidang->id_kategori,
                    'warna'          => $bidang->warna ?? '#3b82f6',
                    'jenis_hak_id'   => $bidang->id_jenis_hak,
                    'jenis_hak_warna' => $bidang->jenis_hak_warna ?? '#f59e0b',
                ],
            ];
        });

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
