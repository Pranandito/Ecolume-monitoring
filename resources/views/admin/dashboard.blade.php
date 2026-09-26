<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>
    <link rel="icon" href="{{ asset('images/ecolume-logo.svg') }}" type="image/x-icon">
    @vite('resources/css/app.css')
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

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #373737;
            border-radius: 999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #4d4d4d;
        }

        /* Firefox */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #373737 transparent;
        }

        .hide-scrollbar {
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE dan Edge lama */
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Edge baru */
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
            bottom: 20px;
            left: 20px;
            z-index: 1000;

            display: flex;
            flex-direction: column;
            gap: 3px;

            align-items: flex-start;
        }

        /* Tiap item location bar */
        .location-bar {
            padding: 10px 16px;

            background: rgba(86, 86, 86, 0.36);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);

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
</head>

<section id="overlay-timepicker" class="hidden fixed top-0 bottom-0 left-0 right-0 bg-black/75 z-[1112] lg:z-[1110]"></section>

<div id="addAdmin-Card" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[#171717] rounded-2xl shadow-3xl p-6 w-full max-w-[350px] lg:max-w-md z-[1113] text-zinc-400">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 4C13.0609 4 14.0783 4.42143 14.8284 5.17157C15.5786 5.92172 16 6.93913 16 8C16 9.06087 15.5786 10.0783 14.8284 10.8284C14.0783 11.5786 13.0609 12 12 12C10.9391 12 9.92172 11.5786 9.17157 10.8284C8.42143 10.0783 8 9.06087 8 8C8 6.93913 8.42143 5.92172 9.17157 5.17157C9.92172 4.42143 10.9391 4 12 4ZM12 6C11.4696 6 10.9609 6.21071 10.5858 6.58579C10.2107 6.96086 10 7.46957 10 8C10 8.53043 10.2107 9.03914 10.5858 9.41421C10.9609 9.78929 11.4696 10 12 10C12.5304 10 13.0391 9.78929 13.4142 9.41421C13.7893 9.03914 14 8.53043 14 8C14 7.46957 13.7893 6.96086 13.4142 6.58579C13.0391 6.21071 12.5304 6 12 6ZM12 13C14.67 13 20 14.33 20 17V20H4V17C4 14.33 9.33 13 12 13ZM12 14.9C9.03 14.9 5.9 16.36 5.9 17V18.1H18.1V17C18.1 16.36 14.97 14.9 12 14.9Z" fill="white" />
                </svg>
            </div>
            <h3 class="text-lg text-white">Penambahan Admin</h3>
        </div>
        <button id="btn-close-addAdmin" class="">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.6673 16.6667L3.33398 3.33334M16.6673 3.33334L3.33398 16.6667" stroke="#C2C2C2" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
    </div>
    <h1 class="text-white mt-5">Masukkan Email</h1>
    <p class="text-sm mb-5">Masukkan email user yang ingin ditambahkan menjadi admin. Pastikan akun sudah terdaftar sebagai user</p>

    <form action="{{ route('admin.add') }}" method="POST">
        @csrf

        <div class="mb-5">
            <input
                type="email"
                name="email"
                placeholder="Contoh: xxxx@gmail.com"
                class="w-full rounded-xl border border-zinc-700 bg-[#262626] px-4 py-3 text-white placeholder:text-zinc-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition"
                required>
        </div>

        <button type="submit"
            class="block text-center w-full text-sm rounded-full border border-zinc-700 py-1 text-[#979797] hover:bg-zinc-800 transition">
            Tambahkan
        </button>
    </form>
</div>

<div id="addDevice-Card" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[#171717] rounded-2xl shadow-3xl p-6 w-full max-w-[350px] lg:max-w-md z-[1113] text-zinc-400">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 16H16V4H4V16ZM12 18H8V20H6V18H3C2.73478 18 2.48043 17.8946 2.29289 17.7071C2.10536 17.5196 2 17.2652 2 17V14H0V12H2V8H0V6H2V3C2 2.73478 2.10536 2.48043 2.29289 2.29289C2.48043 2.10536 2.73478 2 3 2H6V0H8V2H12V0H14V2H17C17.2652 2 17.5196 2.10536 17.7071 2.29289C17.8946 2.48043 18 2.73478 18 3V6H20V8H18V12H20V14H18V17C18 17.2652 17.8946 17.5196 17.7071 17.7071C17.5196 17.8946 17.2652 18 17 18H14V20H12V18ZM6 6H14V14H6V6Z" fill="#FBFBFB" />
                </svg>
            </div>
            <h3 class="text-lg text-white">Penambahan Perangkat</h3>
        </div>
        <button id="btn-close-addDevice" class="">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.6673 16.6667L3.33398 3.33334M16.6673 3.33334L3.33398 16.6667" stroke="#C2C2C2" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
    </div>
    <h1 class="text-white mt-5">Masukkan Versi Firmware</h1>
    <p class="text-sm mb-5">Masukkan versi firmware yang diupload pada perangkat</p>

    <form action="{{ route('device.create') }}" method="POST">
        @csrf

        <div class="mb-5">
            <input
                type="text"
                name="firmware_version"
                placeholder="Contoh: v1.0.0"
                value="v1.0.0"
                class="w-full rounded-xl border border-zinc-700 bg-[#262626] px-4 py-3 text-white placeholder:text-zinc-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition"
                required>
        </div>

        <button type="submit"
            class="block text-center w-full text-sm rounded-full border border-zinc-700 py-1 text-[#979797] hover:bg-zinc-800 transition">
            Tambahkan
        </button>
    </form>
</div>

<div id="delDevConfirm-card" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[#171717] rounded-2xl shadow-3xl p-6 w-full max-w-[350px] lg:max-w-md z-[1113] text-zinc-400">
    <h1 class="text-white text-center">Konfirmasi Penghapusan</h1>
    <p class="text-sm mb-4 mt-2 text-center">
        Apakah Anda yakin ingin menghapus perangkat
    </p>

    <form action="{{ route('device.delete') }}" method="POST">
        @csrf
        @method('DELETE')

        <input type="hidden" name="device_id" id="delDevInput">
        <div class="flex gap-3">
            <button type="button" onclick="closeDelDevConfirmCard()"
                class="block text-center w-full text-sm rounded-full border border-zinc-700 py-2 text-[#979797] hover:bg-zinc-800 transition">
                Batal
            </button>
            <button type="submit"
                class="block text-center w-full text-sm rounded-full border border-red-900 py-2 text-red-500 bg-red-500/10 hover:bg-red-500/20 transition">
                Hapus
            </button>
        </div>
    </form>
</div>

<section id="overlay" class="hidden fixed top-0 bottom-0 left-0 right-0 bg-black/50 z-[1112]"></section>

<x-sidebar />

<body class="lg:my-12 my-6 lg:mx-16 mx-3 text-white">
    <x-navbar>
        Dashboard Admin
    </x-navbar>

    <hr class="my-5 border-[#373737]">

    <div class="grid grid-cols-1 lg:grid-cols-3 grid-rows-1 gap-6 relative">
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-[#171717] rounded-2xl p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.37292 18.5855C8.92986 18.1424 8.70833 17.6001 8.70833 16.9584C8.70833 16.3167 8.92986 15.7744 9.37292 15.3313C9.81597 14.8883 10.3583 14.6667 11 14.6667C11.6417 14.6667 12.184 14.8883 12.6271 15.3313C13.0701 15.7744 13.2917 16.3167 13.2917 16.9584C13.2917 17.6001 13.0701 18.1424 12.6271 18.5855C12.184 19.0286 11.6417 19.2501 11 19.2501C10.3583 19.2501 9.81597 19.0286 9.37292 18.5855ZM5.82083 14.0709L3.89583 12.1001C4.79722 11.1987 5.85536 10.4846 7.07025 9.95783C8.28514 9.43105 9.59506 9.16736 11 9.16675C12.4049 9.16614 13.7152 9.4335 14.9307 9.96883C16.1462 10.5042 17.204 11.2299 18.1042 12.1459L16.1792 14.0709C15.5069 13.3987 14.7278 12.8716 13.8417 12.4897C12.9556 12.1077 12.0083 11.9167 11 11.9167C9.99167 11.9167 9.04444 12.1077 8.15833 12.4897C7.27222 12.8716 6.49306 13.3987 5.82083 14.0709ZM1.925 10.1751L0 8.25008C1.40556 6.81397 3.04792 5.69105 4.92708 4.88133C6.80625 4.07161 8.83056 3.66675 11 3.66675C13.1694 3.66675 15.1938 4.07161 17.0729 4.88133C18.9521 5.69105 20.5944 6.81397 22 8.25008L20.075 10.1751C18.8986 8.99869 17.5352 8.07836 15.9848 7.41408C14.4344 6.7498 12.7728 6.41736 11 6.41675C9.22717 6.41614 7.56586 6.74858 6.01608 7.41408C4.46631 8.07958 3.10261 8.99991 1.925 10.1751Z" fill="white" />
                            </svg>
                        </div>
                        <h3 class="text-lg text-white">Perangkat Online</h3>
                    </div>

                    <div class="flex items-baseline gap-1 mt-5">
                        <span class="text-4xl text-white">{{ $count->online }}</span>
                        <span class="text-sm text-zinc-400">Device</span>
                    </div>
                    <h1 class="text-xs text-zinc-400 mb-5">Offline: {{ $count->offline }} Device</h1>

                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-zinc-400">Perangkat yang online hari ini:</span>
                        <span class="text-sm text-white">{{ $count->onlineToday }} Device</span>
                    </div>
                </div>
                <div class="lg:col-span-2 bg-[#171717] rounded-2xl p-6 text-[#979797]">
                    <div class="flex items-center justify-between pb-2">
                        <h3 class="text-lg text-white">Daftar Admin</h3>
                        <button id="btn-open-addAdmin">
                            <div class="py-1 px-3 bg-[#262626] rounded-full flex items-center gap-2 text-white">
                                <h1 class="text-sm">Tambahkan</h1>
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="white"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15.0413 10.2901H10.2913V15.0401H8.70801V10.2901H3.95801V8.70676H8.70801V3.95676H10.2913V8.70676H15.0413V10.2901Z"
                                        fill="" />
                                </svg>
                            </div>
                        </button>
                    </div>
                    <div class="h-[140px] overflow-y-auto custom-scrollbar pr-2">
                        @foreach($adminList as $admin)
                        <hr class="border-[#373737] my-3">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-12 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 4C13.0609 4 14.0783 4.42143 14.8284 5.17157C15.5786 5.92172 16 6.93913 16 8C16 9.06087 15.5786 10.0783 14.8284 10.8284C14.0783 11.5786 13.0609 12 12 12C10.9391 12 9.92172 11.5786 9.17157 10.8284C8.42143 10.0783 8 9.06087 8 8C8 6.93913 8.42143 5.92172 9.17157 5.17157C9.92172 4.42143 10.9391 4 12 4ZM12 6C11.4696 6 10.9609 6.21071 10.5858 6.58579C10.2107 6.96086 10 7.46957 10 8C10 8.53043 10.2107 9.03914 10.5858 9.41421C10.9609 9.78929 11.4696 10 12 10C12.5304 10 13.0391 9.78929 13.4142 9.41421C13.7893 9.03914 14 8.53043 14 8C14 7.46957 13.7893 6.96086 13.4142 6.58579C13.0391 6.21071 12.5304 6 12 6ZM12 13C14.67 13 20 14.33 20 17V20H4V17C4 14.33 9.33 13 12 13ZM12 14.9C9.03 14.9 5.9 16.36 5.9 17V18.1H18.1V17C18.1 16.36 14.97 14.9 12 14.9Z" fill="white" />
                                </svg>
                            </div>

                            <div class="w-full flex items-center justify-between">
                                <div class="">
                                    <h1 class="text-base text-white">{{ $admin->name }}</h1>
                                    <h1 class="text-xs lg:text-sm">{{ $admin->email }}</h1>
                                </div>
                                <div class="text-end hidden lg:block">
                                    <h1 class="text-xs text-white">Ditambahkan pada:</h1>
                                    <h1 class="text-xs">{{ $admin->role_updated_at->locale('id')->translatedFormat('j F Y') }}</h1>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <hr class="border-[#373737] my-3">
                    </div>
                </div>
                <div data-admin-duration-chart
                    data-config='{"endpoint":"/device/admin-chart","refreshIntervalMs":30000,"maxVisiblePoints":200}'
                    class="bg-[#171717] rounded-2xl p-6 text-[#979797] lg:col-span-3 h-[650px] z-[1111]">
                    <div class="lg:flex block justify-between items-center gap-4 mb-8">
                        <div class="flex items-center gap-3 mb-3 lg:mb-0">
                            <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.486 5.57839C13.5577 5.3146 13.7313 5.0901 13.9685 4.95423C14.2057 4.81835 14.4871 4.78221 14.751 4.85376L19.0355 6.02114C19.298 6.09266 19.5216 6.26503 19.6576 6.50069C19.7936 6.73634 19.8309 7.01619 19.7615 7.27926L18.6299 11.5706C18.5545 11.8286 18.3814 12.047 18.1475 12.1793C17.9135 12.3116 17.6371 12.3473 17.3772 12.2788C17.1173 12.2104 16.8944 12.0431 16.756 11.8127C16.6175 11.5823 16.5745 11.307 16.6361 11.0454L17.0995 9.28401C15.3083 10.5143 13.6457 11.922 12.1371 13.486C12.042 13.5846 11.9283 13.6633 11.8025 13.7174C11.6767 13.7716 11.5413 13.8001 11.4044 13.8014C11.2674 13.8027 11.1316 13.7767 11.0048 13.7249C10.878 13.6731 10.7627 13.5966 10.6659 13.4998L8.24998 11.0825L3.13498 16.1975C3.0398 16.296 2.92597 16.3745 2.80013 16.4285C2.67429 16.4824 2.53895 16.5108 2.40203 16.5119C2.2651 16.5131 2.12932 16.4869 2.0026 16.435C1.87589 16.3831 1.76079 16.3065 1.66401 16.2096C1.56723 16.1127 1.49071 15.9975 1.43891 15.8708C1.38712 15.744 1.36109 15.6082 1.36235 15.4713C1.3636 15.3344 1.39211 15.1991 1.44622 15.0733C1.50032 14.9475 1.57894 14.8337 1.67748 14.7386L7.52123 8.89489C7.71459 8.70177 7.9767 8.59329 8.24998 8.59329C8.52326 8.59329 8.78537 8.70177 8.97873 8.89489L11.3932 11.308C12.8794 9.8588 14.4939 8.54719 16.2167 7.38926L14.2092 6.84201C13.9457 6.76996 13.7215 6.59629 13.5859 6.35912C13.4503 6.12195 13.4144 5.84204 13.486 5.57839Z" fill="white" />
                                </svg>
                            </div>
                            <h3 class="text-lg text-white font-medium">Tren Aktual</h3>
                        </div>

                        <div class="flex items-center gap-3 w-full lg:w-fit flex-wrap-reverse lg:flex-nowrap">
                            <div class="flex items-center gap-3 w-full lg:w-fit">
                                <div class="flex items-center p-0.5 rounded-lg bg-[#242424] border border-[#242424]" data-role="range-filter-group">
                                    <button type="button" data-range="1H"
                                        class="range-filter-btn px-3 py-1.5 text-xs font-medium text-white bg-[#171717] rounded-md shadow">1H</button>
                                    <button type="button" data-range="1M"
                                        class="range-filter-btn px-3 py-1.5 text-xs font-medium text-zinc-400 hover:text-white rounded-md transition-colors">1M</button>
                                    <button type="button" data-range="CUSTOM"
                                        class="range-filter-btn px-3 py-1.5 text-xs font-medium text-zinc-400 hover:text-white rounded-md transition-colors">Custom</button>
                                </div>

                                <button type="button" data-role="export-btn" disabled
                                    title="Export data yang sedang ditampilkan ke CSV"
                                    class="w-full lg:w-fit flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-[#242424] text-xs font-medium text-zinc-300 hover:text-white transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3v12" />
                                        <path d="m7 10 5 5 5-5" />
                                        <path d="M5 21h14" />
                                    </svg>
                                    <span data-role="export-label">CSV</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div data-role="chart" class="h-max"></div>
                </div>
            </div>
        </div>
        <div class="bg-[#171717] rounded-2xl p-6 text-[#979797] h-full">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/chain.svg') }}" alt="" class="w-5">
                <div>
                    <h3 class="text-lg text-white">Log Aktivitas Pompa</h3>
                </div>
            </div>
            <p class="text-[#979797] text-sm">Catatan 10 aktivitas terakhir sistem</p>

            <div id="pumpLogList" class="overflow-hidden max-h-[220px] transition-[max-height] duration-500 ease-in-out lg:!max-h-none lg:overflow-visible">
                @foreach($activityLogs as $log)
                <hr class="border-[#373737] my-3">
                <div class="flex items-center gap-5">
                    <h1 class="w-16 h-[52px] flex items-center justify-center text-white font-semibold rounded-full bg-[#262626]">
                        {{ $log->created_at->locale('id')->translatedFormat('j/n') }}
                    </h1>
                    <div class="w-full">
                        <div class="items-center justify-between flex text-[#979797]">
                            <h1 class="text-base text-white">{{ $log->title }}</h1>
                            <h1 class="text-xs"> {{ $log->created_at->locale('id')->translatedFormat('H:i') }}</h1>
                        </div>
                        <h1 class="text-sm">{{ $log->desc }}</h1>
                    </div>
                </div>
                @endforeach
                <hr class="border-[#373737] my-3">
            </div>
            <div class="flex justify-center mt-4 lg:hidden">
                <button
                    type="button"
                    id="pumpLogToggleBtn"
                    onclick="togglePumpLog()"
                    class="w-9 h-9 flex items-center justify-center rounded-full bg-[#262626] border border-[#323232] hover:bg-[#2f2f2f] transition-colors">
                    <svg
                        id="pumpLogToggleIcon"
                        width="14" height="14" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        class="transition-transform duration-300">
                        <path d="M6 9L12 15L18 9" stroke="#979797" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
        <div id="time-filter-panel"
            class="hidden absolute right-0 bottom-0 lg:w-[475px] w-[350px] max-h-[665px] z-[1113] bg-[#171717] rounded-2xl p-6">
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
    </div>

    <div class="flex items-center justify-between mt-6 mb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M4.93045 4.92901C5.11796 4.74137 5.37233 4.6359 5.6376 4.6358C5.90287 4.63571 6.15731 4.741 6.34495 4.92851C6.53259 5.11601 6.63806 5.37038 6.63816 5.63565C6.63825 5.90092 6.53296 6.15537 6.34545 6.34301C4.84538 7.84327 4.00266 9.87795 4.00266 11.9995C4.00266 14.1211 4.84538 16.1557 6.34545 17.656C6.53309 17.8435 6.63856 18.0979 6.63866 18.3632C6.63875 18.6284 6.53346 18.8829 6.34595 19.0705C6.15844 19.2581 5.90408 19.3636 5.63881 19.3637C5.37354 19.3638 5.11909 19.2585 4.93145 19.071C1.02545 15.166 1.02545 8.83401 4.93145 4.92901M17.6595 4.92901C17.847 4.74153 18.1013 4.63622 18.3665 4.63622C18.6316 4.63622 18.8859 4.74153 19.0735 4.92901C22.9795 8.83401 22.9795 15.166 19.0735 19.071C18.8858 19.2585 18.6314 19.3638 18.3661 19.3637C18.1008 19.3636 17.8465 19.2581 17.659 19.0705C17.4714 18.8829 17.3662 18.6284 17.3663 18.3632C17.3663 18.0979 17.4718 17.8435 17.6595 17.656C19.1595 16.1557 20.0022 14.1211 20.0022 11.9995C20.0022 9.87795 19.1595 7.84327 17.6595 6.34301C17.472 6.15548 17.3667 5.90117 17.3667 5.63601C17.3667 5.37084 17.472 5.11653 17.6595 4.92901ZM7.75945 7.75701C7.94793 7.57471 8.20046 7.47373 8.46266 7.47582C8.72486 7.47792 8.97575 7.58291 9.16129 7.76819C9.34683 7.95346 9.45218 8.20421 9.45464 8.4664C9.4571 8.7286 9.35648 8.98128 9.17445 9.17001C8.803 9.54144 8.50834 9.98241 8.30731 10.4677C8.10628 10.953 8.00281 11.4732 8.00281 11.9985C8.00281 12.5238 8.10628 13.044 8.30731 13.5293C8.50834 14.0146 8.803 14.4556 9.17445 14.827C9.35661 15.0156 9.45741 15.2682 9.45513 15.5304C9.45285 15.7926 9.34768 16.0434 9.16227 16.2288C8.97686 16.4142 8.72605 16.5194 8.46385 16.5217C8.20166 16.524 7.94906 16.4232 7.76045 16.241C6.63532 15.1158 6.00323 13.5897 6.00323 11.9985C6.00323 10.4073 6.63532 8.88121 7.76045 7.75601M14.8315 7.75601C15.019 7.56853 15.2733 7.46322 15.5385 7.46322C15.8036 7.46322 16.0579 7.56853 16.2455 7.75601C17.3706 8.88121 18.0027 10.4073 18.0027 11.9985C18.0027 13.5897 17.3706 15.1158 16.2455 16.241C16.0569 16.4232 15.8042 16.524 15.5421 16.5217C15.2799 16.5194 15.029 16.4142 14.8436 16.2288C14.6582 16.0434 14.5531 15.7926 14.5508 15.5304C14.5485 15.2682 14.6493 15.0156 14.8315 14.827C15.2029 14.4556 15.4976 14.0146 15.6986 13.5293C15.8996 13.044 16.0031 12.5238 16.0031 11.9985C16.0031 11.4732 15.8996 10.953 15.6986 10.4677C15.4976 9.98241 15.2029 9.54144 14.8315 9.17001C14.644 8.98248 14.5387 8.72817 14.5387 8.46301C14.5387 8.19784 14.644 7.94353 14.8315 7.75601ZM12.0005 10.5C12.3983 10.5 12.7798 10.658 13.0611 10.9393C13.3424 11.2206 13.5005 11.6022 13.5005 12C13.5005 12.3978 13.3424 12.7794 13.0611 13.0607C12.7798 13.342 12.3983 13.5 12.0005 13.5C11.6026 13.5 11.2211 13.342 10.9398 13.0607C10.6585 12.7794 10.5005 12.3978 10.5005 12C10.5005 11.6022 10.6585 11.2206 10.9398 10.9393C11.2211 10.658 11.6026 10.5 12.0005 10.5Z"
                        fill="white" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg text-white">Daftar Perangkat <span class="hidden lg:inline-block">pada Sistem</span></h3>
                <p class="text-xs text-[#979797] hidden lg:block">{{ $deviceList->total() }} perangkat tercatat dalam sistem</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button id="btn-open-addDevice">
                <div class="py-2 px-2 lg:px-5 bg-[#262626] rounded-full flex items-center gap-2 text-white">
                    <h1 class="text-sm hidden lg:block">Tambahkan Perangkat</h1>
                    <svg width="19" height="19" viewBox="0 0 19 19" fill="white"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M15.0413 10.2901H10.2913V15.0401H8.70801V10.2901H3.95801V8.70676H8.70801V3.95676H10.2913V8.70676H15.0413V10.2901Z"
                            fill="" />
                    </svg>
                </div>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-white/10 hide-scrollbar">
        <table class="w-full text-left border-collapse min-w-[1450px]">
            <thead>
                <tr class="bg-[#171717] text-gray-400 text-sm">
                    <th class="px-6 py-4 font-medium">Id</th>
                    <th class="px-6 py-4 font-medium">Device</th>
                    <th class="px-6 py-4 font-medium">Firmware</th>
                    <th class="px-6 py-4 font-medium">Serial Number</th>
                    <th class="px-6 py-4 font-medium">Api key</th>
                    <th class="px-6 py-4 font-medium">Konektivitas</th>
                    <th class="px-6 py-4 font-medium">Operasional</th>
                    <th class="px-6 py-4 font-medium">Owner</th>
                    <th class="px-6 py-4 font-medium">Update Terakhir</th>
                    <th class="px-6 py-4 font-medium">Ditambahkan Pada</th>
                    <th class="px-6 py-4 font-medium sticky right-0 bg-[#171717] z-10"></th>
                </tr>
            </thead>
            <tbody id="table-body" class="text-sm text-[#979797] divide-y divide-white/10">
                @foreach($deviceList as $dev)
                <tr class="hover:bg-white/[0.03] transition">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $dev->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{$dev->device_name}}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{$dev->firmware_version}}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-300">
                        <div class="flex items-center gap-2">
                            <span>{{ $dev->serial_number }}</span>
                            <button type="button" class="btn-copy text-gray-500 hover:text-gray-200 transition" data-copy="{{ $dev->serial_number }}" title="Copy serial number">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-mono text-[#979797]">
                        <div class="flex items-center gap-2">
                            <span>{{ $dev->API_keys }}</span>
                            <button type="button" class="btn-copy text-gray-500 hover:text-gray-200 transition" data-copy="{{ $dev->API_keys }}" title="Copy API key">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium {{ $dev->online_status ? 'bg-green-500/5 text-[#00A451]' : 'bg-red-500/5 text-[#DC2626]' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dev->online_status ? 'bg-[#00A451]' : 'bg-[#DC2626]' }}"></span>
                            {{ $dev->online_status ? 'Online' : 'Offline' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium 
                    @if ($dev->operational_status === 'Anomaly')
                        bg-[#F59E0B]/5 text-[#F59E0B]
                    @elseif ($dev->operational_status == 'Off')
                        bg-[#DC2626]/5 text-[#DC2626]
                    @else
                        bg-[#00A451]/5 text-[#00A451]
                    @endif">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-dasharray="4 4" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                            {{ $dev->operational_status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $dev->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $dev->last_seen ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $dev->created_at ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right sticky right-0 bg-[#121212] z-10">
                        <div class="inline-block text-left" data-dropdown>
                            <button type="button" onclick="toggleDropdown(this)" class="text-gray-500 hover:text-gray-300 w-8 h-8 flex items-center justify-center rounded-md hover:bg-white/5 ml-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="5" r="1.5" />
                                    <circle cx="12" cy="12" r="1.5" />
                                    <circle cx="12" cy="19" r="1.5" />
                                </svg>
                            </button>
                            <div class="hidden w-36 rounded-lg border border-white/10 bg-[#1a1a1a] shadow-lg overflow-hidden" data-menu>
                                <a href="{{ route('dashboard', ['device_name' => $dev->device_name, 'device_id' => $dev->id]) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:bg-white/5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    Lihat
                                </a>
                                <button type="button" data-device-id="{{ $dev->id }}"
                                    class="btnOpenDeviceConfirmCard w-full flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-white/5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6" />
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                        <line x1="10" y1="11" x2="10" y2="17" />
                                        <line x1="14" y1="11" x2="14" y2="17" />
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between mt-4 px-2">

        <span class="hidden lg:block"></span>

        <span class="text-xs text-gray-500 self-center">
            {{ $deviceList->firstItem() }}–{{ $deviceList->lastItem() }}
            dari {{ $deviceList->total() }} Device
        </span>

        <div class="flex items-center gap-2">

            {{-- Previous --}}
            @if ($deviceList->onFirstPage())
            <button
                disabled
                class="w-8 h-8 flex items-center justify-center rounded-full
                       border border-white/10 text-gray-600 cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            @else
            <a href="{{ $deviceList->previousPageUrl() }}"
                class="w-8 h-8 flex items-center justify-center rounded-full
                       border border-white/10 text-gray-400
                       hover:bg-white/5 transition">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </a>
            @endif

            {{-- Next --}}
            @if ($deviceList->hasMorePages())
            <a href="{{ $deviceList->nextPageUrl() }}"
                class="w-8 h-8 flex items-center justify-center rounded-full
                       border border-white/10 text-gray-400
                       hover:bg-white/5 transition">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
            @else
            <button
                disabled
                class="w-8 h-8 flex items-center justify-center rounded-full
                       border border-white/10 text-gray-600 cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
            @endif

        </div>
    </div>

    <div class="bg-[#171717] rounded-2xl p-3 mt-6 relative">
        <div class="location-bar-container">
            @foreach($devices as $device)
            <a href="{{ route('dashboard', ['device_id' => $device->id, 'device_name' => $device->device_name]) }}">
                <div class="location-bar">
                    <div class="flex items-center justify-between bg-[#444444]/44 rounded-lg w-60 hover:scale-[1.01] transition-transform">
                        <div class="font-normal">
                            <h1>{{$device->device_name}}</h1>
                            <p class="text-[#C5C5C5] text-xs">{{$device->device_config->location}}</p>
                        </div>
                        <button
                            type="button"
                            class="gps-point p-1 rounded-lg bg-white"
                            data-lat="{{ $device->device_config->lat }}"
                            data-lng="{{ $device->device_config->long }}"
                            aria-label="Go to {{ $device->device_name }} location">
                            <svg width="20" height="20" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_332_1698)">
                                    <path d="M6 4C4.895 4 4 4.895 4 6C4 7.105 4.895 8 6 8C7.105 8 8 7.105 8 6C8 4.895 7.105 4 6 4ZM10.47 5.5C10.3567 4.48606 9.90208 3.54077 9.18065 2.81935C8.45923 2.09792 7.51394 1.64326 6.5 1.53V0.5H5.5V1.53C4.48606 1.64326 3.54077 2.09792 2.81935 2.81935C2.09792 3.54077 1.64326 4.48606 1.53 5.5H0.5V6.5H1.53C1.64326 7.51394 2.09792 8.45923 2.81935 9.18065C3.54077 9.90208 4.48606 10.3567 5.5 10.47V11.5H6.5V10.47C7.51394 10.3567 8.45923 9.90208 9.18065 9.18065C9.90208 8.45923 10.3567 7.51394 10.47 6.5H11.5V5.5H10.47ZM6 9.5C4.065 9.5 2.5 7.935 2.5 6C2.5 4.065 4.065 2.5 6 2.5C7.935 2.5 9.5 4.065 9.5 6C9.5 7.935 7.935 9.5 6 9.5Z" fill="#121212" />
                                </g>
                                <defs>
                                    <clipPath id="clip0_332_1698">
                                        <rect width="12" height="12" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </button>
                    </div>
                </div>
            </a>
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

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png?key=cb1_2fz2_1_a235180b6236030d085af705', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>, &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

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
        <script>
            document.querySelectorAll('.gps-point').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault(); // cegah <a> parent ikut navigasi
                    e.stopPropagation();

                    const lat = parseFloat(this.dataset.lat);
                    const lng = parseFloat(this.dataset.lng);

                    if (isNaN(lat) || isNaN(lng)) return;

                    map.flyTo([lat, lng], 18, {
                        duration: 1.2
                    });

                    // opsional: buka popup marker yang sesuai
                    const target = markerGroup.find(m => {
                        const p = m.getLatLng();
                        return p.lat === lat && p.lng === lng;
                    });
                    if (target) target.openPopup();
                });
            });
        </script>
    </div>

    <footer class="mt-12">
        <div class="flex flex-col md:flex-row md:place-content-between items-center md:items-center gap-6 md:gap-4 text-[#C4C4C4] text-center md:text-left">
            <div class="flex items-center gap-4 text-xl sm:text-2xl">
                <img src="{{ asset('images/ecolume-logo.svg') }}" alt="" class="w-7 h-7 sm:w-auto sm:h-auto">
                <h1>Ecolume</h1>
            </div>

            <div class="flex flex-wrap justify-center items-center gap-x-6 gap-y-2 sm:gap-x-8 text-sm sm:text-base">
                <a href="{{ route('admin.dashboard') }}" class="hover:underline">Dashboard</a>
                <a href="{{ route('admin.storage') }}" class="hover:underline">Penyimpanan</a>
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

    @stack('scripts')
</body>

<script>
    function toggleDropdown(btn) {
        const wrapper = btn.closest('[data-dropdown]');
        const menu = wrapper.querySelector('[data-menu]');
        const isOpen = !menu.classList.contains('hidden');

        document.querySelectorAll('[data-menu]').forEach(m => m.classList.add('hidden'));

        if (!isOpen) {
            menu.classList.remove('hidden');
        }
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('[data-dropdown]')) {
            document.querySelectorAll('[data-menu]').forEach(m => m.classList.add('hidden'));
        }
    });
</script>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const btnOpenSidebar = document.getElementById('btn-open-sidebar');
    const btnCloseSidebar = document.getElementById('btn-close-sidebar');

    const btnOpenAddAdmin = document.getElementById('btn-open-addAdmin');
    const btnCloseAddAdmin = document.getElementById('btn-close-addAdmin');
    const addAdminCard = document.getElementById('addAdmin-Card');

    const btnOpenAddDevice = document.getElementById('btn-open-addDevice');
    const btnCloseAddDevice = document.getElementById('btn-close-addDevice');
    const addDeviceCard = document.getElementById('addDevice-Card');

    const deleteDeviceConfirmCard = document.getElementById('delDevConfirm-card');
    const btnOpenDeviceConfirmCard = document.getElementById('openDelDevConfirm-btn');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
    }

    function closeCard() {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('hidden');

        addAdminCard.classList.add('hidden');
        addDeviceCard.classList.add('hidden');
        deleteDeviceConfirmCard.classList.add('hidden');
    }

    function openAddAdminCard() {
        addAdminCard.classList.remove('hidden');
        overlay.classList.remove('hidden');
    }

    function closeAddAdminCard() {
        addAdminCard.classList.add('hidden');
        overlay.classList.add('hidden');
    }

    function openAddDeviceCard() {
        addDeviceCard.classList.remove('hidden');
        overlay.classList.remove('hidden');
    }

    function closeAddDeviceCard() {
        addDeviceCard.classList.add('hidden');
        overlay.classList.add('hidden');
    }

    function openDelDevConfirmCard() {
        deleteDeviceConfirmCard.classList.remove('hidden');
        overlay.classList.remove('hidden');
    }

    function closeDelDevConfirmCard() {
        deleteDeviceConfirmCard.classList.add('hidden');
        overlay.classList.add('hidden');
    }

    btnOpenSidebar.addEventListener('click', openSidebar);
    btnCloseSidebar.addEventListener('click', closeCard);

    btnOpenAddAdmin.addEventListener('click', openAddAdminCard);
    btnCloseAddAdmin.addEventListener('click', closeAddAdminCard);

    btnOpenAddDevice.addEventListener('click', openAddDeviceCard);
    btnCloseAddDevice.addEventListener('click', closeAddDeviceCard);

    overlay.addEventListener('click', closeCard);

    document.querySelectorAll('.btnOpenDeviceConfirmCard').forEach(button => {
        button.addEventListener('click', function() {
            const deviceId = this.dataset.deviceId;
            document.getElementById('delDevInput').value = deviceId;
            openDelDevConfirmCard();
        });
    });
