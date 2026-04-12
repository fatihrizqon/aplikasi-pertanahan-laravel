/* ════════════════════════════════════════
   wilayah.js
   Depends on: window.petaMap, window.provinsiLayer (dari map.js)
               jQuery ($)
   Exposes:    -
════════════════════════════════════════ */

/* ── DEPENDENT DROPDOWN ── */
function dependentDropdown(triggerSelector, targetSelector, wrapperSelector, url, paramKey, defaultLabel, onReset) {
    $(triggerSelector).on('change', function () {
        const value = $(this).val();
        const $target = $(targetSelector);
        const $wrapper = $(wrapperSelector);

        $target.html(`<option value="">${defaultLabel}</option>`).prop('disabled', true);
        $wrapper.addClass('hidden');

        if (typeof onReset === 'function') onReset();
        if (!value) return;

        $.ajax({
            url,
            type: 'GET',
            data: { [paramKey]: value },
            success(data) {
                $.each(data, (i, item) => $target.append(`<option value="${item.value}">${item.label}</option>`));
                $target.prop('disabled', false);
                $wrapper.removeClass('hidden');
            },
            error() {
                console.error('Gagal memuat data dari ' + url);
            }
        });
    });
}

/* ── WILAYAH LAYER STATE ── */
const wilayahLayers = {
    kabupaten: null,
    kecamatan: null,
    kelurahan: null,
};

const WILAYAH_HIERARCHY = ['kabupaten', 'kecamatan', 'kelurahan'];

function clearWilayahLayer(level) {
    const fromIndex = WILAYAH_HIERARCHY.indexOf(level);
    WILAYAH_HIERARCHY.slice(fromIndex).forEach(lvl => {
        if (wilayahLayers[lvl] && window.petaMap) {
            window.petaMap.removeLayer(wilayahLayers[lvl]);
            wilayahLayers[lvl] = null;
        }
    });
}

function loadWilayahLayer(geojsonUrl, bboxUrl, kode, style, level) {
    if (!kode || !window.petaMap) return;

    clearWilayahLayer(level);

    $.ajax({
        url: geojsonUrl,
        type: 'GET',
        data: { kode },
        success(data) {
            wilayahLayers[level] = L.geoJSON(data, {
                style: style,
                onEachFeature(feature, layer) {
                    if (feature.properties?.nama) {
                        layer.bindPopup(`<strong>${feature.properties.nama}</strong>`);
                    }
                }
            }).addTo(window.petaMap);
        }
    });

    $.ajax({
        url: bboxUrl,
        type: 'GET',
        data: { kode },
        success(data) {
            if (data?.bbox) {
                window.petaMap.fitBounds([
                    [data.bbox[1], data.bbox[0]],
                    [data.bbox[3], data.bbox[2]]
                ], { padding: [24, 24], maxZoom: 14 });
            }
        }
    });
}

/* ── STYLE PER LEVEL ── */
const styleKabupaten = { color: '#2563eb', weight: 2.5, fillColor: '#2563eb', fillOpacity: 0.06 };
const styleKecamatan = { color: '#d97706', weight: 2,   fillColor: '#d97706', fillOpacity: 0.08 };
const styleKelurahan = { color: '#059669', weight: 1.5, fillColor: '#059669', fillOpacity: 0.10 };

/* ── INIT ── */
document.addEventListener('DOMContentLoaded', function () {

    // Search koordinat — enter key
    document.getElementById('search-koordinat')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') window.searchKoordinat?.();
    });

    // Kabupaten → load kecamatan
    dependentDropdown(
        '#filter-kabupaten', '#filter-kecamatan', '#wrapper-kecamatan',
        '/api/v1/wilayah/kecamatan', 'kode', 'Semua Kecamatan',
        function () {
            $('#filter-kelurahan').html('<option value="">Semua Kelurahan/Desa</option>').prop('disabled', true);
            $('#wrapper-kelurahan').addClass('hidden');
        }
    );

    // Kecamatan → load kelurahan
    dependentDropdown(
        '#filter-kecamatan', '#filter-kelurahan', '#wrapper-kelurahan',
        '/api/v1/wilayah/kelurahan', 'kode', 'Semua Kelurahan/Desa'
    );

    // Kabupaten change
    $('#filter-kabupaten').on('change', function () {
        const kode = $(this).val();
        if (kode) {
            loadWilayahLayer(
                '/api/v1/wilayah/kabupaten/geojson',
                '/api/v1/wilayah/kabupaten/bbox',
                kode, styleKabupaten, 'kabupaten'
            );
        } else {
            clearWilayahLayer('kabupaten');
            if (window.provinsiLayer && window.petaMap) {
                window.petaMap.fitBounds(window.provinsiLayer.getBounds(), { padding: [24, 24] });
            }
        }
    });

    // Kecamatan change
    $('#filter-kecamatan').on('change', function () {
        const kode = $(this).val();
        if (kode) {
            loadWilayahLayer(
                '/api/v1/wilayah/kecamatan/geojson',
                '/api/v1/wilayah/kecamatan/bbox',
                kode, styleKecamatan, 'kecamatan'
            );
        } else {
            clearWilayahLayer('kecamatan');
            if (wilayahLayers.kabupaten && window.petaMap) {
                window.petaMap.fitBounds(wilayahLayers.kabupaten.getBounds(), { padding: [24, 24] });
            }
        }
    });

    // Kelurahan change
    $('#filter-kelurahan').on('change', function () {
        const kode = $(this).val();
        if (kode) {
            loadWilayahLayer(
                '/api/v1/wilayah/kelurahan/geojson',
                '/api/v1/wilayah/kelurahan/bbox',
                kode, styleKelurahan, 'kelurahan'
            );
        } else {
            clearWilayahLayer('kelurahan');
            if (wilayahLayers.kecamatan && window.petaMap) {
                window.petaMap.fitBounds(wilayahLayers.kecamatan.getBounds(), { padding: [24, 24] });
            }
        }
    });
});
