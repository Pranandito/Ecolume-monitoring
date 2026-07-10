<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ramalan Cuaca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('images/ecolume-logo.svg') }}" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            /* Warna background utama */
        }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .apexcharts-tooltip {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            overflow: visible !important;
        }

        .apexcharts-tooltip.apexcharts-theme-light {
            border: none !important;
            background: transparent !important;
        }

        .apexcharts-xaxistooltip {
            display: none !important;
        }

        .apexcharts-xcrosshairs,
        .apexcharts-ycrosshairs {
            stroke-dasharray: 4;
            stroke: #3f3f46;
        }

        .apexcharts-series-markers circle {
            stroke-width: 2 !important;
        }
    </style>
</head>

<aside id="sidebar" class="fixed left-0 top-0 bottom-0 h-lvh w-[400px] bg-[#171717]  z-[1113] p-11 flex flex-col justify-between  -translate-x-full transition-transform duration-300">
    <div>
        <div class=" flex items-center justify-between">
            <div class="flex items-center gap-4 text-2xl">
                <img src="{{ asset('images/ecolume-logo.svg') }}" alt="" class="w-8">
                <h1>Ecolume</h1>
            </div>
            <button id="btn-close-sidebar" class="p-1.5 text-white hover:text-white rounded-md hover:bg-zinc-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                </svg>
            </button>
        </div>
        <hr class="my-8 -m-11 border-[#333333]">
        <p class="text-[#979797]">Menu</p>
        <a href="{{ route('beranda') }}" class="block mb-3">
            <div class="group flex items-center gap-[10px] px-3 py-2 hover:bg-[#2A2A2A] text-[#979797] hover:text-white rounded-xl">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" class="fill-[#979797] group-hover:fill-white">
                    <path d="M9.16639 17.4167V12.8333H12.8331V17.4167C12.8331 17.9208 13.2456 18.3333 13.7497 18.3333H16.4997C17.0039 18.3333 17.4164 17.9208 17.4164 17.4167V11H18.9747C19.3964 11 19.5981 10.4775 19.2772 10.2025L11.6139 3.3C11.2656 2.98834 10.7339 2.98834 10.3856 3.3L2.72222 10.2025C2.41056 10.4775 2.60306 11 3.02472 11H4.58306V17.4167C4.58306 17.9208 4.99556 18.3333 5.49972 18.3333H8.24972C8.75389 18.3333 9.16639 17.9208 9.16639 17.4167Z" />
                </svg>
                <h1>Beranda</h1>
            </div>
        </a>
        <div>
            <button class="w-full flex items-center justify-between px-3 py-2 rounded-xl bg-[#2A2A2A] text-white">
                <div class="flex items-center gap-3">
                    <!-- Grid Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                    </svg>
                    <span>Dashboard</span>
                </div>
                <!-- Arrow -->
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M6 9l6 6 6-6" />
                </svg>
            </button>
            <div class="ml-5 mt-2 border-l border-gray-700 pl-7 space-y-2">
                @foreach($devices as $device_)
                <a href="{{ route('dashboard', ['device_id' => $device_->id, 'device_name' => $device_->device_name]) }}" class="flex gap-2 py-2 hover:text-white hover:underline">
                    <span>{{ $device_->device_name }}</span>
                    <span class="w-[6px] h-[7px] rounded-full {{ $device_->online_status ? 'bg-[#00A451]' : 'bg-[#DC2626]'}}"></span>
                </a>
                @endforeach
            </div>
        </div>
        <a href="{{ route('ramalan-cuaca', ['device_name' => $devices[0]->device_name, 'device_id' => $devices[0]->id]) }}" class="block my-3">
            <div class="group flex items-center gap-[13px]  px-3 py-2 hover:bg-[#2A2A2A] text-[#979797] hover:text-white rounded-xl">
                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" class="fill-[#979797] group-hover:fill-white">
                    <path d="M15.4097 8.3125C15.9045 8.3125 16.3683 8.40527 16.8013 8.59082C17.2342 8.77637 17.6146 9.02686 17.9424 9.34229C18.2702 9.65771 18.5269 10.035 18.7124 10.4741C18.8979 10.9132 18.9938 11.3802 19 11.875C19 12.3698 18.9072 12.8306 18.7217 13.2573C18.5361 13.6841 18.2826 14.0614 17.9609 14.3892C17.6393 14.717 17.259 14.9736 16.8198 15.1592C16.3807 15.3447 15.9199 15.4375 15.4375 15.4375H4.75C4.0944 15.4375 3.479 15.3138 2.90381 15.0664C2.32861 14.819 1.82454 14.4819 1.3916 14.0552C0.958659 13.6284 0.61849 13.1243 0.371094 12.543C0.123698 11.9616 0 11.3431 0 10.6875C0 10.0319 0.123698 9.4165 0.371094 8.84131C0.61849 8.26611 0.955566 7.76204 1.38232 7.3291C1.80908 6.89616 2.31315 6.55599 2.89453 6.30859C3.47591 6.0612 4.0944 5.9375 4.75 5.9375C5.03451 5.9375 5.3221 5.96533 5.61279 6.021C5.86019 5.63753 6.14469 5.29427 6.46631 4.99121C6.78792 4.68815 7.14355 4.42839 7.5332 4.21191C7.92285 3.99544 8.33105 3.83464 8.75781 3.72949C9.18457 3.62435 9.62988 3.56868 10.0938 3.5625C10.7803 3.5625 11.4266 3.68311 12.0327 3.92432C12.6388 4.16553 13.18 4.49642 13.6562 4.91699C14.1325 5.33757 14.519 5.84163 14.8159 6.4292C15.1128 7.01676 15.3107 7.64453 15.4097 8.3125ZM15.4375 14.25C15.7653 14.25 16.0715 14.1882 16.356 14.0645C16.6405 13.9408 16.894 13.7707 17.1167 13.5542C17.3394 13.3377 17.5094 13.0872 17.627 12.8027C17.7445 12.5182 17.8063 12.209 17.8125 11.875C17.8125 11.5472 17.7507 11.241 17.627 10.9565C17.5033 10.672 17.3332 10.4185 17.1167 10.1958C16.9002 9.97314 16.6497 9.80306 16.3652 9.68555C16.0807 9.56803 15.7715 9.50619 15.4375 9.5H14.25V8.90625C14.25 8.33105 14.1418 7.79297 13.9253 7.29199C13.7088 6.79102 13.4119 6.3488 13.0347 5.96533C12.6574 5.58187 12.2183 5.28499 11.7173 5.07471C11.2163 4.86442 10.6751 4.75618 10.0938 4.75C9.66081 4.75 9.24333 4.81494 8.84131 4.94482C8.43929 5.07471 8.07129 5.25716 7.7373 5.49219C7.40332 5.72721 7.10335 6.00863 6.8374 6.33643C6.57145 6.66423 6.36426 7.03532 6.21582 7.44971C5.75195 7.23324 5.26335 7.125 4.75 7.125C4.25521 7.125 3.79443 7.21777 3.36768 7.40332C2.94092 7.58887 2.56364 7.84245 2.23584 8.16406C1.90804 8.48568 1.65137 8.86605 1.46582 9.30518C1.28027 9.7443 1.1875 10.2051 1.1875 10.6875C1.1875 11.1823 1.28027 11.6431 1.46582 12.0698C1.65137 12.4966 1.90495 12.8739 2.22656 13.2017C2.54818 13.5295 2.92546 13.7861 3.3584 13.9717C3.79134 14.1572 4.25521 14.25 4.75 14.25H15.4375Z" />
                </svg>
                <h1>Ramalan Cuaca</h1>
            </div>
        </a>
        <a href="" class="">
            <div class="group flex items-center gap-4  px-3 py-2 hover:bg-[#2A2A2A] rounded-xl text-[#979797] hover:text-white">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="fill-[#979797] group-hover:fill-white">
                    <path d="M5 9H9V10H5V9ZM5 6.5H11V7.5H5V6.5ZM5 11.5H7.5V12.5H5V11.5Z" />
                    <path d="M12.5 2.5H11V2C11 1.73478 10.8946 1.48043 10.7071 1.29289C10.5196 1.10536 10.2652 1 10 1H6C5.73478 1 5.48043 1.10536 5.29289 1.29289C5.10536 1.48043 5 1.73478 5 2V2.5H3.5C3.23478 2.5 2.98043 2.60536 2.79289 2.79289C2.60536 2.98043 2.5 3.23478 2.5 3.5V14C2.5 14.2652 2.60536 14.5196 2.79289 14.7071C2.98043 14.8946 3.23478 15 3.5 15H12.5C12.7652 15 13.0196 14.8946 13.2071 14.7071C13.3946 14.5196 13.5 14.2652 13.5 14V3.5C13.5 3.23478 13.3946 2.98043 13.2071 2.79289C13.0196 2.60536 12.7652 2.5 12.5 2.5ZM6 2H10V4H6V2ZM12.5 14H3.5V3.5H5V5H11V3.5H12.5V14Z" />
                </svg>
                <h1>Laporan Analitik</h1>
            </div>
        </a>
    </div>
    <div>
        <hr class="my-6 border-[#333333] -m-11">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-5">
                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    alt="Profile picture" class="w-10 h-10 rounded-full object-cover border border-zinc-700">
                <div class="hidden sm:flex flex-col">
                    <span class="text-xs text-zinc-100">{{ auth()->user()->name }}</span>
                    <span class="text-[10px] text-zinc-400">{{ auth()->user()->email }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-1.5 text-white hover:text-white rounded-md hover:bg-zinc-800 transition-colors">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.83333 24.5C5.19167 24.5 4.64256 24.2717 4.186 23.8152C3.72944 23.3586 3.50078 22.8091 3.5 22.1667V5.83333C3.5 5.19167 3.72867 4.64256 4.186 4.186C4.64333 3.72944 5.19244 3.50078 5.83333 3.5H14V5.83333H5.83333V22.1667H14V24.5H5.83333ZM18.6667 19.8333L17.0625 18.1417L20.0375 15.1667H10.5V12.8333H20.0375L17.0625 9.85833L18.6667 8.16667L24.5 14L18.6667 19.8333Z" fill="#979797" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<section id="overlay" class="hidden fixed top-0 bottom-0 left-0 right-0 bg-black/30 z-[1112]"></section>

<body class="my-12 mx-16 text-white">

    <nav class="flex items-center justify-between bg-[#121212] text-white w-full">

        <div class="flex items-center gap-4">
            <button id="btn-open-sidebar" class="p-1.5 text-white hover:text-white rounded-md hover:bg-zinc-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                </svg>
            </button>

            <h1 class="text-2xl tracking-wide">Dashboard</h1>

            <div class="h-6 w-[1px] bg-[#373737] mx-2"></div>

            <p class="text-2xl">
                Selamat Datang, {{ auth()->user()->name }} 👋
            </p>
        </div>

        <div class="flex items-center gap-5">

            <div class="hidden sm:flex flex-col items-end">
                <span class="text-xs text-zinc-100">{{ auth()->user()->name }}</span>
                <span class="text-[10px] text-zinc-400">{{ auth()->user()->email }}</span>
            </div>

            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                alt="Profile picture" class="w-10 h-10 rounded-full object-cover border border-zinc-700">

            <button
                class="relative p-2 text-zinc-400 hover:text-white rounded-full hover:bg-zinc-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <span
                    class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 border-2 border-[#18181b] rounded-full"></span>
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-1.5 text-white hover:text-white rounded-md hover:bg-zinc-800 transition-colors">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.83333 24.5C5.19167 24.5 4.64256 24.2717 4.186 23.8152C3.72944 23.3586 3.50078 22.8091 3.5 22.1667V5.83333C3.5 5.19167 3.72867 4.64256 4.186 4.186C4.64333 3.72944 5.19244 3.50078 5.83333 3.5H14V5.83333H5.83333V22.1667H14V24.5H5.83333ZM18.6667 19.8333L17.0625 18.1417L20.0375 15.1667H10.5V12.8333H20.0375L17.0625 9.85833L18.6667 8.16667L24.5 14L18.6667 19.8333Z" fill="#979797" />
                    </svg>
                </button>
            </form>
        </div>
    </nav>

    <hr class="my-5 border-[#373737]">

    <div class="grid grid-cols-7 grid-rows-1 gap-6">
        <div class="col-span-4">
            <div class="bg-[#171717] rounded-2xl p-3">
                <div class="rounded-lg bg-[#121212] p-7">
                    <div class="flex items-center justify-between">
                        <div>
                            <div
                                class="flex items-center text-white gap-2 text-md py-1 px-2 rounded-full bg-[#262626] w-fit">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M7.08285 13.4583V9.91668H9.91619V13.4583C9.91619 13.8479 10.2349 14.1667 10.6245 14.1667H12.7495C13.1391 14.1667 13.4579 13.8479 13.4579 13.4583V8.50001H14.662C14.9879 8.50001 15.1437 8.09626 14.8958 7.88376L8.9741 2.55001C8.70494 2.30918 8.2941 2.30918 8.02494 2.55001L2.10327 7.88376C1.86244 8.09626 2.01119 8.50001 2.33702 8.50001H3.54119V13.4583C3.54119 13.8479 3.85994 14.1667 4.24952 14.1667H6.37452C6.7641 14.1667 7.08285 13.8479 7.08285 13.4583Z"
                                        fill="#FFFFF0" />
                                </svg>
                                <h1>{{ $device->device_config->location }}</h1>
                            </div>
                            <p class="text-[#979797] text-sm mt-1">Cerah Berawan • Rabu, 27 Mei 2026</p>
                        </div>
                        <img src="{{ asset('images/cerah_berawan.svg') }}" alt="">
                    </div>
                    <!-- Sunrise / Sunset Timeline -->
                    <div class="flex items-center gap-3 mt-3">
                        <!-- Sunrise -->
                        <div class="flex flex-col items-center gap-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M12 2v8M4.93 10.93l1.41 1.41M2 18h2M20 18h2M17.66 12.34l1.41-1.41M22 22H2M16 6l-4-4-4 4" />
                                <path d="M12 10a4 4 0 0 1 4 4H8a4 4 0 0 1 4-4Z" />
                            </svg>
                            <span class="text-[11px] text-zinc-400">04.31</span>
                        </div>
                        <!-- Line -->
                        <div class="flex-1 flex items-center">
                            <div
                                class="flex-1 h-px bg-[repeating-linear-gradient(to_right,#434343_0px,#434343_8px,transparent_8px,transparent_12px)]">
                            </div>

                            <div class="flex flex-col items-center mx-2">
                                <span class="text-[10px] text-white">13j 14m</span>
                            </div>

                            <div
                                class="flex-1 h-px bg-[repeating-linear-gradient(to_right,#434343_0px,#434343_8px,transparent_8px,transparent_12px)]">
                            </div>
                        </div>
                        <!-- Sunset -->
                        <div class="flex flex-col items-center gap-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M12 10V2M4.93 10.93l1.41 1.41M2 18h2M20 18h2M17.66 12.34l1.41-1.41M22 22H2M16 18l-4 4-4-4" />
                                <path d="M12 14a4 4 0 0 1 4 4H8a4 4 0 0 1 4-4Z" />
                            </svg>
                            <span class="text-[11px] text-zinc-400">17.45</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center text-white mt-5 mx-4 justify-between">
                    <div class="flex items-center gap-10">
                        <div>
                            <h1>Hujan</h1>
                            <div class="flex items-center gap-2 text-[#979797] mb-5">
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M8.70827 1.58337C8.05714 1.58337 7.41297 1.71724 6.81576 1.97666C6.21854 2.23608 5.68104 2.61551 5.23664 3.0914C4.79223 3.56728 4.45041 4.12945 4.2324 4.743C4.0144 5.35654 3.92486 6.00835 3.96935 6.65796C3.35728 6.92262 2.82199 7.33762 2.41317 7.86445C2.00434 8.39128 1.73524 9.01285 1.63084 9.67147C1.52644 10.3301 1.59013 11.0044 1.81598 11.6318C2.04184 12.2593 2.42255 12.8195 2.92277 13.2605C3.08024 13.3995 3.28648 13.4702 3.49611 13.4571C3.70575 13.4441 3.90161 13.3483 4.0406 13.1908C4.1796 13.0333 4.25034 12.8271 4.23728 12.6174C4.22422 12.4078 4.12841 12.212 3.97094 12.073C3.64297 11.7833 3.40132 11.4088 3.27263 10.9906C3.14394 10.5724 3.1332 10.1267 3.24159 9.70283C3.34999 9.27892 3.57331 8.89316 3.88694 8.58806C4.20058 8.28296 4.59236 8.07036 5.0191 7.97371C5.22372 7.92776 5.40174 7.8025 5.5141 7.62543C5.62645 7.44836 5.66396 7.23395 5.61839 7.02925C5.53112 6.6226 5.52479 6.20274 5.59978 5.79364C5.67476 5.38454 5.82959 4.99422 6.05542 4.64496C6.28125 4.2957 6.57367 3.99434 6.91597 3.75809C7.25826 3.52184 7.64374 3.35532 8.05039 3.26804C8.45705 3.18076 8.87691 3.17444 9.286 3.24942C9.6951 3.32441 10.0854 3.47924 10.4347 3.70507C10.7839 3.9309 11.0853 4.22331 11.3216 4.56561C11.5578 4.90791 11.7243 5.29339 11.8116 5.70004C11.8486 5.88141 11.948 6.04409 12.0925 6.1598C12.2369 6.2755 12.4174 6.33692 12.6025 6.33337H12.6666C13.3312 6.33337 13.979 6.54249 14.5182 6.93111C15.0574 7.31972 15.4606 7.86813 15.6708 8.49865C15.8809 9.12917 15.8874 9.80984 15.6892 10.4442C15.4911 11.0786 15.0983 11.6346 14.5666 12.0334C14.4834 12.0958 14.4134 12.174 14.3604 12.2635C14.3075 12.353 14.2727 12.4521 14.258 12.555C14.2433 12.658 14.2491 12.7628 14.2749 12.8635C14.3008 12.9643 14.3462 13.0589 14.4087 13.1421C14.4711 13.2253 14.5493 13.2953 14.6388 13.3483C14.7283 13.4012 14.8274 13.436 14.9303 13.4507C15.0333 13.4654 15.1381 13.4596 15.2388 13.4338C15.3396 13.4079 15.4342 13.3625 15.5174 13.3C16.2785 12.729 16.8507 11.9426 17.1596 11.0426C17.4686 10.1427 17.5001 9.17068 17.2501 8.25258C17.0002 7.33448 16.4802 6.51261 15.7577 5.89347C15.0352 5.27433 14.1434 4.88646 13.1978 4.78012C12.8748 3.8466 12.2688 3.03701 11.4641 2.46404C10.6594 1.89107 9.6961 1.58323 8.70827 1.58337ZM10.2512 12.1252C10.2841 12.0265 10.2972 11.9224 10.2898 11.8187C10.2824 11.7149 10.2546 11.6137 10.2081 11.5207C10.1615 11.4277 10.0971 11.3448 10.0186 11.2767C9.93999 11.2086 9.84876 11.1566 9.7501 11.1237C9.65144 11.0909 9.54728 11.0778 9.44355 11.0852C9.33983 11.0926 9.23858 11.1204 9.14559 11.1669C8.95778 11.2609 8.81499 11.4256 8.74864 11.6249L7.16531 16.3749C7.13246 16.4735 7.11936 16.5777 7.12676 16.6814C7.13417 16.7851 7.16193 16.8864 7.20846 16.9794C7.30244 17.1672 7.46718 17.31 7.66644 17.3763C7.86569 17.4427 8.08314 17.4272 8.27095 17.3332C8.45876 17.2392 8.60155 17.0745 8.66789 16.8752L10.2512 12.1252ZM12.9168 10.3321C13.0154 10.3649 13.1067 10.4169 13.1853 10.485C13.2639 10.5531 13.3283 10.636 13.3748 10.729C13.4213 10.822 13.4491 10.9232 13.4565 11.027C13.4639 11.1307 13.4508 11.2349 13.4179 11.3335L11.8346 16.0835C11.7682 16.2828 11.6254 16.4475 11.4376 16.5415C11.2498 16.6355 11.0324 16.651 10.8331 16.5847C10.6338 16.5183 10.4691 16.3755 10.3751 16.1877C10.2812 15.9999 10.2656 15.7825 10.332 15.5832L11.9153 10.8332C11.9481 10.7345 12.0001 10.6433 12.0682 10.5647C12.1363 10.4861 12.2192 10.4217 12.3122 10.3752C12.4052 10.3286 12.5065 10.3009 12.6102 10.2935C12.7139 10.2861 12.8181 10.2992 12.9168 10.3321ZM7.48119 11.3335C7.54753 11.1343 7.53201 10.9168 7.43803 10.729C7.34405 10.5412 7.17932 10.3984 6.98006 10.3321C6.78081 10.2657 6.56335 10.2813 6.37554 10.3752C6.18773 10.4692 6.04495 10.634 5.9786 10.8332L4.39527 15.5832C4.32892 15.7825 4.34444 15.9999 4.43842 16.1877C4.5324 16.3755 4.69714 16.5183 4.89639 16.5847C5.09565 16.651 5.3131 16.6355 5.50091 16.5415C5.68872 16.4475 5.8315 16.2828 5.89785 16.0835L7.48119 11.3335Z"
                                        fill="#979797" />
                                </svg>
                                <h1>75 %</h1>
                            </div>
                            <h1>Kelembapan</h1>
                            <div class="flex items-center gap-2 text-[#979797]">
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.5 15.8333C8.24022 15.8333 7.03204 15.3328 6.14124 14.442C5.25045 13.5513 4.75 12.3431 4.75 11.0833C4.75 7.91663 9.5 2.57288 9.5 2.57288C9.5 2.57288 14.25 7.91663 14.25 11.0833C14.25 12.3431 13.7496 13.5513 12.8588 14.442C11.968 15.3328 10.7598 15.8333 9.5 15.8333Z" fill="#979797" />
                                </svg>
                                <h1>84 %</h1>
                            </div>
                        </div>
                        <div>
                            <h1>Tekanan</h1>
                            <div class="flex items-center gap-2 text-[#979797] mb-5">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.877 5.847L11.857 6.867C11.7633 6.96089 11.636 7.01368 11.5034 7.01378C11.3707 7.01387 11.2434 6.96125 11.1495 6.8675C11.0556 6.77375 11.0028 6.64654 11.0027 6.51385C11.0026 6.38117 11.0553 6.25389 11.149 6.16L12.249 5.061C12.199 5.00767 12.1483 4.95533 12.097 4.904C11.0016 3.80464 9.54755 3.13563 8 3.019V4.5C8 4.63261 7.94733 4.75979 7.85356 4.85355C7.75979 4.94732 7.63261 5 7.50001 5C7.3674 5 7.24022 4.94732 7.14645 4.85355C7.05268 4.75979 7.00001 4.63261 7.00001 4.5V3.019C5.37119 3.14157 3.84901 3.87569 2.73901 5.074L3.80901 6.145C3.89645 6.23987 3.94383 6.36487 3.94125 6.49387C3.93867 6.62287 3.88633 6.74587 3.79516 6.83717C3.70399 6.92847 3.58106 6.98099 3.45206 6.98375C3.32307 6.98652 3.198 6.93931 3.103 6.852L2.113 5.862C1.28836 7.07681 0.902143 8.53631 1.01801 10H2.50001C2.63261 10 2.75979 10.0527 2.85356 10.1464C2.94733 10.2402 3.00001 10.3674 3.00001 10.5C3.00001 10.6326 2.94733 10.7598 2.85356 10.8536C2.75979 10.9473 2.63261 11 2.50001 11H1.17401C1.25734 11.3533 1.36967 11.6967 1.51101 12.03C1.53661 12.0905 1.55005 12.1555 1.55054 12.2213C1.55103 12.287 1.53857 12.3522 1.51387 12.4131C1.48917 12.474 1.45271 12.5295 1.40658 12.5763C1.36045 12.6231 1.30554 12.6604 1.24501 12.686C1.18447 12.7116 1.11948 12.725 1.05374 12.7255C0.988014 12.726 0.92283 12.7136 0.861915 12.6889C0.800999 12.6642 0.745546 12.6277 0.69872 12.5816C0.651894 12.5354 0.614613 12.4805 0.589005 12.42C0.19941 11.4958 -0.000872503 10.5029 5.04216e-06 9.5C-0.00113885 8.51479 0.192359 7.53905 0.569386 6.62883C0.946414 5.71861 1.49954 4.89184 2.19701 4.196C3.60357 2.78981 5.51109 1.99989 7.50001 2C8.48506 1.99995 9.46047 2.19394 10.3705 2.57091C11.2806 2.94787 12.1075 3.50042 12.804 4.197C13.5012 4.8928 14.054 5.71945 14.4309 6.62948C14.8077 7.53952 15.0011 8.51502 15 9.5C15.0006 10.503 14.7999 11.4959 14.41 12.42C14.355 12.537 14.2568 12.6281 14.1361 12.6742C14.0153 12.7204 13.8814 12.718 13.7624 12.6675C13.6434 12.6171 13.5486 12.5225 13.4978 12.4036C13.447 12.2848 13.4442 12.1509 13.49 12.03C13.63 11.6967 13.742 11.3533 13.826 11H12.5C12.3674 11 12.2402 10.9473 12.1465 10.8536C12.0527 10.7598 12 10.6326 12 10.5C12 10.3674 12.0527 10.2402 12.1465 10.1464C12.2402 10.0527 12.3674 10 12.5 10H13.981C14.0961 8.53039 13.7067 7.06546 12.877 5.847ZM6.83601 11.164C6.64855 10.9791 6.54218 10.7274 6.54031 10.4641C6.53843 10.2008 6.6412 9.94753 6.82601 9.76C7.21001 9.375 9.708 7.758 9.97501 8.025C10.242 8.292 8.625 10.79 8.24 11.175C8.05247 11.3598 7.79922 11.4626 7.53593 11.4607C7.27265 11.4588 7.02089 11.3525 6.83601 11.165" fill="#979797" />
                                </svg>
                                <h1>1.3 bar</h1>
                            </div>
                            <h1>Angin</h1>
                            <div class="flex items-center gap-2 text-[#979797]">
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.4709 5.54163C15.6607 5.54163 16.625 6.42829 16.625 7.52079C16.625 8.61329 15.6607 9.49996 14.4709 9.49996H2.375M14.2009 15.8333C15.1026 15.8333 16.2292 15.4375 16.2292 13.8541C16.2292 12.2708 15.1026 11.875 14.2009 11.875H2.375M8.24283 3.16663C9.37492 3.16663 10.2917 4.05329 10.2917 5.14579C10.2917 6.23829 9.37412 7.12496 8.24283 7.12496H2.375" stroke="#979797" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <h1>3.2 m/s</h1>
                            </div>
                        </div>
                        <div>
                            <h1>Iradiasi Matahari</h1>
                            <div class="flex items-center gap-2 text-[#979797] mb-5">
                                <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.23892 0C2.38111 0 1.64423 0.587891 1.47548 1.40547L0.0354811 8.40547C-0.186706 9.48828 0.662669 10.5 1.79892 10.5H7.90204V12.25H6.10204C5.60423 12.25 5.20204 12.641 5.20204 13.125C5.20204 13.609 5.60423 14 6.10204 14H11.502C11.9999 14 12.402 13.609 12.402 13.125C12.402 12.641 11.9999 12.25 11.502 12.25H9.70204V10.5H15.8052C16.9414 10.5 17.7936 9.49102 17.5714 8.40547L16.1314 1.40547C15.9599 0.587891 15.223 0 14.3652 0H3.23892ZM7.13986 1.75H10.4642L10.7567 4.59375H6.84736L7.13986 1.75ZM5.49173 4.59375H2.65673L3.23892 1.75H5.78142L5.49173 4.59375ZM2.38392 5.90625H5.35392L5.06423 8.75H1.79892L2.38392 5.90625ZM6.71236 5.90625H10.8917L11.1842 8.75H6.41986L6.71236 5.90625ZM12.2474 5.90625H15.2174L15.8052 8.75H12.5427L12.2474 5.90625ZM14.9474 4.59375H12.1124L11.8199 1.75H14.3624L15.2455 1.57773L14.3652 1.75L14.9502 4.59375H14.9474Z" fill="#979797" />
                                </svg>
                                <h1>500 W/m²</h1>
                            </div>
                            <h1>Tutupan Awan</h1>
                            <div class="flex items-center gap-2 text-[#979797]">
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.6875 16.625C4.59608 16.625 3.66712 16.2441 2.90062 15.4823C2.13354 14.7216 1.75 13.795 1.75 12.7024C1.75 11.6996 2.09271 10.8097 2.77812 10.0327C3.46354 9.25575 4.3155 8.83838 5.334 8.78062C5.53058 7.50604 6.11683 6.45313 7.09275 5.62188C8.06867 4.79063 9.20442 4.375 10.5 4.375C11.9595 4.375 13.1994 4.88513 14.2196 5.90538C15.2399 6.92563 15.75 8.1655 15.75 9.625V10.5H16.289C17.1267 10.5268 17.8296 10.8345 18.3977 11.4231C18.9659 12.0117 19.25 12.7248 19.25 13.5625C19.25 14.4206 18.9543 15.1457 18.3628 15.7378C17.7701 16.3293 17.045 16.625 16.1875 16.625H5.6875ZM5.6875 15.75H16.1875C16.8 15.75 17.3177 15.5385 17.7406 15.1156C18.1635 14.6927 18.375 14.175 18.375 13.5625C18.375 12.95 18.1635 12.4323 17.7406 12.0094C17.3177 11.5865 16.8 11.375 16.1875 11.375H14.875V9.625C14.875 8.41458 14.4483 7.38267 13.5949 6.52925C12.7415 5.67583 11.7098 5.24942 10.5 5.25C9.29017 5.25058 8.25854 5.67729 7.40513 6.53012C6.55171 7.38296 6.125 8.41458 6.125 9.625H5.6875C4.84167 9.625 4.11979 9.92396 3.52188 10.5219C2.92396 11.1198 2.625 11.8417 2.625 12.6875C2.625 13.5333 2.92396 14.2552 3.52188 14.8531C4.11979 15.451 4.84167 15.75 5.6875 15.75Z" fill="#979797" />
                                </svg>
                                <h1>40%</h1>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex">
                            <h1 class="text-4xl">30.2</h1>
                            <p>°C</p>
                        </div>
                        <p class="text-[#979797] text-sm">Terasa: 30.2 °C</p>
                    </div>
                </div>
            </div>

            <div class="bg-[#171717] rounded-2xl p-6 mt-6 flex flex-col">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.486 5.57839C13.5577 5.3146 13.7313 5.0901 13.9685 4.95423C14.2057 4.81835 14.4871 4.78221 14.751 4.85376L19.0355 6.02114C19.298 6.09266 19.5216 6.26503 19.6576 6.50069C19.7936 6.73634 19.8309 7.01619 19.7615 7.27926L18.6299 11.5706C18.5545 11.8286 18.3814 12.047 18.1475 12.1793C17.9135 12.3116 17.6371 12.3473 17.3772 12.2788C17.1173 12.2104 16.8944 12.0431 16.756 11.8127C16.6175 11.5823 16.5745 11.307 16.6361 11.0454L17.0995 9.28401C15.3083 10.5143 13.6457 11.922 12.1371 13.486C12.042 13.5846 11.9283 13.6633 11.8025 13.7174C11.6767 13.7716 11.5413 13.8001 11.4044 13.8014C11.2674 13.8027 11.1316 13.7767 11.0048 13.7249C10.878 13.6731 10.7627 13.5966 10.6659 13.4998L8.24998 11.0825L3.13498 16.1975C3.0398 16.296 2.92597 16.3745 2.80013 16.4285C2.67429 16.4824 2.53895 16.5108 2.40203 16.5119C2.2651 16.5131 2.12932 16.4869 2.0026 16.435C1.87589 16.3831 1.76079 16.3065 1.66401 16.2096C1.56723 16.1127 1.49071 15.9975 1.43891 15.8708C1.38712 15.744 1.36109 15.6082 1.36235 15.4713C1.3636 15.3344 1.39211 15.1991 1.44622 15.0733C1.50032 14.9475 1.57894 14.8337 1.67748 14.7386L7.52123 8.89489C7.71459 8.70177 7.9767 8.59329 8.24998 8.59329C8.52326 8.59329 8.78537 8.70177 8.97873 8.89489L11.3932 11.308C12.8794 9.8588 14.4939 8.54719 16.2167 7.38926L14.2092 6.84201C13.9457 6.76996 13.7215 6.59629 13.5859 6.35912C13.4503 6.12195 13.4144 5.84204 13.486 5.57839Z" fill="white" />
                            </svg>
                        </div>
                        <h3 class="text-lg text-white font-medium">Tren Suhu Lingkungan</h3>
                    </div>

                    <div class="flex items-center p-0.5 rounded-lg bg-zinc-800/50 border border-zinc-700">
                        <button
                            class="px-3 py-1 text-xs font-medium text-white bg-zinc-700 rounded-md shadow">1H</button>
                        <button
                            class="px-3 py-1 text-xs font-medium text-zinc-400 hover:text-white transition-colors">1M</button>
                        <button
                            class="px-3 py-1 text-xs font-medium text-zinc-400 hover:text-white transition-colors">Custom</button>
                    </div>
                </div>

                <div id="chart" class="h-[300px]"></div>

                <div class="flex justify-center items-center gap-6 mt-6">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#F97316]"></span>
                        <span class="text-sm text-zinc-400">Suhu (°C)</span>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <script>
                var CONFIG = {
                    MAX_VISIBLE_POINTS: 500,
                    REFRESH_INTERVAL_MS: 30000,
                    //API_ENDPOINT: '/device/weather-forecast/' // GANTI KE ENDPOINT CUACA ASLI NANTI
                };

                var rawSeriesData = [];
                var currentRange = '5D';
                var SERIES_COLORS = ['#F97316']; // oranye untuk suhu
                var SERIES_UNITS = ['°C'];

                // Formatting Label Tooltip
                function fmtLabel(tsMs, range) {
                    var d = new Date(tsMs);
                    var pad = function(v) {
                        return v < 10 ? '0' + v : '' + v;
                    };
                    var time = pad(d.getHours()) + ':' + pad(d.getMinutes());
                    var date = pad(d.getDate()) + ' ' + ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'][d.getMonth()];
                    if (range === '1D') return time;
                    if (range === '5D') return date + ' ' + time;
                    return date + (range === '1A' ? ' ' + d.getFullYear() : '');
                }

                // Algoritma LTTB untuk Downsampling
                function lttb(data, threshold) {
                    var n = data.length;
                    if (threshold <= 0 || n <= threshold) return data;

                    var sampled = [data[0]];
                    var a = 0;
                    var bucketSize = (n - 2) / (threshold - 2);

                    for (var i = 0; i < threshold - 2; i++) {
                        var avgStart = Math.floor((i + 1) * bucketSize) + 1;
                        var avgEnd = Math.min(Math.floor((i + 2) * bucketSize) + 1, n);
                        var avgX = 0,
                            avgY = 0,
                            avgLen = avgEnd - avgStart;
                        for (var j = avgStart; j < avgEnd; j++) {
                            avgX += data[j].x;
                            avgY += data[j].y;
                        }
                        avgX /= avgLen;
                        avgY /= avgLen;

                        var rangeStart = Math.floor(i * bucketSize) + 1;
                        var rangeEnd = Math.min(Math.floor((i + 1) * bucketSize) + 1, n);
                        var maxArea = -1,
                            maxPoint = rangeStart;
                        var ax = data[a].x,
                            ay = data[a].y;

                        for (var k = rangeStart; k < rangeEnd; k++) {
                            var area = Math.abs((ax - avgX) * (data[k].y - ay) - (ax - data[k].x) * (avgY - ay)) * 0.5;
                            if (area > maxArea) {
                                maxArea = area;
                                maxPoint = k;
                            }
                        }
                        sampled.push(data[maxPoint]);
                        a = maxPoint;
                    }
                    sampled.push(data[n - 1]);
                    return sampled;
                }

                // Konfigurasi Chart Utama
                var chartOptions = {
                    series: [],
                    chart: {
                        id: 'ptsp-chart',
                        height: 350,
                        type: 'area',
                        fontFamily: 'inherit',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        },
                        background: 'transparent',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 500,
                            dynamicAnimation: {
                                enabled: true
                            }
                        }
                    },
                    theme: {
                        mode: 'dark'
                    },
                    colors: SERIES_COLORS,
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            type: 'vertical',
                            shadeIntensity: 0,
                            opacityFrom: 0.12,
                            opacityTo: 0.0,
                            stops: [0, 100]
                        }
                    },
                    yaxis: {
                        tickAmount: 4,
                        labels: {
                            show: true,
                            style: {
                                colors: '#52525b',
                                fontSize: '11px',
                                fontWeight: 500
                            },
                            formatter: function(val) {
                                return Math.round(val) + '°';
                            }
                        }
                    },
                    xaxis: {
                        type: 'datetime',
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: '#71717a',
                                fontSize: '12px',
                                fontWeight: 500
                            },
                            datetimeUTC: false,
                            offsetY: 4
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    grid: {
                        borderColor: '#3f3f46',
                        strokeDashArray: 4,
                        xaxis: {
                            lines: {
                                show: false
                            }
                        },
                        yaxis: {
                            lines: {
                                show: true
                            }
                        },
                        padding: {
                            top: 0,
                            right: 16,
                            bottom: 0,
                            left: 8
                        }
                    },
                    legend: {
                        show: false
                    },
                    markers: {
                        size: 0,
                        strokeWidth: 2.5,
                        strokeColors: '#1c1c1e',
                        fillOpacity: 1,
                        hover: {
                            size: 6,
                            sizeOffset: 0
                        }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        x: {
                            show: false
                        },
                        custom: function(opts) {
                            var series = opts.series;
                            var dataPointIdx = opts.dataPointIndex;
                            var w = opts.w;
                            var ts = w.globals.seriesX[0][dataPointIdx];
                            var lbl = fmtLabel(ts, currentRange);
                            var seriesNames = w.globals.seriesNames;

                            var rows = '';
                            for (var si = 0; si < series.length; si++) {
                                var rawArr = rawSeriesData[si] ? rawSeriesData[si].data : [];
                                var rawVal = null;
                                if (rawArr.length > 0) {
                                    var ts0 = w.globals.seriesX[si][dataPointIdx];
                                    var best = rawArr[0],
                                        bestDiff = Math.abs(rawArr[0].x - ts0);
                                    for (var ri = 1; ri < rawArr.length; ri++) {
                                        var diff = Math.abs(rawArr[ri].x - ts0);
                                        if (diff < bestDiff) {
                                            bestDiff = diff;
                                            best = rawArr[ri];
                                        }
                                    }
                                    rawVal = best.y;
                                }
                                var color = SERIES_COLORS[si] || '#e4e4e7';
                                var unit = SERIES_UNITS[si] || '';

                                if (rawVal != null) {
                                    var displayVal = rawVal.toFixed(1);
                                    var name = seriesNames[si] || ('Seri ' + (si + 1));

                                    rows += '<div style="border-left:3px solid ' + color + ';padding-left:10px;line-height:1.3;">' +
                                        '<div style="font-size:11px;color:#71717a;font-weight:600;margin-bottom:5px;text-transform:uppercase;letter-spacing:.05em;">' + name + '</div>' +
                                        '<div style="font-weight:600;color:#e4e4e7;font-size:14px;">' + displayVal + ' <span style="font-size:11px;color:#71717a;">' + unit + '</span></div>' +
                                        '</div>';
                                }
                            }

                            return '<div style="background:#2c2c2e;border:1px solid #3f3f46;border-radius:16px;padding:14px 16px;min-width:160px;box-shadow:0 10px 40px rgba(0,0,0,0.4);">' +
                                '<div style="font-size:12px;color:#e4e4e7;font-weight:600;margin-bottom:12px;">' + lbl + '</div>' +
                                '<div style="display:flex;flex-direction:column;gap:12px;">' + rows + '</div></div>';
                        }
                    }
                };

                var apexChart = new ApexCharts(document.querySelector('#chart'), chartOptions);
                apexChart.render();

                // Generator data dummy ramalan suhu (pola harian: puncak ~jam 14:00, base 27°C)
                function generateDummyWeatherData(totalHours) {
                    totalHours = totalHours || 120; // default 5 hari x 24 jam
                    var now = new Date();
                    now.setMinutes(0, 0, 0);

                    var baseTemp = 27;
                    var amplitude = 5;
                    var data = [];

                    for (var i = 0; i < totalHours; i++) {
                        var t = new Date(now.getTime() + i * 3600 * 1000);
                        var hourOfDay = t.getHours();
                        var phase = ((hourOfDay - 14) / 24) * 2 * Math.PI;
                        var noise = (Math.random() * 1.5 - 0.75);
                        var temp = baseTemp + amplitude * Math.cos(phase) + noise;

                        data.push({
                            x: t.getTime(),
                            y: parseFloat(temp.toFixed(1))
                        });
                    }

                    return [{
                        name: 'Suhu',
                        data: data
                    }];
                }

                // Muat data dummy ke chart (nanti tinggal ganti isi fungsi ini dengan fetch ke API cuaca asli)
                function loadDummyWeatherData() {
                    try {
                        var series = generateDummyWeatherData(120);
                        rawSeriesData = series;

                        var downsampled = series.map(function(s) {
                            return {
                                name: s.name,
                                data: lttb(s.data, CONFIG.MAX_VISIBLE_POINTS)
                            };
                        });

                        apexChart.updateSeries(downsampled);
                    } catch (error) {
                        console.error("Gagal memuat data dummy cuaca:", error);
                    }
                }

                // Inisialisasi awal dan auto-refresh (simulasi update berkala)
                loadDummyWeatherData();
                setInterval(loadDummyWeatherData, CONFIG.REFRESH_INTERVAL_MS);
            </script>
        </div>
        <div class="col-span-3 col-start-5">
            <div class="flex justify-between items-center mb-3">
                <h1 class="text-2xl">Lokasi Device Lainnya</h1>
                <h1 class="text-[#979797] text-sm">Lebih Banyak →</h1>
            </div>
            @foreach($devices as $dev)
            <a href="{{ route('ramalan-cuaca', ['device_name' => $dev->device_name, 'device_id' => $dev->id]) }}">
                <div class="bg-[#171717] hover:scale-[1.02] rounded-2xl p-6 flex justify-between {{ $dev_id == $dev->id ? 'border border-[#4A4A4A]' : '' }} mb-6">
                    <div class="flex flex-col justify-between">
                        <div>
                            <p class="mb-4 text-[#979797]">{{ $dev->device_name }}</p>
                            <h1>{{ $dev->device_config->location }}</h1>
                        </div>
                        <p class="text-[#979797]">Cerah Berawan</p>
                    </div>
                    <div class="place-items-end">
                        <img src="{{ asset('images/cerah_berawan.svg') }}" alt="" class="w-[64px] mb-6">
                        <div class="flex">
                            <h1 class="text-4xl">30.2</h1>
                            <p>°C</p>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach

            <div class="p-6 bg-[#171717] rounded-2xl mt-6">
                <div class="flex items-center gap-5">
                    <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 12H17V17H12V12ZM19 3H18V1H16V3H8V1H6V3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 5V7H5V5H19ZM5 19V9H19V19H5Z"
                                fill="white" />
                        </svg>
                    </div>
                    <h3 class="text-lg text-white">Ramalan Cuaca 4 Hari Kedepan</h3>
                </div>

                <div class="grid grid-cols-4 grid-rows-1 gap-4 mt-6">
                    <div class="rounded-xl bg-[#121212] place-items-center h-[343px] flex flex-col justify-between p-4">
                        <div>
                            <h1 class="text-center">Kamis</h1>
                            <p class="text-sm text-[#979797]">28 Mei</p>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <img src="{{ asset('images/cerah_berawan.svg') }}" alt="">
                            <p class="text-xs">Cerah Berawan</p>
                        </div>
                        <div>
                            <h1 class="text-xl">28.4 °C</h1>
                        </div>
                    </div>
                    <div class="rounded-xl bg-[#121212] place-items-center h-[343px] flex flex-col justify-between p-4">
                        <div class="text-center">
                            <h1>Jumat</h1>
                            <p class="text-sm text-[#979797]">29 Mei</p>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <img src="{{ asset('images/cerah_berawan.svg') }}" alt="">
                            <p class="text-xs">Cerah Berawan</p>
                        </div>
                        <div>
                            <h1 class="text-xl">29.9 °C</h1>
                        </div>
                    </div>
                    <div class="rounded-xl bg-[#121212] place-items-center h-[343px] flex flex-col justify-between p-4">
                        <div class="text-center">
                            <h1>Sabtu</h1>
                            <p class="text-sm text-[#979797]">30 Mei</p>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <img src="{{ asset('images/cerah_berawan.svg') }}" alt="">
                            <p class="text-xs">Cerah Berawan</p>
                        </div>
                        <div>
                            <h1 class="text-xl">32.2 °C</h1>
                        </div>
                    </div>
                    <div class="rounded-xl bg-[#121212] place-items-center h-[343px] flex flex-col justify-between p-4">
                        <div class="text-center">
                            <h1>Minggu</h1>
                            <p class="text-sm text-[#979797]">31 Mei</p>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <img src="{{ asset('images/cerah_berawan.svg') }}" alt="">
                            <p class="text-xs">Cerah Berawan</p>
                        </div>
                        <div>
                            <h1 class="text-xl">33.8 °C</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-12">
        <div class="flex place-content-between items-center text-[#C4C4C4]">
            <div class="flex items-center gap-4 text-2xl">
                <img src="{{ asset('images/ecolume-logo.svg') }}" alt="" class="">
                <h1>Ecolume</h1>
            </div>
            <div class="flex items-center gap-8">
                <a href="{{ route('beranda') }}" class="hover:underline">Beranda</a>
                <a href="{{ route('dashboard', ['device_name' => $devices[0]->device_name, 'device_id' => $devices[0]->id]) }}" class="hover:underline">Dashboard</a>
                <a href="{{ route('ramalan-cuaca', ['device_name' => $devices[0]->device_name, 'device_id' => $devices[0]->id]) }}" class="hover:underline">Ramalan Cuaca</a>
                <a href="" class="hover:underline">Laporan Analitik</a>
            </div>
            <h1 class="text-xl">@Ecolume.id</h1>
        </div>
        <hr class="border-[#373737] my-6">
        <div class="flex justify-between items-center text-[#979797]">
            <h1>All Rights Reserved © 2026</h1>
            <div class="flex items-center gap-7">
                <a href="" class="hover:underline">Kebijakan Privasi</a>
                <a href="" class="hover:underline">Syarat dan Ketentuan</a>
            </div>
        </div>
    </footer>

</body>
<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const btnOpen = document.getElementById('btn-open-sidebar');
    const btnClose = document.getElementById('btn-close-sidebar');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('hidden');
    }

    btnOpen.addEventListener('click', openSidebar);
    btnClose.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);
</script>

</html>