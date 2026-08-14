@props([
'device',
'field' => 'Debit',
'fullThreshold' => 100, // Debit >= ini dianggap "Operasi Penuh"
'gapMatiMinutes' => 20, // tanpa data selama ini dianggap "Mati"
])

@php
$endpoint = route('dashboard.line-chart', $device->id);
@endphp

<div
    data-pump-usage-bar
    data-config="{{ json_encode([
        'deviceId' => $device->id,
        'endpoint' => $endpoint,
        'field' => $field,
        'range' => '1H',
        'fullThreshold' => $fullThreshold,
        'gapMatiMs' => $gapMatiMinutes * 60 * 1000,
        'refreshIntervalMs' => 30000,
    ]) }}">
    <div class="mb-5">
        <div class="h-3 w-full flex rounded overflow-hidden mb-2.5" data-role="bar"></div>
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
    <div data-role="tooltip" class="hidden" style="position:fixed;z-index:50;background:#2c2c2e;border:1px solid #3f3f46;border-radius:10px;padding:8px 12px;font-size:11px;box-shadow:0 10px 40px rgba(0,0,0,0.4);pointer-events:none;"></div>
</div>

@once
@push('scripts')
<script src="{{ asset('js/pump-usage.js') }}" defer></script>
@endpush
@endonce