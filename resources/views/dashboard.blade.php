<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring</title>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            /* Warna background utama */
        }
    </style>

    <link rel="icon" href="{{ asset('images/ecolume-logo.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 280px;
            background: #1c1c1e;
        }

        @media (min-width: 640px) {
            #map {
                height: 360px;
            }
        }

        @media (min-width: 1024px) {
            #map {
                height: 100%;
            }
        }

        /* Location Bar */
        .location-bar {
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);

            z-index: 1111;

            padding: 8px 12px;

            background: rgba(86, 86, 86, 0.36);

            border: 1px solid rgba(23, 23, 23);
            border-radius: 15px;

            color: #f5f5f7;
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;

            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);

            max-width: calc(100% - 24px);
        }

        @media (min-width: 640px) {
            .location-bar {
                padding: 10px 16px;
                font-size: 14px;
            }
        }

        /* Marker Navigasi */
        .nav-pulse-container {
            position: relative;
            width: 40px;
            height: 40px;
        }

        .pulse-ring {
            position: absolute;
            top: 5px;
            left: 5px;
            width: 30px;
            height: 30px;
            background-color: rgba(0, 122, 255, 0.45);
            border-radius: 9999px;
            z-index: 1;
            animation: radar-pulse 2s infinite ease-out;
        }

        .nav-arrow-svg {
            position: absolute;
            top: 5px;
            left: 5px;
            z-index: 2;
            filter: drop-shadow(0px 3px 5px rgba(0, 0, 0, 0.3));
            transform-origin: center center;
            transform: rotate(35deg);
        }

        @keyframes radar-pulse {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }

            100% {
                transform: scale(2.8);
                opacity: 0;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<aside id="sidebar" class="fixed left-0 top-0 bottom-0 h-lvh w-[400px] bg-[#171717]  z-[1114] p-11 flex flex-col justify-between  -translate-x-full transition-transform duration-300">
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
                <div class="flex flex-col">
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

<section id="overlay" class="hidden fixed top-0 bottom-0 left-0 right-0 bg-black/75 z-[1113]"></section>
<section id="overlay-timepicker" class="hidden fixed top-0 bottom-0 left-0 right-0 bg-black/75 z-[1110]"></section>

<x-mode.select :id="$id" :serial_number="$device->serial_number" />

<body class="lg:my-12 my-6 lg:mx-16 mx-3 text-white">
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

            <button
                class="relative p-2 text-zinc-400 hover:text-white rounded-full hover:bg-zinc-800 transition-colors hidden lg:block">
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
                <button type="submit" class="p-1.5 text-white hover:text-white rounded-md hover:bg-zinc-800 transition-colors hidden lg:block">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.83333 24.5C5.19167 24.5 4.64256 24.2717 4.186 23.8152C3.72944 23.3586 3.50078 22.8091 3.5 22.1667V5.83333C3.5 5.19167 3.72867 4.64256 4.186 4.186C4.64333 3.72944 5.19244 3.50078 5.83333 3.5H14V5.83333H5.83333V22.1667H14V24.5H5.83333ZM18.6667 19.8333L17.0625 18.1417L20.0375 15.1667H10.5V12.8333H20.0375L17.0625 9.85833L18.6667 8.16667L24.5 14L18.6667 19.8333Z" fill="#979797" />
                    </svg>
                </button>
            </form>
        </div>
    </nav>

    <hr class="my-5 border-[#373737]">
    <header class="w-full flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
        <div class="flex flex-col gap-1.5 min-w-0">
            <h2 class="text-lg sm:text-xl lg:text-2xl text-white tracking-wide break-words">
                Dashboard Monitoring <br class="hidden sm:block lg:hidden">{{ $device->device_name }}
            </h2>
            <p class="text-[#979797] text-sm hidden lg:block">
                Pantau performa dan efisiensi pompa air tenaga surya Anda secara real-time.
            </p>
        </div>

        <!-- Right Section: Status and Last Update -->
        <div class="flex flex-col sm:items-end gap-2 shrink-0">

            <!-- Status Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl w-fit {{ $device->online_status == 1 ? 'bg-[#00A451]/5' : 'bg-[#DC2626]/5'}}">
                <!-- Blinking/Static Green Dot -->
                <span class="w-2 h-2 rounded-full shrink-0 {{ $device->online_status == 1 ? 'bg-[#00A451]' : 'bg-[#DC2626]'}}"></span>
                <span class="{{ $device->online_status == 1 ? 'text-[#00A451]' : 'text-[#DC2626]'}} text-sm whitespace-nowrap">Pompa {{ $device->online_status == 1 ? 'Online' : 'Offline'}}</span>
            </div>

            <!-- Last Update Text -->
            <p class="text-[#979797] text-xs sm:text-sm text-left sm:text-right">
                Terakhir Update : {{ \Carbon\Carbon::parse($latest['_time'], 'UTC')
                            ->timezone('Asia/Jakarta')
                            ->locale('id')
                            ->translatedFormat('d F Y H:i:s') }}
            </p>

        </div>

    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">

        <div class="bg-[#171717] rounded-2xl p-6 flex flex-col justify-between h-full">
            <div class="flex justify-between items-center ">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                        <img src="{{ asset('images/electric4.png') }}" alt="" class="w-4">
                    </div>
                    <h3 class="text-lg text-white">Produksi Listrik</h3>
                </div>
                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-l text-xs {{ $latest['Daya'] == 0 ? 'bg-[#DC2626]/5 text-[#DC2626]' : 'bg-[#00A451]/5 text-[#00A451]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-dasharray="4 4" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                    </svg>
                    Pompa {{ $latest["Daya"] == 0 ? "Mati" : "Menyala" }}
                </div>
            </div>

            <div class="flex items-baseline gap-1 my-2 lg:my-0">
                <span class="text-4xl text-white">{{ $latest["Daya"] }}</span>
                <span class="text-sm text-zinc-400">Watt</span>
            </div>

            <div class="flex justify-between">
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-medium text-zinc-500">Arus</span>
                    <span class="text-sm font-medium text-white">{{ round($latest["Daya"]/$latest["Tegangan"], 2)}} Amp</span>
                </div>
                <div class="flex flex-col gap-1 text-right">
                    <span class="text-xs text-zinc-500">Tegangan</span>
                    <span class="text-sm text-white">{{ $latest["Tegangan"] }} Volt</span>
                </div>
            </div>
        </div>

        <div class="bg-[#171717] rounded-2xl p-6 flex flex-col justify-between h-full">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg text-white">Suhu Kontroller</h3>
                </div>
                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs {{ $latest['Suhu'] > 50 ? 'bg-[#DC2626]/5 text-[#DC2626]' : 'bg-[#00A451]/5 text-[#00A451]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z" />
                    </svg>
                    Suhu {{ $latest['Suhu'] > 50 ? "Panas" : "Normal"}}
                </div>
            </div>

            <div>
                <div class="flex items-start gap-1 my-2 lg:my-0">
                    <span class="text-4xl text-white">{{ $latest["Suhu"] }}</span>
                    <span class="text-sm text-white mt-1">°C</span>
                </div>
                <p class="text-xs text-zinc-400 mt-1">Hari ini : {{ round($avgSuhu, 2) }}°C</p>
            </div>

            <div>
                <p class="text-xs text-zinc-500">Suhu kontroller Anda berada di {{ $latest['Suhu'] > 50 ? "atas" : "bawah"}} suhu normal 50°C</p>
            </div>
        </div>

        <div class="device-mode-card" data-device-id="{{ $device->id }}">
            <x-dynamic-component
                :component="'mode.'.Str::of($device->device_config->mode)->kebab()"
                :device_config="$device->device_config"
                :tegangan="$latest['Tegangan']"
                :latest_energi="$latest['Energi']"
                :latest_volume="$latest['Volume']" />
        </div>

        <script>
            // taruh SEKALI di layout utama, bukan di dalam component yang di-loop/di-swap
            window.initSessionCard = async function(card) {
                const deviceId = card.dataset.deviceId;
                const jobCreated = card.dataset.jobCreated;
                const volumeEl = card.querySelector('.js-session-volume');
                const energiEl = card.querySelector('.js-session-energi'); // opsional, gak ada di card timer-waktu

                try {
                    const res = await fetch(`/device/${encodeURIComponent(deviceId)}/session-baseline?job_created=${encodeURIComponent(jobCreated)}`);
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);

                    const {
                        baseline
                    } = await res.json();

                    // cache baseline di elemen -> dipakai ulang tiap ada data baru dari Echo,
                    // tanpa perlu fetch session-baseline lagi
                    card.dataset.baseVolume = parseFloat(baseline.Volume ?? 0);
                    card.dataset.baseEnergi = parseFloat(baseline.Energi ?? 0);

                    window.renderSessionCard(card);
                } catch (err) {
                    console.error(`Gagal ambil baseline untuk device ${deviceId}:`, err);
                    if (volumeEl) volumeEl.textContent = 'N/A';
                    if (energiEl) energiEl.textContent = 'N/A';
                }
            };

            // render ulang pakai data-latest-* + baseline yang sudah ke-cache
            window.renderSessionCard = function(card) {
                const volumeEl = card.querySelector('.js-session-volume');
                const energiEl = card.querySelector('.js-session-energi');

                const baseVolume = parseFloat(card.dataset.baseVolume ?? 0);
                const baseEnergi = parseFloat(card.dataset.baseEnergi ?? 0);
                const latestVolume = parseFloat(card.dataset.latestVolume ?? 0);
                const latestEnergi = parseFloat(card.dataset.latestEnergi ?? 0);

                if (volumeEl) {
                    volumeEl.textContent = Math.max(latestVolume - baseVolume, 0).toLocaleString('id-ID', {
                        maximumFractionDigits: 0
                    });
                }
                if (energiEl) {
                    energiEl.textContent = Math.max(latestEnergi - baseEnergi, 0).toLocaleString('id-ID', {
                        maximumFractionDigits: 2
                    });
                }
            };

            // dipanggil dari listener Echo saat data baru masuk untuk device tertentu
            window.updateSessionCardsForDevice = function(deviceId, latest) {
                document.querySelectorAll(`.js-session-card[data-device-id="${deviceId}"]`).forEach(card => {
                    if (card.dataset.baseVolume === undefined) return; // baseline belum siap, biarin initSessionCard yg urus
                    card.dataset.latestVolume = latest.Volume ?? card.dataset.latestVolume;
                    card.dataset.latestEnergi = latest.Energi ?? card.dataset.latestEnergi;
                    window.renderSessionCard(card);
                });
            };

            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.js-session-card').forEach(window.initSessionCard);
            });
        </script>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6 relative">
        <div id="time-filter-panel"
            class="hidden absolute right-0 w-[475px] max-h-[665px] z-[1113] bg-[#171717] rounded-2xl p-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 12H17V17H12V12ZM19 3H18V1H16V3H8V1H6V3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 5V7H5V5H19ZM5 19V9H19V19H5Z" fill="white" />
                        </svg>
                    </div>
                    <h3 class="text-lg text-white">Pengaturan Filter Waktu</h3>
                </div>
                <button id="btn-close-time-filter-chart">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.6673 16.6667L3.33398 3.33334M16.6673 3.33334L3.33398 16.6667" stroke="#C2C2C2" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
            <div class="mt-6">
                <div class="flex items-center gap-2 mb-4">
                    <button id="tf-preset-5" type="button" data-preset="5" class="tf-preset-chip px-3 py-1.5 rounded-lg border border-[#3f3f46] text-xs font-medium text-zinc-400 hover:text-white hover:border-zinc-500 transition-colors">5 Hari Terakhir</button>
                    <button id="tf-preset-30" type="button" data-preset="30" class="tf-preset-chip px-3 py-1.5 rounded-lg border border-[#3f3f46] text-xs font-medium text-zinc-400 hover:text-white hover:border-zinc-500 transition-colors">30 Hari Terakhir</button>
                    <button id="tf-preset-month" type="button" data-preset="month" class="tf-preset-chip px-3 py-1.5 rounded-lg border border-[#3f3f46] text-xs font-medium text-zinc-400 hover:text-white hover:border-zinc-500 transition-colors">Bulan Ini</button>
                </div>
                <div class="bg-[#2c2c2e] border border-[#3f3f46] rounded-2xl p-5 relative">
                    <div class="flex items-center justify-between mb-4">
                        <button id="tf-prev-month" type="button" class="text-zinc-400 hover:text-white transition-colors p-1">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                        </button>
                        <div id="tf-month-label" class="text-sm font-semibold text-white"></div>
                        <button id="tf-next-month" type="button" class="text-zinc-400 hover:text-white transition-colors p-1">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 18l6-6-6-6" />
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-7 text-center text-[11px] text-zinc-500 font-medium mb-2">
                        <div>Min</div>
                        <div>Sen</div>
                        <div>Sel</div>
                        <div>Rab</div>
                        <div>Kam</div>
                        <div>Jum</div>
                        <div>Sab</div>
                    </div>
                    <div id="tf-days-grid" class="grid grid-cols-7 text-center text-sm"></div>
                </div>

                <!-- Blok resolusi: disembunyikan otomatis saat context = 'kinerja' -->
                <div id="tf-resolution-wrapper" class="mt-5">
                    <div class="text-base text-zinc-500 mb-2 px-1">Resolusi Data</div>
                    <div class="flex items-center p-0.5 rounded-lg bg-[#242424] border border-[#242424]" id="tf-resolution-group">
                        <button type="button" data-resolution="detail" class="tf-resolution-btn flex-1 px-3 py-1.5 text-xs font-medium text-white bg-[#171717] rounded-md shadow transition-colors">Detail</button>
                        <button type="button" data-resolution="harian" class="tf-resolution-btn flex-1 px-3 py-1.5 text-xs font-medium text-zinc-400 hover:text-white rounded-md transition-colors">Harian</button>
                    </div>
                </div>

                <button id="tf-apply-btn" type="button" class="w-full mt-4 py-2.5 rounded-xl border border-[#5a5a5a] text-sm text-zinc-200 hover:text-white hover:border-zinc-400 transition-colors">
                    Terapkan
                </button>
            </div>
        </div>
        <div class="bg-[#171717] z-[1111] rounded-2xl p-6 lg:col-span-2 flex flex-col h-full" id="chart-card-container">

            <div class="lg:flex block justify-between items-center gap-4 mb-8">
                <div class="flex items-center gap-3 mb-3 lg:mb-0">
                    <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M13.486 5.57839C13.5577 5.3146 13.7313 5.0901 13.9685 4.95423C14.2057 4.81835 14.4871 4.78221 14.751 4.85376L19.0355 6.02114C19.298 6.09266 19.5216 6.26503 19.6576 6.50069C19.7936 6.73634 19.8309 7.01619 19.7615 7.27926L18.6299 11.5706C18.5545 11.8286 18.3814 12.047 18.1475 12.1793C17.9135 12.3116 17.6371 12.3473 17.3772 12.2788C17.1173 12.2104 16.8944 12.0431 16.756 11.8127C16.6175 11.5823 16.5745 11.307 16.6361 11.0454L17.0995 9.28401C15.3083 10.5143 13.6457 11.922 12.1371 13.486C12.042 13.5846 11.9283 13.6633 11.8025 13.7174C11.6767 13.7716 11.5413 13.8001 11.4044 13.8014C11.2674 13.8027 11.1316 13.7767 11.0048 13.7249C10.878 13.6731 10.7627 13.5966 10.6659 13.4998L8.24998 11.0825L3.13498 16.1975C3.0398 16.296 2.92597 16.3745 2.80013 16.4285C2.67429 16.4824 2.53895 16.5108 2.40203 16.5119C2.2651 16.5131 2.12932 16.4869 2.0026 16.435C1.87589 16.3831 1.76079 16.3065 1.66401 16.2096C1.56723 16.1127 1.49071 15.9975 1.43891 15.8708C1.38712 15.744 1.36109 15.6082 1.36235 15.4713C1.3636 15.3344 1.39211 15.1991 1.44622 15.0733C1.50032 14.9475 1.57894 14.8337 1.67748 14.7386L7.52123 8.89489C7.71459 8.70177 7.9767 8.59329 8.24998 8.59329C8.52326 8.59329 8.78537 8.70177 8.97873 8.89489L11.3932 11.308C12.8794 9.8588 14.4939 8.54719 16.2167 7.38926L14.2092 6.84201C13.9457 6.76996 13.7215 6.59629 13.5859 6.35912C13.4503 6.12195 13.4144 5.84204 13.486 5.57839Z" fill="white" />
                        </svg>
                    </div>
                    <h3 class="text-lg text-white font-medium">Tren Aktual</h3>
                </div>

                <div class="flex items-center gap-3 w-full lg:w-fit">
                    <div class="relative w-full lg:w-fit" id="data-dropdown-wrapper">
                        <button id="data-dropdown-btn" type="button"
                            class="flex items-center justify-center gap-2 px-5 py-1.5 rounded-lg bg-[#171717] border border-[#242424] text-sm text-white relative z-50 lg:w-fit w-full">
                            Data
                            <svg id="data-dropdown-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                style="transition: transform .2s ease;">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div id="data-dropdown-overlay" class="hidden fixed inset-0 z-40" style="background:rgba(0,0,0,0.15);"></div>

                        <div id="data-dropdown-menu"
                            class="hidden absolute left-0 mt-2 w-64 rounded-2xl bg-[#2c2c2e] border border-[#3f3f46] p-3 z-50"
                            style="box-shadow:0 10px 40px rgba(0,0,0,0.4);">
                            <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wide px-2 pb-2">
                                Pilih Data
                            </div>
                            <div id="data-dropdown-list" class="flex flex-col gap-1 max-h-72 overflow-y-auto"></div>
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-[#3f3f46] px-2">
                                <button id="data-dropdown-cancel" type="button"
                                    class="text-xs text-zinc-500 hover:text-white transition-colors">
                                    Batal
                                </button>
                                <button id="data-dropdown-apply" type="button"
                                    class="px-4 py-1.5 text-xs font-medium text-white rounded-lg transition-colors"
                                    style="background:#f97316;">
                                    Terapkan
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center p-0.5 rounded-lg bg-[#242424] border border-[#242424]" id="range-filter-group">
                        <button type="button" data-range="1H"
                            class="range-filter-btn px-3 py-1.5 text-xs font-medium text-white bg-[#171717] rounded-md shadow">1H</button>
                        <button type="button" data-range="1M"
                            class="range-filter-btn px-3 py-1.5 text-xs font-medium text-zinc-400 hover:text-white rounded-md transition-colors">1M</button>
                        <button type="button" data-range="CUSTOM"
                            class="range-filter-btn px-3 py-1.5 text-xs font-medium text-zinc-400 hover:text-white rounded-md transition-colors">Custom</button>
                    </div>
                </div>
            </div>
            <div id="chart" class="h-max"></div>
        </div>


        <style>
            #chart .apexcharts-legend {
                margin-top: 32px;
                /* atur sesuai kebutuhan, mis. 16px, 24px, 32px */
            }
        </style>

        <script>
            // =========================================================
            // 0. SUMBER KEBENARAN: label, satuan, warna tiap data
            //    (dipakai bareng oleh chart, tooltip, dan dropdown)
            // =========================================================
            var FIELD_META = {
                'Daya': {
                    label: 'Daya',
                    unit: 'W',
                    color: '#f97316'
                },
                'Debit': {
                    label: 'Debit',
                    unit: 'l/min',
                    color: '#0ea5e9'
                },
                'Durasi_Operasional': {
                    label: 'Durasi Operasional',
                    unit: 'menit',
                    color: '#8b5cf6'
                },
                'Energi': {
                    label: 'Energi',
                    unit: 'Wh',
                    color: '#f59e0b'
                },
                'Suhu': {
                    label: 'Suhu',
                    unit: '°C',
                    color: '#f43f5e'
                },
                'Tegangan': {
                    label: 'Tegangan',
                    unit: 'V',
                    color: '#06b6d4'
                },
                'Volume': {
                    label: 'Volume',
                    unit: 'L',
                    color: '#10b981'
                }
            };
            // Urutan kanonis (dipakai untuk urutan legend & urutan checkbox dropdown)
            var ALL_FIELDS = ['Debit', 'Daya', 'Suhu', 'Tegangan', 'Energi', 'Volume', 'Durasi_Operasional'];
            var DEFAULT_FIELDS = ['Debit', 'Daya'];

            var CONFIG = {
                MAX_VISIBLE_POINTS: 500,
                REFRESH_INTERVAL_MS: 30000,
                API_ENDPOINT: '/device/line-chart/{{ $device->id }}' // GANTI DENGAN URL API ANDA
            };

            var rawSeriesData = [];
            var currentRange = '1H';
            var appliedFields = DEFAULT_FIELDS.slice(); // field yang BENAR-BENAR dipakai chart (hanya berubah saat Terapkan)
            var selectedFields = DEFAULT_FIELDS.slice(); // draft sementara di dalam dropdown (berubah saat klik checkbox)

            // Urutkan field terpilih sesuai urutan kanonis ALL_FIELDS
            function getOrderedFields(fields) {
                return ALL_FIELDS.filter(function(f) {
                    return fields.indexOf(f) !== -1;
                });
            }

            // 1. Fungsi Normalisasi (Y axis 0-100%)
            function normalizeSeries(seriesArray) {
                rawSeriesData = seriesArray;
                return seriesArray.map(function(s) {
                    var values = s.data.map(function(p) {
                        return p.y;
                    });
                    var minVal = Math.min.apply(null, values);
                    var maxVal = Math.max.apply(null, values);
                    var range = maxVal - minVal || 1;

                    return {
                        name: s.name,
                        data: s.data.map(function(p) {
                            return {
                                x: p.x,
                                y: parseFloat(((p.y - minVal) / range * 100).toFixed(2))
                            };
                        })
                    };
                });
            }

            // 2. Formatting Label Tooltip
            function fmtLabel(tsMs, range) {
                var d = new Date(tsMs);
                var pad = function(v) {
                    return v < 10 ? '0' + v : '' + v;
                };
                var time = pad(d.getHours()) + ':' + pad(d.getMinutes());
                var date = pad(d.getDate()) + ' ' + ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'][d.getMonth()];

                if (range === '1H') {
                    // Window ~1 hari: waktu saja sudah cukup jelas
                    return time;
                }
                // 1M (7 hari) & CUSTOM (250 titik, bisa lintas beberapa hari): tampilkan tanggal + waktu
                return date + ' ' + time;
            }

            // 3. Algoritma LTTB untuk Downsampling
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

            // 4. Konfigurasi Chart Utama
            var chartOptions = {
                series: [],
                chart: {
                    id: 'ptsp-chart',
                    height: 500,
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
                colors: DEFAULT_FIELDS.map(function(f) {
                    return FIELD_META[f].color;
                }), // warna awal, akan di-update dinamis
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
                    min: 0,
                    max: 100,
                    tickAmount: 4,
                    labels: {
                        show: true,
                        style: {
                            colors: '#52525b',
                            fontSize: '11px',
                            fontWeight: 500
                        },
                        formatter: function(val) {
                            return Math.round(val);
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
                    show: true,
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '14px',
                    fontFamily: 'inherit',
                    fontWeight: 400,
                    itemMargin: {
                        horizontal: 12,
                        vertical: 8
                    },
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 12,
                        offsetX: 0,
                        offsetY: 0
                    },
                    labels: {
                        colors: '#a1a1aa',
                        useSeriesColors: false
                    }
                    // reverseSeriesOrder dihapus: urutan series sekarang sudah diatur lewat
                    // getOrderedFields() / ALL_FIELDS, jadi tidak perlu hack pembalik urutan lagi.
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
                            var fieldKey = seriesNames[si];
                            var meta = FIELD_META[fieldKey] || {
                                label: fieldKey,
                                unit: '',
                                color: '#e4e4e7'
                            };

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

                            if (rawVal != null) {
                                var displayVal = meta.unit === 'l/min' ? parseFloat(rawVal).toFixed(1) : Math.round(rawVal);

                                rows += '<div style="border-left:3px solid ' + meta.color + ';padding-left:10px;line-height:1.3;">' +
                                    '<div style="font-size:11px;color:#71717a;font-weight:600;margin-bottom:5px;text-transform:uppercase;letter-spacing:.05em;">' + meta.label + '</div>' +
                                    '<div style="font-weight:600;color:#e4e4e7;font-size:14px;">' + displayVal + ' <span style="font-size:11px;color:#71717a;">' + meta.unit + '</span></div>' +
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

            // 5. Ambil data dari API sesuai field yang dipilih
            async function loadDataFromAPI() {
                try {
                    var ordered = getOrderedFields(appliedFields);
                    var url = CONFIG.API_ENDPOINT + '?fields=' + ordered.join(',') + '&range=' + currentRange;
                    if (currentRange === 'CUSTOM' && tfApplied.start && tfApplied.end) {
                        url += '&start=' + tfFormatDateParam(tfApplied.start) + '&end=' + tfFormatDateParam(tfApplied.end);
                        url += '&resolution=' + tfAppliedResolution; // 'detail' atau 'harian'
                    }
                    const response = await fetch(url);

                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                    const fetchedData = await response.json();

                    const downsampledData = fetchedData.map(function(s) {
                        return {
                            name: s.name,
                            data: lttb(s.data, CONFIG.MAX_VISIBLE_POINTS)
                        };
                    });

                    const finalSeries = normalizeSeries(downsampledData);

                    const chartColors = fetchedData.map(function(s) {
                        return (FIELD_META[s.name] && FIELD_META[s.name].color) || '#a1a1aa';
                    });

                    apexChart.updateOptions({
                        colors: chartColors,
                        series: finalSeries
                    }, true, true);
                    apexChart.updateSeries(finalSeries);

                } catch (error) {
                    console.error("Gagal mengambil data dari API InfluxDB:", error);
                }
            }

            // 6. Inisialisasi awal dan auto-refresh
            loadDataFromAPI();
            setInterval(loadDataFromAPI, CONFIG.REFRESH_INTERVAL_MS);

            // =========================================================
            // 7. LOGIKA DROPDOWN "Data"
            // =========================================================
            var ddBtn = document.getElementById('data-dropdown-btn');
            var ddMenu = document.getElementById('data-dropdown-menu');
            var ddOverlay = document.getElementById('data-dropdown-overlay');
            var ddIcon = document.getElementById('data-dropdown-icon');
            var ddList = document.getElementById('data-dropdown-list');

            function renderFieldOptions() {
                ddList.innerHTML = '';
                ALL_FIELDS.forEach(function(key) {
                    var meta = FIELD_META[key];
                    var isChecked = selectedFields.indexOf(key) !== -1;

                    var item = document.createElement('div');
                    item.className = 'flex items-center gap-3 px-2.5 py-2 rounded-lg hover:bg-[#242424] cursor-pointer transition-colors';
                    item.style.borderLeft = '3px solid ' + meta.color;

                    item.innerHTML =
                        '<span class="field-checkbox-box w-4 h-4 rounded-md border flex items-center justify-center transition-colors" ' +
                        'style="border-color:' + meta.color + ';background:' + (isChecked ? meta.color : 'transparent') + ';">' +
                        '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" ' +
                        'stroke-linecap="round" stroke-linejoin="round" style="opacity:' + (isChecked ? '1' : '0') + '">' +
                        '<path d="M20 6 9 17l-5-5"/></svg>' +
                        '</span>' +
                        '<span class="flex-1 text-[11px] font-semibold text-zinc-400 uppercase tracking-wide">' + meta.label + '</span>' +
                        '<span class="text-[11px]" style="color:#52525b;">' + meta.unit + '</span>';

                    // Klik checkbox HANYA mengubah draft (selectedFields), tidak menutup dropdown
                    item.addEventListener('click', function() {
                        var idx = selectedFields.indexOf(key);
                        if (idx === -1) {
                            selectedFields.push(key);
                        } else {
                            if (selectedFields.length === 1) return; // minimal 1 data harus aktif
                            selectedFields.splice(idx, 1);
                        }
                        renderFieldOptions();
                    });

                    ddList.appendChild(item);
                });
            }

            function openDropdown() {
                selectedFields = appliedFields.slice(); // mulai draft dari state yang sedang aktif
                renderFieldOptions();
                ddMenu.classList.remove('hidden');
                ddOverlay.classList.remove('hidden');
                ddIcon.style.transform = 'rotate(180deg)';
            }

            function closeDropdownCancel() {
                // Batal: buang perubahan draft, kembalikan ke state applied terakhir
                selectedFields = appliedFields.slice();
                ddMenu.classList.add('hidden');
                ddOverlay.classList.add('hidden');
                ddIcon.style.transform = 'rotate(0deg)';
            }

            function closeDropdownApply() {
                appliedFields = selectedFields.slice();
                ddMenu.classList.add('hidden');
                ddOverlay.classList.add('hidden');
                ddIcon.style.transform = 'rotate(0deg)';
                loadDataFromAPI(); // muat ulang chart dengan field yang baru diterapkan
            }

            renderFieldOptions();

            // Tombol "Data": toggle. Kalau lagi terbuka, klik lagi = cancel (sesuai request)
            ddBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                var isOpen = !ddMenu.classList.contains('hidden');
                if (isOpen) {
                    closeDropdownCancel();
                } else {
                    openDropdown();
                }
            });

            // Klik overlay (area luar card, di bawah card dropdown) = cancel
            ddOverlay.addEventListener('click', closeDropdownCancel);

            // Klik "Batal" di dalam card = cancel juga, tapi TANPA menutup (biar user bisa langsung pilih ulang)
            document.getElementById('data-dropdown-cancel').addEventListener('click', function() {
                selectedFields = appliedFields.slice();
                renderFieldOptions();
            });

            document.getElementById('data-dropdown-apply').addEventListener('click', closeDropdownApply);

            // =========================================================
            // 8. LOGIKA FILTER WAKTU (1H / 1M / Custom)
            // =========================================================
            var rangeButtons = document.querySelectorAll('.range-filter-btn');

            rangeButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var newRange = btn.getAttribute('data-range');
                    if (newRange === 'CUSTOM') {
                        tfOpenPanel();
                        return;
                    }
                    if (newRange === currentRange) return; // sudah aktif, tidak perlu reload
                    currentRange = newRange;

                    rangeButtons.forEach(function(b) {
                        b.classList.remove('text-white', 'bg-[#171717]', 'shadow');
                        b.classList.add('text-zinc-400');
                    });
                    btn.classList.remove('text-zinc-400');
                    btn.classList.add('text-white', 'bg-[#171717]', 'shadow');

                    loadDataFromAPI();
                });
            });
        </script>
        <script>
            // =========================================================
            // 9. DATEPICKER RANGE — "Pengaturan Filter Waktu"
            // Dipakai bersama oleh: Line Chart Tren Aktual (context: 'chart')
            //                        Card Kinerja Pompa Air (context: 'kinerja')
            // =========================================================
            var MONTH_NAMES_ID = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            var tfPanel = document.getElementById('time-filter-panel');
            var tfOverlay = document.getElementById('overlay-timepicker');
            var tfMonthLabel = document.getElementById('tf-month-label');
            var tfDaysGrid = document.getElementById('tf-days-grid');
            var tfResolutionWrapper = document.getElementById('tf-resolution-wrapper'); // TAMBAHAN
            var tfViewDate = new Date(); // bulan yang sedang ditampilkan
            var tfToday = new Date();
            tfToday.setHours(0, 0, 0, 0); // batas maksimal tanggal yang boleh dipilih = hari ini

            var tfDraft = {
                start: null,
                end: null
            }; // seleksi sementara (belum ditekan Terapkan)

            var tfApplied = {
                start: null,
                end: null
            }; // seleksi yang sudah aktif dipakai chart (dipertahankan untuk kompatibilitas loadDataFromAPI)

            // >>> TAMBAHAN: dukungan multi-context (chart vs kinerja)
            var tfContext = 'chart'; // context yang sedang aktif saat panel dibuka

            var tfAppliedByContext = {
                chart: {
                    start: null,
                    end: null,
                    resolution: 'detail'
                },
                kinerja: {
                    start: null,
                    end: null
                }
            };

            var TF_POSITION_CLASSES = {
                chart: ['right-0'],
                kinerja: ['right-6', '-translate-x-full']
            };
            var TF_ALL_POSITION_CLASSES = ['right-0', 'right-6', '-translate-x-full'];

            var tfContainers = {
                chart: document.getElementById('chart-card-container'),
                kinerja: document.getElementById('kinerja-card-container')
            };
            // <<< /TAMBAHAN

            // >>> TAMBAHAN: state resolusi data ('detail' = data mentah, 'harian' = 1 hari 1 data)
            var tfResolutionButtons = document.querySelectorAll('.tf-resolution-btn');
            var tfDraftResolution = 'detail';
            var tfAppliedResolution = 'detail';

            function tfSyncResolutionButtons() {
                tfResolutionButtons.forEach(function(b) {
                    var active = b.getAttribute('data-resolution') === tfDraftResolution;
                    b.classList.toggle('text-white', active);
                    b.classList.toggle('bg-[#171717]', active);
                    b.classList.toggle('shadow', active);
                    b.classList.toggle('text-zinc-400', !active);
                });
            }

            tfResolutionButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    tfDraftResolution = btn.getAttribute('data-resolution');
                    tfSyncResolutionButtons();
                });
            });
            // <<< /TAMBAHAN

            // >>> TAMBAHAN: preset rentang cepat
            var tfPresetChips = document.querySelectorAll('.tf-preset-chip');

            function tfComputePresetRange(preset) {
                var end = new Date(tfToday);
                var start;
                if (preset === 'month') {
                    start = new Date(tfToday.getFullYear(), tfToday.getMonth(), 1);
                } else {
                    start = new Date(tfToday);
                    start.setDate(start.getDate() - (parseInt(preset, 10) - 1));
                }
                return {
                    start: start,
                    end: end
                };
            }

            function tfUpdatePresetChips() {
                tfPresetChips.forEach(function(chip) {
                    var range = tfComputePresetRange(chip.getAttribute('data-preset'));
                    var isActive = tfSameDate(tfDraft.start, range.start) && tfSameDate(tfDraft.end, range.end);
                    if (isActive) {
                        chip.classList.add('border-blue-600', 'text-white', 'bg-[#3f3f46]');
                        chip.classList.remove('border-[#3f3f46]', 'text-zinc-400');
                    } else {
                        chip.classList.remove('border-blue-600', 'text-white', 'bg-[#3f3f46]');
                        chip.classList.add('border-[#3f3f46]', 'text-zinc-400');
                    }
                });
            }

            tfPresetChips.forEach(function(chip) {
                chip.addEventListener('click', function() {
                    var range = tfComputePresetRange(chip.getAttribute('data-preset'));
                    tfDraft.start = range.start;
                    tfDraft.end = range.end;
                    tfViewDate = new Date(range.end);
                    tfRenderCalendar();
                });
            });
            // <<< /TAMBAHAN

            function tfSameDate(a, b) {
                return a && b && a.getFullYear() === b.getFullYear() &&
                    a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
            }

            function tfFormatDateParam(d) {
                var pad = function(v) {
                    return v < 10 ? '0' + v : '' + v;
                };
                return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
            }

            // >>> TAMBAHAN: format tanggal + jam untuk dikirim ke endpoint kinerja-baseline (?start=&stop=)
            function tfFormatDateTimeParam(d, endOfDay) {
                return tfFormatDateParam(d) + (endOfDay ? ' 23:59:59' : ' 00:00:00');
            }
            // <<< /TAMBAHAN

            function tfBuildMonthCells(year, month) {
                var firstDay = new Date(year, month, 1);
                var startWeekday = firstDay.getDay();
                var daysInMonth = new Date(year, month + 1, 0).getDate();
                var daysInPrevMonth = new Date(year, month, 0).getDate();
                var cells = [];

                for (var i = startWeekday - 1; i >= 0; i--) {
                    cells.push({
                        date: new Date(year, month - 1, daysInPrevMonth - i),
                        currentMonth: false
                    });
                }
                for (var d = 1; d <= daysInMonth; d++) {
                    cells.push({
                        date: new Date(year, month, d),
                        currentMonth: true
                    });
                }
                while (cells.length < 42) {
                    var lastDate = cells[cells.length - 1].date;
                    var nextDate = new Date(lastDate);
                    nextDate.setDate(lastDate.getDate() + 1);
                    cells.push({
                        date: nextDate,
                        currentMonth: false
                    });
                }
                return cells;
            }

            function tfRenderCalendar() {
                var year = tfViewDate.getFullYear();
                var month = tfViewDate.getMonth();
                tfMonthLabel.textContent = MONTH_NAMES_ID[month] + ' ' + year;

                var cells = tfBuildMonthCells(year, month);
                tfDaysGrid.innerHTML = '';

                cells.forEach(function(cell, idx) {
                    var col = idx % 7;
                    var isStart = tfSameDate(cell.date, tfDraft.start);
                    var isEnd = tfSameDate(cell.date, tfDraft.end);
                    var inRange = tfDraft.start && tfDraft.end &&
                        cell.date > tfDraft.start && cell.date < tfDraft.end;

                    var isFuture = cell.date > tfToday; // tanggal setelah hari ini tidak boleh dipilih

                    var wrap = document.createElement('div');
                    wrap.className = 'py-1';
                    if (!isFuture && (inRange || (isStart && tfDraft.end) || (isEnd && tfDraft.start))) {
                        wrap.classList.add('bg-[#3f3f46]');
                        if (col === 0) wrap.classList.add('rounded-l-full');
                        if (col === 6) wrap.classList.add('rounded-r-full');
                    }

                    var num = document.createElement('div');
                    num.textContent = cell.date.getDate();
                    num.className = 'w-9 h-9 mx-auto flex items-center justify-center rounded-full select-none transition-colors';

                    if (isFuture) {
                        num.classList.add('text-zinc-700', 'cursor-not-allowed');
                    } else if (isStart || isEnd) {
                        num.classList.add('bg-blue-600', 'text-white', 'font-semibold', 'cursor-pointer');
                    } else if (!cell.currentMonth) {
                        num.classList.add('text-zinc-600', 'hover:bg-[#3f3f46]', 'cursor-pointer');
                    } else {
                        num.classList.add('text-zinc-200', 'hover:bg-[#3f3f46]', 'cursor-pointer');
                    }

                    if (!isFuture) {
                        num.addEventListener('click', function(e) {
                            e.stopPropagation();
                            tfHandleDayClick(cell.date);
                        });
                    }

                    wrap.appendChild(num);
                    tfDaysGrid.appendChild(wrap);
                });

                tfUpdateNavButtons();
                tfUpdatePresetChips();
            }

            function tfUpdateNavButtons() {
                var nextBtn = document.getElementById('tf-next-month');
                var isCurrentMonth = tfViewDate.getFullYear() === tfToday.getFullYear() &&
                    tfViewDate.getMonth() === tfToday.getMonth();

                if (isCurrentMonth) {
                    nextBtn.classList.add('opacity-30', 'cursor-not-allowed', 'pointer-events-none');
                } else {
                    nextBtn.classList.remove('opacity-30', 'cursor-not-allowed', 'pointer-events-none');
                }
            }

            function tfHandleDayClick(date) {
                if (!tfDraft.start || (tfDraft.start && tfDraft.end)) {
                    tfDraft.start = date;
                    tfDraft.end = null;
                } else if (date < tfDraft.start) {
                    tfDraft.end = tfDraft.start;
                    tfDraft.start = date;
                } else {
                    tfDraft.end = date;
                }
                tfRenderCalendar();
            }

            document.getElementById('tf-prev-month').addEventListener('click', function() {
                tfViewDate.setMonth(tfViewDate.getMonth() - 1);
                tfRenderCalendar();
            });

            document.getElementById('tf-next-month').addEventListener('click', function() {
                tfViewDate.setMonth(tfViewDate.getMonth() + 1);
                tfRenderCalendar();
            });

            // >>> TAMBAHAN: tfOpenPanel sekarang menerima context ('chart' | 'kinerja')
            function tfOpenPanel(context) {
                tfContext = (context === 'kinerja') ? 'kinerja' : 'chart';
                var applied = tfAppliedByContext[tfContext];

                tfDraft.start = applied.start;
                tfDraft.end = applied.end;
                tfDraftResolution = applied.resolution || 'detail';
                tfSyncResolutionButtons();

                // reposisi panel sesuai context — tanpa pindah elemen antar container
                tfPanel.classList.remove.apply(tfPanel.classList, TF_ALL_POSITION_CLASSES);
                tfPanel.classList.add.apply(tfPanel.classList, TF_POSITION_CLASSES[tfContext]);

                // card kinerja tidak punya opsi resolusi data
                tfResolutionWrapper.classList.toggle('hidden', tfContext === 'kinerja');

                tfViewDate = applied.start ? new Date(applied.start) : new Date();
                tfRenderCalendar();
                tfPanel.classList.remove('hidden');
                tfOverlay.classList.remove('hidden');
                if (tfContext === 'kinerja') {
                    tfContainers.chart.classList.remove('z-[1111]');
                }
            }

            // dipicu tombol Custom line chart maupun tombol Custom kinerja
            document.addEventListener('tf:open', function(e) {
                tfOpenPanel(e.detail && e.detail.context);
            });
            // <<< /TAMBAHAN

            function tfClosePanel() {
                var applied = tfAppliedByContext[tfContext]; // TAMBAHAN: reset draft sesuai context aktif
                tfDraft.start = applied.start;
                tfDraft.end = applied.end;
                tfDraftResolution = applied.resolution || tfDraftResolution;
                tfSyncResolutionButtons();
                tfPanel.classList.add('hidden');
                tfOverlay.classList.add('hidden');
                tfContainers.chart.classList.add('z-[1111]');
            }

            document.getElementById('btn-close-time-filter-chart').addEventListener('click', function() {
                tfClosePanel();
            });

            tfOverlay.addEventListener('click', function() {
                tfClosePanel();
            });

            document.getElementById('tf-apply-btn').addEventListener('click', function() {
                if (!tfDraft.start || !tfDraft.end) return; // minimal harus pilih 2 tanggal

                var applied = tfAppliedByContext[tfContext];
                applied.start = tfDraft.start;
                applied.end = tfDraft.end;
                if (tfContext === 'chart') applied.resolution = tfDraftResolution;

                tfPanel.classList.add('hidden');
                tfOverlay.classList.add('hidden');

                if (tfContext === 'chart') {
                    // >>> perilaku asli, dipertahankan agar loadDataFromAPI() tetap jalan seperti sebelumnya
                    tfApplied.start = applied.start;
                    tfApplied.end = applied.end;
                    tfAppliedResolution = applied.resolution;
                    currentRange = 'CUSTOM';

                    rangeButtons.forEach(function(b) {
                        b.classList.remove('text-white', 'bg-[#171717]', 'shadow');
                        b.classList.add('text-zinc-400');
                    });
                    var customBtn = document.querySelector('.range-filter-btn[data-range="CUSTOM"]');
                    customBtn.classList.remove('text-zinc-400');
                    customBtn.classList.add('text-white', 'bg-[#171717]', 'shadow');

                    loadDataFromAPI();
                } else {
                    // >>> TAMBAHAN: context kinerja — broadcast ke script card kinerja (partial terpisah)
                    document.dispatchEvent(new CustomEvent('tf:apply', {
                        detail: {
                            context: 'kinerja',
                            start: applied.start,
                            end: applied.end,
                            startParam: tfFormatDateTimeParam(applied.start, false),
                            stopParam: tfFormatDateTimeParam(applied.end, true)
                        }
                    }));
                }
            });

            // klik di luar panel = batal, kembali ke seleksi yang sudah diterapkan sebelumnya
            document.addEventListener('click', function(e) {
                if (tfPanel.classList.contains('hidden')) return; // TAMBAHAN: perbaikan, sebelumnya cek class 'translate-x-full' yang tak pernah terpasang
                if (tfPanel.contains(e.target)) return;
                if (e.target.closest('[data-range="CUSTOM"], [data-range="custom"]')) return; // TAMBAHAN: cakup trigger chart & kinerja
                tfClosePanel();
            });

            // >>> TAMBAHAN: buka panel dari tombol Custom line chart lewat event, bukan panggilan langsung
            var tfChartCustomBtn = document.querySelector('.range-filter-btn[data-range="CUSTOM"]');
            if (tfChartCustomBtn) {
                tfChartCustomBtn.addEventListener('click', function() {
                    document.dispatchEvent(new CustomEvent('tf:open', {
                        detail: {
                            context: 'chart'
                        }
                    }));
                });
            }
            // <<< /TAMBAHAN

            tfRenderCalendar();
        </script>


        <div class="bg-[#171717] rounded-2xl p-6 flex flex-col z-[1111] relative" id="kinerja-card-container">

            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.9168 12.4583C9.56922 10.8333 5.4168 6.50001 9.75013 3.25001C10.8595 2.41801 14.0835 1.62501 16.7918 4.33335C17.8242 5.36576 17.4797 6.3906 15.1668 8.12501C14.1246 8.9061 12.6751 10.1833 13.5418 11.9167C15.1668 9.57018 19.5001 5.41668 22.7501 9.75001C23.5821 10.8604 24.3751 14.0833 21.6668 16.7917C20.6344 17.8241 19.6096 17.4796 17.8751 15.1667C17.0941 14.1256 15.8168 12.675 14.0835 13.5417C16.4311 15.1667 20.5835 19.5 16.2501 22.75C15.1397 23.5831 11.9168 24.375 9.20847 21.6667C8.17605 20.6343 8.52055 19.6105 10.8335 17.875C11.8746 17.095 13.3251 15.8167 12.4585 14.0833C10.8335 16.4309 6.50013 20.5833 3.25014 16.25C2.41814 15.1407 1.62514 11.9167 4.33347 9.20835C5.36589 8.17593 6.39072 8.52043 8.12513 10.8333C8.90622 11.8755 10.1835 13.325 11.9168 12.4583Z" fill="#FFFFF0" />
                    </svg>
                </div>
                <h3 class="text-lg text-white m-0">Kinerja Pompa Air</h3>
            </div>

            <div class="relative flex justify-center items-center mb-2 w-[260px] h-[200px] mx-auto">
                <canvas id="gauge" class="absolute inset-0 pointer-events-none"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pt-6">
                    <div class="text-5xl text-white tracking-tighter" id="debit_card" data-debit="{{ $latest['Debit'] }}">{{ $latest['Debit'] }}</div>
                    <span class="text-md text-gray-400">Debit l/m</span>
                </div>
            </div>

            <p class="text-center text-[#a1a1aa] text-[15px] mb-7 leading-snug px-4">
                Pompa air sedang berjalan <span class="text-orange-500">{{ round($latest['Debit']/5) }}</span>% dari kapasitas maksimum
            </p>

            <div class="h-px bg-zinc-700 w-full mb-7 opacity-50"></div>

            <div class="flex justify-center mb-8">
                <div class="bg-[#242424] rounded-full p-1 flex" id="kinerja-filter-group">
                    <button type="button" data-range="1h" class="filter-btn bg-[#171717] text-white text-[13px] py-1.5 px-6 rounded-full border-0 cursor-pointer">1H</button>
                    <button type="button" data-range="1m" class="filter-btn text-gray-400 text-[13px] py-1.5 px-6 rounded-full border-0 bg-transparent cursor-pointer">1M</button>
                    <button type="button" data-range="custom" class="filter-btn text-gray-400 text-[13px] py-1.5 px-6 rounded-full border-0 bg-transparent cursor-pointer">Custom</button>
                </div>
            </div>

            <div class="flex justify-between items-end mb-10 px-1" id="kinerja-stats">
                <div>
                    <div class="flex items-baseline text-white">
                        <span class="text-2xl" id="stat-volume">{{ number_format($today['Volume'], 1) }}</span>
                        <span class="text-xl text-gray-400 ml-1">L</span>
                    </div>
                    <div class="text-sm text-gray-400 mt-1">Volume</div>
                </div>
                <div class="text-center">
                    <div class="flex items-baseline text-white justify-center">
                        <span class="text-2xl" id="stat-energi">{{ number_format($today['Energi']/1000, 2) }}</span>
                        <span class="text-sm text-gray-400 ml-1">kWh</span>
                    </div>
                    <div class="text-sm text-gray-400 mt-1">Energi</div>
                </div>
                <div class="text-right">
                    <div class="flex items-baseline text-white justify-end">
                        <span class="text-2xl" id="stat-jam">{{ floor($today['Durasi_Operasional']/3600) }}</span>
                        <span class="text-xl text-gray-400 ml-0.5 mr-1.5">j</span>
                        <span class="text-2xl" id="stat-menit">{{ gmdate('i', $today['Durasi_Operasional']) }}</span>
                        <span class="text-xl text-gray-400 ml-0.5">m</span>
                    </div>
                    <div class="text-sm text-gray-400 mt-1">Operasional</div>
                </div>
            </div>
            <div class="mb-5">
                <div class="h-3 w-full flex rounded overflow-hidden mb-2.5" id="pump-usage-bar">
                </div>
                <div class="flex justify-between text-[11px] text-gray-500 font-medium px-0.5">
                    <span>06:00</span>
                    <span>12:00</span>
                    <span>18:00</span>
                </div>
            </div>
            <div class="flex justify-center gap-5 text-[11px] text-gray-400">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#6a5acd]"></div>
                    <span>Debit Tinggi</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#4ea8de]"></div>
                    <span>Debit Rendah</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#3f3f46]"></div>
                    <span>Mati</span>
                </div>
            </div>
            <div id="pump-usage-tooltip" class="hidden" style="position:fixed;z-index:50;background:#2c2c2e;border:1px solid #3f3f46;border-radius:10px;padding:8px 12px;font-size:11px;box-shadow:0 10px 40px rgba(0,0,0,0.4);pointer-events:none;"></div>
        </div>
    </div>

    <script>
        // =========================================================
        // 9. CHART PENGGUNAAN POMPA — independen dari line chart
        // =========================================================
        document.addEventListener('DOMContentLoaded', function() {

            var PUMP_CONFIG = {
                FIELD: 'Debit',
                RANGE: '1H', // selalu 1 hari terakhir, statis
                FULL_THRESHOLD: 100, // Debit >= ini = Operasi Penuh
                GAP_MATI_MS: 20 * 60 * 1000, // 10 menit tanpa data = Mati
                REFRESH_INTERVAL_MS: 30000
            };

            var STATUS_COLOR = {
                penuh: '#6a5acd',
                sedang: '#4ea8de',
                mati: '#3f3f46'
            };
            var STATUS_LABEL = {
                penuh: 'Operasi Penuh',
                sedang: 'Operasi Sedang',
                mati: 'Mati'
            };

            var barEl = document.getElementById('pump-usage-bar');
            var tooltipEl = document.getElementById('pump-usage-tooltip');

            // guard: kalau elemen belum ada di halaman ini, hentikan tanpa error
            if (!barEl || !tooltipEl) {
                console.warn('Chart penggunaan pompa: elemen #pump-usage-bar / #pump-usage-tooltip tidak ditemukan di DOM.');
                return;
            }

            function pad(v) {
                return v < 10 ? '0' + v : '' + v;
            }

            function fmtTime(d) {
                return pad(d.getHours()) + ':' + pad(d.getMinutes());
            }

            function classifyValue(y) {
                return y >= PUMP_CONFIG.FULL_THRESHOLD ? 'penuh' : 'sedang';
            }

            function buildSegments(points, windowStart, windowEnd) {
                var raw = [];

                if (points.length === 0) {
                    return [{
                        start: windowStart,
                        end: windowEnd,
                        status: 'mati'
                    }];
                }

                if (points[0].x > windowStart) {
                    raw.push({
                        start: windowStart,
                        end: points[0].x,
                        status: 'mati'
                    });
                }

                for (var i = 0; i < points.length; i++) {
                    var cur = points[i];
                    var next = points[i + 1];
                    var segEnd = next ? next.x : windowEnd;
                    var gap = segEnd - cur.x;

                    if (gap > PUMP_CONFIG.GAP_MATI_MS) {
                        raw.push({
                            start: cur.x,
                            end: cur.x + PUMP_CONFIG.GAP_MATI_MS,
                            status: classifyValue(cur.y)
                        });
                        raw.push({
                            start: cur.x + PUMP_CONFIG.GAP_MATI_MS,
                            end: segEnd,
                            status: 'mati'
                        });
                    } else {
                        raw.push({
                            start: cur.x,
                            end: segEnd,
                            status: classifyValue(cur.y)
                        });
                    }
                }

                var merged = [];
                raw.forEach(function(seg) {
                    if (seg.end <= seg.start) return;
                    var last = merged[merged.length - 1];
                    if (last && last.status === seg.status) {
                        last.end = seg.end;
                    } else {
                        merged.push({
                            start: seg.start,
                            end: seg.end,
                            status: seg.status
                        });
                    }
                });
                return merged;
            }

            function renderSegments(segments, windowStart, windowEnd) {
                var totalMs = windowEnd - windowStart;
                barEl.innerHTML = '';
                segments.forEach(function(seg) {
                    var pct = ((seg.end - seg.start) / totalMs) * 100;
                    if (pct <= 0) return;

                    var div = document.createElement('div');
                    div.className = 'h-full';
                    div.style.width = pct + '%';
                    div.style.backgroundColor = STATUS_COLOR[seg.status];
                    div.style.cursor = 'pointer';
                    div.addEventListener('mousemove', function(e) {
                        showTooltip(e, seg);
                    });
                    div.addEventListener('mouseleave', hideTooltip);

                    barEl.appendChild(div);
                });
            }

            function showTooltip(e, seg) {
                tooltipEl.innerHTML =
                    '<div style="font-weight:600;color:#e4e4e7;margin-bottom:2px;">' + STATUS_LABEL[seg.status] + '</div>' +
                    '<div style="color:#a1a1aa;">' + fmtTime(new Date(seg.start)) + ' – ' + fmtTime(new Date(seg.end)) + '</div>';
                tooltipEl.style.left = (e.clientX + 12) + 'px';
                tooltipEl.style.top = (e.clientY - 36) + 'px';
                tooltipEl.classList.remove('hidden');
            }

            function hideTooltip() {
                tooltipEl.classList.add('hidden');
            }

            async function loadPumpUsageData() {
                try {
                    var url = CONFIG.API_ENDPOINT + '?fields=' + PUMP_CONFIG.FIELD + '&range=' + PUMP_CONFIG.RANGE;
                    var response = await fetch(url);
                    if (!response.ok) throw new Error('HTTP error! status: ' + response.status);
                    var fetchedData = await response.json();

                    var series = fetchedData.find(function(s) {
                        return s.name === PUMP_CONFIG.FIELD;
                    });
                    var allPoints = series ? series.data.slice().sort(function(a, b) {
                        return a.x - b.x;
                    }) : [];

                    var refDate = allPoints.length ? new Date(allPoints[allPoints.length - 1].x) : new Date();
                    var dayStart = new Date(refDate.getFullYear(), refDate.getMonth(), refDate.getDate()).getTime();

                    // window hanya 06:00 - 18:00
                    var windowStart = dayStart + 6 * 60 * 60 * 1000;
                    var windowEnd = dayStart + 18 * 60 * 60 * 1000;

                    // buang titik di luar jam 06:00-18:00
                    var points = allPoints.filter(function(p) {
                        return p.x >= windowStart && p.x <= windowEnd;
                    });

                    renderSegments(buildSegments(points, windowStart, windowEnd), windowStart, windowEnd);
                } catch (error) {
                    console.error('Gagal mengambil data penggunaan pompa:', error);
                }
            }

            loadPumpUsageData();
            setInterval(loadPumpUsageData, PUMP_CONFIG.REFRESH_INTERVAL_MS);

        });
    </script>

    <script>
        (function() {
            const canvasEl = document.getElementById('gauge');
            const ctx = canvasEl.getContext('2d');

            const value = parseFloat(document.getElementById('debit_card').dataset.debit);
            const maxCapacity = 500;

            function draw() {
                const wrap = canvasEl.parentElement;
                const dpr = window.devicePixelRatio || 1;
                const W = wrap.clientWidth || 260;
                const H = wrap.clientHeight || 200;

                canvasEl.width = W * dpr;
                canvasEl.height = H * dpr;
                canvasEl.style.width = W + 'px';
                canvasEl.style.height = H + 'px';
                ctx.scale(dpr, dpr);

                const cx = W / 2;
                const cy = H * 0.58;
                const R = Math.min(cx, cy) * 0.88;
                const lineW = R * 0.12;

                const startRad = 135 * Math.PI / 180;
                const endRad = 405 * Math.PI / 180;
                const totalSweep = endRad - startRad;

                const tickPositions = [
                    0,
                    maxCapacity * 0.40,
                    maxCapacity * 0.60,
                    maxCapacity * 0.78,
                    maxCapacity
                ];

                const BG_COLOR = '#3a2e24';
                const FG_COLOR = '#f97316';

                // Naikkan multiplier dari 1.05 → 1.6 untuk jarak visible antar segmen
                const CAP_R = lineW / 2;
                const CAP_GAP = Math.asin(CAP_R / R) * 1.6;

                function valToAngle(v) {
                    return startRad + (v / maxCapacity) * totalSweep;
                }

                function drawSegment(vFrom, vTo, color) {
                    const a0 = valToAngle(vFrom) + CAP_GAP;
                    const a1 = valToAngle(vTo) - CAP_GAP;
                    if (a1 <= a0) return;

                    ctx.beginPath();
                    ctx.arc(cx, cy, R, a0, a1);
                    ctx.strokeStyle = color;
                    ctx.lineWidth = lineW;
                    ctx.lineCap = 'round';
                    ctx.stroke();
                }

                ctx.clearRect(0, 0, W, H);

                for (let i = 0; i < tickPositions.length - 1; i++) {
                    drawSegment(tickPositions[i], tickPositions[i + 1], BG_COLOR);
                }

                if (value > 0) {
                    const clampedVal = Math.min(value, maxCapacity);
                    let endSegIdx = tickPositions.length - 2;
                    for (let i = 0; i < tickPositions.length - 1; i++) {
                        if (clampedVal <= tickPositions[i + 1]) {
                            endSegIdx = i;
                            break;
                        }
                    }

                    for (let i = 0; i < endSegIdx; i++) {
                        drawSegment(tickPositions[i], tickPositions[i + 1], FG_COLOR);
                    }

                    const a0 = valToAngle(tickPositions[endSegIdx]) + CAP_GAP;
                    const a1 = valToAngle(clampedVal) - CAP_GAP;
                    if (a1 > a0) {
                        ctx.beginPath();
                        ctx.arc(cx, cy, R, a0, a1);
                        ctx.strokeStyle = FG_COLOR;
                        ctx.lineWidth = lineW;
                        ctx.lineCap = 'round';
                        ctx.stroke();
                    }
                }
            }

            setTimeout(draw, 60);
            window.addEventListener('resize', draw);
        })();
    </script>

    <div class="grid grid-cols-1 lg:grid-cols-5 lg:grid-rows-1 gap-6 mt-6">
        <div class="lg:col-span-2 bg-[#171717] rounded-2xl p-3">
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
                            <h1>Jebres, Surakarta</h1>
                        </div>
                        <p class="text-[#979797] text-sm mt-1">Cerah Berawan • Rabu, 27 Mei 2026</p>
                    </div>
                    <img src="cerah_berawan.svg" alt="">
                </div>
                <!-- Sunrise / Sunset Timeline -->
                <div class="flex items-center gap-3 mt-3">
                    <!-- Sunrise -->
                    <div class="flex flex-col items-center gap-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M12 10V2M4.93 10.93l1.41 1.41M2 18h2M20 18h2M17.66 12.34l1.41-1.41M22 22H2M16 18l-4 4-4-4" />
                            <path d="M12 14a4 4 0 0 1 4 4H8a4 4 0 0 1 4-4Z" />
                        </svg>
                        <span class="text-[11px] text-zinc-400">17.45</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center text-white mt-5 lg:mx-4 justify-between">
                <div class="flex items-center lg:gap-14 gap-4">
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
                </div>
                <div>
                    <div class="flex">
                        <h1 class="text-4xl">30.2</h1>
                        <p>°C</p>
                    </div>
                    <p class="text-[#979797] text-sm">Terasa: 30.2 °C</p>
                </div>
            </div>
            <div class="text-[#979797] mt-6 border border-[#373737] rounded-full text-center p-1 hover:bg-[#121212]">
                <a href="{{ route('ramalan-cuaca', ['device_name' => $name, 'device_id' => $id]) }}">
                    <h1>Detail Selengkapnya ⤍</h1>
                </a>
            </div>
        </div>
        <div class="lg:col-span-3 lg:col-start-3 bg-[#171717] rounded-2xl p-3 relative">

            <!-- Location Bar -->
            <div class="location-bar p-1">
                <div class="flex items-center justify-between gap-3 sm:gap-20 bg-[#444444]/44 rounded-lg py-1 px-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                            <g clip-path="url(#clip0_301_829)">
                                <path
                                    d="M5.38333 15.68C0.842667 9.09467 0 8.41933 0 6C0 2.686 2.686 0 6 0C9.314 0 12 2.686 12 6C12 8.41933 11.1573 9.09333 6.61667 15.6773C6.54771 15.7766 6.45579 15.8578 6.34872 15.914C6.24165 15.9701 6.1226 15.9996 6.0017 15.9999C5.88079 16.0001 5.76162 15.9712 5.65431 15.9155C5.54699 15.8598 5.45472 15.779 5.38533 15.68L5.384 15.6773L5.38333 15.68ZM6 8.5C6.66304 8.5 7.29893 8.23661 7.76777 7.76777C8.23661 7.29893 8.5 6.66304 8.5 6C8.5 5.33696 8.23661 4.70107 7.76777 4.23223C7.29893 3.76339 6.66304 3.5 6 3.5C5.33696 3.5 4.70107 3.76339 4.23223 4.23223C3.76339 4.70107 3.5 5.33696 3.5 6C3.5 6.66304 3.76339 7.29893 4.23223 7.76777C4.70107 8.23661 5.33696 8.5 6 8.5Z"
                                    fill="white" />
                            </g>
                            <defs>
                                <clipPath id="clip0_301_829">
                                    <rect width="12" height="16" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                        <h1 class="truncate text-sm sm:text-base">{{ $device->device_config->location }}</h1>
                    </div>
                    <a href="" class="p-1 rounded-lg bg-white shrink-0">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 10L14 14M10.6667 14H14V10.6667M10 6L14 2M10.6667 2H14V5.33333M5.33333 14H2V10.6667M2 14L6 10M5.33333 2H2V5.33333M2 2L6 6"
                                stroke="#121212" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
            </div>

            <div id="map" class="rounded-lg h-[280px] sm:h-[360px] lg:h-full lg:min-h-[420px]"></div>

            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
                // const koordinatLokasi = [-7.5581, 110.8560];
                const koordinatLokasi = [<?php echo (float) ($device->device_config->lat ?? -7.5581); ?>, <?php echo (float) ($device->device_config->long ?? 110.8560); ?>];

                const map = L.map('map', {
                    center: koordinatLokasi,
                    zoom: 16,
                    zoomControl: false
                });

                L.control.zoom({
                    position: 'bottomright'
                }).addTo(map);

                L.tileLayer(
                    'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                        maxZoom: 20,
                    }
                ).addTo(map);

                const iconNavPulse = L.divIcon({
                    className: '',
                    html: `
                        <div class="nav-pulse-container">
                            <div class="pulse-ring"></div>
        
                            <svg
                                class="nav-arrow-svg"
                                width="30"
                                height="30"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="white"
                                stroke-width="2"
                                stroke-linejoin="round"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M12 2L2 22L12 17L22 22L12 2Z"
                                    fill="#007AFF"
                                />
                            </svg>
                        </div>
                    `,
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                });

                L.marker(koordinatLokasi, {
                        icon: iconNavPulse
                    })
                    .addTo(map)
                    .bindPopup(`
                        <div class="text-sm">
                            <div class="font-semibold">Sedang Berjalan</div>
                            <div class="text-zinc-300">Perangkat aktif.</div>
                        </div>
                    `);
            </script>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 grid-rows-1 gap-6 mt-6">
        <div class="lg:col-span-2 col-span-1 p-6 bg-[#171717] rounded-2xl">
            <div class="flex items-center gap-5">
                <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11.9167 22.75V15.6L6.87917 20.6646L5.33542 19.1208L10.4 14.0833H3.25V11.9167H10.4L5.33542 6.87917L6.87917 5.33542L11.9167 10.4V3.25H14.0833V10.4L19.1208 5.33542L20.6646 6.87917L15.6 11.9167H22.75V14.0833H15.6L20.6646 19.1208L19.1208 20.6646L14.0833 15.6V22.75H11.9167Z"
                            fill="white" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg text-white">Statistik Total Penggunaan</h3>
                    <p class="text-[#979797] text-sm">{{ \Carbon\Carbon::parse($device->created_at)->locale('id')->translatedFormat('F Y') }} - {{ \Carbon\Carbon::parse($latest['_time'])->locale('id')->translatedFormat('F Y') }}</p>
                </div>
            </div>
            <div class="flex items-baseline mx-auto w-fit my-12 gap-2">
                <h1 class="text-4xl">{{ round($latest['Volume']/1000,1) }}</h1>
                <p>m³</p>
            </div>
            <hr class="border-[#373737] my-3">
            <div class="px-2 flex items-center justify-between text-[#979797]">
                <div class="flex items-center gap-5">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8.5 14.1667C7.18497 14.1667 5.9238 13.6443 4.99393 12.7144C4.06406 11.7846 3.54167 10.5234 3.54167 9.20837C3.54167 7.89334 4.06406 6.63217 4.99393 5.7023C5.9238 4.77244 7.18497 4.25004 8.5 4.25004C9.81503 4.25004 11.0762 4.77244 12.0061 5.7023C12.9359 6.63217 13.4583 7.89334 13.4583 9.20837C13.4583 10.5234 12.9359 11.7846 12.0061 12.7144C11.0762 13.6443 9.81503 14.1667 8.5 14.1667ZM13.4796 5.23462L14.4854 4.22879C14.1667 3.86754 13.8479 3.54171 13.4867 3.23004L12.4808 4.25004C11.3829 3.35754 10.0017 2.83337 8.5 2.83337C6.80924 2.83337 5.18774 3.50502 3.99219 4.70057C2.79665 5.89611 2.125 7.51762 2.125 9.20837C2.125 10.8991 2.79665 12.5206 3.99219 13.7162C5.18774 14.9117 6.80924 15.5834 8.5 15.5834C12.0417 15.5834 14.875 12.7288 14.875 9.20837C14.875 7.70671 14.3508 6.32546 13.4796 5.23462ZM7.79167 9.91671H9.20833V5.66671H7.79167M10.625 0.708374H6.375V2.12504H10.625V0.708374Z"
                            fill="#979797" />
                    </svg>
                    <h1>Durasi operasional</h1>
                </div>
                <h1>{{ round($latest['Durasi_Operasional']/3600) }} Jam</h1>
            </div>
            <hr class="border-[#373737] my-3">
            <div class="px-2 flex items-center justify-between text-[#979797]">
                <div class="flex items-center gap-5">
                    <img src="{{ asset('images/electric4.png') }}" alt="" class="w-3">
                    <h1>Produksi energi</h1>
                </div>
                <h1>{{ round($latest['Energi']/1000, 2) }} kWh</h1>
            </div>
            <hr class="border-[#373737] my-3">
            <div class="px-2 flex items-center justify-between text-[#979797]">
                <div class="flex items-center gap-5">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.5 5H17.5V15H2.5V5ZM10 7.5C10.663 7.5 11.2989 7.76339 11.7678 8.23223C12.2366 8.70107 12.5 9.33696 12.5 10C12.5 10.663 12.2366 11.2989 11.7678 11.7678C11.2989 12.2366 10.663 12.5 10 12.5C9.33696 12.5 8.70107 12.2366 8.23223 11.7678C7.76339 11.2989 7.5 10.663 7.5 10C7.5 9.33696 7.76339 8.70107 8.23223 8.23223C8.70107 7.76339 9.33696 7.5 10 7.5ZM5.83333 6.66667C5.83333 7.10869 5.65774 7.53262 5.34518 7.84518C5.03262 8.15774 4.60869 8.33333 4.16667 8.33333V11.6667C4.60869 11.6667 5.03262 11.8423 5.34518 12.1548C5.65774 12.4674 5.83333 12.8913 5.83333 13.3333H14.1667C14.1667 12.8913 14.3423 12.4674 14.6548 12.1548C14.9674 11.8423 15.3913 11.6667 15.8333 11.6667V8.33333C15.3913 8.33333 14.9674 8.15774 14.6548 7.84518C14.3423 7.53262 14.1667 7.10869 14.1667 6.66667H5.83333Z" fill="#979797" />
                    </svg>
                    <h1>Penghematan biaya operasional</h1>
                </div>
                <h1>Rp. {{ number_format(round($latest['Energi'] * 1.5386), 0, ',', '.') }}</h1>
            </div>
            <hr class="border-[#373737] my-3">
            <div class="px-2 flex items-center justify-between text-[#979797]">
                <div class="flex items-center gap-5">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.16667 5.83337C3.72464 5.83337 3.30072 6.00897 2.98816 6.32153C2.67559 6.63409 2.5 7.05801 2.5 7.50004V12.5C2.5 12.9421 2.67559 13.366 2.98816 13.6786C3.30072 13.9911 3.72464 14.1667 4.16667 14.1667H6.66667V12.5H4.16667V7.50004H6.66667V5.83337H4.16667ZM9.16667 5.83337C8.72464 5.83337 8.30072 6.00897 7.98816 6.32153C7.6756 6.63409 7.5 7.05801 7.5 7.50004V12.5C7.5 12.9421 7.6756 13.366 7.98816 13.6786C8.30072 13.9911 8.72464 14.1667 9.16667 14.1667H10.8333C11.2754 14.1667 11.6993 13.9911 12.0118 13.6786C12.3244 13.366 12.5 12.9421 12.5 12.5V7.50004C12.5 7.05801 12.3244 6.63409 12.0118 6.32153C11.6993 6.00897 11.2754 5.83337 10.8333 5.83337H9.16667ZM9.16667 7.50004H10.8333V12.5H9.16667V7.50004ZM13.3333 8.75004V10H15.8333V11.25H14.5833C14.2518 11.25 13.9339 11.3817 13.6995 11.6162C13.465 11.8506 13.3333 12.1685 13.3333 12.5V15H17.0833V13.75H14.5833V12.5H15.8333C16.1649 12.5 16.4828 12.3683 16.7172 12.1339C16.9516 11.8995 17.0833 11.5816 17.0833 11.25V10C17.0833 9.66852 16.9516 9.35058 16.7172 9.11616C16.4828 8.88174 16.1649 8.75004 15.8333 8.75004H13.3333Z" fill="#979797" />
                    </svg>
                    <h1>Reduksi emisi</h1>
                </div>
                <h1>{{ round($latest['Energi'] * 0.00085,2) }} kg CO²</h1>
            </div>
            <hr class="border-[#373737] mt-3">
        </div>
        <div class="lg:col-span-3 lg:col-start-3 col-span-1 p-6 bg-[#171717] rounded-2xl block lg:flex ">
            <div class="lg:w-1/2 mb-4 lg:mb-0 flex flex-col justify-between">
                <!-- Header -->
                <div class="flex items-center gap-5">
                    <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 12H17V17H12V12ZM19 3H18V1H16V3H8V1H6V3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 5V7H5V5H19ZM5 19V9H19V19H5Z"
                                fill="white" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg text-white">Aktivitas Bulan Ini</h3>
                        <p class="text-[#979797] text-sm"><span id="monthlyCount">0</span> aktivitas pompa terekam bulan ini</p>
                    </div>
                </div>

                <!-- Value -->
                <div class="flex justify-center items-baseline mt-10">
                    <span id="monthlyVolume" class="text-white text-4xl font-light leading-none">
                        0
                    </span>
                    <span class="text-white text-xl ml-2 mt-2">
                        m³
                    </span>
                </div>

                <!-- Heatmap -->
                <div class="flex justify-center mt-10 mr-8">
                    <div id="heatmap-container" class="grid grid-cols-10 gap-3">
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex justify-center gap-12 mt-10">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-[#10B981]"></div>
                        <span class="text-[#A1A1AA]">
                            Digunakan
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-[#262626]"></div>
                        <span class="text-[#A1A1AA]">
                            Tidak digunakan
                        </span>
                    </div>
                </div>
            </div>

            <script>
                const deviceId = "{{ $id }}"

                async function renderHeatmap(deviceId) {
                    const container = document.getElementById('heatmap-container');
                    const volumeTag = document.getElementById('monthlyVolume');
                    const countTag = document.getElementById('monthlyCount');

                    try {
                        const res = await fetch(`/device/test/{{ $id }}`);
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);

                        const {
                            heatmap,
                            volume_delta_30d,
                            count
                        } = await res.json();

                        container.innerHTML = heatmap.map((day) => {
                            const tanggal = new Date(day.date).toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            });

                            return `
                    <div
                        class="
                            w-[26px]
                            lg:h-8
                            h-[26px]
                            lg:w-8
                            rounded-full
                            transition-all
                            duration-300
                            hover:scale-110
                            cursor-pointer
                            relative
                            group
                        "
                        style="background-color: ${day.used ? '#10B981' : '#262626'};"
                    >
                        <div
                            class="
                                absolute
                                bottom-full
                                left-1/2
                                -translate-x-1/2
                                mb-2
                                hidden
                                group-hover:block
                                z-10
                            "
                        >
                            <div
                                class="
                                    bg-black
                                    text-white
                                    text-xs
                                    rounded-lg
                                    px-3
                                    py-2
                                    whitespace-nowrap
                                "
                            >
                                ${tanggal}
                                <br>
                                ${day.used ? 'Digunakan' : 'Tidak digunakan'}
                            </div>
                        </div>
                    </div>
                `;
                        }).join('');


                        if (volumeTag) {
                            volumeTag.textContent = volume_delta_30d !== null ?
                                `${volume_delta_30d}` :
                                '—';
                        }

                        if (countTag) {
                            countTag.textContent = count !== null ?
                                `${count.toLocaleString('id-ID')}` :
                                '—';
                        }

                    } catch (err) {
                        console.error('Gagal memuat data heatmap/volume:', err);
                        container.innerHTML = '<p class="text-red-400 text-sm">Gagal memuat data</p>';
                        if (volumeTag) volumeTag.textContent = '—';
                    }
                }

                renderHeatmap(deviceId);
            </script>

            <div class="lg:w-1/2">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/chain.svg') }}" alt="" class="w-5">
                    <h3 class="text-lg text-white">Log Aktivitas Pompa</h3>
                </div>
                <p class="text-[#979797] text-sm">Laporan 4 aktivitas terakhir pompa Anda</p>

                @foreach($logs as $log)
                @continue($log['Volume_delta'] === null)
                <hr class="border-[#373737] my-3">
                <div class="flex items-center gap-5">
                    <h1 class="w-16 h-[52px] flex items-center justify-center text-white font-semibold rounded-full bg-[#262626]">
                        {{ \Carbon\Carbon::parse($log['_time'])->locale('id')->translatedFormat('j/n') }}
                    </h1>
                    <div class="w-full">
                        <h1 class="text-xl lg:text-base">{{ \Carbon\Carbon::parse($log['_time'])->locale('id')->translatedFormat('d F Y') }}</h1>
                        <div class=" items-center justify-between hidden lg:flex text-[#979797]">
                            <h1>{{ round($log['Volume_delta'], 1) }} L</h1>
                            <h1>•</h1>
                            <h1>{{ round($log['Durasi_Operasional_delta']/3600) }} jam</h1>
                            <h1>•</h1>
                            <h1>{{ round($log['Energi_delta']/1000) }} kWh</h1>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between lg:hidden mt-3 text-[#979797]">
                    <h1>{{ $log['Volume_delta'] }} L</h1>
                    <h1>•</h1>
                    <h1>{{ round($log['Durasi_Operasional_delta']/3600) }} jam</h1>
                    <h1>•</h1>
                    <h1>{{ round($log['Energi_delta']/1000) }} kWh</h1>
                </div>
                @endforeach
                <hr class="border-[#373737] mt-3">
            </div>
        </div>
    </div>

    <footer class="mt-12">
        <div class="flex flex-col md:flex-row md:place-content-between items-center md:items-center gap-6 md:gap-4 text-[#C4C4C4] text-center md:text-left">
            <div class="flex items-center gap-4 text-xl sm:text-2xl">
                <img src="{{ asset('images/ecolume-logo.svg') }}" alt="" class="w-7 h-7 sm:w-auto sm:h-auto">
                <h1>Ecolume</h1>
            </div>

            <div class="flex flex-wrap justify-center items-center gap-x-6 gap-y-2 sm:gap-x-8 text-sm sm:text-base">
                <a href="{{ route('beranda') }}" class="hover:underline">Beranda</a>
                <a href="{{ route('dashboard', ['device_name' => $devices[0]->device_name, 'device_id' => $devices[0]->id]) }}" class="hover:underline">Dashboard</a>
                <a href="{{ route('ramalan-cuaca', ['device_name' => $devices[0]->device_name, 'device_id' => $devices[0]->id]) }}" class="hover:underline">Ramalan Cuaca</a>
                <a href="" class="hover:underline">Laporan Analitik</a>
            </div>

            <h1 class="hidden md:block text-xl">@Ecolume.id</h1>
        </div>

        <hr class="border-[#373737] my-6">

        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4 text-[#979797] text-center text-sm sm:text-base">
            <h1>All Rights Reserved © 2026</h1>
            <div class="flex items-center gap-5 sm:gap-7">
                <a href="" class="hover:underline">Kebijakan Privasi</a>
                <a href="" class="hover:underline">Syarat dan Ketentuan</a>
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const state = {
                deviceId: "{{ $id }}",
                range: '1h',
                first: @json($first),
                latest: @json($latest),
            };

            function computeToday() {
                return {
                    Volume: state.latest.Volume - state.first.Volume,
                    Energi: state.latest.Energi - state.first.Energi,
                    Durasi_Operasional: state.latest.Durasi_Operasional - state.first.Durasi_Operasional,
                };
            }

            function renderStats() {
                const today = computeToday();
                document.getElementById('stat-volume').textContent = today.Volume.toFixed(1);
                document.getElementById('stat-energi').textContent = (today.Energi / 1000).toFixed(2);
                document.getElementById('stat-jam').textContent = Math.floor(today.Durasi_Operasional / 3600);
                document.getElementById('stat-menit').textContent =
                    String(Math.floor((today.Durasi_Operasional % 3600) / 60)).padStart(2, '0');
            }

            function renderGauge(debit) {
                const el = document.getElementById('debit_card');
                el.textContent = debit;
                el.dataset.debit = debit;
                // updateGaugeChart(debit);
            }

            async function refreshBaseline(range) {
                const res = await fetch(`/device/${state.deviceId}/kinerja-baseline?range=${range}`);
                const data = await res.json();
                state.first = data.first;
                state.latest = data.latest;
                state.range = range;
                renderStats();
                renderGauge(state.latest.Debit);
            }

            // --- Ganti filter ---
            const filterGroup = document.getElementById('kinerja-filter-group');

            if (!filterGroup) {
                console.error('Element #kinerja-filter-group tidak ditemukan di DOM');
                return;
            }

            filterGroup.addEventListener('click', function(e) {
                const btn = e.target.closest('.filter-btn');
                if (!btn) return;
                const range = btn.dataset.range;

                if (range === 'custom') {
                    // buka panel timepicker dengan context 'kinerja'
                    document.dispatchEvent(new CustomEvent('tf:open', {
                        detail: {
                            context: 'kinerja'
                        }
                    }));
                    return;
                }

                this.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('bg-[#171717]', 'text-white');
                    b.classList.add('text-gray-400', 'bg-transparent');
                });
                btn.classList.remove('text-gray-400', 'bg-transparent');
                btn.classList.add('bg-[#171717]', 'text-white');

                refreshBaseline(range);
            });

            // --- Terima hasil apply dari timepicker (context kinerja) ---
            async function refreshBaselineCustom(startParam, stopParam) {
                const res = await fetch(
                    `/device/${state.deviceId}/kinerja-baseline?start=${encodeURIComponent(startParam)}&stop=${encodeURIComponent(stopParam)}`
                );
                const data = await res.json();
                state.first = data.first;
                state.latest = data.latest;
                state.range = 'custom';
                renderStats();
                renderGauge(state.latest.Debit);
            }

            document.addEventListener('tf:apply', function(e) {
                if (e.detail.context !== 'kinerja') return; // sebelumnya salah cek 'chart'

                // set tombol Custom jadi aktif
                filterGroup.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('bg-[#171717]', 'text-white');
                    b.classList.add('text-gray-400', 'bg-transparent');
                });
                const customBtn = filterGroup.querySelector('.filter-btn[data-range="custom"]');
                if (customBtn) {
                    customBtn.classList.remove('text-gray-400', 'bg-transparent');
                    customBtn.classList.add('bg-[#171717]', 'text-white');
                }

                refreshBaselineCustom(e.detail.startParam, e.detail.stopParam);
            });

        });
    </script>
