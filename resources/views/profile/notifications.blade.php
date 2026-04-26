<x-dashboard-layout>
    @include('profile.partials.nav')

    <div class="bg-white border border-neutral-200 shadow-xl rounded-xl dark:bg-neutral-800 dark:border-neutral-700 p-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    {{ __('Notification Center') }}
                </h3>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ __("View and manage your notifications.") }}
                </p>
            </div>

            <span id="page-unread-count" class="inline-flex items-center gap-x-1.5 px-3 py-1 rounded-full text-xs font-medium
                bg-neutral-100 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-200">
                {{ $models->whereNull('read_at')->count() }} Unread
            </span>
        </div>

        <!-- Notification List -->
        <ul id="page-notif-list" class="list-none divide-y divide-neutral-200 dark:divide-neutral-700">
            @forelse ($models as $model)
            @php
            $data = $model->data;
            $isUnread = is_null($model->read_at);
            @endphp

            <li data-id="{{ $model->id }}" class="flex gap-x-4 py-4 px-4 transition
                    {{ $isUnread ? 'bg-blue-50/60 dark:bg-blue-900/20' : 'hover:bg-neutral-50 dark:hover:bg-neutral-700/40' }}">

                <!-- Icon -->
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center justify-center size-10 rounded-full
                            {{ $isUnread ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300' : 'bg-neutral-100 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-300' }}">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z" />
                        </svg>
                    </span>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-neutral-900 dark:text-white">
                            {{ $data['title'] ?? 'Notification' }}
                        </p>

                        @if ($isUnread)
                        <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                            New
                        </span>
                        @endif
                    </div>

                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                        {{ $data['message'] ?? '-' }}
                    </p>

                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-neutral-400">
                            {{ $model->created_at->diffForHumans() }}
                        </span>

                        @if ($isUnread)
                        <form method="POST" action="{{ route('notifications.mark-read', $model->id) }}">
                            @csrf
                            <button type="submit" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                Mark as read
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </li>
            @empty
            <li class="text-center py-12 text-sm text-neutral-500 dark:text-neutral-400">
                No notifications found.
            </li>
            @endforelse
        </ul>

        <!-- Pagination -->
        <div class="flex justify-end mt-4">
            {{ $models->appends(request()->query())->links() }}
        </div>
    </div>
</x-dashboard-layout>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    if (!userId) return;

    // KHUSUS HALAMAN
    const list = document.getElementById('page-notif-list');
    const badge = document.getElementById('page-unread-count');

    if (!list || !badge) return;

    let unread = parseInt(badge.textContent || 0, 10);

    window.Echo.private(`App.Models.User.${userId}`)
        .notification((n) => {
            console.log('PAGE realtime:', n);

            unread++;
            badge.textContent = unread;

            const li = document.createElement('li');
            li.className = 'flex gap-x-4 py-4 px-4 bg-blue-50/60 dark:bg-blue-900/20';
            li.innerHTML = `
                <div class="flex-shrink-0">
                    <span class="inline-flex size-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                        🔔
                    </span>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold">${n.title}</p>
                        <span class="text-xs text-blue-600 font-medium">New</span>
                    </div>

                    <p class="mt-1 text-sm text-gray-500">${n.message}</p>
                    <div class="mt-2 text-xs text-gray-400">just now</div>
                </div>
            `;

            list.prepend(li);
        });
});
</script>

