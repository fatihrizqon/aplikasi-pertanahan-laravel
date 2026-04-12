<aside id="sidebar" class="fixed lg:static top-0 left-0 h-screen w-[275px] bg-white border-e border-gray-200 dark:bg-neutral-800 dark:border-neutral-700 transform lg:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out pt-10 z-20">
    <div class="flex flex-col h-full">
        <nav class="flex-1 px-4 pt-4 overflow-y-auto space-y-1 scrollbar-custom">
            <ul class="flex flex-col space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Route::currentRouteName() === 'dashboard' ? 'nav-active' : '' }}">
                        <i data-lucide="gauge" class="w-4 h-4"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <!-- User Management -->
                <li>
                    <a href="{{ route('users.index') }}" class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm rounded-lg {{ Route::currentRouteName() == 'users.index' ? 'bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-white' : 'text-gray-800 dark:text-neutral-200' }}">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span class="nav-text">User Management</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
