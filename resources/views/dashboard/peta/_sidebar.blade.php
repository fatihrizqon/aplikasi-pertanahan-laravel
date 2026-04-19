<div id="peta-wrapper" class="flex bg-white dark:bg-neutral-900" style="position:fixed; top:var(--navbar-height,112px); left:0; right:0; bottom:0; z-index:10; overflow:hidden;">
    <div id="peta-sidebar" class="flex flex-col flex-shrink-0 bg-white dark:bg-neutral-900 border-r border-gray-200 dark:border-neutral-700" style="width:260px; height:100%; overflow-y:auto; overflow-x:hidden;">
        <div class="flex flex-shrink-0 border-b border-gray-200 dark:border-neutral-700" style="position:sticky; top:0; z-index:10; background:inherit;">
            <nav class="flex w-full" aria-label="Tabs" role="tablist" data-hs-tabs='{"tabActiveClasses":"text-red-600 border-red-600 dark:text-red-400 dark:border-red-400"}'>
                <button type="button" id="tab-layer" class="hs-tab-active:text-red-600 hs-tab-active:border-red-600 dark:hs-tab-active:text-red-400 dark:hs-tab-active:border-red-400 active flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-semibold border-b-2 border-transparent text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 transition-all" aria-selected="true" data-hs-tab="#panel-layer" aria-controls="panel-layer" role="tab">
                    <i data-lucide="layers" class="w-3 h-3"></i>
                    LAYER
                </button>
                <button type="button" id="tab-radius" class="hs-tab-active:text-red-600 hs-tab-active:border-red-600 dark:hs-tab-active:text-red-400 dark:hs-tab-active:border-red-400 flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-semibold border-b-2 border-transparent text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 transition-all" aria-selected="false" data-hs-tab="#panel-radius" aria-controls="panel-radius" role="tab">
                    <i data-lucide="radius" class="w-3 h-3"></i>
                    RADIUS
                </button>
            </nav>
        </div>

        <div id="panel-layer" role="tabpanel" aria-labelledby="tab-layer" class="flex flex-col flex-shrink-0">
            <div class="flex-shrink-0 border-b border-gray-100 dark:border-neutral-800 bg-white dark:bg-neutral-900" style="top:40px; z-index:9;">
                <div class="p-3 space-y-2">
                    <div class="relative">
                        <input type="text" id="search-koordinat" name="search-koordinat" placeholder="Cari latitude, longitude" autocomplete="off" class="py-1.5 px-3 pr-8 block w-full border-gray-200 rounded-lg text-xs focus:border-red-500 focus:ring-red-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                        <button type="button" onclick="searchKoordinat()" class="absolute inset-y-0 right-2 flex items-center text-gray-400 hover:text-red-500 transition">
                            <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 dark:text-neutral-500">Contoh: -7.805147, 110.363054</p>

                    <select id="filter-provinsi" class="py-1.5 px-2.5 block w-full border-gray-200 rounded-lg text-xs bg-gray-50 text-gray-500 cursor-not-allowed dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-400" disabled>
                        <option value="34" selected>Daerah Istimewa Yogyakarta</option>
                    </select>

                    {{-- Filter Kabupaten (diisi via API saat load) --}}
                    <div id="wrapper-kabupaten">
                        <select id="filter-kabupaten" class="py-1.5 px-2.5 block w-full border-gray-200 rounded-lg text-xs focus:border-red-500 focus:ring-red-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:focus:ring-neutral-600">
                            <option value="">Semua Kabupaten/Kota</option>
                        </select>
                    </div>

                    {{-- Filter Kecamatan (muncul setelah kabupaten dipilih) --}}
                    <div id="wrapper-kecamatan" class="hidden">
                        <select id="filter-kecamatan" class="py-1.5 px-2.5 block w-full border-gray-200 rounded-lg text-xs
                                       focus:border-red-500 focus:ring-red-500
                                       dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400
                                       dark:focus:ring-neutral-600">
                            <option value="">Semua Kapanewon/Kemantren</option>
                        </select>
                    </div>

                    {{-- Filter Kelurahan (muncul setelah kecamatan dipilih) --}}
                    <div id="wrapper-kelurahan" class="hidden">
                        <select id="filter-kelurahan" class="py-1.5 px-2.5 block w-full border-gray-200 rounded-lg text-xs
                                       focus:border-red-500 focus:ring-red-500
                                       dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400
                                       dark:focus:ring-neutral-600">
                            <option value="">Semua Kalurahan/Kelurahan</option>
                        </select>
                    </div>

                </div>
            </div>

            {{-- Layer Tree --}}
            <div id="layer-tree" role="tree" aria-orientation="vertical" data-hs-tree-view>
                {{-- ================================ --}}
                {{-- GROUP 1: Kategori --}}
                {{-- ================================ --}}
                <div class="hs-accordion active border-b border-gray-100 dark:border-neutral-800/60" role="treeitem" aria-expanded="true" id="hs-layer-heading-kategori" data-hs-tree-view-item='{"value": "Kepemilikan Tanah", "isDir": true}'>
                    {{-- Header Group --}}
                    <div class="hs-accordion-heading">
                        <div class="flex items-center gap-2 px-3 py-[9px] hover:bg-gray-50 dark:hover:bg-neutral-800/70 select-none">
                            <input type="checkbox" id="cb-group-kategori" onclick="event.stopPropagation()" onchange="onGroupChange(this, 'kategori')" class="shrink-0 size-3.5 rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-neutral-600 dark:bg-neutral-800 cursor-pointer">
                            <span class="flex-1 text-[11.5px] font-medium text-gray-700 dark:text-neutral-200 leading-snug">
                                Kategori
                            </span>
                            <button class="hs-accordion-toggle shrink-0" aria-expanded="true" aria-controls="hs-layer-collapse-kategori" onclick="event.stopPropagation()">
                                <svg class="size-3 text-gray-400 dark:text-neutral-500 hs-accordion-active:rotate-90 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Accordion Content: Kategori --}}
                    <div id="hs-layer-collapse-kategori" class="hs-accordion-content overflow-hidden transition-[height] duration-300" role="group" aria-labelledby="hs-layer-heading-kategori">
                        {{-- Opacity Slider --}}
                        <div class="flex items-center gap-2 px-3 pb-2 pt-0.5" onclick="event.stopPropagation()">
                            <svg class="size-3 text-gray-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <input type="range" min="0" max="100" value="100" class="flex-1 h-1 accent-red-600 cursor-pointer" oninput="updateOpacity(this, 'group-kategori')">
                            <span class="text-[10px] text-gray-400 dark:text-neutral-500 w-7 text-right opacity-val">100%</span>
                        </div>
                        @foreach($data['filters']['kategori'] as $kategori)
                        <div role="treeitem" class="flex items-center gap-2 px-3 py-1.5 pl-6 hover:bg-gray-50 dark:hover:bg-neutral-800/50" data-hs-tree-view-item='{"value": "{{ $kategori->nama }}"}'>
                            <input type="checkbox" data-kategori-id="{{ $kategori->id }}" onchange="onKategoriChange()" class="layer-kategori-cb shrink-0 size-3.5 rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-neutral-600 dark:bg-neutral-800 cursor-pointer">
                            <span class="shrink-0 size-2 rounded-full border border-black/10" style="background-color: {{ $kategori->warna ?? '#94a3b8' }};"></span>
                            <span class="flex-1 text-[11.5px] text-gray-600 dark:text-neutral-300 leading-snug">
                                {{ $kategori->nama }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- ================================ --}}
                {{-- GROUP 2: Penggunaan --}}
                {{-- ================================ --}}
                <div class="hs-accordion border-b border-gray-100 dark:border-neutral-800/60" role="treeitem" aria-expanded="false" id="hs-layer-heading-penggunaan" data-hs-tree-view-item='{"value": "Penggunaan Tanah", "isDir": true}'>
                    {{-- Header Group --}}
                    <div class="hs-accordion-heading">
                        <div class="flex items-center gap-2 px-3 py-[9px] hover:bg-gray-50 dark:hover:bg-neutral-800/70 select-none">
                            <input type="checkbox" id="cb-group-penggunaan" onclick="event.stopPropagation()" onchange="onGroupChange(this, 'penggunaan')" class="shrink-0 size-3.5 rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-neutral-600 dark:bg-neutral-800 cursor-pointer">
                            <span class="flex-1 text-[11.5px] font-medium text-gray-700 dark:text-neutral-200 leading-snug">
                                Penggunaan
                            </span>
                            <button class="hs-accordion-toggle shrink-0" aria-expanded="true" aria-controls="hs-layer-collapse-penggunaan" onclick="event.stopPropagation()">
                                <svg class="size-3 text-gray-400 dark:text-neutral-500 hs-accordion-active:rotate-90 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Accordion Content: Penggunaan --}}
                    <div id="hs-layer-collapse-penggunaan" class="hs-accordion-content hidden overflow-hidden transition-[height] duration-300" role="group" aria-labelledby="hs-layer-heading-penggunaan">
                        {{-- Opacity Slider --}}
                        <div class="flex items-center gap-2 px-3 pb-2 pt-0.5" onclick="event.stopPropagation()">
                            <svg class="size-3 text-gray-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <input type="range" min="0" max="100" value="100" class="flex-1 h-1 accent-red-600 cursor-pointer" oninput="updateOpacity(this, 'group-penggunaan')">
                            <span class="text-[10px] text-gray-400 dark:text-neutral-500 w-7 text-right opacity-val">100%</span>
                        </div>
                        @foreach($data['filters']['penggunaan'] as $penggunaan)
                        <div role="treeitem" class="flex items-center gap-2 px-3 py-1.5 pl-6 hover:bg-gray-50 dark:hover:bg-neutral-800/50" data-hs-tree-view-item='{"value": "{{ $penggunaan->nama }}"}'>
                            <input type="checkbox" data-penggunaan-id="{{ $penggunaan->id }}" onchange="onPenggunaanChange()" class="layer-penggunaan-cb shrink-0 size-3.5 rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-neutral-600 dark:bg-neutral-800 cursor-pointer">
                            <span class="shrink-0 size-2 rounded-full border border-black/10" style="background-color: {{ $penggunaan->warna ?? '#94a3b8' }};"></span>
                            <span class="flex-1 text-[11.5px] text-gray-600 dark:text-neutral-300 leading-snug">
                                {{ $penggunaan->nama }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- ================================ --}}
                {{-- GROUP 3: Jenis Hak --}}
                {{-- ================================ --}}
                <div class="hs-accordion border-b border-gray-100 dark:border-neutral-800/60" role="treeitem" aria-expanded="false" id="hs-layer-heading-jenis-hak" data-hs-tree-view-item='{"value": "Jenis Hak", "isDir": true}'>
                    {{-- Header Group --}}
                    <div class="hs-accordion-heading">
                        <div class="flex items-center gap-2 px-3 py-[9px] hover:bg-gray-50 dark:hover:bg-neutral-800/70 select-none">
                            <input type="checkbox" id="cb-group-jenis-hak" onclick="event.stopPropagation()" onchange="onGroupChange(this, 'jenis-hak')" class="shrink-0 size-3.5 rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-neutral-600 dark:bg-neutral-800 cursor-pointer">
                            <span class="flex-1 text-[11.5px] font-medium text-gray-700 dark:text-neutral-200 leading-snug">
                                Jenis Hak
                            </span>
                            <button class="hs-accordion-toggle shrink-0" aria-expanded="true" aria-controls="hs-layer-collapse-jenis-hak" onclick="event.stopPropagation()">
                                <svg class="size-3 text-gray-400 dark:text-neutral-500 hs-accordion-active:rotate-90 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Accordion Content: Jenis Hak --}}
                    <div id="hs-layer-collapse-jenis-hak" class="hs-accordion-content hidden overflow-hidden transition-[height] duration-300" role="group" aria-labelledby="hs-layer-heading-jenis-hak">
                        {{-- Opacity Slider --}}
                        <div class="flex items-center gap-2 px-3 pb-2 pt-0.5" onclick="event.stopPropagation()">
                            <svg class="size-3 text-gray-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <input type="range" min="0" max="100" value="100" class="flex-1 h-1 accent-red-600 cursor-pointer" oninput="updateOpacity(this, 'group-jenis-hak')">
                            <span class="text-[10px] text-gray-400 dark:text-neutral-500 w-7 text-right opacity-val">100%</span>
                        </div>
                        @foreach($data['filters']['JenisHak'] as $jenisHak)
                        <div role="treeitem" class="flex items-center gap-2 px-3 py-1.5 pl-6 hover:bg-gray-50 dark:hover:bg-neutral-800/50" data-hs-tree-view-item='{"value": "{{ $jenisHak->nama }}"}'>
                            <input type="checkbox" data-jenis-hak-id="{{ $jenisHak->id }}" onchange="onJenisHakChange()" class="layer-jenis-hak-cb shrink-0 size-3.5 rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-neutral-600 dark:bg-neutral-800 cursor-pointer">
                            <span class="shrink-0 size-2 rounded-full border border-black/10" style="background-color: {{ $jenisHak->warna ?? '#94a3b8' }};"></span>
                            <span class="flex-1 text-[11.5px] text-gray-600 dark:text-neutral-300 leading-snug">
                                {{ $jenisHak->nama }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- ================================ --}}
                {{-- GROUP 4: Jenis Hak Adat --}}
                {{-- ================================ --}}
                <div class="hs-accordion border-b border-gray-100 dark:border-neutral-800/60" role="treeitem" aria-expanded="false" id="hs-layer-heading-jenis-hak-adat" data-hs-tree-view-item='{"value": "Jenis Hak Adat", "isDir": true}'>
                    {{-- Header Group --}}
                    <div class="hs-accordion-heading">
                        <div class="flex items-center gap-2 px-3 py-[9px] hover:bg-gray-50 dark:hover:bg-neutral-800/70 select-none">
                            <input type="checkbox" id="cb-group-jenis-hak-adat" onclick="event.stopPropagation()" onchange="onGroupChange(this, 'jenis-hak-adat')" class="shrink-0 size-3.5 rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-neutral-600 dark:bg-neutral-800 cursor-pointer">
                            <span class="flex-1 text-[11.5px] font-medium text-gray-700 dark:text-neutral-200 leading-snug">
                                Jenis Hak Adat
                            </span>
                            <button class="hs-accordion-toggle shrink-0" aria-expanded="true" aria-controls="hs-layer-collapse-jenis-hak-adat" onclick="event.stopPropagation()">
                                <svg class="size-3 text-gray-400 dark:text-neutral-500 hs-accordion-active:rotate-90 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Accordion Content: Jenis Hak Adat --}}
                    <div id="hs-layer-collapse-jenis-hak-adat" class="hs-accordion-content hidden overflow-hidden transition-[height] duration-300" role="group" aria-labelledby="hs-layer-heading-jenis-hak-adat">
                        {{-- Opacity Slider --}}
                        <div class="flex items-center gap-2 px-3 pb-2 pt-0.5" onclick="event.stopPropagation()">
                            <svg class="size-3 text-gray-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <input type="range" min="0" max="100" value="100" class="flex-1 h-1 accent-red-600 cursor-pointer" oninput="updateOpacity(this, 'group-jenis-hak-adat')">
                            <span class="text-[10px] text-gray-400 dark:text-neutral-500 w-7 text-right opacity-val">100%</span>
                        </div>
                        @foreach($data['filters']['JenisHakAdat'] as $jenisHakAdat)
                        <div role="treeitem" class="flex items-center gap-2 px-3 py-1.5 pl-6 hover:bg-gray-50 dark:hover:bg-neutral-800/50" data-hs-tree-view-item='{"value": "{{ $jenisHakAdat->nama }}"}'>
                            <input type="checkbox" data-jenis-hak-adat-id="{{ $jenisHakAdat->id }}" onchange="onJenisHakAdatChange()" class="layer-jenis-hak-adat-cb shrink-0 size-3.5 rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-neutral-600 dark:bg-neutral-800 cursor-pointer">
                            <span class="shrink-0 size-2 rounded-full border border-black/10" style="background-color: {{ $jenisHakAdat->warna ?? '#94a3b8' }};"></span>
                            <span class="flex-1 text-[11.5px] text-gray-600 dark:text-neutral-300 leading-snug">
                                {{ $jenisHakAdat->nama }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- ================================ --}}
                {{-- GROUP 5: Status Kesesuaian --}}
                {{-- ================================ --}}
                <div class="hs-accordion border-b border-gray-100 dark:border-neutral-800/60" role="treeitem" aria-expanded="false" id="hs-layer-heading-status-kesesuaian" data-hs-tree-view-item='{"value": "Status Kesesuaian", "isDir": true}'>
                    {{-- Header Group --}}
                    <div class="hs-accordion-heading">
                        <div class="flex items-center gap-2 px-3 py-[9px] hover:bg-gray-50 dark:hover:bg-neutral-800/70 select-none">
                            <input type="checkbox" id="cb-group-status-kesesuaian" onclick="event.stopPropagation()" onchange="onGroupChange(this, 'status-kesesuaian')" class="shrink-0 size-3.5 rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-neutral-600 dark:bg-neutral-800 cursor-pointer">
                            <span class="flex-1 text-[11.5px] font-medium text-gray-700 dark:text-neutral-200 leading-snug">
                                Status Kesesuaian
                            </span>
                            <button class="hs-accordion-toggle shrink-0" aria-expanded="true" aria-controls="hs-layer-collapse-status-kesesuaian" onclick="event.stopPropagation()">
                                <svg class="size-3 text-gray-400 dark:text-neutral-500 hs-accordion-active:rotate-90 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Accordion Content: Status Kesesuaian --}}
                    <div id="hs-layer-collapse-status-kesesuaian" class="hs-accordion-content hidden overflow-hidden transition-[height] duration-300" role="group" aria-labelledby="hs-layer-heading-status-kesesuaian">
                        {{-- Opacity Slider --}}
                        <div class="flex items-center gap-2 px-3 pb-2 pt-0.5" onclick="event.stopPropagation()">
                            <svg class="size-3 text-gray-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <input type="range" min="0" max="100" value="100" class="flex-1 h-1 accent-red-600 cursor-pointer" oninput="updateOpacity(this, 'group-status-kesesuaian')">
                            <span class="text-[10px] text-gray-400 dark:text-neutral-500 w-7 text-right opacity-val">100%</span>
                        </div>
                        @foreach($data['filters']['StatusKesesuaian'] as $statusKesesuaian)
                        <div role="treeitem" class="flex items-center gap-2 px-3 py-1.5 pl-6 hover:bg-gray-50 dark:hover:bg-neutral-800/50" data-hs-tree-view-item='{"value": "{{ $statusKesesuaian->nama }}"}'>
                            <input type="checkbox" data-status-kesesuaian-id="{{ $statusKesesuaian->id }}" onchange="onStatusKesesuaianChange()" class="layer-status-kesesuaian-cb shrink-0 size-3.5 rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-neutral-600 dark:bg-neutral-800 cursor-pointer">
                            <span class="shrink-0 size-2 rounded-full border border-black/10" style="background-color: {{ $statusKesesuaian->warna ?? '#94a3b8' }};"></span>
                            <span class="flex-1 text-[11.5px] text-gray-600 dark:text-neutral-300 leading-snug">
                                {{ $statusKesesuaian->nama }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- /layer-tree --}}

        </div>{{-- /panel-layer --}}

        {{-- Panel: RADIUS --}}
        <div id="panel-radius" role="tabpanel" aria-labelledby="tab-radius" class="hidden flex-col items-center justify-center p-6 text-center">
            <span class="inline-flex items-center justify-center size-12 rounded-xl bg-gray-100 dark:bg-neutral-800 mb-3">
                <svg class="size-6 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
                </svg>
            </span>
            <p class="text-xs font-medium text-gray-600 dark:text-neutral-400">Segera Hadir</p>
            <p class="text-[10px] text-gray-400 dark:text-neutral-500 mt-1">Fitur radius sedang dalam pengembangan.</p>
        </div>{{-- /peta-radius --}}
    </div>{{-- /peta-sidebar --}}
{{-- peta-wrapper ditutup di _map.blade.php --}}