</body>

<script>
    document.addEventListener('pump-mode-updated', (e) => {
        const {
            device_id,
            html
        } = e.detail; // sesuaikan struktur payload di bawah
        const card = document.querySelector(`.device-mode-card[data-device-id="${device_id}"]`);
        if (card) card.innerHTML = html;
    });
</script>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const btnOpenSideBar = document.getElementById('btn-open-sidebar');
    const btnCloseSideBar = document.getElementById('btn-close-sidebar');

    const modeSelect = document.getElementById('mode-select-card');
    const btnCloseModeSelect = document.getElementById('btn-close-mode-select');

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

    function openModeSelect() {
        overlay.classList.remove('hidden');
        modeSelect.classList.remove('hidden');
    }

    function closeModeSelect() {
        overlay.classList.add('hidden');
        modeSelect.classList.add('hidden');
    }

    function overlayClick() {
        overlay.classList.add('hidden');
        modeSelect.classList.add('hidden');

        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
    }

    btnOpenSideBar.addEventListener('click', openSidebar);
    btnCloseSideBar.addEventListener('click', closeSidebar);

    document.addEventListener('click', (e) => {
        if (e.target.closest('#btn-open-mode-select')) {
            openModeSelect();
        }
    });
    btnCloseModeSelect.addEventListener('click', closeModeSelect);

    overlay.addEventListener('click', overlayClick);
</script>

</html>