/* ════════════════════════════════════════
   wilayah.js
   Depends on: window.petaMap, window.provinsiLayer (dari map.js)
               jQuery ($), Leaflet (L)
   Exposes:    -
════════════════════════════════════════ */

/* ── CONSTANTS ── */
const PROVINSI_DEFAULT = 34;

const API = {
    kabupaten: "/api/v1/wilayah/kabupaten",
    kecamatan: "/api/v1/wilayah/kecamatan",
    kelurahan: "/api/v1/wilayah/kelurahan",
    provinsiGeojson: "/api/v1/wilayah/provinsi/geojson",
};

const WILAYAH_HIERARCHY = ["kabupaten", "kecamatan", "kelurahan"];

const LEVEL_CONFIG = {
    kabupaten: {
        label: "Semua Kabupaten/Kota",
        selector: "#filter-kabupaten",
        wrapper: "#wrapper-kabupaten",
        geojson: "/api/v1/wilayah/kabupaten/geojson",
        bbox: "/api/v1/wilayah/kabupaten/bbox",
        style: {
            color: "#2563eb",
            weight: 2.5,
            fillColor: "#2563eb",
            fillOpacity: 0.06,
        },
        children: ["kecamatan", "kelurahan"],
    },
    kecamatan: {
        label: "Semua Kapanewon/Kemantren",
        selector: "#filter-kecamatan",
        wrapper: "#wrapper-kecamatan",
        geojson: "/api/v1/wilayah/kecamatan/geojson",
        bbox: "/api/v1/wilayah/kecamatan/bbox",
        style: {
            color: "#d97706",
            weight: 2,
            fillColor: "#d97706",
            fillOpacity: 0.08,
        },
        children: ["kelurahan"],
    },
    kelurahan: {
        label: "Semua Kalurahan/Kelurahan",
        selector: "#filter-kelurahan",
        wrapper: "#wrapper-kelurahan",
        geojson: "/api/v1/wilayah/kelurahan/geojson",
        bbox: "/api/v1/wilayah/kelurahan/bbox",
        style: {
            color: "#059669",
            weight: 1.5,
            fillColor: "#059669",
            fillOpacity: 0.1,
        },
        children: [],
    },
};

/* ── LAYER STATE ── */
const wilayahLayers = { kabupaten: null, kecamatan: null, kelurahan: null };

/* ── HELPERS ── */

/** Hapus layer dari level tertentu ke bawah (cascade). */
function clearWilayahLayer(fromLevel) {
    const fromIndex = WILAYAH_HIERARCHY.indexOf(fromLevel);
    WILAYAH_HIERARCHY.slice(fromIndex).forEach((level) => {
        if (wilayahLayers[level] && window.petaMap) {
            window.petaMap.removeLayer(wilayahLayers[level]);
            wilayahLayers[level] = null;
        }
    });
}

/** Reset dropdown anak (children) ke kondisi awal & sembunyikan wrapper-nya. */
function resetChildDropdowns(children) {
    children.forEach((level) => {
        const { selector, wrapper, label } = LEVEL_CONFIG[level];
        $(selector)
            .html(`<option value="">${label}</option>`)
            .prop("disabled", true);
        $(wrapper).addClass("hidden");
    });
}

/** Muat GeoJSON + fit bounds untuk satu level wilayah. */
function loadWilayahLayer(level, kode) {
    if (!kode || !window.petaMap) return;

    const { geojson, bbox, style } = LEVEL_CONFIG[level];
    clearWilayahLayer(level);

    $.ajax({
        url: geojson,
        type: "GET",
        data: { kode },
        success(data) {
            wilayahLayers[level] = L.geoJSON(data, {
                style,
                onEachFeature(feature, layer) {
                    if (feature.properties?.nama) {
                        layer.bindPopup(
                            `<strong>${feature.properties.nama}</strong>`,
                        );
                    }
                },
            }).addTo(window.petaMap);
        },
    });

    $.ajax({
        url: bbox,
        type: "GET",
        data: { kode },
        success(data) {
            if (data?.bbox) {
                window.petaMap.fitBounds(
                    [
                        [data.bbox[1], data.bbox[0]],
                        [data.bbox[3], data.bbox[2]],
                    ],
                    { padding: [24, 24], maxZoom: 14 },
                );
            }
        },
    });
}

/** Fit bounds ke parent layer, atau ke provinsi sebagai fallback. */
function fitBoundsToParent(currentLevel) {
    const idx = WILAYAH_HIERARCHY.indexOf(currentLevel);
    const parentLevel = WILAYAH_HIERARCHY[idx - 1];
    const parentLayer = parentLevel ? wilayahLayers[parentLevel] : null;

    const targetLayer = parentLayer ?? window.provinsiLayer;
    if (targetLayer && window.petaMap) {
        window.petaMap.fitBounds(targetLayer.getBounds(), {
            padding: [24, 24],
        });
    }
}

/**
 * Update petaLayers.wilayah dan trigger re-fetch semua layer aktif.
 * Dipanggil setiap kali salah satu dropdown wilayah berubah.
 */
