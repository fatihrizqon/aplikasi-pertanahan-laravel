<x-dashboard-layout>
    <div class="flex items-center justify-center h-[90vh] mt-4">

        <!-- Coming Soon Card -->
        <div class="max-w-2xl w-full text-center px-6">

            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <span class="inline-flex items-center justify-center size-20 rounded-2xl bg-blue-100 dark:bg-blue-900/30">
                    <svg class="size-10 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-3">
                Coming Soon
            </h1>

            <!-- Subtitle -->
            <p class="text-gray-500 dark:text-gray-400 text-base mb-8 max-w-md mx-auto">
                Fitur ini sedang dalam tahap pengembangan. Kami sedang bekerja keras untuk menghadirkannya secepatnya.
            </p>

            <!-- Divider -->
            <div class="flex items-center gap-4 mb-8">
                <div class="flex-1 h-px bg-gray-200 dark:bg-neutral-700"></div>
                <span class="text-xs text-gray-400 dark:text-neutral-500 uppercase tracking-wider font-medium">Segera Hadir</span>
                <div class="flex-1 h-px bg-gray-200 dark:bg-neutral-700"></div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Progress Pengembangan</span>
                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">75%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-neutral-700">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: 75%"></div>
                </div>
            </div>

            <!-- Info Badges -->
            <div class="flex flex-wrap justify-center gap-3 mb-8">
                <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                    <span class="size-1.5 rounded-full bg-green-500 inline-block"></span>
                    Dalam Pengembangan
                </span>
                <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    Q2 2025
                </span>
                <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                    Notifikasi Aktif
                </span>
            </div>

            <!-- CTA Button -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button type="button" class="py-2.5 px-5 inline-flex items-center gap-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 transition disabled:opacity-50 disabled:pointer-events-none">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                    Beritahu Saya
                </button>
                <button type="button" onclick="history.back()" class="py-2.5 px-5 inline-flex items-center gap-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Kembali
                </button>
            </div>

        </div>

    </div>
</x-dashboard-layout>
