/* ════════════════════════════════════════
   map.js
   Exposes:    window.petaMap, window.provinsiLayer, window.switchBasemap, window.searchKoordinat
════════════════════════════════════════ */

/* ── BASEMAP DEFINITIONS ── */
const basemaps = {
    osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }),
    satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri, Maxar, Earthstar Geographics',
        maxZoom: 19
    }),
    terrain: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://opentopomap.org">OpenTopoMap</a>',
        maxZoom: 17
    }),
};

let activeBasemap = 'osm';

/* ── SWITCH BASEMAP ── */
window.switchBasemap = function (key) {
    if (!window.petaMap || !basemaps[key]) return;
    basemaps[activeBasemap].remove();
    basemaps[key].addTo(window.petaMap);
    basemaps[key].bringToBack();
    activeBasemap = key;

    document.querySelectorAll('#basemap-switcher .basemap-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    const activeBtn = document.getElementById('btn-' + key);
    if (activeBtn) activeBtn.classList.add('active');
};

/* ── INIT MAP ── */
function initMap() {
    // Guard: jangan init dua kali
    if (window.petaMap) return;

    const mapEl = document.getElementById('map');
    if (!mapEl) return;

    // Tunggu container punya dimensi nyata
    if (mapEl.offsetWidth === 0 || mapEl.offsetHeight === 0) {
        requestAnimationFrame(initMap);
        return;
    }

    const petaMap = window.petaMap = L.map('map', {
        center: [-7.805303377839844, 110.36463940268219],
        zoom: 10,
        zoomControl: false,
        attributionControl: true
    });

    // Pasang basemap default
    basemaps[activeBasemap].addTo(petaMap);

    // Paksa recalculate ukuran — beberapa kali antisipasi layout lambat
    setTimeout(() => petaMap.invalidateSize(), 100);
    setTimeout(() => petaMap.invalidateSize(), 400);
    setTimeout(() => petaMap.invalidateSize(), 800);

    // ResizeObserver: otomatis invalidate saat ukuran container berubah
    if (typeof ResizeObserver !== 'undefined') {
        const ro = new ResizeObserver(() => petaMap.invalidateSize());
        ro.observe(mapEl);
    }

    // Broadcast ke script lain bahwa peta sudah siap
    window.dispatchEvent(new CustomEvent('petaMapReady', { detail: petaMap }));

    /* ── SKALA ── */
    const scaleMap = {
        6: '1 : 4.4 JUTA', 7: '1 : 2.2 JUTA', 8: '1 : 1.1 JUTA',
        9: '1 : 546 RIBU', 10: '1 : 273 RIBU', 11: '1 : 136 RIBU',
        12: '1 : 68 RIBU', 13: '1 : 34 RIBU', 14: '1 : 17 RIBU',
        15: '1 : 8.5 RIBU', 16: '1 : 4.2 RIBU', 17: '1 : 2.1 RIBU',
        18: '1 : 1 RIBU', 19: '1 : 500'
    };
    petaMap.on('zoomend', () => {
        const z = petaMap.getZoom();
        document.getElementById('map-scale').textContent =
            '= ' + (scaleMap[z] ?? '1 : ' + Math.round(550000 / Math.pow(2, z - 9)) + ' RIBU');
    });

    /* ── SEARCH KOORDINAT ── */
    window.searchKoordinat = function () {
        const val = document.getElementById('search-koordinat').value.trim();
        const parts = val.split(',').map(s => parseFloat(s.trim()));
        if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
            petaMap.setView([parts[0], parts[1]], 14);
            L.marker([parts[0], parts[1]]).addTo(petaMap)
                .bindPopup(`📍 ${parts[0]}, ${parts[1]}`).openPopup();
        }
    };
}

document.addEventListener('DOMContentLoaded', initMap);
