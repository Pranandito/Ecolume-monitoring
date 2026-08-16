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

<x-sidebar :devices=$devices />

<section id="overlay" class="hidden fixed top-0 bottom-0 left-0 right-0 bg-black/75 z-[1113]"></section>
<section id="overlay-timepicker" class="hidden fixed top-0 bottom-0 left-0 right-0 bg-black/75 z-[1112] lg:z-[1110]"></section>

<x-mode.select :id="$id" :serial_number="$device->serial_number" />

<div id="carbon-form" class="hidden w-full max-w-[350px] lg:max-w-md bg-[#171717] rounded-2xl p-6 sm:p-7 fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[1113]">
    <div class="flex justify-between items-center pb-4">
        <div class="flex items-center gap-3">
            <svg width="25" height="25" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.1782 24.75C11.6719 24.75 11.2362 24.5812 10.8709 24.2437C10.5057 23.9062 10.2852 23.4938 10.2094 23.0063L9.95629 21.15C9.71254 21.0562 9.48304 20.9438 9.26779 20.8125C9.05254 20.6812 8.84141 20.5406 8.63441 20.3906L6.89066 21.1219C6.42191 21.3281 5.95316 21.3469 5.48441 21.1781C5.01566 21.0094 4.65004 20.7094 4.38754 20.2781L3.06566 17.9719C2.80316 17.5406 2.72816 17.0813 2.84066 16.5938C2.95316 16.1062 3.20629 15.7031 3.60004 15.3844L5.09066 14.2594C5.07191 14.1281 5.06254 14.0014 5.06254 13.8791V13.1198C5.06254 12.9983 5.07191 12.8719 5.09066 12.7406L3.60004 11.6156C3.20629 11.2969 2.95316 10.8938 2.84066 10.4062C2.72816 9.91875 2.80316 9.45937 3.06566 9.02812L4.38754 6.72187C4.65004 6.29063 5.01566 5.99063 5.48441 5.82188C5.95316 5.65313 6.42191 5.67188 6.89066 5.87812L8.63441 6.60938C8.84066 6.45937 9.05629 6.31875 9.28129 6.1875C9.50629 6.05625 9.73129 5.94375 9.95629 5.85L10.2094 3.99375C10.2844 3.50625 10.5049 3.09375 10.8709 2.75625C11.2369 2.41875 11.6727 2.25 12.1782 2.25H14.8219C15.3282 2.25 15.7643 2.41875 16.1303 2.75625C16.4963 3.09375 16.7164 3.50625 16.7907 3.99375L17.0438 5.85C17.2875 5.94375 17.5174 6.05625 17.7334 6.1875C17.9494 6.31875 18.1602 6.45937 18.3657 6.60938L20.1094 5.87812C20.5782 5.67188 21.0469 5.65313 21.5157 5.82188C21.9844 5.99063 22.35 6.29063 22.6125 6.72187L23.9344 9.02812C24.1969 9.45937 24.2719 9.91875 24.1594 10.4062C24.0469 10.8938 23.7938 11.2969 23.4 11.6156L21.9094 12.7406C21.9282 12.8719 21.9375 12.9986 21.9375 13.1209V13.8791C21.9375 14.0014 21.9188 14.1281 21.8813 14.2594L23.3719 15.3844C23.7657 15.7031 24.0188 16.1062 24.1313 16.5938C24.2438 17.0813 24.1688 17.5406 23.9063 17.9719L22.5563 20.2781C22.2938 20.7094 21.9282 21.0094 21.4594 21.1781C20.9907 21.3469 20.5219 21.3281 20.0532 21.1219L18.3657 20.3906C18.1594 20.5406 17.9438 20.6812 17.7188 20.8125C17.4938 20.9438 17.2688 21.0562 17.0438 21.15L16.7907 23.0063C16.7157 23.4938 16.4955 23.9062 16.1303 24.2437C15.765 24.5812 15.3289 24.75 14.8219 24.75H12.1782ZM13.5563 17.4375C14.6438 17.4375 15.5719 17.0531 16.3407 16.2844C17.1094 15.5156 17.4938 14.5875 17.4938 13.5C17.4938 12.4125 17.1094 11.4844 16.3407 10.7156C15.5719 9.94687 14.6438 9.5625 13.5563 9.5625C12.45 9.5625 11.517 9.94687 10.7573 10.7156C9.99754 11.4844 9.61804 12.4125 9.61879 13.5C9.61954 14.5875 9.99941 15.5156 10.7584 16.2844C11.5174 17.0531 12.45 17.4375 13.5563 17.4375Z"
                    fill="white" />
            </svg>

            <h3 class="text-lg text-white">Faktor Emisi Karbon</h3>
        </div>
        <button id="btn-close-carbon-form" class="">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.6673 16.6667L3.33398 3.33334M16.6673 3.33334L3.33398 16.6667" stroke="#C2C2C2" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
    </div>


    <form action="{{ route('carbonFactor.update', ['id' => $id]) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="flex justify-center my-5">
            <button type="button" id="koef-minus" class="text-4xl leading-none text-white/70 transition hover:text-white" aria-label="Kurangi">&minus;</button>
            <input
                type="text"
                inputmode="decimal"
                id="koef-value"
                name="carbon_factor"
                value="{{ auth()->user()->carbon_factor }}"
                class="w-36 text-center border-0 bg-transparent p-0 text-4xl text-white focus:outline-none focus:ring-0">
            <button type="button" id="koef-plus" class="text-4xl leading-none text-white/70 transition hover:text-white" aria-label="Tambah">&#43;</button>
        </div>
        <h1 class="text-base text-center text-white/60 mb-4">Kg Co2/kWh</h1>
        <h1 class="text-sm text-center text-white/60">Merepresentasikan pengurangan emisi karbon <br> per kWh listrik yang dihasilkan</h1>

        <div class="flex items-center gap-3 mt-5">
            <button id="btn-cancel-carbon-factor"
                class="flex-1 rounded-full border border-white/15 text-neutral-300 py-1 hover:bg-white/5 transition">Batalkan</button>
            <button id="btn-apply-carbon-factor" type="submit"
                class="flex-1 rounded-full bg-white text-neutral-900 py-1 flex items-center justify-center gap-1.5 hover:bg-neutral-200 transition">
                Terapkan
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18l6-6-6-6" />
                </svg>
            </button>
        </div>
    </form>
