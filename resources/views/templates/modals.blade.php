<div id="modal-form-ajax" class="hs-overlay [--overlay-backdrop:static] hidden size-full fixed top-0 start-0 z-[9999] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modal-form-ajax-label">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-100 ease-out transition-all sm:max-w-xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-56px)] flex items-center">
        <div class="w-full flex flex-col bg-white shadow-2xs rounded-xl pointer-events-auto dark:bg-neutral-800 dark:shadow-neutral-700/70">
            <div id="modal-form-content"></div>
        </div>
    </div>
</div>

<div id="modal-confirm" class="hs-overlay [--overlay-backdrop:static] hidden size-full fixed top-0 start-0 z-[9999] overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="modal-confirm-label">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-100 ease-out transition-all sm:max-w-xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-56px)] flex items-center">
        <div class="w-full flex flex-col bg-white shadow-2xs rounded-xl pointer-events-auto dark:bg-neutral-800 dark:shadow-neutral-700/70 border border-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div id="modal-confirm-content">
                <form method="POST" class="modal-wrapper">
                    @csrf
                    <input type="hidden" name="_method" value="">
                    <div class="modal-header"></div>
                    <div class="modal-body text-gray-800 dark:text-neutral-400"></div>
                    <div class="modal-footer">
                        <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-red-800 dark:border-red-700 dark:text-white dark:hover:bg-red-700 dark:focus:bg-red-700" onclick="window.HSOverlay.close(document.getElementById('modal-confirm'))">
                            Tutup
                        </button>
                        <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                            Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
