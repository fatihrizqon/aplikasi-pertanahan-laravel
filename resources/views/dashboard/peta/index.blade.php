<x-dashboard-layout :fullscreen="true">
    @push('styles')
    <link rel="stylesheet" href="{{ asset('dist/css/map.css') }}">
    @endpush

    @include('dashboard.peta._sidebar')
    @include('dashboard.peta._map')

    @push('scripts')
    <script src="{{ asset('dist/js/dashboard/layers.js') }}" defer></script>
    <script src="{{ asset('dist/js/dashboard/map.js') }}" defer></script>
    <script src="{{ asset('dist/js/dashboard/wilayah.js') }}" defer></script>
    <script src="{{ asset('dist/js/dashboard/peta-layers.js') }}" defer></script>
    @endpush
</x-dashboard-layout>
