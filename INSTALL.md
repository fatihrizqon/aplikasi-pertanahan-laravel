# Vector Tiles Patch — Panduan Instalasi

## Ringkasan Perubahan

Patch ini mengubah arsitektur peta dari **GeoJSON full-load** menjadi
**Mapbox Vector Tiles (MVT)** yang digenerate PostGIS, dan mengganti
**Leaflet** dengan **MapLibre GL JS** sebagai renderer peta.

### Sebelum → Sesudah

| Aspek | Sebelum | Sesudah |
|---|---|---|
| Frontend renderer | Leaflet + Canvas | **MapLibre GL JS (WebGL)** |
| Format data | GeoJSON (full payload) | **MVT / .pbf per tile** |
| Geometry simplify | Client-side (JS Douglas-Peucker) | **Server-side ST_SimplifyPreserveTopology (PostGIS)** |
| Caching | Tidak ada | **Server tile cache (Laravel Cache, 1 jam)** |
| Beban data initial | Semua polygon sekaligus | **Hanya tile viewport saat ini** |
| 100rb polygon | Lambat / crash browser | **Lancar (WebGL + tiled)** |

---

## File yang Diubah/Ditambahkan

```
app/Http/Controllers/API/VectorTileController.php   ← BARU
routes/api.php                                       ← DIUBAH (tambah /tiles routes)
public/dist/js/dashboard/map.js                      ← DIUBAH (Leaflet → MapLibre)
public/dist/js/dashboard/wilayah.js                 ← DIUBAH (Leaflet layer → MapLibre)
public/dist/js/dashboard/peta-layers.js             ← DIUBAH (GeoJSON → Vector Tiles)
public/dist/js/dashboard/layers.js                  ← DIUBAH (API updated ke MapLibre)
public/dist/css/map.css                              ← DIUBAH (+ MapLibre popup styles)
resources/views/dashboard/peta/index.blade.php      ← DIUBAH (CDN Leaflet → MapLibre)
database/migrations/2024_01_01_000001_add_postgis_spatial_indexes.php  ← BARU
```

---

## Langkah Instalasi

### 1. Merge File Patch

Ekstrak `patch.zip` lalu copy semua file ke root project, merge/replace jika ada konflik:

```bash
cp -r vector-tiles-patch/* /path/to/your-project/
```

### 2. Jalankan Migrasi (Spatial Index)

```bash
php artisan migrate
```

Migrasi ini akan:
- Mengaktifkan extension `postgis` dan `postgis_topology`
- Membuat GIST index pada kolom `geom` di tabel `bidang`, `persil`, dan wilayah
- Membuat B-tree index pada FK kolom filter (`id_kategori`, `id_jenis_hak`, dll.)

> **Penting:** Proses indexing bisa memakan waktu beberapa menit jika tabel sudah
> berisi ratusan ribu baris. Jalankan saat traffic rendah.

### 3. Konfigurasi Cache (.env)

Tambahkan ke `.env` jika belum ada:

```env
# Durasi cache tile MVT dalam detik (default: 3600 = 1 jam)
# Set 0 untuk disable cache (development only)
TILE_CACHE_TTL=3600

# Pastikan CACHE_DRIVER bukan 'array' di production
CACHE_DRIVER=redis   # atau file, database
```

### 4. Verifikasi PostGIS

Pastikan PostGIS terinstall di PostgreSQL server:

```sql
-- Di psql atau DBeaver:
SELECT PostGIS_Version();
-- Harus mengembalikan: 3.x.x ...
```

Jika belum ada:
```bash
# Ubuntu/Debian
sudo apt install postgresql-16-postgis-3  # sesuaikan versi

# Atau via psql
CREATE EXTENSION IF NOT EXISTS postgis;
```

### 5. Test Endpoint Tile

Buka browser atau curl:

