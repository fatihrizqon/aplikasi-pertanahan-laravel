/**
 * peta-layers.js  (Vector Tiles edition — MapLibre GL JS)
 *
 * Fix v3:
 *   - Bug 2: layer bidang hilang saat ganti basemap
 *     → Handler basemapChanged tidak boleh pakai map.once('style.load', ...)
 *       karena style sudah loaded saat event diterima. Langsung setupBidangLayer.
 *   - Bug 3: layer bidang tampil sesuai wilayah aktif
 *     → buildTileUrl() sudah menyertakan filter wilayah. Pastikan dipanggil
 *       refreshTileSource() (bukan setupBidangLayer) setelah wilayahChanged,
 *       agar URL tile diperbarui sesuai kabupaten/kecamatan/kelurahan aktif.
 */

"use strict";

const MIN_ZOOM_BIDANG = 8;

window.petaLayers = window.petaLayers ?? {
    wilayah: {
        kabupaten_kode: null,
        kecamatan_kode: null,
        kelurahan_kode: null,
    },
    opacity: {
        kategori: 1, penggunaan: 1, jenisHak: 1, jenisHakAdat: 1, statusKesesuaian: 1,
    },
    activePopup: null,
};

// ── URL builder ───────────────────────────────────────────────────────────────
// Bug 3: URL tile selalu menyertakan filter wilayah yang aktif sehingga
// server hanya mengembalikan bidang dalam batas wilayah yang dipilih.

function buildTileUrl() {
    const params = new URLSearchParams();
    const w = window.petaLayers.wilayah;

    // Hanya kirim filter wilayah paling spesifik yang aktif
    if (w.kelurahan_kode)      params.set('kelurahan_kode',  w.kelurahan_kode);
    else if (w.kecamatan_kode) params.set('kecamatan_kode',  w.kecamatan_kode);
    else if (w.kabupaten_kode) params.set('kabupaten_kode',  w.kabupaten_kode);

    getKategoriAktif().forEach(id          => params.append('kategori_ids[]',          id));
    getPenggunaanAktif().forEach(id        => params.append('penggunaan_ids[]',        id));
    getJenisHakAktif().forEach(id          => params.append('jenis_hak_ids[]',         id));
    getJenisHakAdatAktif().forEach(id      => params.append('jenis_hak_adat_ids[]',    id));
    getStatusKesesuaianAktif().forEach(id  => params.append('status_kesesuaian_ids[]', id));

    const qs   = params.toString();
    const path = `/api/v1/tiles/bidang/{z}/{x}/{y}${qs ? '?' + qs : ''}`;
    return `${window.location.origin}${path}`;
}

function hasAnyFilter() {
    return [
        ...getKategoriAktif(),
        ...getPenggunaanAktif(),
        ...getJenisHakAktif(),
        ...getJenisHakAdatAktif(),
        ...getStatusKesesuaianAktif(),
    ].length > 0;
}

// ── Source & Layer ─────────────────────────────────────────────────────────────

function setupBidangLayer(map) {
    const tileUrl = buildTileUrl();

    if (map.getSource('bidang-mvt')) {
        map.getSource('bidang-mvt').setTiles([tileUrl]);
        return;
    }

    map.addSource('bidang-mvt', {
        type: 'vector',
        tiles: [tileUrl],
        minzoom: MIN_ZOOM_BIDANG,
        maxzoom: 20,
    });

    // Fill
    map.addLayer({
        id: 'bidang-fill',
        type: 'fill',
        source: 'bidang-mvt',
        'source-layer': 'bidang',
        minzoom: MIN_ZOOM_BIDANG,
        paint: {
            'fill-color': ['coalesce',
                ['get', 'warna'], ['get', 'penggunaan_warna'],
                ['get', 'jenis_hak_warna'], ['get', 'jenis_hak_adat_warna'],
                ['get', 'status_kesesuaian_warna'], '#3b82f6'
            ],
            'fill-opacity': 0.35,
        },
    });

    // Outline
    map.addLayer({
        id: 'bidang-line',
        type: 'line',
        source: 'bidang-mvt',
        'source-layer': 'bidang',
        minzoom: MIN_ZOOM_BIDANG,
        paint: {
            'line-color': ['coalesce',
                ['get', 'warna'], ['get', 'penggunaan_warna'],
                ['get', 'jenis_hak_warna'], ['get', 'jenis_hak_adat_warna'],
                ['get', 'status_kesesuaian_warna'], '#3b82f6'
            ],
            'line-width': ['interpolate', ['linear'], ['zoom'], 8, 0.3, 12, 0.8, 16, 1.5],
            'line-opacity': 0.8,
        },
    });

    // Highlight (selected)
    map.addLayer({
        id: 'bidang-highlight',
        type: 'line',
        source: 'bidang-mvt',
        'source-layer': 'bidang',
        minzoom: MIN_ZOOM_BIDANG,
        paint: { 'line-color': '#ffffff', 'line-width': 2.5, 'line-opacity': 0.9 },
        filter: ['==', ['get', 'id'], ''],
    });

    // Vertex circles
    if (!map.getSource('bidang-vertex')) {
        map.addSource('bidang-vertex', { type: 'geojson', data: { type: 'FeatureCollection', features: [] } });
    }
    if (!map.getLayer('bidang-vertex-circle')) {
        map.addLayer({
            id: 'bidang-vertex-circle',
            type: 'circle',
            source: 'bidang-vertex',
            paint: {
                'circle-radius': 3,
                'circle-color': '#1d4ed8',
                'circle-stroke-width': 1.5,
                'circle-stroke-color': '#ffffff',
            },
        });
    }

    map.on('click', 'bidang-fill', onBidangClick);
    map.on('mouseenter', 'bidang-fill', () => { map.getCanvas().style.cursor = 'pointer'; });
    map.on('mouseleave', 'bidang-fill', () => { map.getCanvas().style.cursor = ''; });
}

