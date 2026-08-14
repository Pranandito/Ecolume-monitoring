@props([
'device',
'fields' => ['Debit', 'Daya', 'Suhu', 'Tegangan', 'Energi', 'Volume', 'Durasi_Operasional'],
'defaultFields' => ['Debit', 'Daya'],
])

@php
$fieldMeta = [
'Daya' => ['label' => 'Daya', 'unit' => 'W', 'color' => '#f97316'],
'Debit' => ['label' => 'Debit', 'unit' => 'l/min', 'color' => '#0ea5e9'],
'Durasi_Operasional' => ['label' => 'Durasi Operasional', 'unit' => 'menit', 'color' => '#8b5cf6'],
'Energi' => ['label' => 'Energi', 'unit' => 'Wh', 'color' => '#f59e0b'],
'Suhu' => ['label' => 'Suhu', 'unit' => '°C', 'color' => '#f43f5e'],
'Tegangan' => ['label' => 'Tegangan', 'unit' => 'V', 'color' => '#06b6d4'],
'Volume' => ['label' => 'Volume', 'unit' => 'L', 'color' => '#10b981'],
];

$endpoint = route('dashboard.line-chart', $device->id);
@endphp

<div
    id="chart-card-container"
    class="bg-[#171717] z-[1111] rounded-2xl p-6 lg:col-span-2 flex flex-col h-full"
    data-chart-card
    data-config="{{ json_encode([
        'deviceId' => $device->id,
        'endpoint' => $endpoint,
        'fieldMeta' => $fieldMeta,
        'allFields' => array_values($fields),
        'defaultFields' => array_values($defaultFields),
        'maxVisiblePoints' => 500,
        'refreshIntervalMs' => 30000,
    ]) }}">
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
            <div class="relative w-full lg:w-fit" data-role="dropdown-wrapper">
                <button type="button" data-role="dropdown-btn"
                    class="flex items-center justify-center gap-2 px-5 py-1.5 rounded-lg bg-[#171717] border border-[#242424] text-sm text-white relative z-50 lg:w-fit w-full">
                    Data
                    <svg data-role="dropdown-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="transition: transform .2s ease;">
                        <path d="m6 9 6 6 6-6" />
                    </svg>
                </button>

                <div data-role="dropdown-overlay" class="hidden fixed inset-0 z-40" style="background:rgba(0,0,0,0.15);"></div>

                <div data-role="dropdown-menu"
                    class="hidden absolute left-0 mt-2 w-64 rounded-2xl bg-[#2c2c2e] border border-[#3f3f46] p-3 z-50"
                    style="box-shadow:0 10px 40px rgba(0,0,0,0.4);">
                    <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wide px-2 pb-2">
                        Pilih Data
                    </div>
                    <div data-role="dropdown-list" class="flex flex-col gap-1 max-h-72 overflow-y-auto"></div>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-[#3f3f46] px-2">
                        <button type="button" data-role="dropdown-cancel"
                            class="text-xs text-zinc-500 hover:text-white transition-colors">
                            Batal
                        </button>
                        <button type="button" data-role="dropdown-apply"
                            class="px-4 py-1.5 text-xs font-medium text-white rounded-lg transition-colors"
                            style="background:#f97316;">
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>

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

{{-- Style khusus jarak legend, dibawa dari versi lama --}}
@once
@push('styles')
<style>
    [data-chart-card] .apexcharts-legend {
        margin-top: 32px;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/time-filter-panel.js') }}" defer></script>
<script src="{{ asset('js/chart-card.js') }}" defer></script>
@endpush
@endonce