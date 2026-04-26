@php
$action = empty($model->id) ? route('persil.bidang.store') : route('persil.bidang.update', $model->id);

$page_title = empty($model->id) ? 'Tambah Bidang Baru' : 'Edit Bidang #' . $model->nomor_bidang;
$page_subtitle = "Lengkapi semua informasi bidang persil di bawah ini.";
@endphp

<x-dashboard-layout>
    <div class="grid grid-cols-1 gap-6">

        {{-- Page Header --}}
        <div class="space-y-1">
            <div class="flex items-center gap-x-3">
                <i data-lucide="map" class="size-7 text-blue-600 dark:text-blue-400"></i>
                <h1 class="page-title">{{ $page_title }}</h1>
            </div>
            <p class="page-subtitle">{{ $page_subtitle }}</p>
        </div>

        <div class="flex flex-col border border-gray-200 rounded-lg bg-white shadow-sm dark:bg-neutral-900 dark:border-neutral-700">
            {{-- Form Card --}}
            <form id="form-element" action="{{ $action }}" method="POST" enctype="multipart/form-data" class="modal-wrapper">
                @csrf
                @if (!empty($model->id))
                @method('PUT')
                @endif

                {{-- ── Body ── --}}
                <div class="modal-body space-y-0 divide-y divide-gray-100 dark:divide-neutral-700 px-2">

                    {{-- ════ 1. Identifikasi ════ --}}
                    <div class="py-5">
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-4">Identifikasi</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- Nomor Bidang --}}
                            <div>
                                <x-input-label for="nomor_bidang" class="mb-1.5">
                                    Nomor Bidang <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input id="nomor_bidang" class="block w-full" type="text" name="nomor_bidang" value="{{ old('nomor_bidang', $model->nomor_bidang) }}" placeholder="Contoh: BD-001" autocomplete="off" />
                            </div>

                            {{-- Kategori --}}
                            <div>
                                <x-input-label for="id_kategori" class="mb-1.5">Kategori</x-input-label>
                                <x-select-dropdown name="id_kategori" :options="$data['options']['kategori']" :selected="old('id_kategori', $model->id_kategori)" placeholder="Pilih kategori..." />
                            </div>

                            <!--
                            {{-- Pemilik --}}
                            <div>
                                <x-input-label class="mb-2">Pemilik</x-input-label>
                                <div class="flex flex-wrap gap-3 pt-0.5">
                                    @foreach(['kasultanan' => 'Kasultanan', 'kadipaten' => 'Kadipaten'] as $val => $label)
                                    <label for="pemilik_{{ $val }}" class="flex items-center gap-x-2.5 py-2 px-4 border border-gray-200 rounded-lg cursor-pointer text-sm text-gray-700 hover:bg-gray-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:has-[:checked]:border-blue-500 dark:has-[:checked]:bg-blue-900/20 dark:has-[:checked]:text-blue-400 transition-all">
                                        <input type="radio" id="pemilik_{{ $val }}" name="pemilik" value="{{ $val }}" {{ old('pemilik', $model->pemilik) === $val ? 'checked' : '' }}
                                        class="shrink-0 size-3.5 text-blue-600 border-gray-300 focus:ring-blue-500 dark:border-neutral-600">
                                        {{ $label }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            -->

                            <div>
                                <x-input-label for="id_jenis_hak" class="mb-1.5">Jenis Hak</x-input-label>
                                <x-select-dropdown name="id_jenis_hak" :options="$data['options']['jenis_hak']" :selected="old('id_jenis_hak', $model->id_jenis_hak)" placeholder="Pilih jenis hak..." />
                            </div>

                            {{-- Nomor Hak --}}
                            <div>
                                <x-input-label for="nomor_hak" class="mb-1.5">Nomor Hak</x-input-label>
                                <x-text-input id="nomor_hak" class="block w-full" type="text" name="nomor_hak" value="{{ old('nomor_hak', $model->nomor_hak) }}" placeholder="Contoh: SHM-12345" autocomplete="off" />
                            </div>

                            <div>
                                <x-input-label for="id_jenis_hak_adat" class="mb-1.5">Jenis Hak Adat</x-input-label>
                                <x-select-dropdown name="id_jenis_hak_adat" :options="$data['options']['jenis_hak_adat']" :selected="old('id_jenis_hak_adat', $model->id_jenis_hak_adat)" placeholder="Pilih jenis hak adat..." />
                            </div>

                            {{-- Nomor Hak Adat --}}
                            <div>
                                <x-input-label for="nomor_hak_adat" class="mb-1.5">Nomor Hak Adat</x-input-label>
                                <x-text-input id="nomor_hak_adat" class="block w-full" type="text" name="nomor_hak_adat" value="{{ old('nomor_hak_adat', $model->nomor_hak_adat) }}" placeholder="Contoh: HA-001" autocomplete="off" />
                            </div>

                        </div>
                    </div>

                    {{-- ════ 2. Pengelolaan ════ --}}
                    <div class="py-5">
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-4">Pengelolaan & Kesesuaian Tata Ruang</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                            <div>
                                <x-input-label for="id_pengelola" class="mb-1.5">Pengelola</x-input-label>
                                <x-select-dropdown name="id_pengelola" :options="$data['options']['pengelola']" :selected="old('id_pengelola', $model->id_pengelola)" placeholder="Pilih pengelola..." :searchable="true" />
                            </div>

                            <div>
                                <x-input-label for="id_penggunaan" class="mb-1.5">Penggunaan</x-input-label>
                                <x-select-dropdown name="id_penggunaan" :options="$data['options']['penggunaan']" :selected="old('id_penggunaan', $model->id_penggunaan)" placeholder="Pilih penggunaan..." :searchable="true" />
                            </div>

                            <div>
                                <x-input-label for="id_status_kesesuaian" class="mb-1.5">Status Kesesuaian Tata Ruang</x-input-label>
                                <x-select-dropdown name="id_status_kesesuaian" :options="$data['options']['status_kesesuaian']" :selected="old('id_status_kesesuaian', $model->id_status_kesesuaian)" placeholder="Pilih status kesesuaian..." />
                            </div>

                        </div>
                    </div>

                    {{-- ════ 3. Fisik & Spasial ════ --}}
                    <div class="py-5">
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-4">Fisik & Spasial</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- Luas --}}
                            <div>
                                <x-input-label for="luas" class="mb-1.5">Luas (m²)</x-input-label>
                                <div class="relative">
                                    <x-text-input id="luas" class="block w-full pe-12" type="number" step="0.01" min="0" name="luas" value="{{ old('luas', $model->luas) }}" placeholder="0.00" autocomplete="off" />
                                    <div class="absolute inset-y-0 end-0 flex items-center pe-3 pointer-events-none">
                                        <span class="text-xs font-medium text-gray-400 dark:text-neutral-500">m²</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Koordinat --}}
                            <div>
                                <x-input-label for="koordinat" class="mb-1.5">Koordinat</x-input-label>
                                <x-text-input id="koordinat" class="block w-full" type="text" name="koordinat" value="{{ old('koordinat', $model->koordinat) }}" placeholder="-7.7829, 110.3671" autocomplete="off" />
                            </div>

                            {{-- Geom --}}
                            <div class="sm:col-span-2">
                                <x-input-label for="geom" class="mb-1.5">
                                    Geometri
                                    <span class="ms-1.5 inline-flex items-center gap-x-1 text-xs font-normal text-gray-400 dark:text-neutral-500">
                                        <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 16v-4m0-4h.01" />
                                        </svg>
                                        GeoJSON / WKT
                                    </span>
                                </x-input-label>
                                <textarea id="geom" name="geom" rows="3" class="font-mono py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-xs text-gray-800 focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 dark:placeholder-neutral-500 dark:focus:border-neutral-600" placeholder='{"type":"Polygon","coordinates":[[[...]]]}'>{{ old('geom', $model->geom) }}</textarea>
                            </div>

                        </div>
                    </div>

                    {{-- ════ 5. Keterangan ════ --}}
                    <div class="py-5">
                        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-4">Keterangan</p>
                        <textarea id="keterangan" name="keterangan" rows="3" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 dark:placeholder-neutral-500 dark:focus:border-neutral-600" placeholder="Catatan tambahan mengenai bidang ini..." data-hs-textarea-auto-height="">{{ old('keterangan', $model->keterangan) }}</textarea>
                    </div>

                </div>{{-- end modal-body --}}

                {{-- ── Footer ── --}}
                <div class="modal-footer flex items-center justify-between">
                    <p class="text-xs text-gray-400 dark:text-neutral-500">
                        <span class="text-red-500">*</span> Wajib diisi
                    </p>
                    <div class="flex items-center gap-x-2">
                        <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 transition-colors" onclick="window.HSOverlay.close(document.getElementById('modal-form-ajax'))">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none transition-colors">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script type="module">
        jsonScriptToFormFields('#form-element', @json($model));
        $('#form-element').formAjaxSubmit();
    </script>
</x-dashboard-layout>
