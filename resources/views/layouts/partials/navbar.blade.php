@php
$unreadNotifs = auth()->user()->unreadNotifications()->latest()->get();
@endphp

<nav class="fixed top-0 left-1/2 -translate-x-1/2 w-full z-30
            bg-white border-b
            dark:bg-neutral-900 dark:border-neutral-700">
    <div class="flex justify-between items-center h-14 px-4">
        <!-- Logo -->
        <a href="{{ route('dashboard') }}" class="shrink-0 flex items-center gap-x-3 text-lg font-semibold logo-text instrument dark:text-white">
            <img src="https://pertanahan.jogjaprov.go.id/static/images/logo-diy.png" alt="App Logo" class="h-10 w-auto">

            <div class="flex flex-col leading-tight">
                <span class="text-xs font-bold tracking-wide">INTANTARUBERINFO</span>
                <span class="text-[12px] text-gray-500 dark:text-neutral-400">
                    Dinas Pertanahan dan Tata Ruang Daerah Istimewa Yogyakarta
                </span>
                <span class="text-[9px] text-gray-400 dark:text-neutral-500 italic">
                    (Kundha Niti Mandala Sarta Tata Sasana)
                </span>
            </div>
        </a>

        <!-- Right side -->
        <div class="flex items-center gap-x-3">
            <!-- Notification -->
            <div class="hs-dropdown relative inline-flex">
                <button type="button" class="hs-dropdown-toggle relative inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-600 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700 transition-colors">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    <span id="notif-count" class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[1.1rem] h-[1.1rem] px-1 text-[10px] font-semibold leading-none text-white bg-red-600 rounded-full">
                        {{ $unreadNotifs->count() }}
                    </span>
                </button>

                <div class="hs-dropdown-menu hidden mt-2 w-64 bg-white shadow-md rounded-lg p-2 dark:bg-neutral-900 dark:border dark:border-neutral-700 z-20" role="menu">
                    <ul id="notif-list" class="list-none space-y-1 text-sm text-gray-600 dark:text-neutral-400">
                        @if($unreadNotifs->isEmpty())
                        <li id="notif-placeholder" class="flex flex-col items-start gap-x-2 p-2 rounded-lg text-gray-400 dark:text-neutral-500">
                            <span>Belum ada notifikasi</span>
                        </li>
                        @else
                        @foreach($unreadNotifs as $notif)
                        <li data-id="{{ $notif->id }}" class="flex flex-col items-start gap-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700 dark:hover:text-white cursor-pointer">
                            <span class="text-blue-600 dark:text-blue-400">
                                <a href="{{ $notif->data['url'] ?? '#' }}">{{ $notif->data['title'] }}</a>
                            </span>
                            <span class="text-xs text-gray-500 dark:text-neutral-500">{{ $notif->data['message'] }}</span>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Avatar -->
            <div class="hs-dropdown relative inline-flex">
                <button type="button" class="hs-dropdown-toggle inline-flex items-center justify-center rounded-full border border-gray-200 bg-white shadow hover:bg-gray-50 dark:bg-neutral-900 dark:border-neutral-700 dark:hover:bg-neutral-700 transition-colors">
                    <img class="size-8 rounded-full" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=300&h=300&q=80" alt="Avatar">
                </button>

                <div class="hs-dropdown-menu hidden mt-2 w-56 bg-white shadow-md rounded-lg p-2 dark:bg-neutral-900 dark:border dark:border-neutral-700 z-20" role="menu">
                    <a class="rounded-lg flex items-center px-4 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300" href="{{ route('profile.edit') }}">
                        <i data-lucide="user-circle" class="w-4 h-4 me-2"></i>
                        Profile
                    </a>
                    <a class="rounded-lg flex items-center px-4 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300" href="#">
                        <i data-lucide="settings" class="w-4 h-4 me-2"></i>
                        Preferences
                    </a>
                    <a class="rounded-lg flex items-center px-4 py-2 text-sm text-red-800 dark:text-red-400 dark:hover:text-red-300 cursor-pointer" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-logout-modal" data-hs-overlay="#hs-logout-modal">
                        <i data-lucide="log-out" class="w-4 h-4 me-2"></i>
                        Logout
                    </a>
                </div>
            </div>

            <!-- Mobile hamburger -->
            <button id="mobileMenuToggle" class="sm:hidden inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-600 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-700 transition-colors">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
        </div>
    </div>

    <div class="flex items-center h-14 px-4 gap-x-4">
        <!-- Nav Links (desktop) -->
        <div class="hidden sm:flex items-center gap-x-1 ms-4 justify-round w-full">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-x-2 px-3 py-2 text-sm transition-colors {{ Route::currentRouteName() === 'dashboard'
                         ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-white'
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:hover:text-white' }}">
                <i data-lucide="earth" class="w-4 h-4"></i>
                Peta Lokasi
            </a>

            <div class="hs-dropdown relative inline-flex">
                <button type="button" class="hs-dropdown-toggle flex items-center gap-x-2 px-3 py-2 text-sm transition-colors {{ Route::currentRouteName() === 'dashboard.overview' || Route::currentRouteName() === 'dashboard.monitoring'
                         ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-white'
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:hover:text-white' }}">
                    <i data-lucide="gauge" class="w-4 h-4"></i>
                    Dashboard
                    <i data-lucide="chevron-down" class="w-3 h-3 opacity-60"></i>
                </button>

                <div class="hs-dropdown-menu hidden mt-1 min-w-48 bg-white shadow-md rounded-lg p-1.5 dark:bg-neutral-900 dark:border dark:border-neutral-700 z-20" role="menu">
                    <a href="{{ route('dashboard.overview') }}" class="flex items-center gap-x-3 px-3 py-2 text-sm rounded-lg {{ Route::currentRouteName() === 'dashboard.overview'
                         ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-white'
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:hover:text-white' }}">
                        <i data-lucide="pie-chart" class="w-4 h-4 text-gray-400 dark:text-neutral-500"></i>
                        Overview
                    </a>
                    <a href="{{ route('dashboard.monitoring') }}" class="flex items-center gap-x-3 px-3 py-2 text-sm rounded-lg {{ Route::currentRouteName() === 'dashboard.monitoring'
                         ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-white'
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:hover:text-white' }}">
                        <i data-lucide="monitor" class="w-4 h-4 text-gray-400 dark:text-neutral-500"></i>
                        Monitoring
                    </a>
                </div>
            </div>

            <a href="#!" class="flex items-center gap-x-2 px-3 py-2 text-sm transition-colors">
                <i data-lucide="map" class="w-4 h-4"></i>
                Persil Tanah
            </a>

            <div class="hs-dropdown relative inline-flex">
                <button type="button" class="hs-dropdown-toggle flex items-center gap-x-2 px-3 py-2 text-sm transition-colors">
                    <i data-lucide="stamp" class="w-4 h-4"></i>
                    Pengajuan
                    <i data-lucide="chevron-down" class="w-3 h-3 opacity-60"></i>
                </button>

                <div class="hs-dropdown-menu hidden mt-1 min-w-48 bg-white shadow-md rounded-lg p-1.5 dark:bg-neutral-900 dark:border dark:border-neutral-700 z-20" role="menu">
                    <a href="#!" class="flex items-center gap-x-2 px-3 py-2 text-sm transition-colors">
                        <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                        Rekomendasi Kekancingan
                    </a>

                    <a href="#!" class="flex items-center gap-x-2 px-3 py-2 text-sm transition-colors">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        Ijin Tanah Kalurahan
                    </a>
                </div>
            </div>


            <div class="hs-dropdown relative inline-flex">
                <button type="button" class="hs-dropdown-toggle flex items-center gap-x-2 px-3 py-2 text-sm transition-colors {{ Route::currentRouteName() === 'jenis_hak.index' || Route::currentRouteName() === 'pengelola.index' || Route::currentRouteName() === 'penggunaan_rdtr.index' || Route::currentRouteName() === 'penggunaan_tkd.index'
                         ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-white'
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:hover:text-white' }}">
                    <i data-lucide="database" class="w-4 h-4"></i>
                    Referensi
                    <i data-lucide="chevron-down" class="w-3 h-3 opacity-60"></i>
                </button>

                <div class="hs-dropdown-menu hidden mt-1 min-w-48 bg-white shadow-md rounded-lg p-1.5 dark:bg-neutral-900 dark:border dark:border-neutral-700 z-20" role="menu">
                    <a href="{{ route('jenis_hak.index') }}" class="flex items-center gap-x-3 px-3 py-2 text-sm rounded-lg {{ Route::currentRouteName() === 'jenis_hak.index'
                         ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-white'
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:hover:text-white' }}">
                        <i data-lucide="layers" class="w-4 h-4 text-gray-400 dark:text-neutral-500"></i>
                        Jenis Hak Adat
                    </a>
                    <a href="{{ route('pengelola.index') }}" class="flex items-center gap-x-3 px-3 py-2 text-sm rounded-lg {{ Route::currentRouteName() === 'pengelola.index'
                         ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-white'
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:hover:text-white' }}">
                        <i data-lucide="square-user" class="w-4 h-4 text-gray-400 dark:text-neutral-500"></i>
                        Pengelola
                    </a>
                    <a href="{{ route('penggunaan_rdtr.index') }}" class="flex items-center gap-x-3 px-3 py-2 text-sm rounded-lg {{ Route::currentRouteName() === 'penggunaan_rdtr.index'
                         ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-white'
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:hover:text-white' }}">
                        <i data-lucide="land-plot" class="w-4 h-4 text-gray-400 dark:text-neutral-500"></i>
                        Penggunaan (Versi RDTR)
                    </a>
                    <a href="{{ route('penggunaan_tkd.index') }}" class="flex items-center gap-x-3 px-3 py-2 text-sm rounded-lg {{ Route::currentRouteName() === 'penggunaan_tkd.index'
                         ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-white'
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:hover:text-white' }}">
                        <i data-lucide="tree-palm" class="w-4 h-4 text-gray-400 dark:text-neutral-500"></i>
                        Penggunaan (Versi Tanah Desa)
                    </a>
                </div>
            </div>

            <a href="{{ route('users.index') }}" class="flex items-center gap-x-2 px-3 py-2 text-sm transition-colors
                      {{ Route::currentRouteName() === 'users.index'
                         ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-white'
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:hover:text-white' }}">
                <i data-lucide="users" class="w-4 h-4"></i>
                User Management
            </a>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden sm:hidden border-t dark:border-neutral-700 px-4 py-2 space-y-1">
        <a href="#" class="flex items-center gap-x-2 px-3 py-2 text-sm transition-colors">
            <i data-lucide="map" class="w-4 h-4"></i>
            Peta Lokasi
        </a>

        <div class="px-3 py-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-neutral-500">Dashboard</span>
        </div>

        <a href="#" class="flex items-center gap-x-2 px-3 py-2 text-sm rounded-lg text-gray-600 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700 ms-2">
            <i data-lucide="pie-chart" class="w-4 h-4"></i>
            Overview
        </a>

        <a href="#" class="flex items-center gap-x-2 px-3 py-2 text-sm rounded-lg text-gray-600 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700 ms-2">
            <i data-lucide="monitor" class="w-4 h-4"></i>
            Monitoring
        </a>

        <a href="#" class="flex items-center gap-x-2 px-3 py-2 text-sm transition-colors">
            <i data-lucide="map-pin" class="w-4 h-4"></i>
            Persil Tanah
        </a>

        <a href="#" class="flex items-center gap-x-2 px-3 py-2 text-sm transition-colors">
            <i data-lucide="stamp" class="w-4 h-4"></i>
            Pengajuan Rekomendasi Kekancingan
        </a>

        <a href="#" class="flex items-center gap-x-2 px-3 py-2 text-sm transition-colors">
            <i data-lucide="clipboard-check" class="w-4 h-4"></i>
            Pengajuan Ijin Tanah Desa
        </a>

        <div class="px-3 py-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-neutral-500">Referensi</span>
        </div>

        <a href="#" class="flex items-center gap-x-2 px-3 py-2 text-sm rounded-lg text-gray-600 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700 ms-2">
            <i data-lucide="layers" class="w-4 h-4"></i>
            Jenis Hak Adat
        </a>

        <a href="#" class="flex items-center gap-x-2 px-3 py-2 text-sm rounded-lg text-gray-600 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700 ms-2">
            <i data-lucide="users" class="w-4 h-4"></i>
            Pengelola
        </a>

        <a href="#" class="flex items-center gap-x-2 px-3 py-2 text-sm rounded-lg text-gray-600 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700 ms-2">
            <i data-lucide="land-plot" class="w-4 h-4"></i>
            Penggunaan (Versi RDTR)
        </a>

        <a href="#" class="flex items-center gap-x-2 px-3 py-2 text-sm rounded-lg text-gray-600 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700 ms-2">
            <i data-lucide="tree-palm" class="w-4 h-4"></i>
            Penggunaan (Versi Tanah Desa)
        </a>

        <a href="{{ route('users.index') }}" class="flex items-center gap-x-2 px-3 py-2 text-sm transition-colors
                  {{ Route::currentRouteName() === 'users.index'
                     ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-white'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-neutral-300 dark:hover:bg-neutral-700' }}">
            <i data-lucide="users" class="w-4 h-4"></i>
            User Management
        </a>
    </div>
</nav>

<!-- Logout Modal -->
<x-modal-dialog :id="'hs-logout-modal'">
    <form method="POST" action="{{ route('logout') }}" class="modal-wrapper">
        @csrf
        <div class="modal-header">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Logout</h1>
        </div>
        <div class="modal-body">
            Are you sure you want to logout?
        </div>
        <div class="modal-footer">
            <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" onclick="window.HSOverlay.close(document.getElementById('hs-logout-modal'))">
                Cancel
            </button>
            <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-hidden focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                Logout
            </button>
        </div>
    </form>
</x-modal-dialog>

<script>
    // Mobile menu toggle
    document.getElementById('mobileMenuToggle')?.addEventListener('click', () => {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    });

    // Notification realtime & mark-read
    document.addEventListener('DOMContentLoaded', () => {
        const userMeta = document.head.querySelector('meta[name="user-id"]');
        if (!userMeta?.content) return;

        const userId = userMeta.content;
        const notifList = document.getElementById('notif-list');
        const notifCount = document.getElementById('notif-count');

        let unreadCount = parseInt(notifCount.textContent || 0, 10);

        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                const placeholder = document.getElementById('notif-placeholder');
                if (placeholder) placeholder.remove();

                unreadCount++;
                notifCount.textContent = unreadCount;

                const li = document.createElement('li');
                li.className = 'flex flex-col items-start gap-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700 dark:hover:text-white';
                li.innerHTML = `
                    <span class="text-blue-600 dark:text-blue-400">
                        <a href="${notification.url || '#'}">${notification.title}</a>
                    </span>
                    <span class="text-xs text-gray-500 dark:text-neutral-500">${notification.message}</span>
                `;
                notifList.prepend(li);
            });

        notifList.addEventListener('click', (e) => {
            const li = e.target.closest('li[data-id]');
            if (!li || li.classList.contains('opacity-50')) return;

            const id = li.dataset.id;
            unreadCount = Math.max(0, unreadCount - 1);
            notifCount.textContent = unreadCount;

            fetch(`/notifications/${id}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            }).catch(err => console.error(err));

            li.classList.add('opacity-50');
        });
    });
</script>
