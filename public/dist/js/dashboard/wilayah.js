/**
 * wilayah.js  (MapLibre edition — v4)
 *
 * Fix v4:
 *   - Bug 1: batas wilayah tidak muncul di basemap non-OSM
 *     → Handler basemapChanged tidak boleh pakai map.once('style.load', ...)
 *       karena event itu sudah lewat saat basemapChanged di-dispatch.
 *       Cukup langsung addWilayahLayers + restore highlight.
 */

"use strict";

const PROVINSI_KODE = '34'; // DIY

const API_LIST = {
    kabupaten: '/api/v1/wilayah/kabupaten',
    kecamatan: '/api/v1/wilayah/kecamatan',
    kelurahan: '/api/v1/wilayah/kelurahan',
};

const API_GEOJSON = {
    provinsi:  '/api/v1/wilayah/provinsi/geojson',
    kabupaten: '/api/v1/wilayah/kabupaten/geojson',
    kecamatan: '/api/v1/wilayah/kecamatan/geojson',
    kelurahan: '/api/v1/wilayah/kelurahan/geojson',
};

const API_BBOX = {
    provinsi:  '/api/v1/wilayah/provinsi/bbox',
    kabupaten: '/api/v1/wilayah/kabupaten/bbox',
    kecamatan: '/api/v1/wilayah/kecamatan/bbox',
    kelurahan: '/api/v1/wilayah/kelurahan/bbox',
};

const LEVEL_PAINT = {
    provinsi:  { fill: '#2563eb', fillOpacity: 0.04, line: '#2563eb', lineWidth: 2.5 },
    kabupaten: { fill: '#2563eb', fillOpacity: 0.06, line: '#2563eb', lineWidth: 2   },
    kecamatan: { fill: '#d97706', fillOpacity: 0.08, line: '#d97706', lineWidth: 1.8 },
    kelurahan: { fill: '#059669', fillOpacity: 0.10, line: '#059669', lineWidth: 1.4 },
};

// ── Source & Layer setup ──────────────────────────────────────────────────────

function addWilayahLayers(map) {
    if (!map.getSource('wilayah-highlight')) {
        map.addSource('wilayah-highlight', {
            type: 'geojson',
            data: { type: 'FeatureCollection', features: [] },
        });
    }
    if (!map.getLayer('wilayah-highlight-fill')) {
        map.addLayer({
            id: 'wilayah-highlight-fill',
            type: 'fill',
            source: 'wilayah-highlight',
            paint: { 'fill-color': '#2563eb', 'fill-opacity': 0.04 },
        });
    }
    if (!map.getLayer('wilayah-highlight-line')) {
        map.addLayer({
            id: 'wilayah-highlight-line',
            type: 'line',
            source: 'wilayah-highlight',
            paint: { 'line-color': '#2563eb', 'line-width': 2.5, 'line-dasharray': [3, 2] },
        });
    }
}

function setHighlight(map, geojsonData, level) {
    if (!map.getSource('wilayah-highlight')) return;
    const p = LEVEL_PAINT[level] ?? LEVEL_PAINT.provinsi;
    map.getSource('wilayah-highlight').setData(geojsonData);
    map.setPaintProperty('wilayah-highlight-fill', 'fill-color',   p.fill);
    map.setPaintProperty('wilayah-highlight-fill', 'fill-opacity', p.fillOpacity);
    map.setPaintProperty('wilayah-highlight-line', 'line-color',   p.line);
    map.setPaintProperty('wilayah-highlight-line', 'line-width',   p.lineWidth);
}

// ── Fetch helpers ─────────────────────────────────────────────────────────────

async function fetchJson(url) {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`${res.status} ${url}`);
    return res.json();
}

// ── Load + tampilkan wilayah tertentu ─────────────────────────────────────────

async function showWilayah(map, level, kode) {
    try {
        const [gj, bboxData] = await Promise.all([
            fetchJson(`${API_GEOJSON[level]}?kode=${kode}`),
            fetchJson(`${API_BBOX[level]}?kode=${kode}`),
        ]);

        setHighlight(map, gj, level);

        if (bboxData?.bbox) {
            const [minLng, minLat, maxLng, maxLat] = bboxData.bbox;
            map.fitBounds([[minLng, minLat], [maxLng, maxLat]], { padding: 40, maxZoom: 14, duration: 600 });
        }
    } catch (e) {
        console.warn(`[wilayah] showWilayah(${level}, ${kode}):`, e.message);
    }
}

// ── Load provinsi default saat halaman pertama dimuat ─────────────────────────

async function loadProvinsiDefault(map) {
    await showWilayah(map, 'provinsi', PROVINSI_KODE);
}

// ── Load dropdown list ────────────────────────────────────────────────────────

async function loadKabupaten() {
    try {
        const data = await fetchJson(`${API_LIST.kabupaten}?kode=${PROVINSI_KODE}`);
        const sel  = document.getElementById('filter-kabupaten');
        if (!sel) return;

        sel.innerHTML = '<option value="">Semua Kabupaten/Kota</option>';
        (data.data ?? data).forEach(item => {
            const kode = item.value ?? item.kode;
            const nama = item.label ?? item.nama;
            sel.insertAdjacentHTML('beforeend', `<option value="${kode}">${nama}</option>`);
        });
    } catch (e) {
        console.warn('[wilayah] loadKabupaten:', e.message);
    }
}

