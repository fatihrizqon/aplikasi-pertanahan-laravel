/**
 * peta-layers.js  (Vector Tiles edition — MapLibre GL JS)
 *
 * Arsitektur:
 *   - Menggunakan endpoint MVT  /api/v1/tiles/bidang/{z}/{x}/{y}
 *   - MapLibre menangani tile fetching, caching, dan WebGL rendering
 *   - Server-side ST_SimplifyPreserveTopology sesuai zoom (lihat VectorTileController)
 *   - Filter layer aktif dikirim sebagai query params pada tile URL
 *   - Popup interaktif saat klik feature
 *
 * Layer yang dikelola:
 *   bidang-fill   – polygon fill per warna kategori/penggunaan/jenis-hak
 *   bidang-line   – outline polygon bidang
 *   bidang-vertex – titik vertex saat polygon diklik
 *
 * Catatan kompatibilitas:
 *   - window.petaLayers.wilayah dibaca oleh wilayah.js
 *   - Event "wilayahChanged" ditrigger wilayah.js untuk memicu tile URL rebuild
 */

"use strict";

// ── Konstanta ─────────────────────────────────────────────────────────────────

const MIN_ZOOM_BIDANG = 10;  // harus sama dengan VectorTileController::MIN_ZOOM_BIDANG

// ── State global ──────────────────────────────────────────────────────────────

/**
 * @type {{
 *   wilayah: { kabupaten_kode: string|null, kecamatan_kode: string|null, kelurahan_kode: string|null },
 *   opacity: { kategori: number, penggunaan: number, jenisHak: number, jenisHakAdat: number, statusKesesuaian: number },
 *   activePopup: maplibregl.Popup|null,
 *   vertexData: GeoJSON.FeatureCollection|null,
 * }}
 */
window.petaLayers = window.petaLayers ?? {
    wilayah: {
        kabupaten_kode: null,
        kecamatan_kode: null,
        kelurahan_kode: null,
    },
    opacity: {
        kategori:          1,
        penggunaan:        1,
        jenisHak:          1,
        jenisHakAdat:      1,
        statusKesesuaian:  1,
    },
    activePopup: null,
    vertexData:  null,
};

// ── URL builder ───────────────────────────────────────────────────────────────

/**
 * Bangun tile URL dengan filter aktif sebagai query string.
 * MapLibre menggunakan placeholder {z}/{x}/{y} dalam URL template.
 */
function buildTileUrl() {
    const params = new URLSearchParams();
    const w      = window.petaLayers.wilayah;

    // Filter wilayah
    if (w.kelurahan_kode)      params.set("kelurahan_kode",  w.kelurahan_kode);
    else if (w.kecamatan_kode) params.set("kecamatan_kode",  w.kecamatan_kode);
    else if (w.kabupaten_kode) params.set("kabupaten_kode",  w.kabupaten_kode);

    // Filter layer aktif
    getKategoriAktif().forEach((id) => params.append("kategori_ids[]", id));
    getPenggunaanAktif().forEach((id) => params.append("penggunaan_ids[]", id));
    getJenisHakAktif().forEach((id) => params.append("jenis_hak_ids[]", id));
    getJenisHakAdatAktif().forEach((id) => params.append("jenis_hak_adat_ids[]", id));
    getStatusKesesuaianAktif().forEach((id) => params.append("status_kesesuaian_ids[]", id));

    const hasFilter = [
        ...getKategoriAktif(),
        ...getPenggunaanAktif(),
        ...getJenisHakAktif(),
        ...getJenisHakAdatAktif(),
        ...getStatusKesesuaianAktif(),
    ].length > 0;

    // Jika tidak ada filter layer aktif, muat semua bidang (mode "all")
    if (!hasFilter) {
        params.set("all", "1");
    }

    const qs = params.toString();
    return `${window.location.origin}/api/v1/tiles/bidang/{z}/{x}/{y}${qs ? "?" + qs : ""}`;
}

