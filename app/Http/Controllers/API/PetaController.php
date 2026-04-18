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
     * Query params (semua opsional, minimal satu filter layer wajib ada):
     *   kategori_ids[]           int[]
     *   penggunaan_ids[]         int[]
     *   jenis_hak_ids[]          int[]
     *   jenis_hak_adat_ids[]     int[]
     *   status_kesesuaian_ids[]  int[]
     *
     *   bbox                     string  "minLng,minLat,maxLng,maxLat"
     *   kabupaten_kode           string
     *   kecamatan_kode           string
     *   kelurahan_kode           string
     *
     * Prioritas filter wilayah: kelurahan > kecamatan > kabupaten > bbox
     */
    public function bidang(Request $request): JsonResponse
    {
        $request->validate([
            'kategori_ids'               => ['nullable', 'array'],
            'kategori_ids.*'             => ['integer'],
            'penggunaan_ids'             => ['nullable', 'array'],
            'penggunaan_ids.*'           => ['integer'],
            'jenis_hak_ids'              => ['nullable', 'array'],
            'jenis_hak_ids.*'            => ['integer'],
            'jenis_hak_adat_ids'         => ['nullable', 'array'],
            'jenis_hak_adat_ids.*'       => ['integer'],
            'status_kesesuaian_ids'      => ['nullable', 'array'],
            'status_kesesuaian_ids.*'    => ['integer'],
            'bbox'                       => ['nullable', 'string', 'regex:/^-?\d+\.?\d*,-?\d+\.?\d*,-?\d+\.?\d*,-?\d+\.?\d*$/'],
            'kabupaten_kode'             => ['nullable', 'string'],
            'kecamatan_kode'             => ['nullable', 'string'],
            'kelurahan_kode'             => ['nullable', 'string'],
        ]);

        $kategoriIds           = $request->input('kategori_ids', []);
        $penggunaanIds         = $request->input('penggunaan_ids', []);
        $jenisHakIds           = $request->input('jenis_hak_ids', []);
        $jenisHakAdatIds       = $request->input('jenis_hak_adat_ids', []);
        $statusKesesuaianIds   = $request->input('status_kesesuaian_ids', []);

        // Minimal salah satu filter layer aktif
        if (
            empty($kategoriIds) &&
            empty($penggunaanIds) &&
            empty($jenisHakIds) &&
            empty($jenisHakAdatIds) &&
            empty($statusKesesuaianIds)
        ) {
            return response()->json(['type' => 'FeatureCollection', 'features' => []]);
        }

        // ── Resolusi filter wilayah ke array kelurahan_id ──────────────────
        // Skema baru: persil memiliki id_kelurahan, id_kecamatan, id_kabupaten langsung
        $kelurahanIds  = null;
        $kecamatanId   = null;
        $kabupatenId   = null;

        if ($request->filled('kelurahan_kode')) {
            $kelurahanIds = Kelurahan::where('kode', $request->kelurahan_kode)
                ->pluck('id')
                ->toArray();

        } elseif ($request->filled('kecamatan_kode')) {
            $kec = Kecamatan::where('kode', $request->kecamatan_kode)->first();
            if ($kec) {
                $kecamatanId = $kec->id;
            }

        } elseif ($request->filled('kabupaten_kode')) {
            $kab = Kabupaten::where('kode', $request->kabupaten_kode)->first();
            if ($kab) {
                $kabupatenId = $kab->id;
            }
        }

        // ── Build query ────────────────────────────────────────────────────
        // Catatan skema baru:
        //   - bidang.id_kategori        (pindah dari persil ke bidang)
        //   - bidang.id_jenis_hak_adat  (kolom baru di bidang)
        //   - persil.nomor_persil       (ganti dari no_persil)
        //   - persil.id_kelurahan/id_kecamatan/id_kabupaten (FK langsung di persil)
        $query = Bidang::query()
            ->select([
                'bidang.id',
                'bidang.nomor_bidang',
                'bidang.luas',
                'bidang.id_persil',
                'bidang.id_jenis_hak',
                'bidang.id_jenis_hak_adat',
                'bidang.id_status_kesesuaian',
                'bidang.geom',
                'bidang.id_kategori',
                'bidang.id_penggunaan',
                'persil.nomor_persil',
                'kategori.warna',
                'penggunaan.warna as penggunaan_warna',
                'jenis_hak.warna as jenis_hak_warna',
                'jenis_hak_adat.warna as jenis_hak_adat_warna',
                'status_kesesuaian.warna as status_kesesuaian_warna',
            ])
            ->join('persil', 'persil.id', '=', 'bidang.id_persil')
            ->leftJoin('kategori', 'kategori.id', '=', 'bidang.id_kategori')
            ->leftJoin('penggunaan', 'penggunaan.id', '=', 'bidang.id_penggunaan')
            ->leftJoin('jenis_hak', 'jenis_hak.id', '=', 'bidang.id_jenis_hak')
            ->leftJoin('jenis_hak_adat', 'jenis_hak_adat.id', '=', 'bidang.id_jenis_hak_adat')
            ->leftJoin('status_kesesuaian', 'status_kesesuaian.id', '=', 'bidang.id_status_kesesuaian')
            ->whereNotNull('bidang.geom');

        // Filter layer: OR logic antar semua tipe yang aktif
        $query->where(function ($q) use (
            $kategoriIds,
            $penggunaanIds,
            $jenisHakIds,
            $jenisHakAdatIds,
            $statusKesesuaianIds
        ) {
            if (!empty($kategoriIds)) {
                $q->orWhereIn('bidang.id_kategori', $kategoriIds);
            }
            if (!empty($penggunaanIds)) {
                $q->orWhereIn('bidang.id_penggunaan', $penggunaanIds);
            }
            if (!empty($jenisHakIds)) {
                $q->orWhereIn('bidang.id_jenis_hak', $jenisHakIds);
            }
            if (!empty($jenisHakAdatIds)) {
                $q->orWhereIn('bidang.id_jenis_hak_adat', $jenisHakAdatIds);
            }
            if (!empty($statusKesesuaianIds)) {
                $q->orWhereIn('bidang.id_status_kesesuaian', $statusKesesuaianIds);
            }
        });

        // ── Filter wilayah ─────────────────────────────────────────────────
        // Skema baru: persil memiliki FK langsung ke kelurahan, kecamatan, kabupaten
        // sehingga tidak perlu sub-query via Kelurahan::whereIn
        if ($kelurahanIds !== null) {
            $query->whereIn('persil.id_kelurahan', $kelurahanIds);

        } elseif ($kecamatanId !== null) {
            // Gunakan id_kecamatan langsung di persil (FK baru)
            $query->where('persil.id_kecamatan', $kecamatanId);

        } elseif ($kabupatenId !== null) {
            // Gunakan id_kabupaten langsung di persil (FK baru)
            $query->where('persil.id_kabupaten', $kabupatenId);

        } elseif ($request->filled('bbox')) {
            // Bbox hanya aktif jika tidak ada filter wilayah
            [$minLng, $minLat, $maxLng, $maxLat] = explode(',', $request->bbox);

            $query->whereRaw(
                'ST_Intersects(bidang.geom, ST_MakeEnvelope(?, ?, ?, ?, 4326))',
                [(float) $minLng, (float) $minLat, (float) $maxLng, (float) $maxLat]
            );
        }

        $bidangs = $query->get();

        // ── Format GeoJSON FeatureCollection ──────────────────────────────
        $features = $bidangs->map(function ($bidang) {
            $geometry = json_decode(json_encode($bidang->geom), true);

            return [
                'type'     => 'Feature',
                'geometry' => $geometry,
                'properties' => [
                    'id'                      => $bidang->id,
                    'no_bidang'               => $bidang->nomor_bidang,   // kolom baru: nomor_bidang
                    'luas'                    => $bidang->luas,
                    'no_persil'               => $bidang->nomor_persil,   // kolom baru: nomor_persil
                    // kategori
                    'kategori_id'             => $bidang->id_kategori,
                    'warna'                   => $bidang->warna,
                    // penggunaan
                    'penggunaan_id'           => $bidang->id_penggunaan,
                    'penggunaan_warna'        => $bidang->penggunaan_warna,
                    // jenis hak
                    'jenis_hak_id'            => $bidang->id_jenis_hak,
                    'jenis_hak_warna'         => $bidang->jenis_hak_warna,
                    // jenis hak adat
                    'jenis_hak_adat_id'       => $bidang->id_jenis_hak_adat,
                    'jenis_hak_adat_warna'    => $bidang->jenis_hak_adat_warna,
                    // status kesesuaian
                    'status_kesesuaian_id'    => $bidang->id_status_kesesuaian,
                    'status_kesesuaian_warna' => $bidang->status_kesesuaian_warna,
                ],
            ];
        });

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
