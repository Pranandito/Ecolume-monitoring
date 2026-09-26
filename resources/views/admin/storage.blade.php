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
    </style>
</head>

@if(session('status'))
<div id="claim-popup" class="text-center fixed bottom-0 lg:bottom-1/2 left-1/2 -translate-x-1/2 lg:translate-y-1/2 bg-[#171717] lg:rounded-b-2xl rounded-t-2xl shadow-3xl p-10 w-full lg:max-w-md z-[1112] text-zinc-400">
    <div class="w-9 h-1 bg-zinc-500 rounded-full mx-auto -mt-8 mb-8 lg:hidden"></div>
    @if(session('status') == 'success')
    <img src="{{ asset('images/success.svg') }}" alt="" class="mx-auto">
    @else
    <img src="{{ asset('images/error.svg') }}" alt="" class="mx-auto">
    @endif

    <h1 class="text-2xl text-white mt-5">{{ session('title') }}</h1>
    <p class="text-sm w-68 mx-auto mt-2">{{ session('desc') }}</p>
    <button type="button" id="close-claim-status-popup"
        class="mt-6 w-full text-sm rounded-full border border-zinc-700 py-1 text-[#979797] hover:bg-zinc-800 transition">
        Tutup
    </button>
</div>
<section id="overlay-claim-popup" class="fixed top-0 bottom-0 left-0 right-0 bg-black/50 z-[1111]"></section>

<script>
    const claimPopup = document.getElementById('claim-popup');
    const overlayPopup = document.getElementById('overlay-claim-popup');
    const claimPopupCloseBtn = document.getElementById('close-claim-status-popup');

    claimPopupCloseBtn.addEventListener('click', function() {
        claimPopup.classList.add('hidden');
        overlayPopup.classList.add('hidden');
    });

    overlayPopup.addEventListener('click', function() {
        claimPopup.classList.add('hidden');
        overlayPopup.classList.add('hidden');
    });
</script>
@endif