// ── Source & Layer management ─────────────────────────────────────────────────

/**
 * Inisialisasi atau perbarui source + layer bidang di MapLibre.
 * Jika source sudah ada, hanya perbarui URL tile-nya.
 */
function setupBidangLayer(map) {
    const tileUrl = buildTileUrl();

    if (map.getSource("bidang-mvt")) {
        // Update URL tiles (trigger re-fetch semua tile)
        map.getSource("bidang-mvt").setTiles([tileUrl]);
        return;
    }

    // ── Source ──────────────────────────────────────────────────────────────
    map.addSource("bidang-mvt", {
        type: "vector",
        tiles: [tileUrl],
        minzoom: MIN_ZOOM_BIDANG,
        maxzoom: 20,
        // Jangan cache di browser terlalu lama — filter bisa berubah
        // MVT tile disajikan dengan Cache-Control dari server
    });

    // ── Fill layer ──────────────────────────────────────────────────────────
    map.addLayer({
        id: "bidang-fill",
        type: "fill",
        source: "bidang-mvt",
        "source-layer": "bidang",
        minzoom: MIN_ZOOM_BIDANG,
        paint: {
            "fill-color": [
                "coalesce",
                ["get", "warna"],          // warna kategori
                ["get", "penggunaan_warna"],
                ["get", "jenis_hak_warna"],
                ["get", "jenis_hak_adat_warna"],
                ["get", "status_kesesuaian_warna"],
                "#3b82f6",                  // default
            ],
            "fill-opacity": 0.35,
        },
    });

    // ── Outline layer ────────────────────────────────────────────────────────
    map.addLayer({
        id: "bidang-line",
        type: "line",
        source: "bidang-mvt",
        "source-layer": "bidang",
        minzoom: MIN_ZOOM_BIDANG,
        paint: {
            "line-color": [
                "coalesce",
                ["get", "warna"],
                ["get", "penggunaan_warna"],
                ["get", "jenis_hak_warna"],
                ["get", "jenis_hak_adat_warna"],
                ["get", "status_kesesuaian_warna"],
                "#3b82f6",
            ],
            "line-width": [
                "interpolate", ["linear"], ["zoom"],
                10, 0.5,
                14, 1,
                17, 1.5,
            ],
            "line-opacity": 0.8,
        },
    });

    // ── Highlight layer (selected feature) ──────────────────────────────────
    map.addLayer({
        id: "bidang-highlight",
        type: "line",
        source: "bidang-mvt",
        "source-layer": "bidang",
        minzoom: MIN_ZOOM_BIDANG,
        paint: {
            "line-color": "#ffffff",
            "line-width": 2.5,
            "line-opacity": 0.9,
        },
        filter: ["==", ["get", "id"], ""],
    });

    // ── Vertex GeoJSON layer (titik pojok polygon terpilih) ──────────────────
    if (!map.getSource("bidang-vertex")) {
        map.addSource("bidang-vertex", {
            type: "geojson",
            data: { type: "FeatureCollection", features: [] },
        });
    }

    if (!map.getLayer("bidang-vertex-circle")) {
        map.addLayer({
            id: "bidang-vertex-circle",
            type: "circle",
            source: "bidang-vertex",
            paint: {
                "circle-radius": 3,
                "circle-color": "#1d4ed8",
                "circle-stroke-width": 1.5,
                "circle-stroke-color": "#ffffff",
            },
        });
    }

    // ── Click handler ────────────────────────────────────────────────────────
    map.on("click", "bidang-fill", onBidangClick);

    // Cursor pointer saat hover di atas bidang
    map.on("mouseenter", "bidang-fill", () => {
        map.getCanvas().style.cursor = "pointer";
    });
    map.on("mouseleave", "bidang-fill", () => {
        map.getCanvas().style.cursor = "";
    });
}

/**
 * Rebuild tile URL dan paksa MapLibre re-fetch semua tile.
 * Dipanggil saat filter layer berubah atau wilayah berubah.
 */
