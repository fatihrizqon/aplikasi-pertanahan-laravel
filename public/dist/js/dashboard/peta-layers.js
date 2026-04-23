/**
 * peta-layers.js  (OPTIMIZED v2 — Progressive Detail)
 *
 * Strategi: fetch mulai dari zoom 9 (skala 1:546rb), tapi geometry disederhanakan
 * secara agresif di zoom jauh. Semakin dekat zoom, semakin detail polygon tampil.
 *
 * Zoom tiers:
 *   zoom  9–11  → tolerance 0.005  (sangat kasar, hanya outline besar)
 *   zoom 12–13  → tolerance 0.001  (kasar, bentuk umum terlihat)
 *   zoom 14–15  → tolerance 0.0002 (sedang, detail mulai terlihat)
 *   zoom 16+    → tolerance 0      (full detail, no simplification)
 *
 * Optimasi aktif:
 *   1. CANVAS RENDERER     — satu <canvas>, bukan ribuan <path> SVG di DOM
 *   2. CLIENT SIMPLIFY     — koordinat polygon disederhanakan per zoom sebelum render
 *   3. BATCH addData       — satu addData() per layer, bukan per feature
 *   4. AbortController     — request lama di-cancel saat fetch baru dimulai
 *   5. DEBOUNCE 600ms      — dipertahankan
 *   6. ZOOM-AWARE REFETCH  — saat zoom berubah tier, re-render dengan tolerance baru
 */

"use strict";

// ── Zoom tiers & tolerance ────────────────────────────────────────────────────

const ZOOM_TIERS = [
    { minZoom: 9,  maxZoom: 11, tolerance: 0.005  },
    { minZoom: 12, maxZoom: 13, tolerance: 0.001  },
    { minZoom: 14, maxZoom: 15, tolerance: 0.0002 },
    { minZoom: 16, maxZoom: 99, tolerance: 0      },
];

const MIN_ZOOM_BIDANG = 9; // mulai fetch dari zoom ini

function getTolerance(zoom) {
    const tier = ZOOM_TIERS.find((t) => zoom >= t.minZoom && zoom <= t.maxZoom);
    return tier ? tier.tolerance : 0.005;
}

function getZoomTierIndex(zoom) {
    return ZOOM_TIERS.findIndex((t) => zoom >= t.minZoom && zoom <= t.maxZoom);
}

// ── Canvas renderer bersama ───────────────────────────────────────────────────

const _canvasRenderer = L.canvas({ padding: 0.5 });

// ── State ─────────────────────────────────────────────────────────────────────

const petaLayers = {
    groups: {},
    penggunaanGroups: {},
    jenisHakGroups: {},
    jenisHakAdatGroups: {},
    statusKesesuaianGroups: {},

    wilayah: {
        kabupaten_kode: null,
        kecamatan_kode: null,
        kelurahan_kode: null,
    },

    // Raw GeoJSON cache per fetch-type — disimpan agar bisa re-simplify tanpa fetch ulang
    _cache: {
        kategori: null,       // { ids: [...], geojson: {...} }
        penggunaan: null,
        jenisHak: null,
        jenisHakAdat: null,
        statusKesesuaian: null,
    },

    _fetchTimer: null,
    _lastTierIndex: -1, // track tier sebelumnya untuk deteksi tier change

    _abortControllers: {
        kategori: null,
        penggunaan: null,
        jenisHak: null,
        jenisHakAdat: null,
        statusKesesuaian: null,
    },
};

// ── Geometry simplification (Ramer-Douglas-Peucker) ──────────────────────────

/**
 * Simplifikasi satu ring koordinat dengan algoritma Ramer-Douglas-Peucker.
 * @param {number[][]} points  - array of [lng, lat]
 * @param {number}     tolerance
 * @returns {number[][]}
 */