</div>

<body class="lg:my-12 my-6 lg:mx-16 mx-3 text-white">
    <x-navbar>
        Dashboard
    </x-navbar>

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
                    <span class="text-sm font-medium text-white">{{ ($latest["Tegangan"] ?? 0) != 0
                            ? round(($latest["Daya"] ?? 0) / $latest["Tegangan"], 2) : 0 }} Amp
                    </span>
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
            class="hidden absolute right-0 lg:w-[475px] w-[350px] max-h-[665px] z-[1113] bg-[#171717] rounded-2xl p-6">
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

        <x-dashboard.chart-card :device="$device" />

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
            <x-dashboard.pump-usage :device="$device" />
        </div>
    </div>
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
                            <h1>{{ $device->device_config->location }}</h1>
                        </div>
                        <p class="text-[#979797] text-sm mt-1">{{ $device->today_weather->weather_code->label() }} • {{ $device->today_weather->date->locale('id')->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <img src="{{ asset('images/cerah_berawan.svg') }}" alt="">
                </div>
                <div class="flex items-center gap-3 mt-3">
                    <div class="flex flex-col items-center gap-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M12 2v8M4.93 10.93l1.41 1.41M2 18h2M20 18h2M17.66 12.34l1.41-1.41M22 22H2M16 6l-4-4-4 4" />
                            <path d="M12 10a4 4 0 0 1 4 4H8a4 4 0 0 1 4-4Z" />
                        </svg>
                        <span class="text-[11px] text-zinc-400">{{ $device->today_weather->sunrise->format('H:i') }}</span>
                    </div>
                    <div class="flex-1 flex items-center">
                        <div
                            class="flex-1 h-px bg-[repeating-linear-gradient(to_right,#434343_0px,#434343_8px,transparent_8px,transparent_12px)]">
                        </div>

                        <div class="flex flex-col items-center mx-2">
                            <span class="text-[10px] text-white">{{ $device->today_weather->sun_duration_h }}j {{ $device->today_weather->sun_duration_m }}m</span>
                        </div>

                        <div
                            class="flex-1 h-px bg-[repeating-linear-gradient(to_right,#434343_0px,#434343_8px,transparent_8px,transparent_12px)]">
                        </div>
                    </div>
                    <div class="flex flex-col items-center gap-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M12 10V2M4.93 10.93l1.41 1.41M2 18h2M20 18h2M17.66 12.34l1.41-1.41M22 22H2M16 18l-4 4-4-4" />
                            <path d="M12 14a4 4 0 0 1 4 4H8a4 4 0 0 1 4-4Z" />
                        </svg>
                        <span class="text-[11px] text-zinc-400">{{ $device->today_weather->sunset->format('H:i') }}</span>
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
                            <h1>{{ $device->today_weather->precipitation_probability_mean }} %</h1>
                        </div>
                        <h1>Kelembapan</h1>
                        <div class="flex items-center gap-2 text-[#979797]">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.5 15.8333C8.24022 15.8333 7.03204 15.3328 6.14124 14.442C5.25045 13.5513 4.75 12.3431 4.75 11.0833C4.75 7.91663 9.5 2.57288 9.5 2.57288C9.5 2.57288 14.25 7.91663 14.25 11.0833C14.25 12.3431 13.7496 13.5513 12.8588 14.442C11.968 15.3328 10.7598 15.8333 9.5 15.8333Z" fill="#979797" />
                            </svg>
                            <h1>{{ $device->today_weather->relative_humidity_mean }} %</h1>
                        </div>
                    </div>
                    <div>
                        <h1>Tekanan</h1>
                        <div class="flex items-center gap-2 text-[#979797] mb-5">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.877 5.847L11.857 6.867C11.7633 6.96089 11.636 7.01368 11.5034 7.01378C11.3707 7.01387 11.2434 6.96125 11.1495 6.8675C11.0556 6.77375 11.0028 6.64654 11.0027 6.51385C11.0026 6.38117 11.0553 6.25389 11.149 6.16L12.249 5.061C12.199 5.00767 12.1483 4.95533 12.097 4.904C11.0016 3.80464 9.54755 3.13563 8 3.019V4.5C8 4.63261 7.94733 4.75979 7.85356 4.85355C7.75979 4.94732 7.63261 5 7.50001 5C7.3674 5 7.24022 4.94732 7.14645 4.85355C7.05268 4.75979 7.00001 4.63261 7.00001 4.5V3.019C5.37119 3.14157 3.84901 3.87569 2.73901 5.074L3.80901 6.145C3.89645 6.23987 3.94383 6.36487 3.94125 6.49387C3.93867 6.62287 3.88633 6.74587 3.79516 6.83717C3.70399 6.92847 3.58106 6.98099 3.45206 6.98375C3.32307 6.98652 3.198 6.93931 3.103 6.852L2.113 5.862C1.28836 7.07681 0.902143 8.53631 1.01801 10H2.50001C2.63261 10 2.75979 10.0527 2.85356 10.1464C2.94733 10.2402 3.00001 10.3674 3.00001 10.5C3.00001 10.6326 2.94733 10.7598 2.85356 10.8536C2.75979 10.9473 2.63261 11 2.50001 11H1.17401C1.25734 11.3533 1.36967 11.6967 1.51101 12.03C1.53661 12.0905 1.55005 12.1555 1.55054 12.2213C1.55103 12.287 1.53857 12.3522 1.51387 12.4131C1.48917 12.474 1.45271 12.5295 1.40658 12.5763C1.36045 12.6231 1.30554 12.6604 1.24501 12.686C1.18447 12.7116 1.11948 12.725 1.05374 12.7255C0.988014 12.726 0.92283 12.7136 0.861915 12.6889C0.800999 12.6642 0.745546 12.6277 0.69872 12.5816C0.651894 12.5354 0.614613 12.4805 0.589005 12.42C0.19941 11.4958 -0.000872503 10.5029 5.04216e-06 9.5C-0.00113885 8.51479 0.192359 7.53905 0.569386 6.62883C0.946414 5.71861 1.49954 4.89184 2.19701 4.196C3.60357 2.78981 5.51109 1.99989 7.50001 2C8.48506 1.99995 9.46047 2.19394 10.3705 2.57091C11.2806 2.94787 12.1075 3.50042 12.804 4.197C13.5012 4.8928 14.054 5.71945 14.4309 6.62948C14.8077 7.53952 15.0011 8.51502 15 9.5C15.0006 10.503 14.7999 11.4959 14.41 12.42C14.355 12.537 14.2568 12.6281 14.1361 12.6742C14.0153 12.7204 13.8814 12.718 13.7624 12.6675C13.6434 12.6171 13.5486 12.5225 13.4978 12.4036C13.447 12.2848 13.4442 12.1509 13.49 12.03C13.63 11.6967 13.742 11.3533 13.826 11H12.5C12.3674 11 12.2402 10.9473 12.1465 10.8536C12.0527 10.7598 12 10.6326 12 10.5C12 10.3674 12.0527 10.2402 12.1465 10.1464C12.2402 10.0527 12.3674 10 12.5 10H13.981C14.0961 8.53039 13.7067 7.06546 12.877 5.847ZM6.83601 11.164C6.64855 10.9791 6.54218 10.7274 6.54031 10.4641C6.53843 10.2008 6.6412 9.94753 6.82601 9.76C7.21001 9.375 9.708 7.758 9.97501 8.025C10.242 8.292 8.625 10.79 8.24 11.175C8.05247 11.3598 7.79922 11.4626 7.53593 11.4607C7.27265 11.4588 7.02089 11.3525 6.83601 11.165" fill="#979797" />
                            </svg>
                            <h1>{{ $device->today_weather->relative_humidity_mean }} bar</h1>
                        </div>
                        <h1>Angin</h1>
                        <div class="flex items-center gap-2 text-[#979797]">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.4709 5.54163C15.6607 5.54163 16.625 6.42829 16.625 7.52079C16.625 8.61329 15.6607 9.49996 14.4709 9.49996H2.375M14.2009 15.8333C15.1026 15.8333 16.2292 15.4375 16.2292 13.8541C16.2292 12.2708 15.1026 11.875 14.2009 11.875H2.375M8.24283 3.16663C9.37492 3.16663 10.2917 4.05329 10.2917 5.14579C10.2917 6.23829 9.37412 7.12496 8.24283 7.12496H2.375" stroke="#979797" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <h1>{{ $device->today_weather->wind_speed_mean }} m/s</h1>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex">
                        <h1 class="text-4xl">{{ $device->today_weather->temperature_mean }}</h1>
                        <p>°C</p>
                    </div>
                    <p class="text-[#979797] text-sm">Terasa: {{ $device->today_weather->apparent_temperature_mean }} °C</p>
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

            <div id="map" class="rounded-lg h-[280px] lg:h-full lg:min-h-[360px]"></div>

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
            <div class="flex justify-between items-center">
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
                <button id="btn-open-carbon-form" type="button" class="p-3 rounded-full hover:bg-[#121212]">
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
                <h1>{{ round($latest['Energi'] * auth()->user()->carbon_factor / 1000, 1) }} kg CO²</h1>
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

    <x-footer :devices=$devices />

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

    const carbonFormCloseBtn = document.getElementById('btn-close-carbon-form');
    const carbonFormOpenBtn = document.getElementById('btn-open-carbon-form');
    const carbonForm = document.getElementById('carbon-form');

    carbonFormCloseBtn.addEventListener('click', function() {
        carbonForm.classList.add('hidden');
        overlay.classList.add('hidden');
    });

    carbonFormOpenBtn.addEventListener('click', function() {
        carbonForm.classList.remove('hidden');
        overlay.classList.remove('hidden');
    });

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

        carbonForm.classList.add('hidden');
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
<script>
    (function() {
        const step = 0.01;
        const min = 0;
        const max = 10;

        const inputEl = document.getElementById('koef-value');
        const minusBtn = document.getElementById('koef-minus');
        const plusBtn = document.getElementById('koef-plus');

        let value = parseFloat(inputEl.value) || 0;

        function clamp(v) {
            return Math.min(max, Math.max(min, v));
        }

        function render() {
            inputEl.value = value.toFixed(2);
        }

        minusBtn.addEventListener('click', function() {
            value = clamp(value - step);
            render();
        });

        plusBtn.addEventListener('click', function() {
            value = clamp(value + step);
            render();
        });

        // Izinkan user mengetik bebas (termasuk koma sebagai desimal)
        inputEl.addEventListener('input', function() {
            inputEl.value = inputEl.value.replace(/,/g, '.').replace(/[^0-9.]/g, '');
        });

        // Rapikan dan validasi nilai saat user selesai mengetik
        inputEl.addEventListener('blur', function() {
            const parsed = parseFloat(inputEl.value);
            value = clamp(isNaN(parsed) ? 0 : parsed);
            render();
        });

        // Enter juga memicu validasi tanpa submit form
        inputEl.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                inputEl.blur();
            }
        });

        render();
    })();
</script>

@stack('scripts')

</html>