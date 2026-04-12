/* ════════════════════════════════════════
   layers.js
   Depends on: window.petaMap (dari map.js)
   Exposes:    window.layerRegistry, window.toggleLayer, window.toggleLeafLayer,
               window.updateOpacity, window.toggleGroup
════════════════════════════════════════ */

/* ── LAYER REGISTRY ── */
// Daftarkan semua layer WMS/GeoJSON di sini.
// Key harus sama dengan `id` di $layerGroups PHP.
window.layerRegistry = {};

// Contoh WMS — aktifkan saat endpoint tersedia:
// window.layerRegistry['rtrw-bantul-cagar-budaya'] = L.tileLayer.wms('https://geoserver.example.com/wms', {
//     layers: 'bantul:cagar_budaya',
//     format: 'image/png',
//     transparent: true,
//     opacity: 1,
//     attribution: 'GeoServer Bantul'
// });

// Contoh GeoJSON:
// window.layerRegistry['batas'] = L.geoJSON(null, {
//     style: { color: '#374151', weight: 1.5, fillOpacity: 0 }
// });
// fetch('/api/geojson/batas').then(r => r.json()).then(data => {
//     window.layerRegistry['batas'].addData(data);
// });

/* ── TOGGLE LAYER (checkbox level 0 & 1) ── */
window.toggleLayer = function (checkbox, layerKey) {
    const headerRow = checkbox.closest('.flex.items-center');
    const sliderRow = headerRow ? headerRow.nextElementSibling : null;
    if (sliderRow && sliderRow.classList.contains('opacity-slider-row')) {
        sliderRow.classList.toggle('hidden', !checkbox.checked);
        sliderRow.classList.toggle('flex', checkbox.checked);
    }

    const layer = window.layerRegistry[layerKey];
    if (!layer || !window.petaMap) return;
    checkbox.checked ? layer.addTo(window.petaMap) : layer.remove();
};

/* ── TOGGLE LAYER (checkbox leaf / level 2) ── */
window.toggleLeafLayer = function (checkbox, layerKey) {
    const leafRow = checkbox.closest('div');
    const sliderRow = leafRow ? leafRow.nextElementSibling : null;
    if (sliderRow && sliderRow.classList.contains('opacity-slider-row')) {
        sliderRow.classList.toggle('hidden', !checkbox.checked);
        sliderRow.classList.toggle('flex', checkbox.checked);
    }

    const layer = window.layerRegistry[layerKey];
    if (!layer || !window.petaMap) return;
    checkbox.checked ? layer.addTo(window.petaMap) : layer.remove();
};

/* ── UPDATE OPACITY ── */
window.updateOpacity = function (slider, layerKey) {
    const row = slider.closest('.opacity-slider-row');
    if (row) row.querySelector('.opacity-val').textContent = slider.value + '%';

    const layer = window.layerRegistry[layerKey];
    if (!layer) return;
    if (layer.setOpacity) {
        layer.setOpacity(slider.value / 100);
    } else if (layer.setStyle) {
        // GeoJSON
        layer.setStyle({ opacity: slider.value / 100, fillOpacity: slider.value / 100 });
    }
};

/* ── ACCORDION TOGGLE ── */
window.toggleGroup = function (headerEl) {
    const group = headerEl.closest('.layer-group');
    const children = group.querySelector(':scope > .layer-children');
    const chevron = headerEl.querySelector('.chevron');
    if (!children) return;

    const isOpen = !children.classList.contains('hidden');
    children.classList.toggle('hidden', isOpen);
    if (chevron) chevron.style.transform = isOpen ? '' : 'rotate(90deg)';
};