function refreshTileSource() {
    const map = window.petaMap;
    if (!map || !map.isStyleLoaded()) return;

    const tileUrl = buildTileUrl();

    if (map.getSource("bidang-mvt")) {
        map.getSource("bidang-mvt").setTiles([tileUrl]);
    } else {
        setupBidangLayer(map);
    }

    updateCountLabel();
}

// ── Click & Popup ─────────────────────────────────────────────────────────────

function onBidangClick(e) {
    const map = window.petaMap;
    if (!e.features || e.features.length === 0) return;

    const feature = e.features[0];
    const props   = feature.properties;

    // Close popup sebelumnya
    if (window.petaLayers.activePopup) {
        window.petaLayers.activePopup.remove();
        window.petaLayers.activePopup = null;
    }

    // Highlight feature terpilih
    map.setFilter("bidang-highlight", ["==", ["get", "id"], props.id ?? ""]);

    // Render vertex corners
    renderVertexFromMvtFeature(feature);

    // Buka popup
    const popup = new maplibregl.Popup({ maxWidth: "240px", offset: 10 })
        .setLngLat(e.lngLat)
        .setHTML(buildPopupHtml(props))
        .addTo(map);

    popup.on("close", () => {
        map.setFilter("bidang-highlight", ["==", ["get", "id"], ""]);
        clearVertices();
    });

    window.petaLayers.activePopup = popup;
}

/**
 * Render titik-titik vertex dari feature MVT (koordinat sudah dalam tile coords;
 * MapLibre menyediakan geometry dalam coordinate space yang bisa kita gunakan
 * via turf atau langsung dari e.features[0].geometry).
 */
function renderVertexFromMvtFeature(feature) {
    const map = window.petaMap;
    if (!map || !map.getSource("bidang-vertex")) return;

    const { type, coordinates } = feature.geometry;
    const points = [];

    const addRing = (ring) => {
        ring.forEach(([lng, lat]) => {
            points.push({
                type: "Feature",
                geometry: { type: "Point", coordinates: [lng, lat] },
                properties: {},
            });
        });
    };

    if (type === "Polygon") {
        coordinates.forEach(addRing);
    } else if (type === "MultiPolygon") {
        coordinates.forEach((poly) => poly.forEach(addRing));
    }

    map.getSource("bidang-vertex").setData({
        type: "FeatureCollection",
        features: points,
    });
}

function clearVertices() {
    const map = window.petaMap;
    if (!map || !map.getSource("bidang-vertex")) return;
    map.getSource("bidang-vertex").setData({ type: "FeatureCollection", features: [] });
}

function buildPopupHtml(props) {
    const esc = (v) =>
        String(v ?? "–")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");

    const luas = props.luas
        ? Number(props.luas).toLocaleString("id-ID") + " m²"
        : null;

    return `
        <div style="min-width:160px; font-size:12px; line-height:1.7; font-family:inherit;">
            <p style="font-weight:600; margin:0 0 4px; font-size:13px;">${esc(props.nomor_bidang)}</p>
            <p style="color:#6b7280; margin:0;">Persil: ${esc(props.nomor_persil)}</p>
            ${luas ? `<p style="color:#6b7280; margin:0;">Luas: ${esc(luas)}</p>` : ""}
        </div>
    `;
}

// ── Helper: filter aktif ──────────────────────────────────────────────────────

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

// ── Helper: count label ───────────────────────────────────────────────────────

function updateCountLabel(message) {
    document.querySelectorAll(".bidang-count").forEach((el) => {
        el.textContent = message ?? "Memuat bidang…";
    });
}

// ── Helper: group checkbox state ──────────────────────────────────────────────