</script>

<script>
    function togglePumpLog() {
        const list = document.getElementById('pumpLogList');
        const icon = document.getElementById('pumpLogToggleIcon');
        const isExpanded = list.classList.contains('max-h-[2000px]');

        if (isExpanded) {
            list.classList.remove('max-h-[2000px]');
            list.classList.add('max-h-[220px]');
            icon.classList.remove('rotate-180');
        } else {
            list.classList.remove('max-h-[220px]');
            list.classList.add('max-h-[2000px]');
            icon.classList.add('rotate-180');
        }
    }
</script>

<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-copy');
        if (!btn) return;

        navigator.clipboard.writeText(btn.dataset.copy).then(() => {
            const originalSvg = btn.innerHTML;

            // Ubah icon menjadi centang
            btn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-3.5 h-3.5 text-green-400"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
            `;

            // Tampilkan toast
            showCopyToast();

            // Kembalikan icon setelah 1.2 detik
            setTimeout(() => {
                btn.innerHTML = originalSvg;
            }, 1200);

        }).catch(err => console.error('Gagal copy:', err));
    });

    function showCopyToast() {
        // Hapus toast sebelumnya jika masih ada
        const existingToast = document.getElementById('copy-toast');
        if (existingToast) {
            existingToast.remove();
        }

        const toast = document.createElement('div');
        toast.id = 'copy-toast';

        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 text-green-400"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <span>Berhasil disalin</span>
            </div>
        `;

        toast.className = `
            fixed bottom-5 right-5
            z-[1112]
            px-4 py-2.5
            bg-gray-800
            border border-gray-700
            text-white text-sm
            rounded-lg
            shadow-lg
            opacity-0
            translate-y-2
            transition-all duration-300
        `;

        document.body.appendChild(toast);

        // Animasi masuk
        requestAnimationFrame(() => {
            toast.classList.remove('opacity-0', 'translate-y-2');
        });

        // Hilangkan setelah 1.5 detik
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');

            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 1500);
    }
