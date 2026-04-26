@php
$indexHref = route('users.index');
$exportHref = route('users.export', request()->except([
    'page',
    'per-page',
]));
$importHref = route('users.import');
$createHref = route('users.create');
$updateHref = fn ($model) => route('users.edit', $model->id);
$lockHref = fn ($model) => route('users.lock', $model->id);
$unlockHref = fn ($model) => route('users.unlock', $model->id);
$deleteHref = fn ($model) => route('users.destroy', $model->id);

$page_title = "Manajemen User";
$page_subtitle = "Kelola data pengguna dan pengelolaan akses.";
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

                            <div class="hs-dropdown [--strategy:absolute] [--flip:false] hs-dropdown-export-import relative inline-flex">
                                <button id="hs-dropdown-export-import" type="button" class="hs-dropdown-toggle btn-sm btn-ghost focus:outline-hidden" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                                    <i data-lucide="arrow-up-down" class="w-4 h-4"></i>
                                    Export/Import
                                </button>

                                <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-100 w-44 hidden z-10 mt-2 bg-white shadow-md rounded-lg p-2 dark:bg-neutral-900 dark:border dark:border-neutral-700 dark:divide-neutral-700" can="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-export-import">
                                    <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-xs font-normal text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700" href="{{ $exportHref }}">
                                        <i data-lucide="download" class="w-4 h-4"></i>
                                        Export
                                    </a>
                                    <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-xs font-normal text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-import-modal" data-hs-overlay="#hs-import-modal">
                                        <i data-lucide="upload" class="w-4 h-4"></i>
                                        Import
                                    </a>
                                </div>
                            </div>
                            @endcan
                        </div>

                        <div class="ms-auto flex gap-2">
                            <!-- Filter -->
                            <button type="button" class="btn-sm btn-ghost focus:outline-hidden" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-filter-modal" data-hs-overlay="#hs-filter-modal">
                                <i data-lucide="filter" class="w-4 h-4"></i>
                                Filter
                            </button>

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
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Name</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Role</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Username</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Email</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Status</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Registered</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                @forelse($models as $model)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">{{ $models->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        @can(['update_user','activate_user','delete_user'])
                                        <div class="grid lg:flex gap-1">
                                            @if($model->id !== 1)
                                            <a href="{{ $updateHref($model) }}" onclick="modalFormAjax(this,event)" class="p-2 inline-flex items-center gap-x-2 text-xs font-normal text-gray-800 shadow-2xs focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:bg-transparent dark:text-blue-500 dark:hover:text-blue-400 shrink-0">
                                                <i data-lucide="settings-2" class="w-4 h-4"></i>
                                            </a>
                                            @if($model->status === 1)
                                            <a href="{{ $lockHref($model) }}" onclick="modalConfirm(this,event)" data-title="Lock" data-content="Are you sure to disable this record?" data-method="PUT" class="p-2 inline-flex items-center gap-x-2 text-xs font-normal text-gray-800 shadow-2xs focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:bg-transparent dark:text-amber-500 dark:hover:text-amber-400 shrink-0">
                                                <i data-lucide="lock-open" class="w-4 h-4"></i>
                                            </a>
                                            @else
                                            <a href="{{ $unlockHref($model) }}" onclick="modalConfirm(this,event)" data-title="Unlock" data-content="Are you sure to enable this record?" data-method="PUT" class="p-2 inline-flex items-center gap-x-2 text-xs font-normal text-gray-800 shadow-2xs focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:bg-transparent dark:text-amber-500 dark:hover:text-amber-400 shrink-0">
                                                <i data-lucide="lock" class="w-4 h-4"></i>
                                            </a>
                                            @endif
                                            <a href="{{ $deleteHref($model) }}" onclick="modalConfirm(this,event)" data-title="Konfirmasi" data-content="Apakah anda yakin menghapus data tersebut?" data-method="DELETE" class="p-2 inline-flex items-center gap-x-2 text-xs font-normal text-gray-800 shadow-2xs focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:bg-transparent dark:text-red-500 dark:hover:text-red-400 shrink-0">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </a>
                                            @endif
                                        </div>
                                        @endcan
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">{{ $model->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">{{ $model->getRoleNames()->first() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">{{ $model->username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        <a href="mailto:{{ $model->email }}" class="text-blue-600 underline decoration-blue-600 hover:opacity-80 focus:outline-hidden focus:opacity-80">{{ $model->email }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">
                                        @if($model->status === 1)
                                        <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-500">
                                            <span class="animate-ping size-1.5 inline-block rounded-full bg-green-800 dark:bg-green-500"></span>
                                            Active
                                        </span>
                                        @else
                                        <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-500">
                                            <span class="animate-ping size-1.5 inline-block rounded-full bg-red-800 dark:bg-red-500"></span>
                                            Inactive
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-normal text-sm text-gray-800 dark:text-neutral-200">@date($model->created_at)</td>
                                    @empty
                                    <td colspan="999" class="text-center font-normal text-sm py-2">Data Tidak Ditemukan</td>
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

<!-- Import Modal -->
<x-modal-dialog :id="'hs-import-modal'">
    <form method="POST" action="{{ $importHref }}" class="modal-wrapper" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Import</h1>
        </div>
        <div class="modal-body">
            <div class="flex w-full items-start gap-4">
                <div class="space-y-4 w-full">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                            Upload File
                        </label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full text-sm border border-gray-300 rounded-lg bg-gray-50
                       dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300">
                    </div>

                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                        Supported format: XLSX, XLS, CSV
                    </p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" onclick="window.HSOverlay.close(document.getElementById('hs-import-modal'))">
                Cancel
            </button>
            <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                Submit
            </button>
        </div>
    </form>
</x-modal-dialog>
