/**
 * peta-layers.js  (OPTIMIZED)
 *
 * Tanggung jawab:
 *   1. Inisialisasi L.geoJSON layer untuk setiap item layer di sidebar
 *   2. Mendengarkan perubahan checkbox layer di sidebar
 *   3. Mendengarkan perubahan filter wilayah (kabupaten/kecamatan/kelurahan)
 *   4. Mendengarkan event moveend/zoomend Leaflet (bbox berubah)
 *   5. Fetch GET /api/v1/peta/bidang dengan params yang sesuai
 *   6. Render GeoJSON FeatureCollection ke Leaflet, warna per kategori/penggunaan/dll
 *   7. Cleanup polygon lama sebelum render ulang
 *
 * Layer types yang didukung (5 jenis):
 *   - kategori            → groups,                .layer-kategori-cb,            data-kategori-id,            cb-group-kategori
 *   - penggunaan          → penggunaanGroups,       .layer-penggunaan-cb,          data-penggunaan-id,          cb-group-penggunaan
 *   - jenis-hak           → jenisHakGroups,         .layer-jenis-hak-cb,           data-jenis-hak-id,           cb-group-jenis-hak
 *   - jenis-hak-adat      → jenisHakAdatGroups,     .layer-jenis-hak-adat-cb,      data-jenis-hak-adat-id,      cb-group-jenis-hak-adat
 *   - status-kesesuaian   → statusKesesuaianGroups, .layer-status-kesesuaian-cb,   data-status-kesesuaian-id,   cb-group-status-kesesuaian
 *
 * Dependensi global (dari map.js):
 *   window.petaMap  — instance L.Map
 *
 * ══════════════════════════════════════════════════════════════════════════════
 * OPTIMASI:
 *   1. ZOOM GATE (MIN_ZOOM_BIDANG = 14) — fetch hanya dilakukan saat zoom ≥ 14
 *      (skala ≤ 1:17.000). Di zoom 13 (skala 1:34.000) bidang dikosongkan + pesan
 *      "perbesar peta" ditampilkan.
 *   2. CANVAS RENDERER — menggunakan L.canvas() menggantikan SVG default Leaflet.
 *      Jauh lebih efisien untuk 10.000+ polygon (satu <canvas> vs ribuan <path> di DOM).
 *   3. BATCH L.geoJSON — satu L.geoJSON per layer-type (bukan satu per feature).
 *      Mengurangi overhead Leaflet dari O(n) layer objects menjadi O(1) per tipe.
 *   4. AbortController — request sebelumnya dibatalkan saat fetch baru dimulai.
 *      Mencegah race condition dan trafik jaringan yang terbuang.
 *   5. DEBOUNCE 600 ms — dipertahankan.
 * ══════════════════════════════════════════════════════════════════════════════
 */

"use strict";

// ── Konstanta ─────────────────────────────────────────────────────────────────

/**
 * Zoom minimum sebelum layer bidang dimuat.
 * zoom 14 ≈ skala 1:17.000 — bidang tanah sudah cukup besar untuk ditampilkan.
 * Di zoom 13 (skala 1:34.000) bidang terlalu kecil & jumlahnya terlalu banyak.
 */
const MIN_ZOOM_BIDANG = 14;

/**
 * Canvas renderer bersama — jauh lebih efisien daripada SVG default Leaflet
 * saat menampilkan ribuan polygon sekaligus.
 */
const _canvasRenderer = L.canvas({ padding: 0.5 });

// ── State ────────────────────────────────────────────────────────────────────

const petaLayers = {
    // L.GeoJSON layer per kategori_id  → { [id]: { layer: L.GeoJSON, opacity: 1 } }
    groups: {},

    // L.GeoJSON layer per penggunaan_id
    penggunaanGroups: {},

    // L.GeoJSON layer per jenis_hak_id
    jenisHakGroups: {},

    // L.GeoJSON layer per jenis_hak_adat_id
    jenisHakAdatGroups: {},

    // L.GeoJSON layer per status_kesesuaian_id
    statusKesesuaianGroups: {},

    // Filter wilayah aktif
    wilayah: {
        kabupaten_kode: null,
        kecamatan_kode: null,
        kelurahan_kode: null,
    },

    // Debounce timer untuk moveend
    _fetchTimer: null,

    // AbortController aktif per fetch-type (untuk membatalkan request lama)
    _abortControllers: {
        kategori: null,
        penggunaan: null,
        jenisHak: null,
        jenisHakAdat: null,
        statusKesesuaian: null,
    },
};

