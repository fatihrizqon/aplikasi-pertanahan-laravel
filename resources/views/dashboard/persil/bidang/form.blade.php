@php
$action = empty($model->id) ? route('penggunaan_rdtr.store') : route('penggunaan_rdtr.update', $model->id);
@endphp

<form id="form-element" action="{{ $action }}" method="POST" enctype="multipart/form-data" class="modal-wrapper">
    @csrf
    @if (!empty($model->id))
    @method('PUT')
    @endif
    <div class="modal-header">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Form</h1>
    </div>
    <div class="modal-body">
        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="nomor_persil">Nomor Persil</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="nomor_persil" class="block w-full" type="text" name="nomor_persil" autocomplete="nomor_persil" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="id_kategori">Kategori</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="id_kategori" class="block w-full" type="text" name="id_kategori" autocomplete="id_kategori" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="id_jenis_hak">Jenis Hak</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="id_jenis_hak" class="block w-full" type="text" name="id_jenis_hak" autocomplete="id_jenis_hak" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="nomor_hak">Nomor Hak</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="nomor_hak" class="block w-full" type="text" name="nomor_hak" autocomplete="nomor_hak" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="id_jenis_hak_adat">Jenis Hak Adat</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="id_jenis_hak_adat" class="block w-full" type="text" name="id_jenis_hak_adat" autocomplete="id_jenis_hak_adat" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="nomor_hak_adat">Nomor Hak Adat</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="nomor_hak_adat" class="block w-full" type="text" name="nomor_hak_adat" autocomplete="nomor_hak_adat" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="alamat">Alamat</x-input-label>
            </div>
            <div class="col-span-2">
                <textarea id="alamat" name="alamat" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 dark:placeholder-neutral-500 dark:focus:border-neutral-700 dark:focus:ring-neutral-600" rows="3" placeholder="" data-hs-textarea-auto-height=""></textarea>
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="nomor_bidang">Nomor Bidang</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="nomor_bidang" class="block w-full" type="text" name="nomor_bidang" autocomplete="nomor_bidang" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="luas">Luas</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="luas" class="block w-full" type="text" name="luas" autocomplete="luas" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="id_pengelola">Pengelola</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="id_pengelola" class="block w-full" type="text" name="id_pengelola" autocomplete="id_pengelola" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="id_penggunaan">Penggunaan</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="id_penggunaan" class="block w-full" type="text" name="id_penggunaan" autocomplete="id_penggunaan" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="id_status_kesesuaian">Status Kesesuaian</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="id_status_kesesuaian" class="block w-full" type="text" name="id_status_kesesuaian" autocomplete="id_status_kesesuaian" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="keterangan">Keterangan</x-input-label>
            </div>
            <div class="col-span-2">
                <textarea id="keterangan" name="keterangan" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 dark:placeholder-neutral-500 dark:focus:border-neutral-700 dark:focus:ring-neutral-600" rows="3" placeholder="" data-hs-textarea-auto-height=""></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" onclick="window.HSOverlay.close(document.getElementById('modal-form-ajax'))">
            Cancel
        </button>
        <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
            Save
        </button>
    </div>
</form>

<script>
    jsonScriptToFormFields('#form-element', @json($model));
    $('#form-element').formAjaxSubmit();
</script>