function simplifyRing(points, tolerance) {
    if (tolerance === 0 || points.length <= 4) return points;

    function perpendicularDist(p, a, b) {
        const dx = b[0] - a[0], dy = b[1] - a[1];
        if (dx === 0 && dy === 0) {
            return Math.hypot(p[0] - a[0], p[1] - a[1]);
        }
        return Math.abs(dy * p[0] - dx * p[1] + b[0] * a[1] - b[1] * a[0]) /
            Math.hypot(dx, dy);
    }

    function rdp(pts, start, end, tol, keep) {
        if (end - start <= 1) return;
        let maxDist = 0, maxIdx = start;
        for (let i = start + 1; i < end; i++) {
            const d = perpendicularDist(pts[i], pts[start], pts[end]);
            if (d > maxDist) { maxDist = d; maxIdx = i; }
        }
        if (maxDist > tol) {
            keep[maxIdx] = true;
            rdp(pts, start, maxIdx, tol, keep);
            rdp(pts, maxIdx, end, tol, keep);
        }
    }

    const keep = new Array(points.length).fill(false);
    keep[0] = true;
    keep[points.length - 1] = true;
    rdp(points, 0, points.length - 1, tolerance, keep);

    const result = points.filter((_, i) => keep[i]);
    // Pastikan ring tertutup dan minimal 4 titik
    if (result.length < 4) return points;
    return result;
}

/**
 * Simplifikasi satu GeoJSON feature (Polygon / MultiPolygon).
 * Feature lain dikembalikan apa adanya.
 */
function simplifyFeature(feature, tolerance) {
    if (tolerance === 0) return feature;
    const { type, coordinates } = feature.geometry;

    if (type === "Polygon") {
        return {
            ...feature,
            geometry: {
                type,
                coordinates: coordinates.map((ring) =>
                    simplifyRing(ring, tolerance)
                ),
            },
        };
    }

    if (type === "MultiPolygon") {
        return {
            ...feature,
            geometry: {
                type,
                coordinates: coordinates.map((poly) =>
                    poly.map((ring) => simplifyRing(ring, tolerance))
                ),
            },
        };
    }

    return feature;
}

// ── Init layer groups ─────────────────────────────────────────────────────────

function initLayerGroups() {
    const map = window.petaMap;
    if (!map) return;

    const makeLayer = (defaultColor) =>
        L.geoJSON(null, {
            renderer: _canvasRenderer,
            style: (feature) => {
                const warna = feature?.properties?._warna ?? defaultColor;
                return {
                    color: warna,
                    fillColor: warna,
                    fillOpacity: 0.25,
                    weight: 1.5,
                    opacity: 1,
                };
            },
            onEachFeature(feature, lyr) {
                const { no_bidang, no_persil, luas } = feature.properties ?? {};
                lyr.bindPopup(buildPopup(no_bidang, no_persil, luas), {
                    maxWidth: 220,
                });
            },
        }).addTo(map);

    document.querySelectorAll(".layer-kategori-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.kategoriId);
        if (!id || petaLayers.groups[id]) return;
        petaLayers.groups[id] = { layer: makeLayer("#3b82f6"), opacity: 1 };
    });

    document.querySelectorAll(".layer-penggunaan-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.penggunaanId);
        if (!id || petaLayers.penggunaanGroups[id]) return;
        petaLayers.penggunaanGroups[id] = { layer: makeLayer("#10b981"), opacity: 1 };
    });

    document.querySelectorAll(".layer-jenis-hak-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.jenisHakId);
        if (!id || petaLayers.jenisHakGroups[id]) return;
        petaLayers.jenisHakGroups[id] = { layer: makeLayer("#f59e0b"), opacity: 1 };
    });

    document.querySelectorAll(".layer-jenis-hak-adat-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.jenisHakAdatId);
        if (!id || petaLayers.jenisHakAdatGroups[id]) return;
        petaLayers.jenisHakAdatGroups[id] = { layer: makeLayer("#8b5cf6"), opacity: 1 };
    });

    document.querySelectorAll(".layer-status-kesesuaian-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.statusKesesuaianId);
        if (!id || petaLayers.statusKesesuaianGroups[id]) return;
        petaLayers.statusKesesuaianGroups[id] = { layer: makeLayer("#ef4444"), opacity: 1 };
    });
}

