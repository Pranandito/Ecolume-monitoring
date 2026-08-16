<nav class="flex items-center justify-between bg-[#121212] text-white w-full">

    <div class="flex items-center gap-4">
        <button id="btn-open-sidebar" class="p-1.5 text-white hover:text-white rounded-md hover:bg-zinc-800 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="9" y1="3" x2="9" y2="21"></line>
            </svg>
        </button>

        <h1 class="text-2xl tracking-wide">{{ $slot }}</h1>

        <div class="h-6 w-[1px] bg-[#373737] mx-2 hidden lg:block"></div>

        <p class="hidden lg:block text-2xl">
            Selamat Datang, {{ auth()->user()->name }} 👋
        </p>
    </div>

    <div class="items-center gap-5 flex">

        <div class="hidden sm:flex flex-col items-end">
            <span class="text-xs text-zinc-100">{{ auth()->user()->name }}</span>
            <span class="text-[10px] text-zinc-400">{{ auth()->user()->email }}</span>
        </div>

        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
            alt="Profile picture" class="w-10 h-10 rounded-full object-cover border border-zinc-700">

        <!-- <button
            class="relative p-2 text-zinc-400 hover:text-white rounded-full hover:bg-zinc-800 transition-colors hidden lg:block">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span
                class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 border-2 border-[#18181b] rounded-full"></span>
        </button> -->

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="p-1.5 text-white hover:text-white rounded-md hover:bg-zinc-800 transition-colors hidden lg:block">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.83333 24.5C5.19167 24.5 4.64256 24.2717 4.186 23.8152C3.72944 23.3586 3.50078 22.8091 3.5 22.1667V5.83333C3.5 5.19167 3.72867 4.64256 4.186 4.186C4.64333 3.72944 5.19244 3.50078 5.83333 3.5H14V5.83333H5.83333V22.1667H14V24.5H5.83333ZM18.6667 19.8333L17.0625 18.1417L20.0375 15.1667H10.5V12.8333H20.0375L17.0625 9.85833L18.6667 8.16667L24.5 14L18.6667 19.8333Z" fill="#979797" />
                </svg>
            </button>
        </form>
    </div>
</nav>