</script>

<script>
    function toggleDropdown(btn) {
        const menu = btn.nextElementSibling;
        const isCurrentlyOpen = menu.dataset.open === 'true';

        // tutup semua dropdown yang sedang terbuka
        document.querySelectorAll('[data-menu]').forEach(m => {
            if (m.dataset.open === 'true') {
                m.dataset.open = 'false';
                m.classList.add('hidden');
                if (m._originalParent) {
                    m._originalParent.insertBefore(m, m._originalNextSibling);
                }
            }
        });

        if (isCurrentlyOpen) return; // klik ke-2 pada tombol yang sama = tutup

        // simpan parent asli sekali saja
        if (!menu._originalParent) {
            menu._originalParent = menu.parentElement;
            menu._originalNextSibling = menu.nextSibling;
        }

        // ⭐ FIX: unhide dulu (tapi sembunyikan via opacity) baru ukur, baru pindah
        menu.classList.remove('hidden');
        menu.style.visibility = 'hidden'; // sembunyikan sementara saat mengukur
        document.body.appendChild(menu);

        const rect = btn.getBoundingClientRect();
        const menuWidth = menu.offsetWidth; // sekarang terukur benar karena sudah ter-render

        let left = rect.right - menuWidth;
        let top = rect.bottom + 4;

        // jaga-jaga biar tidak keluar layar
        if (left < 8) left = 8;
        if (top + menu.offsetHeight > window.innerHeight) {
            top = rect.top - menu.offsetHeight - 4; // buka ke atas kalau mepet bawah
        }

        menu.style.position = 'fixed';
        menu.style.top = `${top}px`;
        menu.style.left = `${left}px`;
        menu.style.zIndex = '9999';
        menu.style.visibility = 'visible';
        menu.dataset.open = 'true';
    }

    document.addEventListener('click', (e) => {
        const clickedInsideAnyMenu = e.target.closest('[data-menu]');
        const clickedToggleBtn = e.target.closest('[data-dropdown] > button');

        if (!clickedInsideAnyMenu && !clickedToggleBtn) {
            document.querySelectorAll('[data-menu]').forEach(m => {
                if (m.dataset.open === 'true') {
                    m.dataset.open = 'false';
                    m.classList.add('hidden');
                    if (m._originalParent) {
                        m._originalParent.insertBefore(m, m._originalNextSibling);
                    }
                }
            });
        }
    });
</script>

<script src="{{ asset('js/admin-chart.js') }}"></script>