<div id="addFolder-Card" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[#171717] rounded-2xl shadow-3xl p-6 w-full max-w-[350px] lg:max-w-md z-[1113] text-zinc-400 max-h-[90vh] overflow-y-auto">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300 shrink-0">
                <svg width="22" height="22" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.1875 6.11953H10.1758L7.88477 3.81855C7.85562 3.7899 7.81727 3.7738 7.77734 3.77344H2.8125C2.4668 3.77344 2.1875 4.0667 2.1875 4.42969V16.5703C2.1875 16.9333 2.4668 17.2266 2.8125 17.2266H17.1875C17.5332 17.2266 17.8125 16.9333 17.8125 16.5703V6.77578C17.8125 6.41279 17.5332 6.11953 17.1875 6.11953Z" fill="white" />
                </svg>
            </div>
            <h3 class="text-lg text-white font-medium">Penambahan Folder Gdrive</h3>
        </div>
        <button id="btn-close-addFolder" class="text-zinc-500 hover:text-white transition shrink-0">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.6673 16.6667L3.33398 3.33334M16.6673 3.33334L3.33398 16.6667" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
    </div>

    <!-- STEP 1: Share ke Service Account -->
    <div class="mt-5 rounded-xl bg-[#1f1f1f] border border-zinc-800 p-4">
        <div class="flex items-center gap-2 mb-2">
            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-500/15 text-blue-400 text-xs shrink-0">1</span>
            <p class="text-sm text-zinc-200">Beri akses ke sistem</p>
        </div>
        <p class="text-xs text-zinc-500 mb-3 leading-relaxed">
            Buka folder Google Drive Anda → klik <span class="text-zinc-300">Share</span> → tambahkan email di bawah ini dengan peran <span class="text-zinc-300">Editor</span>.
        </p>

        <div class="flex items-center gap-2 bg-[#262626] border border-zinc-700 rounded-lg px-3 py-2">
            <span id="gdrive-service-email" class="flex-1 text-xs text-zinc-300 truncate select-all">ecolume-laravel-gdrive-bot@avid-folder-448602-f0.iam.gserviceaccount.com</span>
            <button
                type="button"
                onclick="copyServiceAccountEmail(this)"
                class="shrink-0 flex items-center gap-1 text-xs text-zinc-400 hover:text-white bg-zinc-800 hover:bg-zinc-700 rounded-md px-2 py-1 transition">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="9" y="9" width="13" height="13" rx="2" stroke="currentColor" stroke-width="2" />
                    <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" stroke="currentColor" stroke-width="2" />
                </svg>
                <span class="copy-label">Salin</span>
            </button>
        </div>
    </div>

    <!-- STEP 2: Akses link untuk admin lain -->
    <div class="mt-3 rounded-xl bg-[#1f1f1f] border border-zinc-800 p-4">
        <div class="flex items-center gap-2 mb-2">
            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-500/15 text-blue-400 text-xs shrink-0">2</span>
            <p class="text-sm text-zinc-200">Izinkan admin lain melihat isi folder</p>
        </div>
        <p class="text-xs text-zinc-500 leading-relaxed">
            Di <span class="text-zinc-300">General access</span>, ubah ke <span class="text-zinc-300">Anyone with the link</span> (Editor).
        </p>
    </div>

    <!-- STEP 3: Ambil Folder ID -->
    <div class="mt-3 rounded-xl bg-[#1f1f1f] border border-zinc-800 p-4">
        <div class="flex items-center gap-2 mb-2">
            <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-500/15 text-blue-400 text-xs shrink-0">3</span>
            <p class="text-sm text-zinc-200">Ambil ID folder</p>
        </div>
        <ol class="list-decimal list-inside text-xs text-zinc-500 space-y-1 leading-relaxed">
            <li>Buka folder di Google Drive.</li>
            <li>Ambil bagian setelah <span class="text-zinc-300">/folders/</span>.</li>
        </ol>
        <p class="mt-2 text-[11px] text-zinc-600 break-all">
            Contoh: drive.google.com/drive/folders/<span class="text-blue-400">1Hz5sg_lJ4yNoWrpeOXhcOEv9tutZN29</span>
        </p>
    </div>

    <!-- FORM -->
    <form action="{{ route('folder.create') }}" method="POST" class="mt-5">
        @csrf
        <label class="block text-sm text-white mb-2">Masukkan ID Folder</label>
        <input
            type="text"
            name="folder_id"
            placeholder="Contoh: 1Hz5sg_lJ4yNoWrpeOXhcOEv9tutZN29"
            class="w-full rounded-xl border border-zinc-700 bg-[#262626] px-4 py-3 text-white placeholder:text-zinc-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition mb-4"
            required>

        <button type="submit"
            class="w-full text-sm rounded-full bg-blue-600 hover:bg-blue-500 py-2.5 text-white transition">
            Tambahkan Folder
        </button>
    </form>
</div>

<script>
    function copyServiceAccountEmail(btn) {
        const email = document.getElementById('gdrive-service-email').textContent;
        navigator.clipboard.writeText(email).then(() => {
            const label = btn.querySelector('.copy-label');
            const original = label.textContent;
            label.textContent = 'Disalin!';
            setTimeout(() => {
                label.textContent = original;
            }, 1500);
        });
    }
</script>

<div id="delDevConfirm-card" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[#171717] rounded-2xl shadow-3xl p-6 w-full max-w-[350px] lg:max-w-md z-[1113] text-zinc-400">
    <h1 class="text-white text-center">Konfirmasi Penghapusan</h1>
    <p class="text-sm mb-4 mt-2 text-center">
        Apakah Anda yakin ingin menghapus perangkat
    </p>

    <form action="{{ route('folder.delete') }}" method="POST">
        @csrf
        @method('DELETE')

        <input type="hidden" name="id" id="delDevInput">
        <div class="flex gap-3">
            <button type="button" onclick="closeDelFolderConfirmCard()"
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
        Penyimpanan Server
    </x-navbar>
    <hr class="my-5 border-[#373737]">


    <div class="grid lg:grid-cols-5 grid-cols-1 grid-rows-1 gap-6">
        <div class="bg-[#171717] rounded-2xl p-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4C13.0609 4 14.0783 4.42143 14.8284 5.17157C15.5786 5.92172 16 6.93913 16 8C16 9.06087 15.5786 10.0783 14.8284 10.8284C14.0783 11.5786 13.0609 12 12 12C10.9391 12 9.92172 11.5786 9.17157 10.8284C8.42143 10.0783 8 9.06087 8 8C8 6.93913 8.42143 5.92172 9.17157 5.17157C9.92172 4.42143 10.9391 4 12 4ZM12 6C11.4696 6 10.9609 6.21071 10.5858 6.58579C10.2107 6.96086 10 7.46957 10 8C10 8.53043 10.2107 9.03914 10.5858 9.41421C10.9609 9.78929 11.4696 10 12 10C12.5304 10 13.0391 9.78929 13.4142 9.41421C13.7893 9.03914 14 8.53043 14 8C14 7.46957 13.7893 6.96086 13.4142 6.58579C13.0391 6.21071 12.5304 6 12 6ZM12 13C14.67 13 20 14.33 20 17V20H4V17C4 14.33 9.33 13 12 13ZM12 14.9C9.03 14.9 5.9 16.36 5.9 17V18.1H18.1V17C18.1 16.36 14.97 14.9 12 14.9Z" fill="white" />
                    </svg>
                </div>
                <h3 class="text-lg text-white">Jumlah user aktif</h3>
            </div>

            <div class="flex items-baseline gap-1 mt-5">
                <span class="text-4xl text-white">{{ $users->activeThisWeek }}</span>
                <span class="text-sm text-zinc-400">User</span>
            </div>
            <h1 class="text-xs text-zinc-400 mb-5">total: {{ $users->count }} User</h1>

            <div class="flex flex-col gap-1">
                <span class="text-xs text-zinc-400">User yang online hari ini:</span>
                <span class="text-sm text-white">{{ $users->activeToday }} User</span>
            </div>
        </div>
        <div id="mqtt-card" class="bg-[#171717] rounded-2xl p-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M4.93045 4.92901C5.11796 4.74137 5.37233 4.6359 5.6376 4.6358C5.90287 4.63571 6.15731 4.741 6.34495 4.92851C6.53259 5.11601 6.63806 5.37038 6.63816 5.63565C6.63825 5.90092 6.53296 6.15537 6.34545 6.34301C4.84538 7.84327 4.00266 9.87795 4.00266 11.9995C4.00266 14.1211 4.84538 16.1557 6.34545 17.656C6.53309 17.8435 6.63856 18.0979 6.63866 18.3632C6.63875 18.6284 6.53346 18.8829 6.34595 19.0705C6.15844 19.2581 5.90408 19.3636 5.63881 19.3637C5.37354 19.3638 5.11909 19.2585 4.93145 19.071C1.02545 15.166 1.02545 8.83401 4.93145 4.92901M17.6595 4.92901C17.847 4.74153 18.1013 4.63622 18.3665 4.63622C18.6316 4.63622 18.8859 4.74153 19.0735 4.92901C22.9795 8.83401 22.9795 15.166 19.0735 19.071C18.8858 19.2585 18.6314 19.3638 18.3661 19.3637C18.1008 19.3636 17.8465 19.2581 17.659 19.0705C17.4714 18.8829 17.3662 18.6284 17.3663 18.3632C17.3663 18.0979 17.4718 17.8435 17.6595 17.656C19.1595 16.1557 20.0022 14.1211 20.0022 11.9995C20.0022 9.87795 19.1595 7.84327 17.6595 6.34301C17.472 6.15548 17.3667 5.90117 17.3667 5.63601C17.3667 5.37084 17.472 5.11653 17.6595 4.92901ZM7.75945 7.75701C7.94793 7.57471 8.20046 7.47373 8.46266 7.47582C8.72486 7.47792 8.97575 7.58291 9.16129 7.76819C9.34683 7.95346 9.45218 8.20421 9.45464 8.4664C9.4571 8.7286 9.35648 8.98128 9.17445 9.17001C8.803 9.54144 8.50834 9.98241 8.30731 10.4677C8.10628 10.953 8.00281 11.4732 8.00281 11.9985C8.00281 12.5238 8.10628 13.044 8.30731 13.5293C8.50834 14.0146 8.803 14.4556 9.17445 14.827C9.35661 15.0156 9.45741 15.2682 9.45513 15.5304C9.45285 15.7926 9.34768 16.0434 9.16227 16.2288C8.97686 16.4142 8.72605 16.5194 8.46385 16.5217C8.20166 16.524 7.94906 16.4232 7.76045 16.241C6.63532 15.1158 6.00323 13.5897 6.00323 11.9985C6.00323 10.4073 6.63532 8.88121 7.76045 7.75601M14.8315 7.75601C15.019 7.56853 15.2733 7.46322 15.5385 7.46322C15.8036 7.46322 16.0579 7.56853 16.2455 7.75601C17.3706 8.88121 18.0027 10.4073 18.0027 11.9985C18.0027 13.5897 17.3706 15.1158 16.2455 16.241C16.0569 16.4232 15.8042 16.524 15.5421 16.5217C15.2799 16.5194 15.029 16.4142 14.8436 16.2288C14.6582 16.0434 14.5531 15.7926 14.5508 15.5304C14.5485 15.2682 14.6493 15.0156 14.8315 14.827C15.2029 14.4556 15.4976 14.0146 15.6986 13.5293C15.8996 13.044 16.0031 12.5238 16.0031 11.9985C16.0031 11.4732 15.8996 10.953 15.6986 10.4677C15.4976 9.98241 15.2029 9.54144 14.8315 9.17001C14.644 8.98248 14.5387 8.72817 14.5387 8.46301C14.5387 8.19784 14.644 7.94353 14.8315 7.75601ZM12.0005 10.5C12.3983 10.5 12.7798 10.658 13.0611 10.9393C13.3424 11.2206 13.5005 11.6022 13.5005 12C13.5005 12.3978 13.3424 12.7794 13.0611 13.0607C12.7798 13.342 12.3983 13.5 12.0005 13.5C11.6026 13.5 11.2211 13.342 10.9398 13.0607C10.6585 12.7794 10.5005 12.3978 10.5005 12C10.5005 11.6022 10.6585 11.2206 10.9398 10.9393C11.2211 10.658 11.6026 10.5 12.0005 10.5Z"
                            fill="white" />
                    </svg>
                </div>
                <h3 class="text-lg text-white">Status MQTT</h3>
            </div>

            <div class="flex items-baseline gap-1 my-7">
                <span id="mqtt-status" class="text-4xl text-white animate-pulse">...</span>
            </div>

            <div class="flex flex-col gap-1">
                <span class="text-xs text-zinc-400">Port ngrok:</span>
                <span id="mqtt-port" class="text-sm text-white animate-pulse">...</span>
            </div>
        </div>
        <div id="storage-card" class="bg-[#171717] rounded-2xl p-6 lg:col-span-3">
            <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-neutral-300 shrink-0" width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.98438 4.98438V24.0156H24.0156V10.4219L18.5781 4.98438H4.98438Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M10.4219 24.0156V17.6719H18.5781V24.0156" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <h1 class="text-white text-lg">Penyimpanan Server</h1>
                </div>
                <div class="text-sm text-neutral-500 whitespace-nowrap">
                    <span id="disk-used" class="text-neutral-400 animate-pulse">...</span> /
                    <span id="disk-total" class="animate-pulse">...</span> GB
                    <span id="disk-used-pct" class="text-neutral-300 font-medium ml-1 animate-pulse">...</span>
                </div>
            </div>

            <div class="flex w-full h-2 rounded-full overflow-hidden bg-neutral-800 mb-4">
                <div id="bar-influxdb" class="bg-red-500" style="width: 0%"></div>
                <div id="bar-sql" class="bg-yellow-400" style="width: 0%"></div>
                <div id="bar-other" class="bg-blue-500" style="width: 0%"></div>
                <div id="bar-other-project" class="bg-green-500 rounded-r-full" style="width: 0%"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-5">
                <div class="space-y-7">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-500 shrink-0"></span>
                                <span class="text-neutral-200 text-sm">Influxdb</span>
                            </div>
                            <span class="text-neutral-500 text-sm whitespace-nowrap">
                                <span id="influxdb-size" class="animate-pulse">...</span> GB
                                <span id="influxdb-pct" class="text-neutral-600 ml-1 animate-pulse">...</span>
                            </span>
                        </div>
                        <div class="h-1.5 rounded-full bg-neutral-800 overflow-hidden">
                            <div id="bar-influxdb-mini" class="h-full bg-neutral-500 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 shrink-0"></span>
                                <span class="text-neutral-200 text-sm">MySQL</span>
                            </div>
                            <span class="text-neutral-500 text-sm whitespace-nowrap">
                                <span id="sql-size" class="animate-pulse">...</span> GB
                                <span id="sql-pct" class="text-neutral-600 ml-1 animate-pulse">...</span>
                            </span>
                        </div>
                        <div class="h-1.5 rounded-full bg-neutral-800 overflow-hidden">
                            <div id="bar-sql-mini" class="h-full bg-neutral-500 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <div class="space-y-7">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                                <span class="text-neutral-200 text-sm">Lainnya</span>
                            </div>
                            <span class="text-neutral-500 text-sm whitespace-nowrap">
                                <span id="other-size" class="animate-pulse">...</span> GB
                                <span id="other-pct" class="text-neutral-600 ml-1 animate-pulse">...</span>
                            </span>
                        </div>
                        <div class="h-1.5 rounded-full bg-neutral-800 overflow-hidden">
                            <div id="bar-other-mini" class="h-full bg-neutral-500 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-green-500 shrink-0"></span>
                                <span class="text-neutral-200 text-sm">Projek lain</span>
                            </div>
                            <span class="text-neutral-500 text-sm whitespace-nowrap">
                                <span id="other-project-size" class="animate-pulse">...</span> GB
                                <span id="other-project-pct" class="text-neutral-600 ml-1 animate-pulse">...</span>
                            </span>
                        </div>
                        <div class="h-1.5 rounded-full bg-neutral-800 overflow-hidden">
                            <div id="bar-other-project-mini" class="h-full bg-neutral-500 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col justify-between gap-4 sm:pl-2">
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-neutral-500">Total Project</span>
                            <span class="text-white whitespace-nowrap">
                                <span id="project-size" class="animate-pulse">...</span> GB
                                <span id="project-pct" class="text-neutral-500 ml-1 animate-pulse">...</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-neutral-500">Status penyimpanan</span>
                            <span id="storage-status" class="text-neutral-100 font-medium whitespace-nowrap animate-pulse">...</span>
                        </div>
                    </div>
                    <button type="button" class="w-full flex items-center justify-center gap-2 bg-neutral-800 hover:bg-neutral-700 transition-colors text-neutral-200 text-sm rounded-xl py-2.5 px-4">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 3v12m0 0l-4-4m4 4l4-4" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4 15v4a1 1 0 001 1h14a1 1 0 001-1v-4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Archive Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between mt-6 mb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                <svg width="22" height="22" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.1875 6.11953H10.1758L7.88477 3.81855C7.85562 3.7899 7.81727 3.7738 7.77734 3.77344H2.8125C2.4668 3.77344 2.1875 4.0667 2.1875 4.42969V16.5703C2.1875 16.9333 2.4668 17.2266 2.8125 17.2266H17.1875C17.5332 17.2266 17.8125 16.9333 17.8125 16.5703V6.77578C17.8125 6.41279 17.5332 6.11953 17.1875 6.11953Z" fill="white" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg text-white">Daftar Folder <span class="hidden lg:inline-block">Archive</span></h3>
                <p class="text-xs text-[#979797] hidden lg:block">Archive akan dilakukan pada folder teratas, pastikan akun gdrive memiliki space yang memadai</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button id="btn-open-addFolder" class="disabled:cursor-not-allowed" @disabled($folders->count() == 5)>
                <div class="py-2 px-2 lg:px-5 bg-[#262626] rounded-full flex items-center gap-2 text-white">
                    <h1 class="text-sm hidden lg:block">Tambahkan Folder</h1>
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
                    <th class="px-6 py-4 font-medium">No</th>
                    <th class="px-6 py-4 font-medium">Folder_id</th>
                    <th class="px-6 py-4 font-medium">Nama Folder</th>
                    <th class="px-6 py-4 font-medium">Akses</th>
                    <th class="px-6 py-4 font-medium">Owner</th>
                    <th class="px-6 py-4 font-medium"></th>
                    <!-- <th class="px-6 py-4 font-medium sticky right-0 bg-[#171717] z-10"></th> -->
                </tr>
            </thead>
            <tbody id="table-body" class="text-sm text-[#979797] divide-y divide-white/10">
                @forelse($folders as $folder)
                <tr class="hover:bg-white/[0.03] transition">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $loop->iteration }}</td>
                    <td class=" px-6 py-4 whitespace-nowrap group">
                        <a href="https://drive.google.com/drive/folders/{{ $folder->folder_id }}" class="group-hover:underline" target="_blank" rel="noopener noreferrer">
                            {{$folder->folder_id}} <span class="group-hover:text-[#979797] text-[#171717]">↗</span>
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-white">{{ $folder->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $folder->permision }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{$folder->owner}}</td>
                    <td class="px-2 py-1 whitespace-nowrap">
                        <button type="button" data-id-folder="{{ $folder->id }}"
                            class="btnOpenDelFolder px-3 py-3 text-sm text-red-400 hover:bg-red-400/10 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                <line x1="10" y1="11" x2="10" y2="17" />
                                <line x1="14" y1="11" x2="14" y2="17" />
                            </svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        Tambahkan direktori folder google drive untuk backup data influxdb
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
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

    <script>
        fetchStorageMetrics();
        fetchMqttMetrics();

        function fetchStorageMetrics() {
            fetch("{{ route('admin.storage.metrics') }}")
                .then(res => res.json())
                .then(({
                    size
                }) => renderStorageMetrics(size))
                .catch(() => {
                    document.querySelectorAll('#storage-card .animate-pulse')
                        .forEach(el => el.textContent = '—');
                });
        }

        function fetchMqttMetrics() {
            fetch("{{ route('admin.mqtt.metrics') }}")
                .then(res => res.json())
                .then(mqtt => renderMqttMetrics(mqtt))
                .catch(() => {
                    document.querySelectorAll('#mqtt-card .animate-pulse')
                        .forEach(el => el.textContent = '—');
                });
        }

        function setText(id, val) {
            const el = document.getElementById(id);
            el.textContent = val;
            el.classList.remove('animate-pulse');
        }

        function setWidth(id, pct) {
            document.getElementById(id).style.width = pct + '%';
        }

        function renderMqttMetrics(mqtt) {
            setText('mqtt-status', mqtt.status ?? 'Offline');
            setText('mqtt-port', mqtt.ngrokPort ?? '-');
        }

        function renderStorageMetrics(size) {
            // Header disk
            setText('disk-used', size.usedDisk);
            setText('disk-total', size.totalDisk);
            setText('disk-used-pct', size.usedDiskPct + '%');

            // Bar utama
            setWidth('bar-influxdb', size.influxdbPct);
            setWidth('bar-sql', size.sqlPct);
            setWidth('bar-other', size.otherPct);
            setWidth('bar-other-project', size.otherProjectPct);

            // Influxdb
            setText('influxdb-size', size.influxdb);
            setText('influxdb-pct', size.influxdbPct + '%');
            setWidth('bar-influxdb-mini', size.influxdbPct);

            // MySQL
            setText('sql-size', size.sql);
            setText('sql-pct', size.sqlPct + '%');
            setWidth('bar-sql-mini', size.sqlPct);

            // Lainnya
            setText('other-size', size.other);
            setText('other-pct', size.otherPct + '%');
            setWidth('bar-other-mini', size.otherPct);

            // Projek lain
            setText('other-project-size', size.otherProject);
            setText('other-project-pct', size.otherProjectPct + '%');
            setWidth('bar-other-project-mini', size.otherProjectPct);

            // Total
            setText('project-size', size.project);
            setText('project-pct', size.projectPct + '%');
            setText('storage-status', size.status);
        }
    </script>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const btnOpenSidebar = document.getElementById('btn-open-sidebar');
        const btnCloseSidebar = document.getElementById('btn-close-sidebar');

        const btnOpenAddFolder = document.getElementById('btn-open-addFolder');
        const btnCloseAddFolder = document.getElementById('btn-close-addFolder');
        const addFolderCard = document.getElementById('addFolder-Card');

        const deleteDeviceConfirmCard = document.getElementById('delDevConfirm-card');
        const btnOpenDelFolder = document.getElementById('btnOpenDelFolder');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
        }

        function closeCard() {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('hidden');

            addFolderCard.classList.add('hidden');
            deleteDeviceConfirmCard.classList.add('hidden');
        }

        function openAddFolderCard() {
            addFolderCard.classList.remove('hidden');
            overlay.classList.remove('hidden');
        }

        function closeAddFolderCard() {
            addFolderCard.classList.add('hidden');
            overlay.classList.add('hidden');
        }

        function openDelFolderConfirmCard() {
            deleteDeviceConfirmCard.classList.remove('hidden');
            overlay.classList.remove('hidden');
        }

        function closeDelFolderConfirmCard() {
            deleteDeviceConfirmCard.classList.add('hidden');
            overlay.classList.add('hidden');
        }

        btnOpenSidebar.addEventListener('click', openSidebar);
        btnCloseSidebar.addEventListener('click', closeCard);

        btnOpenAddFolder.addEventListener('click', openAddFolderCard);
        btnCloseAddFolder.addEventListener('click', closeAddFolderCard);

        overlay.addEventListener('click', closeCard);

        document.querySelectorAll('.btnOpenDelFolder').forEach(button => {
            button.addEventListener('click', function() {
                const idFolder = this.dataset.idFolder;
                document.getElementById('delDevInput').value = idFolder;
                openDelFolderConfirmCard();
            });
        });
    </script>
</body>