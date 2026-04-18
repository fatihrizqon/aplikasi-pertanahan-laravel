<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Provinsi;
use Clickbar\Magellan\Data\Geometries\Geometry;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function provinsi()
    {
        $data = Provinsi::orderBy('nama')
            ->get()
            ->map(fn($item) => [
                'value' => $item->kode,
                'label' => $item->nama,
            ]);

        return response()->json($data);
    }

    public function kabupaten(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $data = Kabupaten::where('kode', 'like', $request->kode . '.%')
            ->orderBy('kode')
            ->get()
            ->map(fn($item) => [
                'value' => $item->kode,
                'label' => $item->nama,
            ]);

        return response()->json($data);
    }

    public function kecamatan(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $data = Kecamatan::where('kode', 'like', $request->kode . '.%')
            ->orderBy('nama')
            ->get()
            ->map(fn($item) => [
                'value' => $item->kode,
                'label' => $item->nama,
            ]);

        return response()->json($data);
    }

    public function kelurahan(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $data = Kelurahan::where('kode', 'like', $request->kode . '.%')
            ->orderBy('nama')
            ->get()
            ->map(fn($item) => [
                'value' => $item->kode,
                'label' => $item->nama,
            ]);

        return response()->json($data);
    }

    public function provinsiBbox(Request $request)
    {
        $request->validate(['kode' => 'required']);

        $provinsi = Provinsi::where('kode', $request->kode)->firstOrFail();

        return response()->json([
            'nama' => $provinsi->nama,
            'bbox' => $this->extractBbox($provinsi->geom),
        ]);
    }

    public function kabupatenBbox(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $kabupaten = Kabupaten::where('kode', $request->kode)->firstOrFail();

        return response()->json([
            'nama' => $kabupaten->nama,
            'bbox' => $this->extractBbox($kabupaten->geom),
        ]);
    }

    public function kecamatanBbox(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $kecamatan = Kecamatan::where('kode', $request->kode)->firstOrFail();

        return response()->json([
            'nama' => $kecamatan->nama,
            'bbox' => $this->extractBbox($kecamatan->geom),
        ]);
    }

    public function kelurahanBbox(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $kelurahan = Kelurahan::where('kode', $request->kode)->firstOrFail();

        return response()->json([
            'nama' => $kelurahan->nama,
            'bbox' => $this->extractBbox($kelurahan->geom),
        ]);
    }

    private function extractBbox(Geometry $geom): array
    {
        $geoJson = json_decode(json_encode($geom), true);

        $coords = $this->flattenCoordinates($geoJson['coordinates']);

        $lngs = array_column($coords, 0);
        $lats = array_column($coords, 1);

        return [min($lngs), min($lats), max($lngs), max($lats)];
    }

    private function flattenCoordinates(array $coords): array
    {
        // Cek apakah sudah level koordinat [lng, lat] (array of 2 numbers)
        if (is_numeric($coords[0])) {
            return [$coords];
        }

        $result = [];
        foreach ($coords as $item) {
            $result = array_merge($result, $this->flattenCoordinates($item));
        }
        return $result;
    }

    public function provinsiGeojson(Request $request)
    {
        $request->validate(['kode' => 'required']);

        $provinsi = Provinsi::where('kode', $request->kode)->firstOrFail();

        return response()->json([
            'type'     => 'Feature',
            'geometry' => json_decode(json_encode($provinsi->geom), true),
            'properties' => [
                'nama' => $provinsi->nama,
                'kode' => $provinsi->kode,
            ],
        ]);
    }

    public function kabupatenGeojson(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $kab = Kabupaten::where('kode', $request->kode)->firstOrFail();

        return response()->json([
            'type'     => 'Feature',
            'geometry' => json_decode(json_encode($kab->geom), true),
            'properties' => [
                'nama' => $kab->nama,
                'kode' => $kab->kode,
            ],
        ]);
    }

    public function kecamatanGeojson(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $kec = Kecamatan::where('kode', $request->kode)->firstOrFail();

        return response()->json([
            'type'     => 'Feature',
            'geometry' => json_decode(json_encode($kec->geom), true),
            'properties' => [
                'nama' => $kec->nama,
                'kode' => $kec->kode,
            ],
        ]);
    }

    public function kelurahanGeojson(Request $request)
    {
        $request->validate(['kode' => 'required|string']);

        $kel = Kelurahan::where('kode', $request->kode)->firstOrFail();

        return response()->json([
            'type'     => 'Feature',
            'geometry' => json_decode(json_encode($kel->geom), true),
            'properties' => [
                'nama' => $kel->nama,
                'kode' => $kel->kode,
            ],
        ]);
    }
}