async function loadKecamatan(kabupatenKode) {
    try {
        const data = await fetchJson(`${API_LIST.kecamatan}?kode=${kabupatenKode}`);
        const sel  = document.getElementById('filter-kecamatan');
        if (!sel) return;

        sel.innerHTML = '<option value="">Semua Kapanewon/Kemantren</option>';
        (data.data ?? data).forEach(item => {
            const kode = item.value ?? item.kode;
            const nama = item.label ?? item.nama;
            sel.insertAdjacentHTML('beforeend', `<option value="${kode}">${nama}</option>`);
        });

        document.getElementById('wrapper-kecamatan')?.classList.remove('hidden');
    } catch (e) {
        console.warn('[wilayah] loadKecamatan:', e.message);
    }
}

async function loadKelurahan(kecamatanKode) {
    try {
        const data = await fetchJson(`${API_LIST.kelurahan}?kode=${kecamatanKode}`);
        const sel  = document.getElementById('filter-kelurahan');
        if (!sel) return;

        sel.innerHTML = '<option value="">Semua Kalurahan/Kelurahan</option>';
        (data.data ?? data).forEach(item => {
            const kode = item.value ?? item.kode;
            const nama = item.label ?? item.nama;
            sel.insertAdjacentHTML('beforeend', `<option value="${kode}">${nama}</option>`);
        });

        document.getElementById('wrapper-kelurahan')?.classList.remove('hidden');
    } catch (e) {
        console.warn('[wilayah] loadKelurahan:', e.message);
    }
}

// ── Handlers perubahan dropdown ───────────────────────────────────────────────

async function onKabupatenChange(kode) {
    const map = window.petaMap;

    document.getElementById('filter-kecamatan').innerHTML = '<option value="">Semua Kapanewon/Kemantren</option>';
    document.getElementById('filter-kelurahan').innerHTML = '<option value="">Semua Kalurahan/Kelurahan</option>';
    document.getElementById('wrapper-kecamatan')?.classList.add('hidden');
    document.getElementById('wrapper-kelurahan')?.classList.add('hidden');

    window.petaLayers.wilayah.kabupaten_kode = kode || null;
    window.petaLayers.wilayah.kecamatan_kode  = null;
    window.petaLayers.wilayah.kelurahan_kode  = null;

    if (!kode) {
        await loadProvinsiDefault(map);
    } else {
        await showWilayah(map, 'kabupaten', kode);
        await loadKecamatan(kode);
    }

    window.dispatchEvent(new CustomEvent('wilayahChanged'));
}

async function onKecamatanChange(kode) {
    const map = window.petaMap;

    document.getElementById('filter-kelurahan').innerHTML = '<option value="">Semua Kalurahan/Kelurahan</option>';
    document.getElementById('wrapper-kelurahan')?.classList.add('hidden');

    window.petaLayers.wilayah.kecamatan_kode = kode || null;
    window.petaLayers.wilayah.kelurahan_kode  = null;

    if (!kode) {
        const kabKode = window.petaLayers.wilayah.kabupaten_kode;
        await showWilayah(map, kabKode ? 'kabupaten' : 'provinsi', kabKode || PROVINSI_KODE);
    } else {
        await showWilayah(map, 'kecamatan', kode);
        await loadKelurahan(kode);
    }

    window.dispatchEvent(new CustomEvent('wilayahChanged'));
}

async function onKelurahanChange(kode) {
    const map = window.petaMap;

    window.petaLayers.wilayah.kelurahan_kode = kode || null;

    if (!kode) {
        const kecKode = window.petaLayers.wilayah.kecamatan_kode;
        await showWilayah(map, 'kecamatan', kecKode);
    } else {
        await showWilayah(map, 'kelurahan', kode);
    }

    window.dispatchEvent(new CustomEvent('wilayahChanged'));
}

// ── Pasang event listener ─────────────────────────────────────────────────────

function attachListeners() {
    document.getElementById('filter-kabupaten')
        ?.addEventListener('change', e => onKabupatenChange(e.target.value));
    document.getElementById('filter-kecamatan')
        ?.addEventListener('change', e => onKecamatanChange(e.target.value));
    document.getElementById('filter-kelurahan')
        ?.addEventListener('change', e => onKelurahanChange(e.target.value));
}

// ── Basemap change: re-add layers + restore highlight ─────────────────────────
// FIX BUG 1: basemapChanged di-dispatch SETELAH style.load selesai (lihat map.js).
// Jangan pakai map.once('style.load', ...) di sini — style sudah loaded saat
// event ini diterima. Langsung re-attach source/layer dan restore data.

window.addEventListener('basemapChanged', () => {
    const map = window.petaMap;
    if (!map) return;

    // Style sudah loaded (dijamin oleh map.js yang dispatch setelah style.load)
    addWilayahLayers(map);

    const w = window.petaLayers?.wilayah;
    if (w?.kelurahan_kode)      showWilayah(map, 'kelurahan',  w.kelurahan_kode);
    else if (w?.kecamatan_kode) showWilayah(map, 'kecamatan',  w.kecamatan_kode);
    else if (w?.kabupaten_kode) showWilayah(map, 'kabupaten',  w.kabupaten_kode);
    else                        loadProvinsiDefault(map);
});

// ── Bootstrap ─────────────────────────────────────────────────────────────────

function initWilayah(map) {
    addWilayahLayers(map);
    attachListeners();
    loadKabupaten();
    loadProvinsiDefault(map);
}

if (window.petaMap) {
    const map = window.petaMap;
    if (map.isStyleLoaded()) initWilayah(map);
    else map.once('load', () => initWilayah(map));
} else {
    window.addEventListener('petaMapReady', e => {
        e.detail.once('load', () => initWilayah(e.detail));
    }, { once: true });
}
