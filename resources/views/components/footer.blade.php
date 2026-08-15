@props([
'devices'
])

<footer class="mt-12">
    <div class="flex flex-col md:flex-row md:place-content-between items-center md:items-center gap-6 md:gap-4 text-[#C4C4C4] text-center md:text-left">
        <div class="flex items-center gap-4 text-xl sm:text-2xl">
            <img src="{{ asset('images/ecolume-logo.svg') }}" alt="" class="w-7 h-7 sm:w-auto sm:h-auto">
            <h1>Ecolume</h1>
        </div>

        <div class="flex flex-wrap justify-center items-center gap-x-6 gap-y-2 sm:gap-x-8 text-sm sm:text-base">
            <a href="{{ route('beranda') }}" class="hover:underline">Beranda</a>
            @php
            $firstDevice = $devices?->first();
            @endphp

            @if($firstDevice)
            <a href="{{ route('dashboard', ['device_name' => $firstDevice->device_name, 'device_id' => $firstDevice->id]) }}" class="hover:underline">Dashboard</a>
            <a href="{{ route('ramalan-cuaca', ['device_name' => $firstDevice->device_name, 'device_id' => $firstDevice->id]) }}" class="hover:underline">Ramalan Cuaca</a>
            @endif
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