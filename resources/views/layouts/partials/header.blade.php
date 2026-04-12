@php
$unreadNotifs = auth()->user()->unreadNotifications()->latest()->get();
@endphp

<header class="flex fixed z-30 items-center w-full px-3 py-2 bg-white border-b dark:bg-neutral-800 dark:border-neutral-700">
    <button id="sidebarToggle" class="text-gray-600 hover:text-gray-900 dark:text-neutral-300 dark:hover:text-white">
        <i data-lucide="panel-left-open" class="w-5 h-5"></i>
    </button>

    <span class="ms-3 text-lg font-normal logo-text instrument">{{ env('APP_NAME', 'Laravel') }}</span>

    <div class="ms-auto flex items-center space-x-4">
        <!-- Notification -->
        <div class="hs-dropdown relative inline-flex">
            <button type="button" class="hs-dropdown-toggle inline-flex items-center gap-x-2 text-sm font-medium rounded-full text-gray-800 shadow hover:bg-gray-50 dark:bg-neutral-800 dark:text-white dark:hover:bg-neutral-700">
                <i data-lucide="bell" class="w-4 h-4"></i>
                <span id="notif-count" class="absolute -top-3 -right-3 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-semibold leading-none text-white bg-red-600 rounded-full">0</span>
            </button>

            <div class="hs-dropdown-menu hidden mt-2 w-56 bg-white shadow-md rounded-lg p-2 dark:bg-neutral-800 dark:border dark:border-neutral-700 dark:divide-neutral-700 z-20" role="menu">
                <ul id="notif-list" class="list-none space-y-2 text-sm text-gray-600 dark:text-neutral-400">
                    @if($unreadNotifs->isEmpty())
                    <li id="notif-placeholder" class="flex flex-col items-start gap-x-2 p-2 rounded-lg text-gray-400 dark:text-neutral-500">
                        <span class="text-gray-500 dark:text-neutral-500">Belum ada notifikasi</span>
                    </li>
                    @else
                    @foreach($unreadNotifs as $notif)
                    <li data-id="{{ $notif->id }}" class="flex flex-col items-start gap-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-700 dark:hover:text-white">
                        <span class="text-blue-600 dark:text-blue-400">
                            <a href="{{ $notif->data['url'] ?? '#' }}">{{ $notif->data['title'] }}</a>
                        </span>
                        <span class="text-xs text-gray-500 dark:text-neutral-500">{{ $notif->data['message'] }}</span>
                    </li>
                    @endforeach
                    @endif
                </ul>
                <script>
                    document.getElementById('notif-count').textContent = "{{ $unreadNotifs->count() }}";
                </script>
            </div>
        </div>

        <!-- Avatar -->
        <div class="hs-dropdown relative inline-flex">
            <button type="button" class="hs-dropdown-toggle inline-flex items-center gap-x-2 text-sm font-medium rounded-full border border-gray-200 bg-white text-gray-800 shadow hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                <img class="inline-block size-8 rounded-full" src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=300&h=300&q=80" alt="Avatar">
            </button>

            <div class="hs-dropdown-menu hidden mt-2 w-56 bg-white shadow-md rounded-lg p-2 dark:bg-neutral-800 dark:border dark:border-neutral-700 dark:divide-neutral-700 z-20" role="menu">
                <a class="rounded-lg flex items-center px-4 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300" href="{{ route('profile.edit') }}">
                    <i data-lucide="user-circle" class="w-4 h-4 me-2"></i>
                    Profile
                </a>
                <a class="rounded-lg flex items-center px-4 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300" href="#">
                    <i data-lucide="settings" class="w-4 h-4 me-2"></i>
                    Preferences
                </a>

                <a class="rounded-lg flex items-center px-4 py-2 text-sm text-red-800  dark:text-red-400 dark:hover:text-red-300" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-logout-modal" data-hs-overlay="#hs-logout-modal">
                    <i data-lucide="log-out" class="w-4 h-4 me-2"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</header>

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
            <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" onclick="window.HSOverlay.close(document.getElementById('hs-logout-modal'))">
                Cancel
            </button>
            <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-hidden focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                Logout
            </button>
        </div>
    </form>
</x-modal-dialog>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const userMeta = document.head.querySelector('meta[name="user-id"]');
        if (!userMeta?.content) return; // kalau belum login, skip

        const userId = userMeta.content;
        const notifList = document.getElementById('notif-list');
        const notifCount = document.getElementById('notif-count');

        // Init unread count
        let unreadCount = parseInt(notifCount.textContent || 0, 10);

        // Subscribe ke private channel Reverb
        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                console.log('Realtime notif:', notification);

                // Hapus placeholder kalau masih ada
                const placeholder = document.getElementById('notif-placeholder');
                if (placeholder) {
                    placeholder.remove();
                }

                // Update badge
                unreadCount++;
                notifCount.textContent = unreadCount;

                // Buat li baru
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


        // Optional: klik notif mark as read
        notifList.addEventListener('click', (e) => {
            const li = e.target.closest('li[data-id]');
            if (!li) return;

            const id = li.dataset.id;
            console.log('notif id:', id);

            if (li.classList.contains('opacity-50')) return;

            unreadCount = Math.max(0, unreadCount - 1);
            notifCount.textContent = unreadCount;

            fetch(`/notifications/${id}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            }).catch(err => console.error(err));

            li.classList.add('opacity-50');
        });

    });

</script>
