<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            height: 100%;
            background: #1c1c1e;
        }

        /* Location Bar */
        .location-bar {
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);

            z-index: 1111;

            padding: 10px 16px;

            background: rgba(86, 86, 86, 0.36);
            /* backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px); */

            border: 1px solid rgba(23, 23, 23);
            border-radius: 15px;

            color: #f5f5f7;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;

            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
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
</head>

<aside id="sidebar" class="fixed left-0 top-0 bottom-0 h-lvh w-[400px] bg-[#171717]  z-[1113] p-11 flex flex-col justify-between  -translate-x-full transition-transform duration-300"">
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
            @foreach($devices as $device_data)
            <a href="{{ route('dashboard', ['device_id' => $device_data->id, 'device_name' => $device_data->device_name]) }}" class="flex gap-2 py-2 hover:text-white hover:underline">
                <span>{{ $device_data->device_name }}</span>
                <span class="w-[6px] h-[7px] rounded-full {{ $device->online_status ? 'bg-[#00A451]' : 'bg-[#DC2626]'}}"></span>
            </a>
            @endforeach
        </div>
    </div>
    <a href="" class="block my-3">
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
            <button class="p-1.5 text-white hover:text-white rounded-md hover:bg-zinc-800 transition-colors">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.83333 24.5C5.19167 24.5 4.64256 24.2717 4.186 23.8152C3.72944 23.3586 3.50078 22.8091 3.5 22.1667V5.83333C3.5 5.19167 3.72867 4.64256 4.186 4.186C4.64333 3.72944 5.19244 3.50078 5.83333 3.5H14V5.83333H5.83333V22.1667H14V24.5H5.83333ZM18.6667 19.8333L17.0625 18.1417L20.0375 15.1667H10.5V12.8333H20.0375L17.0625 9.85833L18.6667 8.16667L24.5 14L18.6667 19.8333Z" fill="#979797" />
                </svg>
            </button>
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

            <button class="p-2 text-zinc-400 hover:text-white rounded-full hover:bg-zinc-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="1.5"></circle>
                    <circle cx="12" cy="5" r="1.5"></circle>
                    <circle cx="12" cy="19" r="1.5"></circle>
                </svg>
            </button>
        </div>
    </nav>

    <hr class="my-5 border-[#373737]">
    <header class="w-full flex flex-col sm:flex-row sm:items-center justify-between gap-4">

        <!-- Left Section: Title and Description -->
        <div class="flex flex-col gap-1.5">
            <h2 class="text-2xl text-white tracking-wide">
                Dashboard Monitoring- {{ $device->device_name }}
            </h2>
            <p class="text-[#979797] text-sm">
                Pantau performa dan efisiensi pompa air tenaga surya Anda secara real-time.
            </p>
        </div>

        <!-- Right Section: Status and Last Update -->
        <div class="flex flex-col sm:items-end gap-2">

            <!-- Status Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl {{ $device->online_status == 1 ? 'bg-[#00A451]/5' : 'bg-[#DC2626]/5'}}">
                <!-- Blinking/Static Green Dot -->
                <span class="w-2 h-2 rounded-full {{ $device->online_status == 1 ? 'bg-[#00A451]' : 'bg-[#DC2626]'}}"></span>
                <span class="{{ $device->online_status == 1 ? 'text-[#00A451]' : 'text-[#DC2626]'}} text-sm">Pompa {{ $device->online_status == 1 ? 'Online' : 'Offline'}}</span>
            </div>

            <!-- Last Update Text -->
            <p class="text-[#979797] text-sm">
                Terakhir Update : {{ \Carbon\Carbon::parse($latest['_time'])->locale('id')->translatedFormat('d F Y H:i:s') }}
            </p>

        </div>

    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">

        <div class="bg-[#171717] rounded-2xl p-6 flex flex-col justify-between h-full">
            <div class="flex justify-between items-center ">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                        <!-- <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="26" viewBox="0 0 16 26">
                            <image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAaCAYAAAC+aNwHAAACp0lEQVR4AaRTXWhSYRg+R2EXk7aL/cDmILc5t2roaDC8CWosumhdddfEQsQokMX0wouY3qX9bFlBJCKxi9Gd0IVX4pVE0UUl1lSGEG7+FSiiEnNHe87xJz/PUS86vO/3/pzne77vfb/vE1H/+XUjEPfhpZv/uxEwuVwu/j0cfhZu0x/h8G48Hn9vMBjmexJ4vd6barVadWFx0bTYpufhDw8PLbtcrmgvAnp9/fpeNBotNkENKyoXi1sjI6NSxDUoJ0Il1CqVUyv3lxyqiePENTJFUZ0EdCaT2RkcHHwKYGsV+OJ8Pm+fnz93FT4hnQRUtXp6g0DUA6ZUKkng8vBEQqvVzjmdL28BSEgkEvFKpVIjklUoIQSBzWrdt9vtnwkEgvHx8Q8wNJQnBAFF01+AIIB+v3+rUChsHB8dfYV+YxV34WMsFtsE9l8TDw9jd2dmZgxItjePWltb25HJZEvSqSlVQ5emp6dXFArFK2BbBPTs7NxrNtFHRel0+jlN0+zOGRbLOqwVB4PB23CI1RG3l0ObTMaFbDbLltl6KxxBIBAwLqlUj8vl8i8c12/WVk5OCjqdbgIkTandv7f5RqlU7iHBrQ5bL2F1dXX3zNDQBC7QmEQiGWVtOpN56/F4kiwIKkomkwezcvkl+MRRcjtAkieTk5P6RlKMl/lAr9dfQdxeEkKqvgPO6xhwVO8aKWZgYGDE5/NlEXf2SJgA733B4XA8wQRRrVZjUNY2fGLriDkRLMFisZjdbvdBKpVy4sjYjreaxs1qGwQJZLKzG2azWZlI/PwELEsAIyyCBAxTfYQyXqysqPcxrevq+MfvgUajkdMUZcFVvQyAYN3It4S3A5tt++HF5eUxIMCDsY/wCORyxZ1QKPQH83hHhhxPeAQNRM+6GxjO/AUAAP//9LbfTAAAAAZJREFUAwBWc+M1LVOJFQAAAABJRU5ErkJggg==" x="0" y="0" width="16" height="26" />
                        </svg> -->
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

            <div class="flex items-baseline gap-1">
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
                <div class="flex items-start gap-1">
                    <span class="text-4xl text-white">{{ $latest["Suhu"] }}</span>
                    <span class="text-sm text-white mt-1">°C</span>
                </div>
                <p class="text-xs text-zinc-400 mt-1">Hari ini : {{ round($avgSuhu, 2) }}°C</p>
            </div>

            <div>
                <p class="text-xs text-zinc-500">Suhu kontroller Anda berada di {{ $latest['Suhu'] > 50 ? "atas" : "bawah"}} suhu normal 50°C</p>
            </div>
        </div>

        <div class="bg-[#171717] rounded-2xl p-6 flex flex-col relative overflow-hidden h-full">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-zinc-800/30 rounded-full blur-3xl"></div>

            <div class="flex justify-between items-center relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path
                                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg text-white">Mode: Timer Volume</h3>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        class="bg-white text-black hover:bg-zinc-200 transition-colors px-3 py-1.5 rounded-full text-xs font-semibold">
                        Batalkan
                    </button>
                    <button class="text-zinc-400 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="21" x2="4" y2="14"></line>
                            <line x1="4" y1="10" x2="4" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12" y2="3"></line>
                            <line x1="20" y1="21" x2="20" y2="16"></line>
                            <line x1="20" y1="12" x2="20" y2="3"></line>
                            <line x1="1" y1="14" x2="7" y2="14"></line>
                            <line x1="9" y1="8" x2="15" y2="8"></line>
                            <line x1="17" y1="16" x2="23" y2="16"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mt-3 flex items-baseline gap-1 relative z-10">
                <span class="text-4xl text-white">79</span>
                <span class="text-sm text-zinc-400">L</span>
            </div>

            <div class="mt-auto pt-2 relative z-10">
                <div class="flex justify-between items-end mb-2">
                    <span class="text-xs text-zinc-400">Limit timer volume</span>
                    <span class="text-sm text-white">45%</span>
                </div>

                <div class="w-full bg-zinc-800 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-sky-500 to-cyan-200 h-1.5 rounded-full w-[45%]"></div>
                </div>

                <div class="flex justify-between mt-3">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[11px] text-zinc-500">Volume terpompa</span>
                        <span class="text-xs font-semibold text-white">36 L</span>
                    </div>
                    <div class="flex flex-col gap-0.5 text-right">
                        <span class="text-[11px] text-zinc-500">Sisa volume</span>
                        <span class="text-xs font-semibold text-white">43 L</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

        <div class="bg-[#171717] rounded-2xl p-6 lg:col-span-2 flex flex-col h-full">

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M13.486 5.57839C13.5577 5.3146 13.7313 5.0901 13.9685 4.95423C14.2057 4.81835 14.4871 4.78221 14.751 4.85376L19.0355 6.02114C19.298 6.09266 19.5216 6.26503 19.6576 6.50069C19.7936 6.73634 19.8309 7.01619 19.7615 7.27926L18.6299 11.5706C18.5545 11.8286 18.3814 12.047 18.1475 12.1793C17.9135 12.3116 17.6371 12.3473 17.3772 12.2788C17.1173 12.2104 16.8944 12.0431 16.756 11.8127C16.6175 11.5823 16.5745 11.307 16.6361 11.0454L17.0995 9.28401C15.3083 10.5143 13.6457 11.922 12.1371 13.486C12.042 13.5846 11.9283 13.6633 11.8025 13.7174C11.6767 13.7716 11.5413 13.8001 11.4044 13.8014C11.2674 13.8027 11.1316 13.7767 11.0048 13.7249C10.878 13.6731 10.7627 13.5966 10.6659 13.4998L8.24998 11.0825L3.13498 16.1975C3.0398 16.296 2.92597 16.3745 2.80013 16.4285C2.67429 16.4824 2.53895 16.5108 2.40203 16.5119C2.2651 16.5131 2.12932 16.4869 2.0026 16.435C1.87589 16.3831 1.76079 16.3065 1.66401 16.2096C1.56723 16.1127 1.49071 15.9975 1.43891 15.8708C1.38712 15.744 1.36109 15.6082 1.36235 15.4713C1.3636 15.3344 1.39211 15.1991 1.44622 15.0733C1.50032 14.9475 1.57894 14.8337 1.67748 14.7386L7.52123 8.89489C7.71459 8.70177 7.9767 8.59329 8.24998 8.59329C8.52326 8.59329 8.78537 8.70177 8.97873 8.89489L11.3932 11.308C12.8794 9.8588 14.4939 8.54719 16.2167 7.38926L14.2092 6.84201C13.9457 6.76996 13.7215 6.59629 13.5859 6.35912C13.4503 6.12195 13.4144 5.84204 13.486 5.57839Z" fill="white" />
                        </svg>
                    </div>
                    <h3 class="text-lg text-white font-medium">Tren Aktual</h3>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        class="flex items-center gap-2 px-4 py-1.5 rounded-lg bg-zinc-800/50 border border-zinc-700 text-sm text-zinc-300 hover:text-white transition-colors">
                        Data
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>
                    <div class="flex items-center p-0.5 rounded-lg bg-zinc-800/50 border border-zinc-700">
                        <button
                            class="px-3 py-1 text-xs font-medium text-white bg-zinc-700 rounded-md shadow">1H</button>
                        <button
                            class="px-3 py-1 text-xs font-medium text-zinc-400 hover:text-white transition-colors">1M</button>
                        <button
                            class="px-3 py-1 text-xs font-medium text-zinc-400 hover:text-white transition-colors">Custom</button>
                    </div>
                </div>
            </div>

            <div id="chart" class="h-max"></div>

            <div class="flex justify-center items-center gap-6 mt-6">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#00A451]"></span>
                    <span class="text-sm text-zinc-400">Daya (Watt)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#EAB308]"></span>
                    <span class="text-sm text-zinc-400">Debit Air (l/min)</span>
                </div>
            </div>
        </div>

        <script>
            var CONFIG = {
                MAX_VISIBLE_POINTS: 500,
                REFRESH_INTERVAL_MS: 30000,
                API_ENDPOINT: '/device/line-chart/{{ $device->id }}' // GANTI DENGAN URL API ANDA
            };

            var rawSeriesData = [];
            var currentRange = '5D'; // Atur sesuai kebutuhan UI Anda
            var SERIES_COLORS = ['#EAB308', '#00A451', '#a855f7', '#22c55e', '#facc15'];
            var SERIES_UNITS = ['l/min', 'W', '', '', ''];

            // 1. Fungsi Normalisasi (Menjadikan Y axis 0-100%)
            function normalizeSeries(seriesArray) {
                rawSeriesData = seriesArray; // Simpan data asli untuk tooltip
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
                if (range === '1D') return time;
                if (range === '5D') return date + ' ' + time;
                return date + (range === '1A' ? ' ' + d.getFullYear() : '');
            }

            // 3. Algoritma LTTB untuk Downsampling (Agar chart tidak lag jika data ribuan)
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
                series: [], // Dikosongkan di awal, akan diisi oleh API
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
                                var displayVal = unit === 'l/min' ? parseFloat(rawVal).toFixed(1) : Math.round(rawVal);
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

            // 5. Fungsi Mengambil Data dari API InfluxDB
            async function loadDataFromAPI() {
                try {
                    // Anda bisa mengirim parameter range ke API melalui query string
                    const response = await fetch(`${CONFIG.API_ENDPOINT}`);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const fetchedData = await response.json();
                    // fetchedData harus array sesuai format JSON di atas

                    // Downsample data menggunakan LTTB sebelum di-render
                    const downsampledData = fetchedData.map(function(s) {
                        return {
                            name: s.name,
                            data: lttb(s.data, CONFIG.MAX_VISIBLE_POINTS)
                        };
                    });

                    // Normalisasi data (mengubah Y menjadi persentase 0-100)
                    const finalSeries = normalizeSeries(downsampledData);

                    // Update Chart
                    apexChart.updateSeries(finalSeries);
                    console.log(fetchedData);
                    console.log(downsampledData);
                    console.log(finalSeries);

                } catch (error) {
                    console.error("Gagal mengambil data dari API InfluxDB:", error);
                }
            }

            // 6. Inisialisasi awal dan auto-refresh
            loadDataFromAPI();
            setInterval(loadDataFromAPI, CONFIG.REFRESH_INTERVAL_MS);
        </script>


        <div class="bg-[#171717] rounded-2xl p-6 flex flex-col">

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
                Pompa air sedang berjalan <span class="text-orange-500">{{ round($latest['Debit']/8.33) }}</span>% dari kapasitas maksimum
            </p>

            <div class="h-px bg-zinc-700 w-full mb-7 opacity-50"></div>

            <div class="flex justify-center mb-8">
                <div class="bg-[#242427] rounded-full p-1 flex">
                    <button class="bg-[#3a3a3c] text-white text-[13px] py-1.5 px-6 rounded-full border-0 cursor-pointer">1H</button>
                    <button class="text-gray-400 text-[13px] py-1.5 px-6 rounded-full border-0 bg-transparent cursor-pointer">1M</button>
                    <button class="text-gray-400 text-[13px] py-1.5 px-6 rounded-full border-0 bg-transparent cursor-pointer">Custom</button>
                </div>
            </div>

            <div class="flex justify-between items-end mb-10 px-1">
                <div>
                    <div class="flex items-baseline text-white">
                        <span class="text-2xl">{{ $today['Volume'] }}</span>
                        <span class="text-xl text-gray-400 ml-1">L</span>
                    </div>
                    <div class="text-sm text-gray-400 mt-1">Volume</div>
                </div>
                <div class="text-center">
                    <div class="flex items-baseline text-white justify-center">
                        <span class="text-2xl">{{ round($today['Energi']/1000, 2) }}</span>
                        <span class="text-sm text-gray-400 ml-1">kWh</span>
                    </div>
                    <div class="text-sm text-gray-400 mt-1">Energi</div>
                </div>
                <div class="text-right">
                    <div class="flex items-baseline text-white justify-end">
                        <span class="text-2xl">{{ floor($today['Durasi_Operasional']/3600) }}</span>
                        <span class="text-xl text-gray-400 ml-0.5 mr-1.5">j</span>
                        <span class="text-2xl">{{ gmdate('i', $today['Durasi_Operasional']) }}</span>
                        <span class="text-xl text-gray-400 ml-0.5">m</span>
                    </div>
                    <div class="text-sm text-gray-400 mt-1">Operasional</div>
                </div>
            </div>

            <div class="mb-5">
                <div class="h-3 w-full flex rounded overflow-hidden mb-2.5">
                    <div class="h-full bg-[#4ea8de] w-[15%]"></div>
                    <div class="h-full bg-[#6a5acd] w-[20%]"></div>
                    <div class="h-full bg-[#4ea8de] w-[5%]"></div>
                    <div class="h-full bg-[#6a5acd] w-[25%]"></div>
                    <div class="h-full bg-[#4ea8de] w-[8%]"></div>
                    <div class="h-full bg-[#ff7f50] w-[12%]"></div>
                    <div class="h-full bg-[#6a5acd] w-[10%]"></div>
                    <div class="h-full bg-[#ff7f50] w-[2%]"></div>
                    <div class="h-full bg-[#6a5acd] w-[3%]"></div>
                </div>
                <div class="flex justify-between text-[11px] text-gray-500 font-medium px-0.5">
                    <span>09:00</span>
                    <span>12:30</span>
                    <span>16:30</span>
                </div>
            </div>

            <div class="flex justify-center gap-5 text-[11px] text-gray-400">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#6a5acd]"></div>
                    <span>Operasi Penuh</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#4ea8de]"></div>
                    <span>Operasi Sedang</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#ff7f50]"></div>
                    <span>Mati</span>
                </div>
            </div>

        </div>
    </div>

    <script>
        (function() {
            const canvasEl = document.getElementById('gauge');
            const ctx = canvasEl.getContext('2d');

            const value = parseFloat(document.getElementById('debit_card').dataset.debit);
            const maxCapacity = 33.3;

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

    <div class="grid grid-cols-5 grid-rows-1 gap-6 mt-6">
        <div class="col-span-2 bg-[#171717] rounded-2xl p-3">
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
            <div class="flex items-center text-white mt-5 mx-4 justify-between">
                <div class="flex items-center gap-14">
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
            <div class="text-[#979797] mt-6 border border-[#373737] rounded-full text-center p-1">
                <a href="/">
                    <h1>Detail Selengkapnya ⤍</h1>
                </a>
            </div>
        </div>
        <div class="col-span-3 col-start-3 bg-[#171717] rounded-2xl p-3 relative">

            <!-- Location Bar -->
            <div class="location-bar p-1">
                <div class="flex items-center gap-20 bg-[#444444]/44 rounded-lg py-1 px-4">
                    <div class="flex items-center gap-3">
                        <svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                        <h1>{{ $device->device_config->location }}</h1>
                    </div>
                    <a href="" class="p-1 rounded-lg bg-white">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 10L14 14M10.6667 14H14V10.6667M10 6L14 2M10.6667 2H14V5.33333M5.33333 14H2V10.6667M2 14L6 10M5.33333 2H2V5.33333M2 2L6 6"
                                stroke="#121212" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
            </div>

            <div id="map" class="rounded-lg"></div>

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

    <div class="grid grid-cols-5 grid-rows-1 gap-6 mt-6">
        <div class="col-span-2 p-6 bg-[#171717] rounded-2xl">
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
                <h1 class="text-4xl">{{ $latest['Volume']/1000 }}</h1>
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
        <div class="col-span-3 col-start-3 p-6 bg-[#171717] rounded-2xl flex">
            <div class="w-1/2">
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
                        <p class="text-[#979797] text-sm">13 aktivitas pompa terekam bulan ini</p>
                    </div>
                </div>

                <!-- Value -->
                <div class="flex justify-center items-baseline mt-10">
                    <span class="text-white text-4xl font-light leading-none">
                        10.5
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
                // 1 = digunakan
                // 0 = tidak digunakan

                const activityData = [
                    1, 1, 1, 0, 0, 1, 1, 1, 1, 0,
                    0, 1, 0, 1, 0, 0, 1, 0, 0, 1,
                    0, 0, 1, 0, 1, 0, 0, 0, 0, 0
                ];

                function renderHeatmap() {
                    const container = document.getElementById('heatmap-container');

                    container.innerHTML = activityData.map((value, index) => `
            <div
                class="
                    w-7
                    h-7
                    rounded-full
                    transition-all
                    duration-300
                    hover:scale-110
                    cursor-pointer
                    relative
                    group
                "
                style="
                    background-color: ${value ? '#10B981' : '#262626'};
                "
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
                        Hari ${index + 1}
                        <br>
                        ${value ? 'Digunakan' : 'Tidak digunakan'}
                    </div>
                </div>
            </div>
        `).join('');
                }

                renderHeatmap();
            </script>

            <div class="w-1/2">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/chain.svg') }}" alt="" class="w-5">
                    <h3 class="text-lg text-white">Log Aktivitas Pompa</h3>
                </div>
                <p class="text-[#979797] text-sm">Laporan 4 aktivitas terakhir pompa Anda</p>

                @foreach($logs as $log)
                @continue($log['Volume_delta'] === null)
                <hr class="border-[#373737] my-3">
                <div class="flex items-center gap-5">
                    <h1 class="px-3 py-4 text-white font-semibold rounded-full bg-[#262626]">{{ \Carbon\Carbon::parse($log['_time'])->locale('id')->translatedFormat('j/n') }}</h1>
                    <div class="w-full">
                        <h1 class="text-xl lg:text-base">{{ \Carbon\Carbon::parse($log['_time'])->locale('id')->translatedFormat('d F Y') }}</h1>
                        <div class=" items-center justify-between hidden lg:flex text-[#979797]">
                            <h1>{{ $log['Volume_delta'] }} L</h1>
                            <h1>•</h1>
                            <h1>{{ round($log['Durasi_Operasional_delta']/3600) }} jam</h1>
                            <h1>•</h1>
                            <h1>{{ round($log['Energi_delta']/1000) }} kWh</h1>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between lg:hidden mt-3">
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
        <div class="flex place-content-between items-center text-[#C4C4C4]">
            <div class="flex items-center gap-4 text-2xl">
                <img src="{{ asset('images/ecolume-logo.svg') }}" alt="" class="">
                <h1>Ecolume</h1>
            </div>
            <div class="flex items-center gap-8">
                <a href="{{ route('beranda') }}" class="hover:underline">Beranda</a>
                <a href="" class="hover:underline">Dashboard</a>
                <a href="" class="hover:underline">Ramalan Cuaca</a>
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