// ── Inisialisasi Layer dari DOM ───────────────────────────────────────────────
//
// Menggunakan L.geoJSON (bukan L.featureGroup) agar polygon dapat di-render
// sekaligus dalam satu layer object — jauh lebih efisien saat ada 100rb+ bidang.
// Canvas renderer digunakan agar tidak ada <path> SVG per polygon di DOM.

function initLayerGroups() {
    const map = window.petaMap;
    if (!map) return;

    // Helper: buat L.geoJSON dengan canvas renderer
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

    // Kategori
    document.querySelectorAll(".layer-kategori-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.kategoriId);
        if (!id || petaLayers.groups[id]) return;
        petaLayers.groups[id] = { layer: makeLayer("#3b82f6"), opacity: 1 };
    });

    // Penggunaan
    document.querySelectorAll(".layer-penggunaan-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.penggunaanId);
        if (!id || petaLayers.penggunaanGroups[id]) return;
        petaLayers.penggunaanGroups[id] = {
            layer: makeLayer("#10b981"),
            opacity: 1,
        };
    });

    // Jenis Hak
    document.querySelectorAll(".layer-jenis-hak-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.jenisHakId);
        if (!id || petaLayers.jenisHakGroups[id]) return;
        petaLayers.jenisHakGroups[id] = {
            layer: makeLayer("#f59e0b"),
            opacity: 1,
        };
    });

    // Jenis Hak Adat
    document.querySelectorAll(".layer-jenis-hak-adat-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.jenisHakAdatId);
        if (!id || petaLayers.jenisHakAdatGroups[id]) return;
        petaLayers.jenisHakAdatGroups[id] = {
            layer: makeLayer("#8b5cf6"),
            opacity: 1,
        };
    });

    // Status Kesesuaian
    document.querySelectorAll(".layer-status-kesesuaian-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.statusKesesuaianId);
        if (!id || petaLayers.statusKesesuaianGroups[id]) return;
        petaLayers.statusKesesuaianGroups[id] = {
            layer: makeLayer("#ef4444"),
            opacity: 1,
        };
    });
}

// ── Zoom gate helper ──────────────────────────────────────────────────────────

/**
 * Kembalikan true jika zoom saat ini cukup tinggi untuk memuat bidang.
 */
function isZoomCukup() {
    if (!window.petaMap) return false;
    return window.petaMap.getZoom() >= MIN_ZOOM_BIDANG;
}

/**
 * Kosongkan semua layer bidang yang aktif dan tampilkan pesan zoom.
 * Dipanggil saat zoom < MIN_ZOOM_BIDANG.
 */
function clearAllBidangLayers() {
    const clearReg = (registry) => {
        Object.values(registry).forEach((r) => r?.layer?.clearLayers?.());
    };
    clearReg(petaLayers.groups);
    clearReg(petaLayers.penggunaanGroups);
    clearReg(petaLayers.jenisHakGroups);
    clearReg(petaLayers.jenisHakAdatGroups);
    clearReg(petaLayers.statusKesesuaianGroups);

    // Tampilkan pesan "zoom lebih dekat" di semua label count yang terlihat
    document.querySelectorAll(".bidang-count").forEach((el) => {
        el.textContent = "Perbesar peta untuk memuat bidang";
    });
}

// ── Init ─────────────────────────────────────────────────────────────────────

function initPetaLayers() {
    initLayerGroups();

    window.petaMap.on("moveend zoomend", () => {
        clearTimeout(petaLayers._fetchTimer);
        petaLayers._fetchTimer = setTimeout(() => {
            if (!isZoomCukup()) {
                clearAllBidangLayers();
                return;
            }

            const aktifKategori = getKategoriAktif();
            const aktifPenggunaan = getPenggunaanAktif();
            const aktifJenisHak = getJenisHakAktif();
            const aktifJenisHakAdat = getJenisHakAdatAktif();
            const aktifStatusKesesuaian = getStatusKesesuaianAktif();

            if (aktifKategori.length > 0) fetchDanRenderBidang(aktifKategori);
            if (aktifPenggunaan.length > 0)
                fetchDanRenderBidangPenggunaan(aktifPenggunaan);
            if (aktifJenisHak.length > 0)
                fetchDanRenderBidangJenisHak(aktifJenisHak);
            if (aktifJenisHakAdat.length > 0)
                fetchDanRenderBidangJenisHakAdat(aktifJenisHakAdat);
            if (aktifStatusKesesuaian.length > 0)
                fetchDanRenderBidangStatusKesesuaian(aktifStatusKesesuaian);
        }, 600);
    });
}

