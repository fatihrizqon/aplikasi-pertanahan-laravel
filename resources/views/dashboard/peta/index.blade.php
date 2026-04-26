<x-dashboard-layout :fullscreen="true">
    @push('styles')
    {{--
        MapLibre GL JS — pengganti Leaflet untuk vector tiles (MVT)
        WebGL rendering: jauh lebih cepat untuk ratusan ribu polygon
    --}}
    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css">
    <link rel="stylesheet" href="{{ asset('dist/css/map.css') }}">
    @endpush

    @include('dashboard.peta._sidebar')
    @include('dashboard.peta._map')

    @push('scripts')
    {{-- MapLibre GL JS (harus dimuat SEBELUM map.js) --}}
    <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>

    {{-- Script peta (urutan penting: map → layers → wilayah → peta-layers) --}}
    <script src="{{ asset('dist/js/dashboard/layers.js') }}" defer></script>
    <script src="{{ asset('dist/js/dashboard/map.js') }}" defer></script>
    <script src="{{ asset('dist/js/dashboard/wilayah.js') }}" defer></script>
    <script src="{{ asset('dist/js/dashboard/peta-layers.js') }}" defer></script>
    @endpush
</x-dashboard-layout>