// ── Init ──────────────────────────────────────────────────────────────────────

function initPetaLayers() {
    initLayerGroups();

    window.petaMap.on("moveend zoomend", () => {
        clearTimeout(petaLayers._fetchTimer);
        petaLayers._fetchTimer = setTimeout(() => {
            const zoom = window.petaMap.getZoom();

            if (zoom < MIN_ZOOM_BIDANG) {
                clearAllBidangLayers();
                return;
            }

            const currentTier = getZoomTierIndex(zoom);
            const tierChanged = currentTier !== petaLayers._lastTierIndex;
            petaLayers._lastTierIndex = currentTier;

            const aktifKategori          = getKategoriAktif();
            const aktifPenggunaan        = getPenggunaanAktif();
            const aktifJenisHak          = getJenisHakAktif();
            const aktifJenisHakAdat      = getJenisHakAdatAktif();
            const aktifStatusKesesuaian  = getStatusKesesuaianAktif();

            // Jika tier berubah dan ada cache → re-render dengan tolerance baru (tanpa fetch)
            // Jika tidak ada cache atau bbox berubah → fetch ulang
            if (aktifKategori.length > 0)
                tierChanged && petaLayers._cache.kategori
                    ? rerenderFromCache("kategori", petaLayers.groups, "kategori_id", "_warna")
                    : fetchDanRenderBidang(aktifKategori);

            if (aktifPenggunaan.length > 0)
                tierChanged && petaLayers._cache.penggunaan
                    ? rerenderFromCache("penggunaan", petaLayers.penggunaanGroups, "penggunaan_id", "_warna")
                    : fetchDanRenderBidangPenggunaan(aktifPenggunaan);

            if (aktifJenisHak.length > 0)
                tierChanged && petaLayers._cache.jenisHak
                    ? rerenderFromCache("jenisHak", petaLayers.jenisHakGroups, "jenis_hak_id", "_warna")
                    : fetchDanRenderBidangJenisHak(aktifJenisHak);

            if (aktifJenisHakAdat.length > 0)
                tierChanged && petaLayers._cache.jenisHakAdat
                    ? rerenderFromCache("jenisHakAdat", petaLayers.jenisHakAdatGroups, "jenis_hak_adat_id", "_warna")
                    : fetchDanRenderBidangJenisHakAdat(aktifJenisHakAdat);

            if (aktifStatusKesesuaian.length > 0)
                tierChanged && petaLayers._cache.statusKesesuaian
                    ? rerenderFromCache("statusKesesuaian", petaLayers.statusKesesuaianGroups, "status_kesesuaian_id", "_warna")
                    : fetchDanRenderBidangStatusKesesuaian(aktifStatusKesesuaian);

        }, 600);
    });
}

// ── Re-render dari cache (tier change, tanpa fetch) ───────────────────────────

function rerenderFromCache(cacheKey, registryMap, idProp, warnaProp) {
    const cached = petaLayers._cache[cacheKey];
    if (!cached) return;

    const zoom = window.petaMap.getZoom();
    const tolerance = getTolerance(zoom);

    // Kelompokkan features per id
    const featuresByGroup = {};
    cached.geojson.features?.forEach((feature) => {
        const id = feature.properties[idProp];
        if (!featuresByGroup[id]) featuresByGroup[id] = [];
        featuresByGroup[id].push(simplifyFeature(feature, tolerance));
    });

    Object.entries(featuresByGroup).forEach(([id, features]) => {
        const reg = registryMap[parseInt(id)];
        if (!reg) return;
        reg.layer.clearLayers();
        if (features.length > 0)
            reg.layer.addData({ type: "FeatureCollection", features });
    });
}

// ── Clear all ─────────────────────────────────────────────────────────────────