```bash
# Tile kosong (zoom 5, tidak ada bidang)
curl -I http://localhost/api/v1/tiles/bidang/5/0/0

# Tile dengan bidang di zoom 12 (sesuaikan x/y dengan area data Anda)
curl -I http://localhost/api/v1/tiles/bidang/12/3270/2152

# Harus mengembalikan:
# HTTP/1.1 200 OK
# Content-Type: application/x-protobuf
```

---

## Cara Kerja

### Backend (VectorTileController)

1. Request masuk: `GET /api/v1/tiles/bidang/{z}/{x}/{y}?kategori_ids[]=1&kategori_ids[]=2`
2. Controller menghitung bounding box WGS84 dari koordinat tile `{z}/{x}/{y}`
3. Query PostgreSQL dengan PostGIS:
   - `ST_Intersects(geom, ST_MakeEnvelope(bbox))` → spatial index hit, hanya data relevan
   - `ST_SimplifyPreserveTopology(geom, tolerance)` → kurangi vertex sesuai zoom
   - `ST_AsMVT(...)` → encode hasil ke binary protobuf langsung di database
4. Response binary `.pbf` dikembalikan dengan cache header

### Frontend (MapLibre GL JS)

1. MapLibre membaca tile URL template: `/api/v1/tiles/bidang/{z}/{x}/{y}?...`
2. Secara otomatis menghitung tile mana yang perlu di-load sesuai viewport
3. WebGL merender polygon dari MVT data — jauh lebih cepat dari SVG/Canvas Leaflet
4. Saat filter layer berubah (checkbox di sidebar), tile URL di-rebuild dengan params baru
5. `map.getSource('bidang-mvt').setTiles([newUrl])` → MapLibre re-fetch tile otomatis

### Simplifikasi Server-Side per Zoom

| Zoom | Toleransi | Cocok untuk |
|---|---|---|
| ≤ 8 | 0.01° | Tampilan provinsi/kabupaten |
| 9 | 0.003° | Tampilan kabupaten penuh |
| 10 | 0.001° | Tampilan kecamatan |
| 11 | 0.0005° | Outline desa terlihat |
| 12 | 0.0002° | Detail bidang mulai jelas |
| 13 | 0.0001° | Bidang jelas |
| 14–15 | 0.00001–0.00005° | Hampir full detail |
| ≥ 16 | 0 | Full detail, no simplification |

---

## Performa yang Diharapkan

- **100.000 polygon** → hanya tile di viewport yang di-load (~50–200 fitur/tile)
- **Waktu response tile** → 50–200ms (dengan index & simplifikasi)
- **Pertama kali load** → tile di-cache, request berikutnya instan dari cache
- **Zoom in/out** → MapLibre meminta tile baru, tile lama tetap terlihat sementara loading

---

## Troubleshooting

### Tile selalu kosong (204 No Content)
- Cek zoom level: bidang hanya tampil mulai zoom 10 (`MIN_ZOOM_BIDANG`)
- Verifikasi data ada di database dengan kolom `geom` tidak null
- Pastikan SRID data adalah 4326: `SELECT ST_SRID(geom) FROM bidang LIMIT 1;`

### Error "function st_asmvt does not exist"
- PostGIS belum terinstall atau versinya < 2.4
- `ST_AsMVT` tersedia sejak PostGIS 2.4, direkomendasikan 3.x

### Tile lambat (>500ms)
- Pastikan migrasi spatial index sudah dijalankan
- Cek `EXPLAIN ANALYZE` pada query tile untuk memverifikasi index digunakan
- Pertimbangkan meningkatkan `shared_buffers` dan `work_mem` di PostgreSQL

### MapLibre tidak tampil / error JS
- Buka DevTools browser, cek console error
- Pastikan tidak ada script Leaflet yang masih di-load (conflict dengan MapLibre)
- Verifikasi CDN MapLibre bisa diakses dari server

### Filter tidak update tile
- Pastikan tidak ada browser cache agresif (hard refresh: Ctrl+Shift+R)
- Cek bahwa `Cache-Control` header dari server di-set dengan benar