function syncWilayahFilter() {
    if (typeof petaLayers === "undefined") return;

    const kabupaten = $("#filter-kabupaten").val() || null;
    const kecamatan = $("#filter-kecamatan").val() || null;
    const kelurahan = $("#filter-kelurahan").val() || null;

    petaLayers.wilayah.kabupaten_kode = kabupaten;
    petaLayers.wilayah.kecamatan_kode = kecamatan;
    petaLayers.wilayah.kelurahan_kode = kelurahan;

    // Trigger ulang fetch untuk semua layer yang sedang aktif
    const aktifKategori =
        typeof getKategoriAktif === "function" ? getKategoriAktif() : [];
    const aktifPenggunaan =
        typeof getPenggunaanAktif === "function" ? getPenggunaanAktif() : [];
    const aktifJenisHak =
        typeof getJenisHakAktif === "function" ? getJenisHakAktif() : [];
    const aktifJenisHakAdat =
        typeof getJenisHakAdatAktif === "function"
            ? getJenisHakAdatAktif()
            : [];
    const aktifStatusKesesuaian =
        typeof getStatusKesesuaianAktif === "function"
            ? getStatusKesesuaianAktif()
            : [];

    if (aktifKategori.length > 0) fetchDanRenderBidang(aktifKategori);
    if (aktifPenggunaan.length > 0)
        fetchDanRenderBidangPenggunaan(aktifPenggunaan);
    if (aktifJenisHak.length > 0) fetchDanRenderBidangJenisHak(aktifJenisHak);
    if (aktifJenisHakAdat.length > 0)
        fetchDanRenderBidangJenisHakAdat(aktifJenisHakAdat);
    if (aktifStatusKesesuaian.length > 0)
        fetchDanRenderBidangStatusKesesuaian(aktifStatusKesesuaian);
}

/** Buat dependent dropdown yang otomatis load opsi via AJAX saat trigger berubah. */
function setupDependentDropdown(level, parentLevel) {
    const { selector: parentSelector } = LEVEL_CONFIG[parentLevel];
    const { selector, wrapper, label, children } = LEVEL_CONFIG[level];

    $(parentSelector).on("change", function () {
        const kode = $(this).val();
        $(selector)
            .html(`<option value="">${label}</option>`)
            .prop("disabled", true);
        $(wrapper).addClass("hidden");
        resetChildDropdowns(children);

        if (!kode) return;

        $.ajax({
            url: API[level],
            type: "GET",
            data: { kode },
            success(data) {
                data.forEach((item) =>
                    $(selector).append(
                        `<option value="${item.value}">${item.label}</option>`,
                    ),
                );
                $(selector).prop("disabled", false);
                $(wrapper).removeClass("hidden");
            },
            error() {
                console.error(`Gagal memuat data ${level} untuk kode ${kode}`);
            },
        });
    });
}

/** Pasang event change untuk memuat layer peta + sync filter. */
function setupLayerChangeHandler(level) {
    const { selector } = LEVEL_CONFIG[level];

    $(selector).on("change", function () {
        const kode = $(this).val();
        if (kode) {
            loadWilayahLayer(level, kode);
        } else {
            clearWilayahLayer(level);
            fitBoundsToParent(level);
        }
        // Sync ke petaLayers.wilayah dan re-fetch layer aktif
        syncWilayahFilter();
    });
}

/* ── INIT ── */
document.addEventListener("DOMContentLoaded", function () {
    // Search koordinat — enter key
    document
        .getElementById("search-koordinat")
        ?.addEventListener(
            "keydown",
            (e) => e.key === "Enter" && window.searchKoordinat?.(),
        );

    // Dependent dropdowns
    setupDependentDropdown("kecamatan", "kabupaten");
    setupDependentDropdown("kelurahan", "kecamatan");

    // Layer change handlers (termasuk sync filter ke peta-layers)
    WILAYAH_HIERARCHY.forEach(setupLayerChangeHandler);

    // Load kabupaten default (DIY)
    $.ajax({
        url: API.kabupaten,
        data: { kode: PROVINSI_DEFAULT },
        success(data) {
            data.forEach((item) => {
                $("#filter-kabupaten").append(
                    `<option value="${item.value}">${item.label}</option>`,
                );
            });
        },
    });

    // Load GeoJSON + fit bounds provinsi default
    // Gunakan waitForMap() agar petaMap sudah siap sebelum .addTo() dipanggil
    waitForMap(function () {
        $.ajax({
            url: API.provinsiGeojson,
            data: { kode: PROVINSI_DEFAULT },
            success(geojson) {
                window.provinsiLayer = L.geoJSON(geojson, {
                    style: {
                        color: "#dc2626",
                        weight: 2.5,
                        fillColor: "#dc2626",
                        fillOpacity: 0.04,
                    },
                    onEachFeature(feature, layer) {
                        if (feature.properties?.nama)
                            layer.bindPopup(
                                `<strong>${feature.properties.nama}</strong>`,
                            );
                    },
                }).addTo(window.petaMap);

                window.petaMap.fitBounds(window.provinsiLayer.getBounds(), {
                    padding: [24, 24],
                });
            },
        });
    });
});

/**
 * Tunggu window.petaMap tersedia, baru jalankan callback.
 * Prioritas: event petaMapReady (dari map.js), fallback ke polling.
 */
function waitForMap(cb) {
    if (window.petaMap) {
        cb();
    } else {
        window.addEventListener('petaMapReady', () => cb(), { once: true });
    }
}