function clearAllBidangLayers() {
    const clearReg = (registry) => {
        Object.values(registry).forEach((r) => r?.layer?.clearLayers?.());
    };
    clearReg(petaLayers.groups);
    clearReg(petaLayers.penggunaanGroups);
    clearReg(petaLayers.jenisHakGroups);
    clearReg(petaLayers.jenisHakAdatGroups);
    clearReg(petaLayers.statusKesesuaianGroups);

    document.querySelectorAll(".bidang-count").forEach((el) => {
        el.textContent = "Perbesar peta untuk memuat bidang";
    });
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function getKategoriAktif() {
    return [...document.querySelectorAll(".layer-kategori-cb:checked")]
        .map((cb) => parseInt(cb.dataset.kategoriId)).filter(Boolean);
}
function getPenggunaanAktif() {
    return [...document.querySelectorAll(".layer-penggunaan-cb:checked")]
        .map((cb) => parseInt(cb.dataset.penggunaanId)).filter(Boolean);
}
function getJenisHakAktif() {
    return [...document.querySelectorAll(".layer-jenis-hak-cb:checked")]
        .map((cb) => parseInt(cb.dataset.jenisHakId)).filter(Boolean);
}
function getJenisHakAdatAktif() {
    return [...document.querySelectorAll(".layer-jenis-hak-adat-cb:checked")]
        .map((cb) => parseInt(cb.dataset.jenisHakAdatId)).filter(Boolean);
}
function getStatusKesesuaianAktif() {
    return [...document.querySelectorAll(".layer-status-kesesuaian-cb:checked")]
        .map((cb) => parseInt(cb.dataset.statusKesesuaianId)).filter(Boolean);
}

function getBboxString() {
    if (!window.petaMap) return null;
    const b = window.petaMap.getBounds();
    return [b.getWest().toFixed(6), b.getSouth().toFixed(6),
            b.getEast().toFixed(6), b.getNorth().toFixed(6)].join(",");
}

function buildWilayahParams(params) {
    const { kabupaten_kode, kecamatan_kode, kelurahan_kode } = petaLayers.wilayah;
    if (kelurahan_kode)       params.set("kelurahan_kode", kelurahan_kode);
    else if (kecamatan_kode)  params.set("kecamatan_kode", kecamatan_kode);
    else if (kabupaten_kode)  params.set("kabupaten_kode", kabupaten_kode);
    else { const bbox = getBboxString(); if (bbox) params.set("bbox", bbox); }
}

function setLayerCountLabel(kategoriId, count) {
    const groupEl = document.querySelector(`[data-group-id="kategori-${kategoriId}"]`);
    if (!groupEl) return;
    const label = groupEl.querySelector(".bidang-count");
    if (label) label.textContent = `${count.toLocaleString("id-ID")} bidang dimuat`;
}

function setLoadingState(kategoriIds, loading) {
    kategoriIds.forEach((id) => {
        const groupEl = document.querySelector(`[data-group-id="kategori-${id}"]`);
        if (!groupEl) return;
        const label = groupEl.querySelector(".bidang-count");
        if (label && loading) label.textContent = "Memuat\u2026";
    });
}

function updateGroupCheckboxState(groupCbId, cbSelector) {
    const groupCb = document.getElementById(groupCbId);
    if (!groupCb) return;
    const total   = document.querySelectorAll(cbSelector).length;
    const checked = document.querySelectorAll(`${cbSelector}:checked`).length;
    if (checked === 0)          { groupCb.checked = false; groupCb.indeterminate = false; }
    else if (checked === total) { groupCb.checked = true;  groupCb.indeterminate = false; }
    else                        { groupCb.checked = false; groupCb.indeterminate = true;  }
}

function freshAbortController(key) {
    petaLayers._abortControllers[key]?.abort();
    const ctrl = new AbortController();
    petaLayers._abortControllers[key] = ctrl;
    return ctrl;
}

function isZoomCukup() {
    return window.petaMap && window.petaMap.getZoom() >= MIN_ZOOM_BIDANG;
}

// ── Render helper (dipakai semua fetch functions) ─────────────────────────────

/**
 * Distribute GeoJSON features ke registry map masing-masing,
 * simplifikasi per zoom, lalu addData sekaligus.
 */
function distributeFeatures(geojson, registryMap, idProp) {
    const zoom      = window.petaMap.getZoom();
    const tolerance = getTolerance(zoom);

    const featuresByGroup = {};
    geojson.features?.forEach((feature) => {
        const id = feature.properties[idProp];
        if (!featuresByGroup[id]) featuresByGroup[id] = [];
        featuresByGroup[id].push(simplifyFeature(feature, tolerance));
    });

    let totalCount = 0;
    Object.entries(featuresByGroup).forEach(([id, features]) => {
        const reg = registryMap[parseInt(id)];
        if (!reg) return;
        reg.layer.clearLayers();
        if (features.length > 0)
            reg.layer.addData({ type: "FeatureCollection", features });
        totalCount += features.length;
    });

    return { featuresByGroup, totalCount };
}

// ── Fetch & Render: Kategori ──────────────────────────────────────────────────

async function fetchDanRenderBidang(kategoriIds) {
    if (!kategoriIds.length) return;

    const ctrl = freshAbortController("kategori");
    kategoriIds.forEach((id) => petaLayers.groups[id]?.layer.clearLayers());
    setLoadingState(kategoriIds, true);

    const params = new URLSearchParams();
    kategoriIds.forEach((id) => params.append("kategori_ids[]", id));
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`, { signal: ctrl.signal });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const geojson = await res.json();

        // Tag warna ke properti _warna agar style() bisa mengaksesnya
        geojson.features?.forEach((f) => {
            f.properties._warna = f.properties.warna ?? "#3b82f6";
        });

        // Simpan cache raw (sebelum simplifikasi)
        petaLayers._cache.kategori = { ids: kategoriIds, geojson };

        const counts = {};
        kategoriIds.forEach((id) => { counts[id] = 0; });

        const featuresByKategori = {};
        const zoom      = window.petaMap.getZoom();
        const tolerance = getTolerance(zoom);

        geojson.features?.forEach((feature) => {
            const id = feature.properties.kategori_id;
            if (!featuresByKategori[id]) featuresByKategori[id] = [];
            featuresByKategori[id].push(simplifyFeature(feature, tolerance));
        });

        kategoriIds.forEach((id) => {
            const reg = petaLayers.groups[id];
            if (!reg) return;
            const features = featuresByKategori[id] ?? [];
            if (features.length > 0)
                reg.layer.addData({ type: "FeatureCollection", features });
            counts[id] = features.length;
            setLayerCountLabel(id, counts[id]);
        });
    } catch (err) {
        if (err.name === "AbortError") return;
        console.error("[peta-layers] fetchDanRenderBidang error:", err);
        kategoriIds.forEach((id) => {
            const label = document.querySelector(`[data-group-id="kategori-${id}"] .bidang-count`);
            if (label) label.textContent = "Gagal memuat data.";
        });
    }
}

// ── Fetch & Render: Penggunaan ────────────────────────────────────────────────

async function fetchDanRenderBidangPenggunaan(penggunaanIds) {
    if (!penggunaanIds.length) return;

    const ctrl = freshAbortController("penggunaan");
    penggunaanIds.forEach((id) => petaLayers.penggunaanGroups[id]?.layer.clearLayers());

    const params = new URLSearchParams();
    penggunaanIds.forEach((id) => params.append("penggunaan_ids[]", id));
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`, { signal: ctrl.signal });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const geojson = await res.json();

        geojson.features?.forEach((f) => {
            f.properties._warna = f.properties.penggunaan_warna ?? "#10b981";
        });
        petaLayers._cache.penggunaan = { ids: penggunaanIds, geojson };

        const zoom = window.petaMap.getZoom();
        const tolerance = getTolerance(zoom);
        const featuresByPenggunaan = {};

        geojson.features?.forEach((feature) => {
            const id = feature.properties.penggunaan_id;
            if (!featuresByPenggunaan[id]) featuresByPenggunaan[id] = [];
            featuresByPenggunaan[id].push(simplifyFeature(feature, tolerance));
        });

        penggunaanIds.forEach((id) => {
            const reg = petaLayers.penggunaanGroups[id];
            if (!reg) return;
            const features = featuresByPenggunaan[id] ?? [];
            if (features.length > 0)
                reg.layer.addData({ type: "FeatureCollection", features });
        });
    } catch (err) {
        if (err.name === "AbortError") return;
        console.error("[peta-layers] fetchDanRenderBidangPenggunaan error:", err);
    }
}

