<x-dashboard-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('dist/css/map.css') }}">
    @endpush

    @include('dashboard.peta._sidebar')
    @include('dashboard.peta._map')

    @push('scripts')
    <script>
        const provinsiData = @json($provinsi);
        const kabupatenData = @json($data['kabupaten']);

        const kategoriData = @json($data['filters']['kategori']);
        const jenisHakData = @json($data['filters']['jenis_hak']);
    </script>
    <script src="{{ asset('dist/js/dashboard/layers.js') }}"></script>
    <script src="{{ asset('dist/js/dashboard/map.js') }}"></script>
    <script src="{{ asset('dist/js/dashboard/wilayah.js') }}"></script>
    <script src="{{ asset('dist/js/dashboard/peta-layers.js') }}"></script>
    @endpush
</x-dashboard-layout>
