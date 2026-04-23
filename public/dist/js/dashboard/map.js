/* ════════════════════════════════════════
   map.js
   Exposes:    window.petaMap, window.provinsiLayer, window.switchBasemap, window.searchKoordinat
════════════════════════════════════════ */

/* ── BASEMAP DEFINITIONS ── */
const basemaps = {
    osm: L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
            '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }),
    satellite: L.tileLayer(
        "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
        {
            attribution: "© Esri, Maxar, Earthstar Geographics",
            maxZoom: 19,
        },
    ),
    terrain: L.tileLayer("https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png", {
        attribution: '© <a href="https://opentopomap.org">OpenTopoMap</a>',
        maxZoom: 17,
    }),
    grayscale: L.tileLayer(
        "https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png",
        {
            attribution: "&copy; OpenStreetMap &copy; CARTO",
            maxZoom: 17,
        },
    ),
};

let activeBasemap = "osm";

/* ── SWITCH BASEMAP ── */
window.switchBasemap = function (key) {
    if (!window.petaMap || !basemaps[key]) return;
    basemaps[activeBasemap].remove();
    basemaps[key].addTo(window.petaMap);
    basemaps[key].bringToBack();
    activeBasemap = key;

    document
        .querySelectorAll("#basemap-switcher .basemap-btn")
        .forEach((btn) => {
            btn.classList.remove("active");
        });
    const activeBtn = document.getElementById("btn-" + key);
    if (activeBtn) activeBtn.classList.add("active");
};

/* ── INIT MAP ── */
function initMap() {
    // Guard: jangan init dua kali
    if (window.petaMap) return;

    const mapEl = document.getElementById("map");
    if (!mapEl) return;

    // Tunggu container punya dimensi nyata
    if (mapEl.offsetWidth === 0 || mapEl.offsetHeight === 0) {
        requestAnimationFrame(initMap);
        return;
    }

    const petaMap = (window.petaMap = L.map("map", {
        center: [-7.805303377839844, 110.36463940268219],
        zoom: 10,
        zoomControl: false,
        attributionControl: true,
        preverCanvas: true,
    }));

    // Pasang basemap default
    basemaps[activeBasemap].addTo(petaMap);

    // Paksa recalculate ukuran — beberapa kali antisipasi layout lambat
    setTimeout(() => petaMap.invalidateSize(), 100);
    setTimeout(() => petaMap.invalidateSize(), 400);
    setTimeout(() => petaMap.invalidateSize(), 800);

    // ResizeObserver: otomatis invalidate saat ukuran container berubah
    if (typeof ResizeObserver !== "undefined") {
        const ro = new ResizeObserver(() => petaMap.invalidateSize());
        ro.observe(mapEl);
    }

    // Broadcast ke script lain bahwa peta sudah siap
    window.dispatchEvent(new CustomEvent("petaMapReady", { detail: petaMap }));

    /* ── SKALA ── */
    const scaleMap = {
        6: "1 : 4,400,000",
        7: "1 : 2,200,000",
        8: "1 : 1,100,000",
        9: "1 : 546,000",
        10: "1 : 273,000",
        11: "1 : 136,000",
        12: "1 : 68,000",
        13: "1 : 34,000",
        14: "1 : 17,000",
        15: "1 : 8,500",
        16: "1 : 4,200",
        17: "1 : 2,100",
        18: "1 : 1,000",
        19: "1 : 500",
    };

    petaMap.on("zoomend", () => {
        const z = petaMap.getZoom();

        const fallback = Math.round(550000 / Math.pow(2, z - 9)).toLocaleString(
            "id-ID",
        ); // format ribuan

        document.getElementById("map-scale").textContent = (scaleMap[z] ?? "1 : " + fallback);
    });
    /* ── SEARCH KOORDINAT ── */
    window.searchKoordinat = function () {
        const val = document.getElementById("search-koordinat").value.trim();
        const parts = val.split(",").map((s) => parseFloat(s.trim()));
        if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
            petaMap.setView([parts[0], parts[1]], 14);
            L.marker([parts[0], parts[1]])
                .addTo(petaMap)
                .bindPopup(`📍 ${parts[0]}, ${parts[1]}`)
                .openPopup();
        }
    };
}

document.addEventListener("DOMContentLoaded", initMap);