function refreshTileSource() {
    const map = window.petaMap;
    if (!map || !map.isStyleLoaded()) return;

    if (map.getSource('bidang-mvt')) {
        map.getSource('bidang-mvt').setTiles([buildTileUrl()]);
    } else {
        setupBidangLayer(map);
    }
}

// ── Click & Popup ─────────────────────────────────────────────────────────────

function onBidangClick(e) {
    const map = window.petaMap;
    if (!e.features?.length) return;

    const feature = e.features[0];
    const props   = feature.properties;

    if (window.petaLayers.activePopup) {
        window.petaLayers.activePopup.remove();
        window.petaLayers.activePopup = null;
    }

    map.setFilter('bidang-highlight', ['==', ['get', 'id'], props.id ?? '']);
    renderVertices(feature);

    const popup = new maplibregl.Popup({ maxWidth: '240px', offset: 10 })
        .setLngLat(e.lngLat)
        .setHTML(buildPopupHtml(props))
        .addTo(map);

    popup.on('close', () => {
        map.setFilter('bidang-highlight', ['==', ['get', 'id'], '']);
        clearVertices();
    });

    window.petaLayers.activePopup = popup;
}

function renderVertices(feature) {
    const map = window.petaMap;
    if (!map?.getSource('bidang-vertex')) return;
    const { type, coordinates } = feature.geometry;
    const pts = [];
    const addRing = ring => ring.forEach(([lng, lat]) =>
        pts.push({ type: 'Feature', geometry: { type: 'Point', coordinates: [lng, lat] }, properties: {} })
    );
    if (type === 'Polygon') coordinates.forEach(addRing);
    else if (type === 'MultiPolygon') coordinates.forEach(p => p.forEach(addRing));
    map.getSource('bidang-vertex').setData({ type: 'FeatureCollection', features: pts });
}

function clearVertices() {
    const map = window.petaMap;
    if (map?.getSource('bidang-vertex')) {
        map.getSource('bidang-vertex').setData({ type: 'FeatureCollection', features: [] });
    }
}

function buildPopupHtml(props) {
    const esc = v => String(v ?? '–').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    const luas = props.luas ? Number(props.luas).toLocaleString('id-ID') + ' m²' : null;
    return `
        <div style="min-width:160px;font-size:12px;line-height:1.7;font-family:inherit;">
            <p style="font-weight:600;margin:0 0 4px;font-size:13px;">${esc(props.nomor_bidang)}</p>
            <p style="color:#6b7280;margin:0;">Persil: ${esc(props.nomor_persil)}</p>
            ${luas ? `<p style="color:#6b7280;margin:0;">Luas: ${esc(luas)}</p>` : ''}
        </div>`;
}

// ── Filter getters (baca dari DOM sidebar) ────────────────────────────────────

function getKategoriAktif()         { return [...document.querySelectorAll('.layer-kategori-cb:checked')].map(cb => +cb.dataset.kategoriId).filter(Boolean); }
function getPenggunaanAktif()       { return [...document.querySelectorAll('.layer-penggunaan-cb:checked')].map(cb => +cb.dataset.penggunaanId).filter(Boolean); }
function getJenisHakAktif()         { return [...document.querySelectorAll('.layer-jenis-hak-cb:checked')].map(cb => +cb.dataset.jenisHakId).filter(Boolean); }
function getJenisHakAdatAktif()     { return [...document.querySelectorAll('.layer-jenis-hak-adat-cb:checked')].map(cb => +cb.dataset.jenisHakAdatId).filter(Boolean); }
function getStatusKesesuaianAktif() { return [...document.querySelectorAll('.layer-status-kesesuaian-cb:checked')].map(cb => +cb.dataset.statusKesesuaianId).filter(Boolean); }

