<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <link rel="icon" href="{{ asset('images/ecolume-logo.svg') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            /* Warna background utama */
        }

        #device-scroll.dragging a {
            pointer-events: none;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 470px;
            background: #1c1c1e;
        }

        /* Container untuk semua location bar - stack di pojok kiri bawah */
        .location-bar-container {
            position: absolute;
            bottom: 24px;
            left: 16px;
            z-index: 1000;

            display: flex;
            flex-direction: column;
            gap: 10px;

            align-items: flex-start;
        }

        /* Tiap item location bar */
        .location-bar {
            padding: 10px 16px;

            background: rgba(86, 86, 86, 0.36);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);

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
                @foreach($devices as $device)
                <a href="{{ route('dashboard', ['device_id' => $device->id, 'device_name' => $device->device_name]) }}" class="flex gap-2 py-2 hover:text-white hover:underline">
                    <span>{{ $device->device_name }}</span>
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

<section id="overlay" class="hidden fixed top-0 bottom-0 left-0 right-0 bg-black/30 z-[1111]"></section>

<div id="addDevice-Card" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[#171717] rounded-2xl shadow-3xl p-6 w-full max-w-md z-[1112] text-zinc-400">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M4.93045 4.92901C5.11796 4.74137 5.37233 4.6359 5.6376 4.6358C5.90287 4.63571 6.15731 4.741 6.34495 4.92851C6.53259 5.11601 6.63806 5.37038 6.63816 5.63565C6.63825 5.90092 6.53296 6.15537 6.34545 6.34301C4.84538 7.84327 4.00266 9.87795 4.00266 11.9995C4.00266 14.1211 4.84538 16.1557 6.34545 17.656C6.53309 17.8435 6.63856 18.0979 6.63866 18.3632C6.63875 18.6284 6.53346 18.8829 6.34595 19.0705C6.15844 19.2581 5.90408 19.3636 5.63881 19.3637C5.37354 19.3638 5.11909 19.2585 4.93145 19.071C1.02545 15.166 1.02545 8.83401 4.93145 4.92901M17.6595 4.92901C17.847 4.74153 18.1013 4.63622 18.3665 4.63622C18.6316 4.63622 18.8859 4.74153 19.0735 4.92901C22.9795 8.83401 22.9795 15.166 19.0735 19.071C18.8858 19.2585 18.6314 19.3638 18.3661 19.3637C18.1008 19.3636 17.8465 19.2581 17.659 19.0705C17.4714 18.8829 17.3662 18.6284 17.3663 18.3632C17.3663 18.0979 17.4718 17.8435 17.6595 17.656C19.1595 16.1557 20.0022 14.1211 20.0022 11.9995C20.0022 9.87795 19.1595 7.84327 17.6595 6.34301C17.472 6.15548 17.3667 5.90117 17.3667 5.63601C17.3667 5.37084 17.472 5.11653 17.6595 4.92901ZM7.75945 7.75701C7.94793 7.57471 8.20046 7.47373 8.46266 7.47582C8.72486 7.47792 8.97575 7.58291 9.16129 7.76819C9.34683 7.95346 9.45218 8.20421 9.45464 8.4664C9.4571 8.7286 9.35648 8.98128 9.17445 9.17001C8.803 9.54144 8.50834 9.98241 8.30731 10.4677C8.10628 10.953 8.00281 11.4732 8.00281 11.9985C8.00281 12.5238 8.10628 13.044 8.30731 13.5293C8.50834 14.0146 8.803 14.4556 9.17445 14.827C9.35661 15.0156 9.45741 15.2682 9.45513 15.5304C9.45285 15.7926 9.34768 16.0434 9.16227 16.2288C8.97686 16.4142 8.72605 16.5194 8.46385 16.5217C8.20166 16.524 7.94906 16.4232 7.76045 16.241C6.63532 15.1158 6.00323 13.5897 6.00323 11.9985C6.00323 10.4073 6.63532 8.88121 7.76045 7.75601M14.8315 7.75601C15.019 7.56853 15.2733 7.46322 15.5385 7.46322C15.8036 7.46322 16.0579 7.56853 16.2455 7.75601C17.3706 8.88121 18.0027 10.4073 18.0027 11.9985C18.0027 13.5897 17.3706 15.1158 16.2455 16.241C16.0569 16.4232 15.8042 16.524 15.5421 16.5217C15.2799 16.5194 15.029 16.4142 14.8436 16.2288C14.6582 16.0434 14.5531 15.7926 14.5508 15.5304C14.5485 15.2682 14.6493 15.0156 14.8315 14.827C15.2029 14.4556 15.4976 14.0146 15.6986 13.5293C15.8996 13.044 16.0031 12.5238 16.0031 11.9985C16.0031 11.4732 15.8996 10.953 15.6986 10.4677C15.4976 9.98241 15.2029 9.54144 14.8315 9.17001C14.644 8.98248 14.5387 8.72817 14.5387 8.46301C14.5387 8.19784 14.644 7.94353 14.8315 7.75601ZM12.0005 10.5C12.3983 10.5 12.7798 10.658 13.0611 10.9393C13.3424 11.2206 13.5005 11.6022 13.5005 12C13.5005 12.3978 13.3424 12.7794 13.0611 13.0607C12.7798 13.342 12.3983 13.5 12.0005 13.5C11.6026 13.5 11.2211 13.342 10.9398 13.0607C10.6585 12.7794 10.5005 12.3978 10.5005 12C10.5005 11.6022 10.6585 11.2206 10.9398 10.9393C11.2211 10.658 11.6026 10.5 12.0005 10.5Z"
                        fill="white" />
                </svg>
            </div>
            <h3 class="text-lg text-white">Penambahan Device</h3>
        </div>
        <button id="btn-close-addDevice" class="">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.6673 16.6667L3.33398 3.33334M16.6673 3.33334L3.33398 16.6667" stroke="#C2C2C2" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
    </div>
    <h1 class="text-white mt-5">Masukkan Device ID</h1>
    <p class="text-sm mb-5">Masukkan device id yang ada pada gerobak</p>

    <form action="" method="POST">
        @csrf

        <div class="mb-5">
            <input
                type="text"
                name="device_id"
                placeholder="Contoh: PTSP-2026-XXXX"
                class="w-full rounded-xl border border-zinc-700 bg-[#262626] px-4 py-3 text-white placeholder:text-zinc-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition"
                required>
        </div>

        <a href=""
            class="block text-center w-full text-sm rounded-full border border-zinc-700 py-1 text-[#979797] hover:bg-zinc-800 transition">
            Tambahkan
        </a>
    </form>
</div>

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
            <h1 class="text-2xl tracking-wide">Beranda</h1>
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

    <div class="grid grid-cols-3 grid-rows-1 gap-6">
        <div class="bg-[#171717] rounded-2xl p-6 text-[#979797]">
            <p>• Profil akun</p>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex gap-2 items-baseline">
                        <h1 class="text-3xl text-white">{{ auth()->user()->name }}</h1>
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.8675 1.25751L13.7425 3.13251L12.3131 4.56251L10.4381 2.68751L11.8675 1.25751ZM5 10H6.875L11.4294 5.44563L9.55438 3.57063L5 8.12501V10Z"
                                fill="#979797" />
                            <path
                                d="M11.875 11.875H5.09875C5.0825 11.875 5.06563 11.8813 5.04938 11.8813C5.02875 11.8813 5.00813 11.8756 4.98688 11.875H3.125V3.125H7.40438L8.65438 1.875H3.125C2.43562 1.875 1.875 2.435 1.875 3.125V11.875C1.875 12.565 2.43562 13.125 3.125 13.125H11.875C12.2065 13.125 12.5245 12.9933 12.7589 12.7589C12.9933 12.5245 13.125 12.2065 13.125 11.875V6.4575L11.875 7.7075V11.875Z"
                                fill="#979797" />
                        </svg>
                    </div>
                    <p>{{ auth()->user()->email }}</p>
                </div>
                <div class="rounded-full bg-[#262626] border-[#323232] border p-2 my-3">
                    <!-- Border -->
                    <div class="rounded-full overflow-hidden">
                        <!-- Default Avatar -->
                        <img src="{{ asset('images/pp.png') }}" alt="Profile Picture" class="object-cover">
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-4">
                <img src="{{ asset('images/chain.svg') }}" alt="" class="w-5">
                </svg>
                <div>
                    <h3 class="text-lg text-white">Log Aktivitas Pompa</h3>
                </div>
            </div>
            <p class="text-[#979797] text-sm">Laporan 8 aktivitas terakhir pompa Anda</p>
            @foreach($deviceLogs as $log)
            <hr class="border-[#373737] my-3">
            <a href="{{ route('dashboard', ['device_id' => $log['Device_Id'], 'device_name' => $logData[$log['Device_Id']]]) }}">
                <div class="flex items-center gap-5 group">
                    <h1 class="px-3 py-4 text-white font-semibold rounded-full bg-[#262626]">{{ \Carbon\Carbon::parse($log['_time'])->locale('id')->translatedFormat('j/n') }}</h1>
                    <div class="w-full">
                        <h1 class="text-xl lg:text-base text-white group-hover:underline">{{ $logData[$log['Device_Id']] }}</h1>
                        <div class=" items-center justify-between hidden lg:flex text-[#979797]">
                            <h1>{{ $log['Volume_delta'] }} L</h1>
                            <h1>•</h1>
                            <h1>{{ round($log['Durasi_Operasional_delta']/60) }} menit</h1>
                            <h1>•</h1>
                            <h1>{{ round($log['Energi_delta']/1000, 2) }} kWh</h1>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="col-span-2">
            <div class="bg-[#171717] rounded-2xl p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4.93045 4.92901C5.11796 4.74137 5.37233 4.6359 5.6376 4.6358C5.90287 4.63571 6.15731 4.741 6.34495 4.92851C6.53259 5.11601 6.63806 5.37038 6.63816 5.63565C6.63825 5.90092 6.53296 6.15537 6.34545 6.34301C4.84538 7.84327 4.00266 9.87795 4.00266 11.9995C4.00266 14.1211 4.84538 16.1557 6.34545 17.656C6.53309 17.8435 6.63856 18.0979 6.63866 18.3632C6.63875 18.6284 6.53346 18.8829 6.34595 19.0705C6.15844 19.2581 5.90408 19.3636 5.63881 19.3637C5.37354 19.3638 5.11909 19.2585 4.93145 19.071C1.02545 15.166 1.02545 8.83401 4.93145 4.92901M17.6595 4.92901C17.847 4.74153 18.1013 4.63622 18.3665 4.63622C18.6316 4.63622 18.8859 4.74153 19.0735 4.92901C22.9795 8.83401 22.9795 15.166 19.0735 19.071C18.8858 19.2585 18.6314 19.3638 18.3661 19.3637C18.1008 19.3636 17.8465 19.2581 17.659 19.0705C17.4714 18.8829 17.3662 18.6284 17.3663 18.3632C17.3663 18.0979 17.4718 17.8435 17.6595 17.656C19.1595 16.1557 20.0022 14.1211 20.0022 11.9995C20.0022 9.87795 19.1595 7.84327 17.6595 6.34301C17.472 6.15548 17.3667 5.90117 17.3667 5.63601C17.3667 5.37084 17.472 5.11653 17.6595 4.92901ZM7.75945 7.75701C7.94793 7.57471 8.20046 7.47373 8.46266 7.47582C8.72486 7.47792 8.97575 7.58291 9.16129 7.76819C9.34683 7.95346 9.45218 8.20421 9.45464 8.4664C9.4571 8.7286 9.35648 8.98128 9.17445 9.17001C8.803 9.54144 8.50834 9.98241 8.30731 10.4677C8.10628 10.953 8.00281 11.4732 8.00281 11.9985C8.00281 12.5238 8.10628 13.044 8.30731 13.5293C8.50834 14.0146 8.803 14.4556 9.17445 14.827C9.35661 15.0156 9.45741 15.2682 9.45513 15.5304C9.45285 15.7926 9.34768 16.0434 9.16227 16.2288C8.97686 16.4142 8.72605 16.5194 8.46385 16.5217C8.20166 16.524 7.94906 16.4232 7.76045 16.241C6.63532 15.1158 6.00323 13.5897 6.00323 11.9985C6.00323 10.4073 6.63532 8.88121 7.76045 7.75601M14.8315 7.75601C15.019 7.56853 15.2733 7.46322 15.5385 7.46322C15.8036 7.46322 16.0579 7.56853 16.2455 7.75601C17.3706 8.88121 18.0027 10.4073 18.0027 11.9985C18.0027 13.5897 17.3706 15.1158 16.2455 16.241C16.0569 16.4232 15.8042 16.524 15.5421 16.5217C15.2799 16.5194 15.029 16.4142 14.8436 16.2288C14.6582 16.0434 14.5531 15.7926 14.5508 15.5304C14.5485 15.2682 14.6493 15.0156 14.8315 14.827C15.2029 14.4556 15.4976 14.0146 15.6986 13.5293C15.8996 13.044 16.0031 12.5238 16.0031 11.9985C16.0031 11.4732 15.8996 10.953 15.6986 10.4677C15.4976 9.98241 15.2029 9.54144 14.8315 9.17001C14.644 8.98248 14.5387 8.72817 14.5387 8.46301C14.5387 8.19784 14.644 7.94353 14.8315 7.75601ZM12.0005 10.5C12.3983 10.5 12.7798 10.658 13.0611 10.9393C13.3424 11.2206 13.5005 11.6022 13.5005 12C13.5005 12.3978 13.3424 12.7794 13.0611 13.0607C12.7798 13.342 12.3983 13.5 12.0005 13.5C11.6026 13.5 11.2211 13.342 10.9398 13.0607C10.6585 12.7794 10.5005 12.3978 10.5005 12C10.5005 11.6022 10.6585 11.2206 10.9398 10.9393C11.2211 10.658 11.6026 10.5 12.0005 10.5Z"
                                    fill="white" />
                            </svg>
                        </div>
                        <h3 class="text-lg text-white">Daftar Perangkat Anda</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="btn-open-addDevice">
                            <div class="p-1 bg-white rounded-full">
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15.0413 10.2901H10.2913V15.0401H8.70801V10.2901H3.95801V8.70676H8.70801V3.95676H10.2913V8.70676H15.0413V10.2901Z"
                                        fill="#121212" />
                                </svg>
                            </div>
                        </button>
                        <div class="py-1 px-3 bg-white rounded-full text-black">
                            <h1 class="text-sm">Lebih Banyak ⟶</h1>
                        </div>
                    </div>
                </div>

                <!-- Scroll Area -->
                <div id="device-scroll" class="overflow-x-auto pb-2 active:cursor-grabbing mt-4"
                    style="-ms-overflow-style:none; scrollbar-width:none;">
                    <div class="flex gap-6 w-max">
                        @foreach($devices as $device)
                        <a href="{{ route('dashboard', ['device_id' => $device->id, 'device_name' => $device->device_name]) }}">
                            <div class="w-[400px] shrink-0 rounded-2xl bg-[#141414] p-5 group transform transition-all duration-200 ease-out hover:scale-[1.02]">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-xl text-white font-light">
                                                {{ $device->device_name }}
                                            </h3>
                                            <i class="fa-solid fa-up-right-from-square text-xs text-[#979797]"></i>
                                        </div>
                                        <p class="text-xs text-[#979797]">
                                            {{ $device->serial_number }}
                                        </p>
                                    </div>
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl {{ $device->online_status ? 'bg-[#00A451]/5' : 'bg-[#DC2626]/5' }}">
                                        <!-- Blinking/Static Green Dot -->
                                        <span class="w-2 h-2 rounded-full {{ $device->online_status ? 'bg-[#00A451]' : 'bg-[#DC2626]' }}"></span>
                                        <span class="{{ $device->online_status ? 'text-[#00A451]' : 'text-[#DC2626]' }} text-sm"> {{ $device->online_status ? 'Online' : 'Offline' }}</span>
                                    </div>
                                </div>
                                <div class="mt-8 space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-[#979797]">Status Operasional</span>
                                        <span class="text-white">{{ $deviceData[$device->id]["Daya"] > 0 ? "Menyala" : "Mati" }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-[#979797]">Mode</span>
                                        <span class="text-white">{{ $device->device_config->mode }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-[#979797]">Debit</span>
                                        <span class="text-white">{{ $deviceData[$device->id]["Debit"] }} l/min</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-[#979797]">Terkahir Update</span>
                                        <span class="text-white">{{ $deviceData[$device->id]["Time"] }}</span>
                                    </div>
                                </div>
                                <button
                                    class="mt-6 w-full text-sm rounded-full border border-zinc-700 py-1 text-[#979797] group-hover:bg-zinc-800 transition">
                                    Detail Selengkapnya ⟶
                                </button>
                            </div>
                        </a>
                        @endforeach
                        <button
                            class="w-[400px] h-[273px] shrink-0 rounded-2xl bg-[#232323] flex flex-col items-center justify-center hover:bg-[#2a2a2a] transform transition-all duration-200 ease-out hover:scale-[1.02]">
                            <div class="w-14 h-14 rounded-full bg-zinc-700 flex items-center justify-center">
                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M23.75 16.2476H16.25V23.7476H13.75V16.2476H6.25V13.7476H13.75V6.24756H16.25V13.7476H23.75V16.2476Z"
                                        fill="white" />
                                </svg>
                            </div>
                            <span class="mt-5 text-zinc-400">
                                Tambahkan Perangkat
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="bg-[#171717] rounded-2xl p-3 mt-6 relative">
                <div class="location-bar-container">
                    @foreach($devices as $device)
                    <div class="location-bar">
                        <a href="{{ route('dashboard', ['device_id' => $device->id, 'device_name' => $device->device_name]) }}">
                            <div class="flex items-center justify-between bg-[#444444]/44 rounded-lg w-60 hover:scale-[1.01] transition-transform">
                                <div class="font-normal">
                                    <h1>{{$device->device_name}}</h1>
                                    <p class="text-[#C5C5C5] text-xs">{{$device->device_config->location}}</p>
                                </div>
                                <a href="" class="p-1 rounded-lg bg-white">
                                    <svg width="20" height="20" viewBox="0 0 12 12" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_332_1698)">
                                            <path
                                                d="M6 4C4.895 4 4 4.895 4 6C4 7.105 4.895 8 6 8C7.105 8 8 7.105 8 6C8 4.895 7.105 4 6 4ZM10.47 5.5C10.3567 4.48606 9.90208 3.54077 9.18065 2.81935C8.45923 2.09792 7.51394 1.64326 6.5 1.53V0.5H5.5V1.53C4.48606 1.64326 3.54077 2.09792 2.81935 2.81935C2.09792 3.54077 1.64326 4.48606 1.53 5.5H0.5V6.5H1.53C1.64326 7.51394 2.09792 8.45923 2.81935 9.18065C3.54077 9.90208 4.48606 10.3567 5.5 10.47V11.5H6.5V10.47C7.51394 10.3567 8.45923 9.90208 9.18065 9.18065C9.90208 8.45923 10.3567 7.51394 10.47 6.5H11.5V5.5H10.47ZM6 9.5C4.065 9.5 2.5 7.935 2.5 6C2.5 4.065 4.065 2.5 6 2.5C7.935 2.5 9.5 4.065 9.5 6C9.5 7.935 7.935 9.5 6 9.5Z"
                                                fill="#121212" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_332_1698">
                                                <rect width="12" height="12" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </a>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>

                <div id="map" class="rounded-lg"></div>

                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script>
                    const map = L.map('map', {
                        center: [<?php echo (float) ($devices[0]->device_config->lat ?? -7.5581); ?>, <?php echo (float) ($devices[0]->device_config->long ?? 110.8560); ?>],
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

                    function buatIconNavPulse(warna = '#007AFF') {
                        return L.divIcon({
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
                                        <path d="M12 2L2 22L12 17L22 22L12 2Z" fill="${warna}" />
                                    </svg>
                                </div>
                            `,
                            iconSize: [40, 40],
                            iconAnchor: [20, 20]
                        });
                    }

                    const markerGroup = [];

                    <?php foreach ($devices as $device): ?>
                            (function() {
                                const lat = <?php echo (float) $device->device_config->lat; ?>;
                                const lng = <?php echo (float) $device->device_config->long; ?>;
                                const nama = <?php echo json_encode($device->device_name); ?>;

                                const marker = L.marker([lat, lng], {
                                        icon: buatIconNavPulse()
                                    })
                                    .addTo(map)
                                    .bindPopup(`
                                        <div class="text-sm font-semibold">${nama}</div>
                                    `);

                                markerGroup.push(marker);
                            })();
                    <?php endforeach; ?>

                    if (markerGroup.length > 1) {
                        const group = new L.featureGroup(markerGroup);
                        map.fitBounds(group.getBounds().pad(0.2));
                    }
                </script>
            </div>

        </div>
    </div>

    <footer class="mt-12">
        <div class="flex place-content-between items-center text-[#C4C4C4]">
            <div class="flex items-center gap-4 text-2xl">
                <img src="\logo.svg" alt="">
                <h1>Ecolume</h1>
            </div>
            <div class="flex items-center gap-8">
                <a href="" class="hover:underline">Beranda</a>
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
    const slider = document.getElementById("device-scroll");

    let isDown = false;
    let startX;
    let scrollLeft;

    slider.addEventListener("mousedown", (e) => {
        isDown = true;
        slider.classList.add("cursor-grabbing");

        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
    });

    document.addEventListener("mouseup", () => {
        isDown = false;
        slider.classList.remove("cursor-grabbing");
        slider.classList.remove("dragging");
    });

    slider.addEventListener("mouseleave", () => {
        isDown = false;
        slider.classList.remove("cursor-grabbing");
        slider.classList.remove("dragging");
    });

    slider.addEventListener("mousemove", (e) => {
        if (!isDown) return;

        slider.classList.add("dragging");

        e.preventDefault();

        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX) * 2;

        slider.scrollLeft = scrollLeft - walk;
    });
</script>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const btnOpen = document.getElementById('btn-open-sidebar');
    const btnClose = document.getElementById('btn-close-sidebar');
    const btnOpen_addDevice = document.getElementById('btn-open-addDevice');
    const btnClose_addDevice = document.getElementById('btn-close-addDevice');
    const addDevice_Card = document.getElementById('addDevice-Card');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
    }

    function closeCard() {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('hidden');
        addDevice_Card.classList.add('hidden');
    }

    function openAddDevice() {
        addDevice_Card.classList.remove('hidden');
        overlay.classList.remove('hidden');
    }

    btnOpen.addEventListener('click', openSidebar);
    btnClose.addEventListener('click', closeCard);

    btnOpen_addDevice.addEventListener('click', openAddDevice);
    btnClose_addDevice.addEventListener('click', closeCard);

    overlay.addEventListener('click', closeCard);
</script>

</html>