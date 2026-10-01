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

<div id="addFirmware-Card" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[#171717] rounded-2xl shadow-3xl p-6 w-full max-w-[350px] lg:max-w-md z-[1113] text-zinc-400 max-h-[90vh] overflow-y-auto">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300 shrink-0">
                <svg width="22" height="22" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.1875 6.11953H10.1758L7.88477 3.81855C7.85562 3.7899 7.81727 3.7738 7.77734 3.77344H2.8125C2.4668 3.77344 2.1875 4.0667 2.1875 4.42969V16.5703C2.1875 16.9333 2.4668 17.2266 2.8125 17.2266H17.1875C17.5332 17.2266 17.8125 16.9333 17.8125 16.5703V6.77578C17.8125 6.41279 17.5332 6.11953 17.1875 6.11953Z" fill="white" />
                </svg>
            </div>
            <h3 class="text-lg text-white font-medium">Penambahan Arsip Firmware</h3>
        </div>
        <button id="btn-close-addFirmware" class="text-zinc-500 hover:text-white transition shrink-0">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.6673 16.6667L3.33398 3.33334M16.6673 3.33334L3.33398 16.6667" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
    </div>

    <form action="{{ route('admin.ota-firmware.create') }}" method="POST" enctype="multipart/form-data"
        class="mt-5">
        @csrf
        <label class="block text-base text-white">Masukkan ID Folder</label>
        <label class="block text-sm mb-2">Gunakan format Mayor . Minor . Patch (contoh: 1.0.0)</label>
        <input
            type="text"
            name="firmware_ver"
            placeholder="Contoh: 1.0.0"
            class="w-full rounded-xl border border-zinc-700 bg-[#262626] px-4 py-3 text-white placeholder:text-zinc-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition mb-4"
            required>

        <div class="">
            <input
                type="file"
                id="firmware"
                name="firmware"
                accept=".bin"
                class="hidden">
            <label
                for="firmware"
                id="dropZone"
                class="flex h-[247px] cursor-pointer flex-col items-center justify-center rounded-xl border border-zinc-700 bg-[#292929] transition hover:bg-[#303030]">
                <svg
                    id="uploadIcon"
                    class="mb-5 h-8 w-8 text-white"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" />
                </svg>

                <p
                    id="dropText"
                    class="text-[#a0a0a0]">
                    Tarik dan letakkan dokumen Anda di sini
                </p>
                <p
                    id="fileText"
                    class="mt-1 text-[#3B82F6]">
                    Klik untuk mencari dokumen
                </p>
            </label>
        </div>
        <div class="mt-1 mb-3 flex justify-between px-2 text-sm text-zinc-600">
            <span class="">
                Format file : .bin
            </span>

            <span class="">
                Maximum size : 5 MB
            </span>
        </div>

        <p
            id="fileError"
            class="mt-1 hidden px-2 text-sm text-red-400"></p>

        <div class="flex gap-3 justify-end">
            <button type="button" onclick="closeAddFirmwareCard()"
                class="block text-center w-fit text-sm rounded-xl border border-zinc-700 py-2 px-6 text-[#979797] hover:bg-zinc-800 transition">
                Batalkan
            </button>
            <button type="submit" class="w-fit flex items-center justify-center gap-2 bg-neutral-800 hover:bg-neutral-700 transition-colors text-neutral-200 text-sm rounded-xl py-2 px-6">
                Tambahkan
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const card = document.getElementById('addFirmware-Card');
        const form = card.querySelector('form');
        const verInput = form.querySelector('input[name="firmware_ver"]');
        const fileInput = document.getElementById('firmware');
        const dropZone = document.getElementById('dropZone');
        const dropText = document.getElementById('dropText');
        const fileText = document.getElementById('fileText');
        const fileError = document.getElementById('fileError');
        const submitBtn = form.querySelector('button[type="submit"]');

        const MAX_SIZE = 5 * 1024 * 1024; // 5 MB
        const VER_REGEX = /^\d+\.\d+\.\d+$/;
        const DEFAULT_DROP_TEXT = dropText.textContent.trim();
        const DEFAULT_FILE_TEXT = fileText.textContent.trim();

        // ---------- Helpers ----------
        const showError = (msg) => {
            fileError.textContent = msg;
            fileError.classList.remove('hidden');
        };

        const clearError = () => {
            fileError.textContent = '';
            fileError.classList.add('hidden');
        };

        const formatSize = (bytes) => {
            if (bytes < 1024) return `${bytes} B`;
            if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
            return `${(bytes / 1024 / 1024).toFixed(2)} MB`;
        };

        const resetFileUI = () => {
            fileInput.value = '';
            dropText.textContent = DEFAULT_DROP_TEXT;
            fileText.textContent = DEFAULT_FILE_TEXT;
        };

        const validateFile = (file) => {
            if (!file.name.toLowerCase().endsWith('.bin')) {
                return 'Format file harus .bin';
            }
            if (file.size === 0) {
                return 'File kosong';
            }
            if (file.size > MAX_SIZE) {
                return `Ukuran file maksimal 5 MB (file Anda ${formatSize(file.size)})`;
            }
            return null;
        };

        const handleFile = (file) => {
            clearError();
            if (!file) return resetFileUI();

            const err = validateFile(file);
            if (err) {
                resetFileUI();
                showError(err);
                return;
            }

            dropText.textContent = file.name;
            fileText.textContent = formatSize(file.size);
        };

        // ---------- File input ----------
        fileInput.addEventListener('change', () => {
            handleFile(fileInput.files[0]);
        });

        // ---------- Drag & drop ----------
        ['dragenter', 'dragover'].forEach(evt =>
            dropZone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropZone.classList.add('border-blue-500', 'bg-[#303030]');
            })
        );

        ['dragleave', 'drop'].forEach(evt =>
            dropZone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-blue-500', 'bg-[#303030]');
            })
        );

        dropZone.addEventListener('drop', (e) => {
            const file = e.dataTransfer.files[0];
            if (!file) return;

            // masukkan file hasil drop ke input agar ikut terkirim saat submit
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            handleFile(file);
        });

        // ---------- Validasi sebelum submit native ----------
        form.addEventListener('submit', (e) => {
            clearError();

            const version = verInput.value.trim();
            const file = fileInput.files[0];

            if (!VER_REGEX.test(version)) {
                e.preventDefault();
                return showError('Format versi tidak valid. Gunakan Mayor.Minor.Patch (contoh: 1.0.0)');
            }
            if (!file) {
                e.preventDefault();
                return showError('Silakan pilih file firmware (.bin)');
            }
            const fileErr = validateFile(file);
            if (fileErr) {
                e.preventDefault();
                return showError(fileErr);
            }

            // lolos validasi: cegah double submit, lalu biarkan browser mengirim form
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
            submitBtn.textContent = 'Mengunggah...';
        });
    });