function updateGroupCheckboxState(groupCbId, cbSelector) {
    const groupCb = document.getElementById(groupCbId);
    if (!groupCb) return;
    const total   = document.querySelectorAll(cbSelector).length;
    const checked = document.querySelectorAll(`${cbSelector}:checked`).length;
    if (checked === 0)         { groupCb.checked = false; groupCb.indeterminate = false; }
    else if (checked === total){ groupCb.checked = true;  groupCb.indeterminate = false; }
    else                       { groupCb.checked = false; groupCb.indeterminate = true;  }
}

// ── Event handlers (dipanggil dari blade/HTML) ────────────────────────────────

window.onGroupChange = function (groupCb, groupName) {
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
};

window.onKategoriChange = function () {
    updateGroupCheckboxState("cb-group-kategori", ".layer-kategori-cb");
    refreshTileSource();
};
function onKategoriChange() { window.onKategoriChange(); }

window.onPenggunaanChange = function () {
    updateGroupCheckboxState("cb-group-penggunaan", ".layer-penggunaan-cb");
    refreshTileSource();
};
function onPenggunaanChange() { window.onPenggunaanChange(); }

window.onJenisHakChange = function () {
    updateGroupCheckboxState("cb-group-jenis-hak", ".layer-jenis-hak-cb");
    refreshTileSource();
};
function onJenisHakChange() { window.onJenisHakChange(); }

window.onJenisHakAdatChange = function () {
    updateGroupCheckboxState("cb-group-jenis-hak-adat", ".layer-jenis-hak-adat-cb");
    refreshTileSource();
};
function onJenisHakAdatChange() { window.onJenisHakAdatChange(); }

window.onStatusKesesuaianChange = function () {
    updateGroupCheckboxState("cb-group-status-kesesuaian", ".layer-status-kesesuaian-cb");
    refreshTileSource();
};
function onStatusKesesuaianChange() { window.onStatusKesesuaianChange(); }

// ── Opacity ───────────────────────────────────────────────────────────────────

window.updateOpacity = function (rangeEl, groupId) {
    const val    = rangeEl.value / 100;
    const label  = rangeEl.closest(".flex.items-center.gap-2")?.querySelector(".opacity-val");
    if (label) label.textContent = `${rangeEl.value}%`;

    const map = window.petaMap;
    if (!map) return;

    // Mapping groupId → layer opacity key
    const opacityKeyMap = {
        "group-kategori":          "kategori",
        "group-penggunaan":        "penggunaan",
        "group-jenis-hak":         "jenisHak",
        "group-jenis-hak-adat":    "jenisHakAdat",
        "group-status-kesesuaian": "statusKesesuaian",
    };

    const key = opacityKeyMap[groupId];
    if (key) {
        window.petaLayers.opacity[key] = val;
    }

    // Hitung opacity efektif sebagai rata-rata minimum (paling restriktif menang)
    const minOpacity = Math.min(...Object.values(window.petaLayers.opacity));

    if (map.getLayer("bidang-fill")) {
        map.setPaintProperty("bidang-fill", "fill-opacity",  0.35 * minOpacity);
    }
    if (map.getLayer("bidang-line")) {
        map.setPaintProperty("bidang-line", "line-opacity",  0.8  * minOpacity);
    }
};

// ── Wilayah changed event (dari wilayah.js) ───────────────────────────────────

window.addEventListener("wilayahChanged", () => {
    refreshTileSource();
});

// ── Basemap changed → re-setup layers ────────────────────────────────────────

window.addEventListener("basemapChanged", () => {
    const map = window.petaMap;
    if (!map) return;
    map.once("styledata", () => {
        setupBidangLayer(map);
        // wilayah-highlight direset oleh wilayah.js
    });
});

// ── Bootstrap ─────────────────────────────────────────────────────────────────

function initPetaLayers() {
    const map = window.petaMap;
    if (!map) return;

    if (map.isStyleLoaded()) {
        setupBidangLayer(map);
    } else {
        map.once("load", () => setupBidangLayer(map));
    }
}

if (window.petaMap) {
    initPetaLayers();
} else {
    window.addEventListener("petaMapReady", () => initPetaLayers(), { once: true });
}
