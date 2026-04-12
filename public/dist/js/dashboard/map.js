/* ════════════════════════════════════════
   map.js
   Depends on: provinsiData (global, set inline di blade)
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
    const mapEl = document.getElementById('map');
    if (!mapEl || mapEl.offsetWidth === 0) {
        requestAnimationFrame(initMap);
        return;
    }

    const petaMap = window.petaMap = L.map('map', {
        center: [-7.805303377839844, 110.36463940268219],
        zoom: 10,
        zoomControl: false,
        attributionControl: true
    });

    /* ── LAYER PROVINSI ── */
    if (provinsiData && provinsiData.geom_json) {
        const geomJson = typeof provinsiData.geom_json === 'string'
            ? JSON.parse(provinsiData.geom_json)
            : provinsiData.geom_json;

        window.provinsiLayer = L.geoJSON(geomJson, {
            style: {
                color: '#dc2626',
                weight: 2,
                fillColor: '#dc2626',
                fillOpacity: 0.08,
            },
            onEachFeature: function (feature, layer) {
                layer.bindPopup(`<strong>${provinsiData.nama}</strong><br>Kode: ${provinsiData.kode}`);
            }
        }).addTo(petaMap);

        // Daftarkan ke registry supaya bisa dikontrol dari sidebar
        window.layerRegistry['batas-provinsi'] = window.provinsiLayer;
    }

    // Pasang basemap default
    basemaps[activeBasemap].addTo(petaMap);

    // Paksa recalculate ukuran container
    setTimeout(() => petaMap.invalidateSize(), 100);

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

window.addEventListener('load', initMap);
