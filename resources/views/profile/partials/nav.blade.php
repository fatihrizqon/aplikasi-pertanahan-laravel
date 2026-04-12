<nav class="flex gap-x-2">
    <a href="{{ route('profile.edit') }}" class="py-2 px-3 inline-flex items-center gap-2 text-sm font-medium rounded-lg border shadow-xl
       {{ request()->routeIs('profile.edit') ? 'bg-white text-neutral-900 border-neutral-200 dark:bg-neutral-800 dark:text-neutral-100 dark:border-neutral-700' : 'bg-transparent text-neutral-500 border-transparent hover:text-neutral-700 focus:outline-none focus:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
        Profile
    </a>

    <a href="{{ route('notifications.index') }}" class="py-2 px-3 inline-flex items-center gap-2 text-sm font-medium rounded-lg border
       {{ request()->routeIs('notifications.index') ? 'bg-white text-neutral-900 border-neutral-200 dark:bg-neutral-800 dark:text-neutral-100 dark:border-neutral-700' : 'bg-transparent text-neutral-500 border-transparent hover:text-neutral-700 focus:outline-none focus:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200' }}">
        Notifications
    </a>
</nav>
