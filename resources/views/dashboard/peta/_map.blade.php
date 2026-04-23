{{-- ── MAP AREA ── --}}
<div class="relative flex-1 bg-gray-100 dark:bg-neutral-800" style="overflow:hidden; min-width:0; height:100%;">
    <div id="map" style="position:absolute; inset:0; width:100%; height:100%;"></div>
</div> <!-- /map-area -->
</div>{{-- /peta-wrapper --}}

{{-- ── Zoom Controls ── --}}
<div style="position:fixed; top:calc(var(--navbar-height,104px) + 8px); right:12px; z-index:1100; display:flex; flex-direction:column;">
    <button onclick="window.petaMap?.zoomIn()" title="Zoom In" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:4px 4px 0 0;border:1px solid #d1d5db;background:#fff;color:#374151;font-size:20px;font-weight:700;line-height:1;cursor:pointer;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#fff'">+</button>
    <button onclick="window.petaMap?.zoomOut()" title="Zoom Out" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:0 0 4px 4px;border:1px solid #d1d5db;border-top:none;background:#fff;color:#374151;font-size:24px;font-weight:300;line-height:1;cursor:pointer;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#fff'">−</button>
</div>

{{-- ── Basemap Switcher ── --}}
<div id="basemap-switcher" class="bg-neutral-900/50 rounded-lg p-4" style="position:fixed; bottom:10px; left:268px; z-index:1100; display:flex; gap:4px;">
    <button id="btn-osm" onclick="switchBasemap('osm')" title="OpenStreetMap" class="basemap-btn active" style="height:32px;padding:0 10px;border-radius:6px;border:1px solid #d1d5db;background:#fff;color:#374151;font-size:10px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px;box-shadow:0 1px 4px rgba(0,0,0,.1);">
        <i data-lucide="map" style="width:12px;height:12px;"></i> OSM
    </button>
    <button id="btn-satellite" onclick="switchBasemap('satellite')" title="Satelit" class="basemap-btn" style="height:32px;padding:0 10px;border-radius:6px;border:1px solid #d1d5db;background:#fff;color:#374151;font-size:10px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px;box-shadow:0 1px 4px rgba(0,0,0,.1);">
        <i data-lucide="satellite" style="width:12px;height:12px;"></i> Satelit
    </button>
    <button id="btn-terrain" onclick="switchBasemap('terrain')" title="Terrain" class="basemap-btn" style="height:32px;padding:0 10px;border-radius:6px;border:1px solid #d1d5db;background:#fff;color:#374151;font-size:10px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px;box-shadow:0 1px 4px rgba(0,0,0,.1);">
        <i data-lucide="mountain" style="width:12px;height:12px;"></i> Terrain
    </button>
    <button id="btn-grayscale" onclick="switchBasemap('grayscale')" title="Grayscale" class="basemap-btn" style="height:32px;padding:0 10px;border-radius:6px;border:1px solid #d1d5db;background:#fff;color:#374151;font-size:10px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px;box-shadow:0 1px 4px rgba(0,0,0,.1);">
        <i data-lucide="sun-moon" style="width:12px;height:12px;"></i> Grayscale
    </button>
</div>

{{-- ── Label Skala ── --}}
<div style="position:fixed;bottom:0;right:0;z-index:1100;display:flex;font-size:10px;line-height:1;user-select:none;font-family:monospace;">
    <div style="width:80px;background:#fff;border:1px solid #9ca3af;padding:4px;text-align:center;color:#6b7280;">SKALA</div>
    <div id="map-scale" style="width:140px;background:#fff;border:1px solid #9ca3af;border-left:none;padding:4px;text-align:center;font-weight:600;color:#374151;white-space:nowrap;overflow:hidden;">1 : 546.000</div>
</div>