// ── Fetch & Render: Jenis Hak ─────────────────────────────────────────────────

async function fetchDanRenderBidangJenisHak(jenisHakIds) {
    if (!jenisHakIds.length) return;

    const ctrl = freshAbortController("jenisHak");
    jenisHakIds.forEach((id) => petaLayers.jenisHakGroups[id]?.layer.clearLayers());

    const params = new URLSearchParams();
    jenisHakIds.forEach((id) => params.append("jenis_hak_ids[]", id));
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`, { signal: ctrl.signal });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const geojson = await res.json();

        geojson.features?.forEach((f) => {
            f.properties._warna = f.properties.jenis_hak_warna ?? "#f59e0b";
        });
        petaLayers._cache.jenisHak = { ids: jenisHakIds, geojson };

        const zoom = window.petaMap.getZoom();
        const tolerance = getTolerance(zoom);
        const featuresByJenisHak = {};

        geojson.features?.forEach((feature) => {
            const id = feature.properties.jenis_hak_id;
            if (!featuresByJenisHak[id]) featuresByJenisHak[id] = [];
            featuresByJenisHak[id].push(simplifyFeature(feature, tolerance));
        });

        jenisHakIds.forEach((id) => {
            const reg = petaLayers.jenisHakGroups[id];
            if (!reg) return;
            const features = featuresByJenisHak[id] ?? [];
            if (features.length > 0)
                reg.layer.addData({ type: "FeatureCollection", features });
        });
    } catch (err) {
        if (err.name === "AbortError") return;
        console.error("[peta-layers] fetchDanRenderBidangJenisHak error:", err);
    }
}

// ── Fetch & Render: Jenis Hak Adat ───────────────────────────────────────────

async function fetchDanRenderBidangJenisHakAdat(jenisHakAdatIds) {
    if (!jenisHakAdatIds.length) return;

    const ctrl = freshAbortController("jenisHakAdat");
    jenisHakAdatIds.forEach((id) => petaLayers.jenisHakAdatGroups[id]?.layer.clearLayers());

    const params = new URLSearchParams();
    jenisHakAdatIds.forEach((id) => params.append("jenis_hak_adat_ids[]", id));
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`, { signal: ctrl.signal });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const geojson = await res.json();

        geojson.features?.forEach((f) => {
            f.properties._warna = f.properties.jenis_hak_adat_warna ?? "#8b5cf6";
        });
        petaLayers._cache.jenisHakAdat = { ids: jenisHakAdatIds, geojson };

        const zoom = window.petaMap.getZoom();
        const tolerance = getTolerance(zoom);
        const featuresByJenisHakAdat = {};

        geojson.features?.forEach((feature) => {
            const id = feature.properties.jenis_hak_adat_id;
            if (!featuresByJenisHakAdat[id]) featuresByJenisHakAdat[id] = [];
            featuresByJenisHakAdat[id].push(simplifyFeature(feature, tolerance));
        });

        jenisHakAdatIds.forEach((id) => {
            const reg = petaLayers.jenisHakAdatGroups[id];
            if (!reg) return;
            const features = featuresByJenisHakAdat[id] ?? [];
            if (features.length > 0)
                reg.layer.addData({ type: "FeatureCollection", features });
        });
    } catch (err) {
        if (err.name === "AbortError") return;
        console.error("[peta-layers] fetchDanRenderBidangJenisHakAdat error:", err);
    }
}