// ── Helpers: getter checkbox aktif ───────────────────────────────────────────

function getKategoriAktif() {
    return [...document.querySelectorAll(".layer-kategori-cb:checked")]
        .map((cb) => parseInt(cb.dataset.kategoriId))
        .filter(Boolean);
}

function getPenggunaanAktif() {
    return [...document.querySelectorAll(".layer-penggunaan-cb:checked")]
        .map((cb) => parseInt(cb.dataset.penggunaanId))
        .filter(Boolean);
}

function getJenisHakAktif() {
    return [...document.querySelectorAll(".layer-jenis-hak-cb:checked")]
        .map((cb) => parseInt(cb.dataset.jenisHakId))
        .filter(Boolean);
}

function getJenisHakAdatAktif() {
    return [...document.querySelectorAll(".layer-jenis-hak-adat-cb:checked")]
        .map((cb) => parseInt(cb.dataset.jenisHakAdatId))
        .filter(Boolean);
}

function getStatusKesesuaianAktif() {
    return [...document.querySelectorAll(".layer-status-kesesuaian-cb:checked")]
        .map((cb) => parseInt(cb.dataset.statusKesesuaianId))
        .filter(Boolean);
}

// ── Helpers: UI ───────────────────────────────────────────────────────────────

function getBboxString() {
    if (!window.petaMap) return null;
    const b = window.petaMap.getBounds();
    return [
        b.getWest().toFixed(6),
        b.getSouth().toFixed(6),
        b.getEast().toFixed(6),
        b.getNorth().toFixed(6),
    ].join(",");
}

function buildWilayahParams(params) {
    const { kabupaten_kode, kecamatan_kode, kelurahan_kode } =
        petaLayers.wilayah;
    if (kelurahan_kode) {
        params.set("kelurahan_kode", kelurahan_kode);
    } else if (kecamatan_kode) {
        params.set("kecamatan_kode", kecamatan_kode);
    } else if (kabupaten_kode) {
        params.set("kabupaten_kode", kabupaten_kode);
    } else {
        const bbox = getBboxString();
        if (bbox) params.set("bbox", bbox);
    }
}

function setLayerCountLabel(kategoriId, count) {
    const groupEl = document.querySelector(
        `[data-group-id="kategori-${kategoriId}"]`,
    );
    if (!groupEl) return;
    const label = groupEl.querySelector(".bidang-count");
    if (label)
        label.textContent = `${count.toLocaleString("id-ID")} bidang dimuat`;
}

function setLoadingState(kategoriIds, loading) {
    kategoriIds.forEach((id) => {
        const groupEl = document.querySelector(
            `[data-group-id="kategori-${id}"]`,
        );
        if (!groupEl) return;
        const label = groupEl.querySelector(".bidang-count");
        if (label && loading) label.textContent = "Memuat\u2026";
    });
}

function updateGroupCheckboxState(groupCbId, cbSelector) {
    const groupCb = document.getElementById(groupCbId);
    if (!groupCb) return;
    const total = document.querySelectorAll(cbSelector).length;
    const checked = document.querySelectorAll(`${cbSelector}:checked`).length;
    if (checked === 0) {
        groupCb.checked = false;
        groupCb.indeterminate = false;
    } else if (checked === total) {
        groupCb.checked = true;
        groupCb.indeterminate = false;
    } else {
        groupCb.checked = false;
        groupCb.indeterminate = true;
    }
}

// ── Abort helper ──────────────────────────────────────────────────────────────

/**
 * Batalkan request sebelumnya (jika ada) dan buat AbortController baru.
 * Menghindari race condition dan request duplikat yang terbuang.
 */