// ── Group checkbox indeterminate state ────────────────────────────────────────

function syncGroupCheckbox(groupId, cbSel) {
    const g = document.getElementById(groupId);
    if (!g) return;
    const total   = document.querySelectorAll(cbSel).length;
    const checked = document.querySelectorAll(`${cbSel}:checked`).length;
    if (checked === 0)        { g.checked = false; g.indeterminate = false; }
    else if (checked === total){ g.checked = true;  g.indeterminate = false; }
    else                       { g.checked = false; g.indeterminate = true;  }
}

// ── Public handlers (dipanggil dari onchange di blade) ────────────────────────

window.onGroupChange = function(groupCb, groupName) {
    const map = {
        'kategori':           ['.layer-kategori-cb',           'onKategoriChange'],
        'penggunaan':         ['.layer-penggunaan-cb',         'onPenggunaanChange'],
        'jenis-hak':          ['.layer-jenis-hak-cb',          'onJenisHakChange'],
        'jenis-hak-adat':     ['.layer-jenis-hak-adat-cb',     'onJenisHakAdatChange'],
        'status-kesesuaian':  ['.layer-status-kesesuaian-cb',  'onStatusKesesuaianChange'],
    };
    const entry = map[groupName];
    if (!entry) return;
    document.querySelectorAll(entry[0]).forEach(cb => { cb.checked = groupCb.checked; });
    window[entry[1]]();
};

window.onKategoriChange         = function() { syncGroupCheckbox('cb-group-kategori',          '.layer-kategori-cb');          refreshTileSource(); };
window.onPenggunaanChange       = function() { syncGroupCheckbox('cb-group-penggunaan',        '.layer-penggunaan-cb');        refreshTileSource(); };
window.onJenisHakChange         = function() { syncGroupCheckbox('cb-group-jenis-hak',         '.layer-jenis-hak-cb');         refreshTileSource(); };
window.onJenisHakAdatChange     = function() { syncGroupCheckbox('cb-group-jenis-hak-adat',    '.layer-jenis-hak-adat-cb');    refreshTileSource(); };
window.onStatusKesesuaianChange = function() { syncGroupCheckbox('cb-group-status-kesesuaian', '.layer-status-kesesuaian-cb'); refreshTileSource(); };

// ── Opacity ───────────────────────────────────────────────────────────────────

window.updateOpacity = function(slider, groupId) {
    const val = slider.value / 100;
    const label = slider.closest('.flex')?.querySelector('.opacity-val');
    if (label) label.textContent = `${slider.value}%`;

    const keyMap = {
        'group-kategori':          'kategori',
        'group-penggunaan':        'penggunaan',
        'group-jenis-hak':         'jenisHak',
        'group-jenis-hak-adat':    'jenisHakAdat',
        'group-status-kesesuaian': 'statusKesesuaian',
    };
    const key = keyMap[groupId];
    if (key) window.petaLayers.opacity[key] = val;

    const map = window.petaMap;
    if (!map) return;
    const min = Math.min(...Object.values(window.petaLayers.opacity));
    if (map.getLayer('bidang-fill')) map.setPaintProperty('bidang-fill', 'fill-opacity', 0.35 * min);
    if (map.getLayer('bidang-line')) map.setPaintProperty('bidang-line', 'line-opacity', 0.8  * min);
};

// ── Events ────────────────────────────────────────────────────────────────────

// Bug 3: wilayahChanged → refresh URL tile agar bidang yang tampil sesuai
// wilayah aktif (kabupaten/kecamatan/kelurahan yang dipilih).
window.addEventListener('wilayahChanged', () => refreshTileSource());

// Bug 2: basemapChanged di-dispatch SETELAH style.load selesai (lihat map.js).
// Jangan pakai map.once('style.load', ...) di sini — style sudah loaded.
// Langsung re-setup semua layer bidang dan gunakan tile URL terkini.
window.addEventListener('basemapChanged', () => {
    const map = window.petaMap;
    if (!map) return;

    // Style sudah loaded saat event ini diterima — langsung setup layer
    setupBidangLayer(map);
});

// ── Bootstrap ─────────────────────────────────────────────────────────────────

function initPetaLayers() {
    const map = window.petaMap;
    if (!map) return;
    if (map.isStyleLoaded()) setupBidangLayer(map);
    else map.once('load', () => setupBidangLayer(map));
}

if (window.petaMap) initPetaLayers();
else window.addEventListener('petaMapReady', () => initPetaLayers(), { once: true });
