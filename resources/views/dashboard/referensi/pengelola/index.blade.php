@php
$indexHref = route('pengelola.index');
$createHref = route('pengelola.create');
$updateHref = fn ($model) => route('pengelola.edit', $model->id);
$deleteHref = fn ($model) => route('pengelola.destroy', $model->id);

$page_title = "Pengelola";
$page_subtitle = "Kelola data pengelola.";
@endphp

<x-dashboard-layout>
    <div class="grid grid-cols-1 gap-6">
        <div class="grid space-y-2">
            <!-- Page Title -->
            <h1 class="page-title">
                {{ $page_title }}
            </h1>

            <!-- Page Subtitle -->
            <p class="page-subtitle">
                {{ $page_subtitle }}
            </p>
        </div>

        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="w-full grid gap-3 align-middle">
                    <!-- Alert Message -->
                    <x-alert-message />

                    <!-- Table Actions -->
                    <div class="grid sm:flex items-center gap-2">
                        <div class="flex gap-2">
                            @can(['create_user','export_user','import_user'])
                            <a href="{{ $createHref }}" onclick="modalFormAjax(this,event)" class="btn-sm btn-ghost focus:outline-hidden">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Tambah
                            </a>
                            @endcan
                        </div>

                        <div class="ms-auto flex gap-2">
                            <!-- Search -->
                            <x-search-input :route="$indexHref" />
                        </div>
                    </div>

                    <!-- Table View -->
                    <div class="border border-gray-200 rounded-lg shadow-xs overflow-x-auto dark:border-neutral-700 dark:shadow-gray-900">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead class="bg-gray-50 dark:bg-neutral-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-400" width="20">#</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-400" width="100">Aksi</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Nama</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Kontak</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">No. Telepon</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Email</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Alamat</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Jumlah Bidang</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Sub Persil</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                @forelse($models as $model)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $models->firstItem() + $loop->index }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        @can(['update_user','activate_user','delete_user'])
                                        <div class="grid lg:flex gap-1">
                                            <a href="{{ $updateHref($model) }}" onclick="modalFormAjax(this,event)" class="p-2 inline-flex items-center gap-x-2 text-xs font-normal text-gray-800 shadow-2xs focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:bg-transparent dark:text-blue-500 dark:hover:text-blue-400 shrink-0">
                                                <i data-lucide="settings-2" class="w-4 h-4"></i>
                                            </a>

                                            <a href="{{ $deleteHref($model) }}" onclick="modalConfirm(this,event)" data-title="Konfirmasi" data-content="Apakah anda yakin menghapus data tersebut?" data-method="DELETE" class="p-2 inline-flex items-center gap-x-2 text-xs font-normal text-gray-800 shadow-2xs focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:bg-transparent dark:text-red-500 dark:hover:text-red-400 shrink-0">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                        @endcan
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $model->nama }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $model->kontak ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $model->no_telepon ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        @if($model->email)
                                        <a href="mailto:{{ $model->email }}" class="text-blue-600 underline decoration-blue-600 hover:opacity-80 focus:outline-hidden focus:opacity-80">
                                            {{ $model->email }}
                                        </a>
                                        @else
                                        -
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $model->alamat ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $model->bidang->count() }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $model->sub_persil->count() }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $model->keterangan ?? '-' }}
                                    </td>

                                    @empty
                                <tr>
                                    <td colspan="999" class="text-center font-normal text-sm py-2">
                                        Data Tidak Ditemukan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Control -->
                    <div class="flex justify-between items-center">
                        <x-page-size :route="$indexHref" />
                        {{ $models->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
</x-dashboard-layout>


<!-- Filter Modal -->
<x-modal-filter :route="$indexHref">
    <div class="grid grid-cols-3 items-start gap-4">
        <div class="col-span-1 pt-2">
            <x-input-label for="status" value="Status" />
        </div>
        <div class="col-span-2">
            <select id="status" name="filters[status]" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                <option value="">All</option>
                <option value="1" {{ request('filters.status')==='1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('filters.status')==='0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>
</x-modal-filter>