// ── Fetch & Render: Status Kesesuaian ────────────────────────────────────────

async function fetchDanRenderBidangStatusKesesuaian(statusKesesuaianIds) {
    if (!statusKesesuaianIds.length) return;

    const ctrl = freshAbortController("statusKesesuaian");
    statusKesesuaianIds.forEach((id) => petaLayers.statusKesesuaianGroups[id]?.layer.clearLayers());

    const params = new URLSearchParams();
    statusKesesuaianIds.forEach((id) => params.append("status_kesesuaian_ids[]", id));
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`, { signal: ctrl.signal });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const geojson = await res.json();

        geojson.features?.forEach((f) => {
            f.properties._warna = f.properties.status_kesesuaian_warna ?? "#ef4444";
        });
        petaLayers._cache.statusKesesuaian = { ids: statusKesesuaianIds, geojson };

        const zoom = window.petaMap.getZoom();
        const tolerance = getTolerance(zoom);
        const featuresByStatus = {};

        geojson.features?.forEach((feature) => {
            const id = feature.properties.status_kesesuaian_id;
            if (!featuresByStatus[id]) featuresByStatus[id] = [];
            featuresByStatus[id].push(simplifyFeature(feature, tolerance));
        });

        statusKesesuaianIds.forEach((id) => {
            const reg = petaLayers.statusKesesuaianGroups[id];
            if (!reg) return;
            const features = featuresByStatus[id] ?? [];
            if (features.length > 0)
                reg.layer.addData({ type: "FeatureCollection", features });
        });
    } catch (err) {
        if (err.name === "AbortError") return;
        console.error("[peta-layers] fetchDanRenderBidangStatusKesesuaian error:", err);
    }
}

// ── Popup builder ─────────────────────────────────────────────────────────────

function buildPopup(no_bidang, no_persil, luas) {
    return `
        <div style="min-width:160px; font-size:12px; line-height:1.6;">
            <p style="font-weight:600; margin:0 0 4px;">${escHtml(no_bidang ?? "-")}</p>
            <p style="color:#6b7280; margin:0;">Persil: ${escHtml(no_persil ?? "-")}</p>
            ${luas ? `<p style="color:#6b7280; margin:0;">Luas: ${Number(luas).toLocaleString("id-ID")} m\u00B2</p>` : ""}
        </div>
    `;
}

// ── Event handlers ────────────────────────────────────────────────────────────

function onGroupChange(groupCb, groupName) {
    const isChecked = groupCb.checked;
    const map = {
        "kategori":           [".layer-kategori-cb",           onKategoriChange],
        "penggunaan":         [".layer-penggunaan-cb",         onPenggunaanChange],
        "jenis-hak":          [".layer-jenis-hak-cb",          onJenisHakChange],
        "jenis-hak-adat":     [".layer-jenis-hak-adat-cb",     onJenisHakAdatChange],
        "status-kesesuaian":  [".layer-status-kesesuaian-cb",  onStatusKesesuaianChange],
    };
    const entry = map[groupName];
    if (!entry) return;
    document.querySelectorAll(entry[0]).forEach((cb) => { cb.checked = isChecked; });
    entry[1]();
}

function onKategoriChange() {
    const aktif = getKategoriAktif();
    document.querySelectorAll(".layer-kategori-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.kategoriId);
        if (!id) return;
        if (!cb.checked) { petaLayers.groups[id]?.layer.clearLayers(); }
    });
    updateGroupCheckboxState("cb-group-kategori", ".layer-kategori-cb");
    if (aktif.length > 0 && isZoomCukup()) fetchDanRenderBidang(aktif);
}

function onPenggunaanChange() {
    const aktif = getPenggunaanAktif();
    document.querySelectorAll(".layer-penggunaan-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.penggunaanId);
        if (!id) return;
        if (!cb.checked) petaLayers.penggunaanGroups[id]?.layer.clearLayers();
    });
    updateGroupCheckboxState("cb-group-penggunaan", ".layer-penggunaan-cb");
    if (aktif.length > 0 && isZoomCukup()) fetchDanRenderBidangPenggunaan(aktif);
}

function onJenisHakChange() {
    const aktif = getJenisHakAktif();
    document.querySelectorAll(".layer-jenis-hak-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.jenisHakId);
        if (!id) return;
        if (!cb.checked) petaLayers.jenisHakGroups[id]?.layer.clearLayers();
    });
    updateGroupCheckboxState("cb-group-jenis-hak", ".layer-jenis-hak-cb");
    if (aktif.length > 0 && isZoomCukup()) fetchDanRenderBidangJenisHak(aktif);
}

function onJenisHakAdatChange() {
    const aktif = getJenisHakAdatAktif();
    document.querySelectorAll(".layer-jenis-hak-adat-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.jenisHakAdatId);
        if (!id) return;
        if (!cb.checked) petaLayers.jenisHakAdatGroups[id]?.layer.clearLayers();
    });
    updateGroupCheckboxState("cb-group-jenis-hak-adat", ".layer-jenis-hak-adat-cb");
    if (aktif.length > 0 && isZoomCukup()) fetchDanRenderBidangJenisHakAdat(aktif);
}

function onStatusKesesuaianChange() {
    const aktif = getStatusKesesuaianAktif();
    document.querySelectorAll(".layer-status-kesesuaian-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.statusKesesuaianId);
        if (!id) return;
        if (!cb.checked) petaLayers.statusKesesuaianGroups[id]?.layer.clearLayers();
    });
    updateGroupCheckboxState("cb-group-status-kesesuaian", ".layer-status-kesesuaian-cb");
    if (aktif.length > 0 && isZoomCukup()) fetchDanRenderBidangStatusKesesuaian(aktif);
}

// ── Opacity ───────────────────────────────────────────────────────────────────

function updateOpacity(rangeEl, groupId) {
    const val = rangeEl.value / 100;

    const opacityLabel = rangeEl.closest(".flex.items-center.gap-2")?.querySelector(".opacity-val");
    if (opacityLabel) opacityLabel.textContent = `${rangeEl.value}%`;

    const applyOpacity = (registryMap, ids) => {
        ids.forEach((id) => {
            const r = registryMap[id];
            if (!r) return;
            r.opacity = val;
            r.layer.setStyle({ fillOpacity: 0.25 * val, opacity: val });
        });
    };

    const opacityMap = {
        "group-kategori":          [petaLayers.groups,                    getKategoriAktif],
        "group-penggunaan":        [petaLayers.penggunaanGroups,          getPenggunaanAktif],
        "group-jenis-hak":         [petaLayers.jenisHakGroups,            getJenisHakAktif],
        "group-jenis-hak-adat":    [petaLayers.jenisHakAdatGroups,        getJenisHakAdatAktif],
        "group-status-kesesuaian": [petaLayers.statusKesesuaianGroups,    getStatusKesesuaianAktif],
    };

    if (opacityMap[groupId]) {
        const [reg, getter] = opacityMap[groupId];
        applyOpacity(reg, getter());
        return;
    }

    // Fallback: individual kategori layer
    const id = parseInt(groupId.replace("kategori-", ""));
    const reg = petaLayers.groups[id];
    if (!reg) return;
    reg.opacity = val;
    reg.layer.setStyle({ fillOpacity: 0.25 * val, opacity: val });
}

// ── Toggle group chevron ──────────────────────────────────────────────────────

function toggleGroup(headerEl) {
    const groupEl  = headerEl.closest(".layer-group");
    const children = groupEl?.querySelector(".layer-children");
    const chevron  = headerEl.querySelector(".chevron");
    if (!children) return;
    const isOpen = !children.classList.contains("hidden");
    children.classList.toggle("hidden", isOpen);
    chevron?.classList.toggle("rotate-90", !isOpen);
}

// ── Escape helpers ────────────────────────────────────────────────────────────

function escHtml(str) {
    return String(str ?? "")
        .replace(/&/g, "&amp;").replace(/</g, "&lt;")
        .replace(/>/g, "&gt;").replace(/"/g, "&quot;");
}

function escAttr(str) {
    return String(str ?? "").replace(/"/g, "&quot;");
}

// ── Bootstrap ─────────────────────────────────────────────────────────────────

if (window.petaMap) {
    initPetaLayers();
} else {
    window.addEventListener("petaMapReady", () => initPetaLayers(), { once: true });
}
