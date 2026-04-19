@php
$action = empty($model->id) ? route('penggunaan_rdtr.store') : route('penggunaan_rdtr.update', $model->id);
@endphp

<form id="form-element" action="{{ $action }}" method="POST" enctype="multipart/form-data" class="modal-wrapper">
    @csrf
    @if (!empty($model->id))
    @method('PUT')
    @endif
    <div class="modal-header">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Monitoring</h1>
    </div>
    <div class="modal-body">
        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="tanggal">Tanggal</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="tanggal" class="block w-full" type="date" name="tanggal" autocomplete="tanggal" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="jenis">Jenis Monitoring</x-input-label>
            </div>
            <div class="col-span-2">
                <x-text-input id="jenis" class="block w-full" type="text" name="jenis" autocomplete="jenis" />
            </div>
        </div>

        <div class="grid grid-cols-3 items-start gap-4">
            <div class="col-span-1 pt-2">
                <x-input-label for="hasil">Hasil Monitoring</x-input-label>
            </div>
            <div class="col-span-2">
                <textarea id="hasil" name="hasil" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm text-gray-800 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 dark:placeholder-neutral-500 dark:focus:border-neutral-700 dark:focus:ring-neutral-600" rows="3" placeholder="" data-hs-textarea-auto-height=""></textarea>
            </div>
        </div>

        <div class="space-y-4 w-full">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                    Upload Foto
                </label>
                <input type="file" name="id_file" accept=".jpg,.jpeg,.png" required class="block w-full text-sm border border-gray-300 rounded-lg bg-gray-50
                       dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300">
            </div>

            <p class="text-xs text-gray-500 dark:text-neutral-400">
                Supported format: JPG, JPEG, PNG
            </p>
        </div>

        <div class="space-y-4 w-full">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                    Upload File Pendukung
                </label>
                <input type="file" name="id_file_pendukung" accept=".pdf,.doc,.docx" required class="block w-full text-sm border border-gray-300 rounded-lg bg-gray-50
                       dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300">
            </div>

            <p class="text-xs text-gray-500 dark:text-neutral-400">
                Supported format: PDF, DOC, DOCX
            </p>
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
