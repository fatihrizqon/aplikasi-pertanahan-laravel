@php
$indexHref = route('penggunaan_rdtr.index');
$createHref = route('penggunaan_rdtr.create');
$updateHref = fn ($model) => route('penggunaan_rdtr.edit', $model->id);
$deleteHref = fn ($model) => route('penggunaan_rdtr.destroy', $model->id);

$page_title = "Penggunaan Rencana Desain Tata Ruang (RDTR)";
$page_subtitle = "Kelola data penggunaan Rencana Desain Tata Ruang (RDTR).";
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
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Nama File</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Warna</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">On Top</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Jumlah Bidang</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Sub Persil</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Penggunaan SG</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                @forelse($models as $model)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $models->firstItem() + $loop->index }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        @can(['update_user','activate_user','delete_user'])
                                        <div class="grid lg:flex gap-1">
                                            <a href="{{ $updateHref($model) }}" onclick="modalFormAjax(this,event)" class="p-2 inline-flex items-center text-xs text-gray-800 dark:text-blue-500 dark:hover:text-blue-400">
                                                <i data-lucide="settings-2" class="w-4 h-4"></i>
                                            </a>

                                            <a href="{{ $deleteHref($model) }}" onclick="modalConfirm(this,event)" data-title="Konfirmasi" data-content="Apakah anda yakin menghapus data tersebut?" data-method="DELETE" class="p-2 inline-flex items-center text-xs text-gray-800 dark:text-red-500 dark:hover:text-red-400">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                        @endcan
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $model->nama }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $model->nama_file ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        @if($model->warna)
                                        <div class="flex items-center gap-2">
                                            <span class="w-4 h-4 rounded" style="background-color: {{ $model->warna }}"></span>
                                            <span>{{ $model->warna }}</span>
                                        </div>
                                        @else
                                        -
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-800 dark:text-neutral-200">
                                        @if($model->ontop)
                                        <span class="text-green-600 font-medium">Yes</span>
                                        @else
                                        <span class="text-gray-400">No</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $model->bidang->count() }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $model->sub_persil->count() }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-800 dark:text-neutral-200">
                                        {{ $model->penggunaan_sg->count() }}
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="999" class="text-center text-sm py-2">
                                        Data Tidak Ditemukan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Control -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2 items-center">
                            <span class="text-sm font-normal w-full">
                                @if ($models->count())
                                Showing {{ $models->firstItem() }} to {{ $models->lastItem() }} of {{ $models->total() }} item(s).
                                @else
                                No record found.
                                @endif
                            </span>
                            <form action="{{ $indexHref }}" method="GET">
                                <select name="per-page" class="py-2 px-2 pe-8 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" onchange="this.form.submit()">
                                    @php
                                    $options = [10, 25, 50, 100];
                                    $selected = request('per-page');
                                    @endphp
                                    <option disabled {{ $selected ? '' : 'selected' }}>Page Size</option>
                                    @foreach ($options as $option)
                                    <option value="{{ $option }}" {{ $selected==$option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        {{ $models->links() }}
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
