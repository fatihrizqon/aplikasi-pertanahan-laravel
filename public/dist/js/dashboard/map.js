/* ════════════════════════════════════════
   map.js  (MapLibre GL JS edition)
   Exposes:  window.petaMap, window.switchBasemap, window.searchKoordinat
   Depends:  MapLibre GL JS (dimuat via CDN di layout)
════════════════════════════════════════ */

"use strict";

/* ── BASEMAP STYLES ── */
const BASEMAP_STYLES = {
    osm: {
        version: 8,
        sources: {
            "osm-tiles": {
                type: "raster",
                tiles: ["https://tile.openstreetmap.org/{z}/{x}/{y}.png"],
                tileSize: 256,
                attribution: "© OpenStreetMap contributors",
                maxzoom: 19,
            },
        },
        layers: [
            { id: "osm-tiles", type: "raster", source: "osm-tiles" },
        ],
    },
    satellite: {
        version: 8,
        sources: {
            "satellite-tiles": {
                type: "raster",
                tiles: [
                    "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
                ],
                tileSize: 256,
                attribution: "© Esri, Maxar, Earthstar Geographics",
                maxzoom: 19,
            },
        },
        layers: [
            {
                id: "satellite-tiles",
                type: "raster",
                source: "satellite-tiles",
            },
        ],
    },
    terrain: {
        version: 8,
        sources: {
            "terrain-tiles": {
                type: "raster",
                tiles: [
                    "https://tile.opentopomap.org/{z}/{x}/{y}.png",
                ],
                tileSize: 256,
                attribution: "© OpenTopoMap",
                maxzoom: 17,
            },
        },
        layers: [
            {
                id: "terrain-tiles",
                type: "raster",
                source: "terrain-tiles",
            },
        ],
    },
    grayscale: {
        version: 8,
        sources: {
            "grayscale-tiles": {
                type: "raster",
                tiles: [
                    "https://a.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png",
                    "https://b.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png",
                    "https://c.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png",
                ],
                tileSize: 256,
                attribution: "&copy; OpenStreetMap &copy; CARTO",
                maxzoom: 19,
            },
        },
        layers: [
            {
                id: "grayscale-tiles",
                type: "raster",
                source: "grayscale-tiles",
            },
        ],
    },
};

let activeBasemap = "osm";

/* ── SWITCH BASEMAP ── */
window.switchBasemap = function (key) {
    if (!window.petaMap || !BASEMAP_STYLES[key]) return;

    // Set style baru (MapLibre mengganti seluruh style; layer data akan ditambahkan ulang)
    window.petaMap.setStyle(BASEMAP_STYLES[key]);
    activeBasemap = key;

    // Setelah style dimuat ulang, broadcast event agar layer lain bisa re-attach
    window.petaMap.once("style.load", () => {
        window.dispatchEvent(new CustomEvent("basemapChanged", { detail: key }));
    });

    document.querySelectorAll("#basemap-switcher .basemap-btn").forEach((btn) => {
        btn.classList.remove("active");
    });
    const activeBtn = document.getElementById("btn-" + key);
    if (activeBtn) activeBtn.classList.add("active");
};

/* ── INIT MAP ── */
function initMap() {
    if (window.petaMap) return;

    const mapEl = document.getElementById("map");
    if (!mapEl) return;

    if (mapEl.offsetWidth === 0 || mapEl.offsetHeight === 0) {
        requestAnimationFrame(initMap);
        return;
    }

    const petaMap = (window.petaMap = new maplibregl.Map({
        container: "map",
        style: BASEMAP_STYLES[activeBasemap],
        center: [110.36463940268219, -7.805303377839844], // [lng, lat]
        zoom: 10,
        attributionControl: false,
        // Aktifkan WebGL rendering — jauh lebih cepat dari Canvas Leaflet
    }));

    // ── Skala ───────────────────────────────────────────────────────────────
    const scaleMap = {
        6:  "1 : 4.400.000",
        7:  "1 : 2.200.000",
        8:  "1 : 1.100.000",
        9:  "1 : 546.000",
        10: "1 : 273.000",
        11: "1 : 136.000",
        12: "1 : 68.000",
        13: "1 : 34.000",
        14: "1 : 17.000",
        15: "1 : 8.500",
        16: "1 : 4.200",
        17: "1 : 2.100",
        18: "1 : 1.000",
        19: "1 : 500",
    };

    function updateScale() {
        const z   = Math.round(petaMap.getZoom());
        const el  = document.getElementById("map-scale");
        if (!el) return;
        const fallback = Math.round(550000 / Math.pow(2, z - 9)).toLocaleString("id-ID");
        el.textContent = scaleMap[z] ?? "1 : " + fallback;
    }

    petaMap.on("zoom", updateScale);
    petaMap.on("load", updateScale);

    // ── Zoom Controls (tombol custom di blade) ───────────────────────────────
    window.petaMap.zoomIn  = () => petaMap.zoomIn();
    window.petaMap.zoomOut = () => petaMap.zoomOut();

    // ── Search Koordinat ─────────────────────────────────────────────────────
    window.searchKoordinat = function () {
        const val   = document.getElementById("search-koordinat").value.trim();
        const parts = val.split(",").map((s) => parseFloat(s.trim()));

        if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
            const [lat, lng] = parts;
            petaMap.flyTo({ center: [lng, lat], zoom: 14 });

            // Tambahkan marker sementara
            if (window._searchMarker) window._searchMarker.remove();
            window._searchMarker = new maplibregl.Marker({ color: "#dc2626" })
                .setLngLat([lng, lat])
                .setPopup(
                    new maplibregl.Popup({ offset: 25 }).setText(
                        `📍 ${lat}, ${lng}`
                    )
                )
                .addTo(petaMap)
                .togglePopup();
        }
    };

    // ── Broadcast ready ──────────────────────────────────────────────────────
    petaMap.on("load", () => {
        window.dispatchEvent(new CustomEvent("petaMapReady", { detail: petaMap }));
    });
}

document.addEventListener("DOMContentLoaded", initMap);
