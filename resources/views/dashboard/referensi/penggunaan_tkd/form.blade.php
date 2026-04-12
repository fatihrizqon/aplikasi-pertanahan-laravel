@php
$action = empty($model->id) ? route('penggunaan_tkd.store') : route('penggunaan_tkd.update', $model->id);
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
                <x-input-label for="nama">Nama</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="nama" class="block w-full" type="text" name="nama" autocomplete="nama" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="warna">Warna</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="warna" class="block w-full" type="text" name="warna" autocomplete="warna" />
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