</script>

<section id="overlay" class="hidden fixed top-0 bottom-0 left-0 right-0 bg-black/50 z-[1112]"></section>

<x-sidebar />

<body class="lg:py-12 py-6 lg:mx-16 mx-3 text-white relative min-h-screen">
    <x-navbar>
        OTA Firmware <span class="lg:inline-block hidden">Update</span>
        <h1 class="font-medium text-sm px-3 py-0.5 rounded-full text-neutral-200 bg-neutral-800">BETA</h1>
    </x-navbar>
    <hr class="my-5 border-[#373737]">

    <main class="">
        <div class="flex items-center justify-between mt-6 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                    <svg width="22" height="22" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.1875 6.11953H10.1758L7.88477 3.81855C7.85562 3.7899 7.81727 3.7738 7.77734 3.77344H2.8125C2.4668 3.77344 2.1875 4.0667 2.1875 4.42969V16.5703C2.1875 16.9333 2.4668 17.2266 2.8125 17.2266H17.1875C17.5332 17.2266 17.8125 16.9333 17.8125 16.5703V6.77578C17.8125 6.41279 17.5332 6.11953 17.1875 6.11953Z" fill="white" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg text-white">Daftar Arsip <span class="hidden lg:inline-block">Firmware</span></h3>
                    <p class="text-xs text-[#979797] hidden lg:block"> Daftar firmware ESP32 untuk OTA broadcast, sekaligus arsip versi firmware yang telah dikembangkan</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button id="btn-open-addFirmware">
                    <div class="py-2 px-2 lg:px-5 bg-[#262626] rounded-full flex items-center gap-2 text-white">
                        <h1 class="text-sm hidden lg:block">Tambahkan File</h1>
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
                        <th class="px-6 py-4 font-medium">Nama File</th>
                        <th class="px-6 py-4 font-medium">Versi</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Pengunggah</th>
                        <th class="px-6 py-4 font-medium">Size</th>
                        <th class="px-6 py-4 font-medium">Ditambahkan Pada</th>
                        <th class="px-6 py-4 font-medium"></th>
                    </tr>
                </thead>
                <tbody id="table-body" class="text-sm text-[#979797] divide-y divide-white/10">
                    @forelse($firmwares as $firmware)
                    <tr class="hover:bg-white/[0.03] transition">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $loop->iteration }}</td>
                        <td class=" px-6 py-4 whitespace-nowrap group">
                            <a href="" class="group-hover:underline" target="_blank" rel="noopener noreferrer">
                                {{$firmware->file_name}} <span class="group-hover:text-[#979797] text-[#171717]">↗</span>
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-white">{{ $firmware->firmware_ver }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium {{ $firmware->is_prod ? 'bg-green-500/5 text-[#00A451]' : 'bg-[#828282]/5 text-[#828282]' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $firmware->is_prod ? 'bg-[#00A451]' : 'bg-[#828282]' }}"></span>
                                {{ $firmware->is_prod ? 'Prod' : 'Archive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $firmware->uploader }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-white">{{ round($firmware->size/1000000, 2) }} MB</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $firmware->created_at }}</td>
                        <td class="px-2 py-1 whitespace-nowrap">
                            <form action="{{ route('admin.ota-firmware.broadcast', $firmware->id) }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-neutral-800 hover:bg-neutral-700 transition-colors text-neutral-200 text-sm rounded-xl py-2">
                                    <svg width="18" height="18" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.57994 5.82705C6.57044 5.81492 6.55831 5.8051 6.54445 5.79835C6.53059 5.79159 6.51538 5.78809 6.49996 5.78809C6.48455 5.78809 6.46934 5.79159 6.45548 5.79835C6.44162 5.8051 6.42948 5.81492 6.41998 5.82705L4.99811 7.62598C4.98639 7.64095 4.97911 7.65891 4.97712 7.67782C4.97513 7.69673 4.9785 7.71582 4.98684 7.7329C4.99518 7.74998 5.00816 7.76437 5.0243 7.77443C5.04044 7.78448 5.05908 7.78979 5.07809 7.78975H6.01627V10.8671C6.01627 10.923 6.06198 10.9687 6.11784 10.9687H6.87955C6.93541 10.9687 6.98112 10.923 6.98112 10.8671V7.79102H7.92184C8.0069 7.79102 8.05387 7.69326 8.00182 7.62725L6.57994 5.82705Z"
                                            fill="#949494" />
                                        <path d="M10.301 4.65537C9.71953 3.12178 8.23799 2.03125 6.50254 2.03125C4.76709 2.03125 3.28555 3.12051 2.7041 4.6541C1.61611 4.93975 0.8125 5.93125 0.8125 7.10938C0.8125 8.51221 1.94873 9.64844 3.35029 9.64844H3.85938C3.91523 9.64844 3.96094 9.60273 3.96094 9.54688V8.78516C3.96094 8.7293 3.91523 8.68359 3.85938 8.68359H3.35029C2.92246 8.68359 2.52002 8.51348 2.22041 8.20498C1.92207 7.89775 1.76338 7.48389 1.77734 7.05479C1.78877 6.71963 1.90303 6.40479 2.10996 6.13945C2.32197 5.86904 2.61904 5.67227 2.94912 5.58467L3.43027 5.45898L3.60674 4.99434C3.71592 4.70488 3.86826 4.43447 4.05996 4.18945C4.24921 3.9466 4.47339 3.73312 4.7252 3.55596C5.24697 3.18906 5.86143 2.99482 6.50254 2.99482C7.14365 2.99482 7.75811 3.18906 8.27988 3.55596C8.53252 3.73369 8.75596 3.94697 8.94512 4.18945C9.13682 4.43447 9.28916 4.70615 9.39834 4.99434L9.57353 5.45771L10.0534 5.58467C10.7415 5.77002 11.2227 6.3959 11.2227 7.10938C11.2227 7.52959 11.0589 7.92568 10.7618 8.22275C10.6161 8.36929 10.4428 8.48548 10.2519 8.56458C10.061 8.64368 9.85634 8.68414 9.64971 8.68359H9.14062C9.08477 8.68359 9.03906 8.7293 9.03906 8.78516V9.54688C9.03906 9.60273 9.08477 9.64844 9.14062 9.64844H9.64971C11.0513 9.64844 12.1875 8.51221 12.1875 7.10938C12.1875 5.93252 11.3864 4.94229 10.301 4.65537Z" fill="#949494" />
                                    </svg>
                                    Broadcast
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            Tambahkan arsip firmware yang telah dikembangkan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between mt-4 px-2">

            <span class="hidden lg:block"></span>

            <span class="text-xs text-gray-500 self-center">
                {{ $firmwares->firstItem() }}–{{ $firmwares->lastItem() }}
                dari {{ $firmwares->total() }} Firmware
            </span>

            <div class="flex items-center gap-2">

                {{-- Previous --}}
                @if ($firmwares->onFirstPage())
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
                <a href="{{ $firmwares->previousPageUrl() }}"
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
                @if ($firmwares->hasMorePages())
                <a href="{{ $firmwares->nextPageUrl() }}"
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
    </main>

    <footer class="block lg:absolute lg:bottom-10 lg:w-full">
        <div class="flex flex-col md:flex-row md:place-content-between items-center md:items-center gap-6 md:gap-4 text-[#C4C4C4] text-center md:text-left">
            <div class="flex items-center gap-4 text-xl sm:text-2xl">
                <img src="{{ asset('images/ecolume-logo.svg') }}" alt="" class="w-7 h-7 sm:w-auto sm:h-auto">
                <h1>Ecolume</h1>
            </div>

            <div class="flex flex-wrap justify-center items-center gap-x-6 gap-y-2 sm:gap-x-8 text-sm sm:text-base">
                <a href="{{ route('admin.dashboard') }}" class="hover:underline">Dashboard</a>
                <a href="{{ route('admin.storage') }}" class="hover:underline">Penyimpanan</a>
                <a href="{{ route('admin.ota-firmware') }}" class="hover:underline">OTA Firmware</a>
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
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const btnOpenSidebar = document.getElementById('btn-open-sidebar');
        const btnCloseSidebar = document.getElementById('btn-close-sidebar');

        const btnOpenAddFirmware = document.getElementById('btn-open-addFirmware');
        const btnCloseAddFirmware = document.getElementById('btn-close-addFirmware');
        const addFirmwareCard = document.getElementById('addFirmware-Card');

        // const deleteDeviceConfirmCard = document.getElementById('delDevConfirm-card');
        // const btnOpenDelFolder = document.getElementById('btnOpenDelFolder');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
        }

        function closeCard() {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('hidden');

            addFirmwareCard.classList.add('hidden');
            // deleteDeviceConfirmCard.classList.add('hidden');
        }

        function openAddFirmwareCard() {
            addFirmwareCard.classList.remove('hidden');
            overlay.classList.remove('hidden');
        }

        function closeAddFirmwareCard() {
            addFirmwareCard.classList.add('hidden');
            overlay.classList.add('hidden');
        }

        // function openDelFolderConfirmCard() {
        //     deleteDeviceConfirmCard.classList.remove('hidden');
        //     overlay.classList.remove('hidden');
        // }

        // function closeDelFolderConfirmCard() {
        //     deleteDeviceConfirmCard.classList.add('hidden');
        //     overlay.classList.add('hidden');
        // }

        btnOpenSidebar.addEventListener('click', openSidebar);
        btnCloseSidebar.addEventListener('click', closeCard);

        btnOpenAddFirmware.addEventListener('click', openAddFirmwareCard);
        btnCloseAddFirmware.addEventListener('click', closeAddFirmwareCard);

        overlay.addEventListener('click', closeCard);

        // document.querySelectorAll('.btnOpenDelFolder').forEach(button => {
        //     button.addEventListener('click', function() {
        //         const idFolder = this.dataset.idFolder;
        //         document.getElementById('delDevInput').value = idFolder;
        //         openDelFolderConfirmCard();
        //     });
        // });
    </script>
</body>