function freshAbortController(key) {
    petaLayers._abortControllers[key]?.abort();
    const ctrl = new AbortController();
    petaLayers._abortControllers[key] = ctrl;
    return ctrl;
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
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`, {
            signal: ctrl.signal,
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const geojson = await res.json();
        const counts = {};
        kategoriIds.forEach((id) => {
            counts[id] = 0;
        });

        // Kelompokkan features per kategori_id
        const featuresByKategori = {};
        geojson.features?.forEach((feature) => {
            const { kategori_id, warna } = feature.properties;
            feature.properties._warna = warna ?? "#3b82f6";
            if (!featuresByKategori[kategori_id])
                featuresByKategori[kategori_id] = [];
            featuresByKategori[kategori_id].push(feature);
        });

        // addData sekaligus per layer — satu operasi DOM
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
            const label = document.querySelector(
                `[data-group-id="kategori-${id}"] .bidang-count`,
            );
            if (label) label.textContent = "Gagal memuat data.";
        });
    }
}

// ── Fetch & Render: Penggunaan ────────────────────────────────────────────────

async function fetchDanRenderBidangPenggunaan(penggunaanIds) {
    if (!penggunaanIds.length) return;

    const ctrl = freshAbortController("penggunaan");
    penggunaanIds.forEach((id) =>
        petaLayers.penggunaanGroups[id]?.layer.clearLayers(),
    );

    const params = new URLSearchParams();
    penggunaanIds.forEach((id) => params.append("penggunaan_ids[]", id));
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`, {
            signal: ctrl.signal,
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const geojson = await res.json();
        const featuresByPenggunaan = {};

        geojson.features?.forEach((feature) => {
            const { penggunaan_id, penggunaan_warna } = feature.properties;
            feature.properties._warna = penggunaan_warna ?? "#10b981";
            if (!featuresByPenggunaan[penggunaan_id])
                featuresByPenggunaan[penggunaan_id] = [];
            featuresByPenggunaan[penggunaan_id].push(feature);
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
        console.error(
            "[peta-layers] fetchDanRenderBidangPenggunaan error:",
            err,
        );
    }
}

// ── Fetch & Render: Jenis Hak ─────────────────────────────────────────────────

async function fetchDanRenderBidangJenisHak(jenisHakIds) {
    if (!jenisHakIds.length) return;

    const ctrl = freshAbortController("jenisHak");
    jenisHakIds.forEach((id) =>
        petaLayers.jenisHakGroups[id]?.layer.clearLayers(),
    );

    const params = new URLSearchParams();
    jenisHakIds.forEach((id) => params.append("jenis_hak_ids[]", id));
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`, {
            signal: ctrl.signal,
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const geojson = await res.json();
        const featuresByJenisHak = {};

        geojson.features?.forEach((feature) => {
            const { jenis_hak_id, jenis_hak_warna } = feature.properties;
            feature.properties._warna = jenis_hak_warna ?? "#f59e0b";
            if (!featuresByJenisHak[jenis_hak_id])
                featuresByJenisHak[jenis_hak_id] = [];
            featuresByJenisHak[jenis_hak_id].push(feature);
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
    jenisHakAdatIds.forEach((id) =>
        petaLayers.jenisHakAdatGroups[id]?.layer.clearLayers(),
    );

    const params = new URLSearchParams();
    jenisHakAdatIds.forEach((id) => params.append("jenis_hak_adat_ids[]", id));
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`, {
            signal: ctrl.signal,
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const geojson = await res.json();
        const featuresByJenisHakAdat = {};

        geojson.features?.forEach((feature) => {
            const { jenis_hak_adat_id, jenis_hak_adat_warna } =
                feature.properties;
            feature.properties._warna = jenis_hak_adat_warna ?? "#8b5cf6";
            if (!featuresByJenisHakAdat[jenis_hak_adat_id])
                featuresByJenisHakAdat[jenis_hak_adat_id] = [];
            featuresByJenisHakAdat[jenis_hak_adat_id].push(feature);
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
        console.error(
            "[peta-layers] fetchDanRenderBidangJenisHakAdat error:",
            err,
        );
    }
}

// ── Fetch & Render: Status Kesesuaian ────────────────────────────────────────

async function fetchDanRenderBidangStatusKesesuaian(statusKesesuaianIds) {
    if (!statusKesesuaianIds.length) return;

    const ctrl = freshAbortController("statusKesesuaian");
    statusKesesuaianIds.forEach((id) =>
        petaLayers.statusKesesuaianGroups[id]?.layer.clearLayers(),
    );

    const params = new URLSearchParams();
    statusKesesuaianIds.forEach((id) =>
        params.append("status_kesesuaian_ids[]", id),
    );
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`, {
            signal: ctrl.signal,
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const geojson = await res.json();
        const featuresByStatus = {};

        geojson.features?.forEach((feature) => {
            const { status_kesesuaian_id, status_kesesuaian_warna } =
                feature.properties;
            feature.properties._warna = status_kesesuaian_warna ?? "#ef4444";
            if (!featuresByStatus[status_kesesuaian_id])
                featuresByStatus[status_kesesuaian_id] = [];
            featuresByStatus[status_kesesuaian_id].push(feature);
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
        console.error(
            "[peta-layers] fetchDanRenderBidangStatusKesesuaian error:",
            err,
        );
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

    if (groupName === "kategori") {
        document.querySelectorAll(".layer-kategori-cb").forEach((cb) => {
            cb.checked = isChecked;
        });
        onKategoriChange();
    } else if (groupName === "penggunaan") {
        document.querySelectorAll(".layer-penggunaan-cb").forEach((cb) => {
            cb.checked = isChecked;
        });
        onPenggunaanChange();
    } else if (groupName === "jenis-hak") {
        document.querySelectorAll(".layer-jenis-hak-cb").forEach((cb) => {
            cb.checked = isChecked;
        });
        onJenisHakChange();
    } else if (groupName === "jenis-hak-adat") {
        document.querySelectorAll(".layer-jenis-hak-adat-cb").forEach((cb) => {
            cb.checked = isChecked;
        });
        onJenisHakAdatChange();
    } else if (groupName === "status-kesesuaian") {
        document
            .querySelectorAll(".layer-status-kesesuaian-cb")
            .forEach((cb) => {
                cb.checked = isChecked;
            });
        onStatusKesesuaianChange();
    }
}

function onKategoriChange() {
    const aktif = getKategoriAktif();

    document.querySelectorAll(".layer-kategori-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.kategoriId);
        if (!id) return;
        if (!cb.checked) petaLayers.groups[id]?.layer.clearLayers();
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

    if (aktif.length > 0 && isZoomCukup())
        fetchDanRenderBidangPenggunaan(aktif);
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

    updateGroupCheckboxState(
        "cb-group-jenis-hak-adat",
        ".layer-jenis-hak-adat-cb",
    );

    if (aktif.length > 0 && isZoomCukup())
        fetchDanRenderBidangJenisHakAdat(aktif);
}

function onStatusKesesuaianChange() {
    const aktif = getStatusKesesuaianAktif();

    document.querySelectorAll(".layer-status-kesesuaian-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.statusKesesuaianId);
        if (!id) return;
        if (!cb.checked)
            petaLayers.statusKesesuaianGroups[id]?.layer.clearLayers();
    });

    updateGroupCheckboxState(
        "cb-group-status-kesesuaian",
        ".layer-status-kesesuaian-cb",
    );

    if (aktif.length > 0 && isZoomCukup())
        fetchDanRenderBidangStatusKesesuaian(aktif);
}

// ── Opacity ───────────────────────────────────────────────────────────────────

function updateOpacity(rangeEl, groupId) {
    const val = rangeEl.value / 100;

    const opacityLabel = rangeEl
        .closest(".flex.items-center.gap-2")
        ?.querySelector(".opacity-val");
    if (opacityLabel) opacityLabel.textContent = `${rangeEl.value}%`;

    const applyOpacity = (registryMap, ids) => {
        ids.forEach((id) => {
            const r = registryMap[id];
            if (!r) return;
            r.opacity = val;
            // L.geoJSON.setStyle() berlaku ke semua sub-layer sekaligus
            r.layer.setStyle({ fillOpacity: 0.25 * val, opacity: val });
        });
    };

    if (groupId === "group-kategori") {
        applyOpacity(petaLayers.groups, getKategoriAktif());
        return;
    }
    if (groupId === "group-penggunaan") {
        applyOpacity(petaLayers.penggunaanGroups, getPenggunaanAktif());
        return;
    }
    if (groupId === "group-jenis-hak") {
        applyOpacity(petaLayers.jenisHakGroups, getJenisHakAktif());
        return;
    }
    if (groupId === "group-jenis-hak-adat") {
        applyOpacity(petaLayers.jenisHakAdatGroups, getJenisHakAdatAktif());
        return;
    }
    if (groupId === "group-status-kesesuaian") {
        applyOpacity(
            petaLayers.statusKesesuaianGroups,
            getStatusKesesuaianAktif(),
        );
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
    const groupEl = headerEl.closest(".layer-group");
    const children = groupEl?.querySelector(".layer-children");
    const chevron = headerEl.querySelector(".chevron");

    if (!children) return;

    const isOpen = !children.classList.contains("hidden");
    children.classList.toggle("hidden", isOpen);
    chevron?.classList.toggle("rotate-90", !isOpen);
}

// ── Escape helpers ────────────────────────────────────────────────────────────

function escHtml(str) {
    return String(str ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

function escAttr(str) {
    return String(str ?? "").replace(/"/g, "&quot;");
}

// ── Bootstrap ─────────────────────────────────────────────────────────────────

if (window.petaMap) {
    initPetaLayers();
} else {
    window.addEventListener("petaMapReady", () => initPetaLayers(), {
        once: true,
    });
}
