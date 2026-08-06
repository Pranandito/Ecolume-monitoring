@props(['device_config', 'latest_volume' => null, 'latest_energi' => null, 'tegangan' => null])
<div
    class="js-session-card bg-[#171717] rounded-2xl p-6 flex flex-col relative overflow-hidden h-full"
    data-device-id="{{ $device_config->device_id }}"
    data-job-created="{{ $device_config->job_created->toIso8601String() }}"
    data-latest-volume="{{ $latest_volume ?? 0 }}">
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
            <h3 class="text-lg text-white">Mode: Timer Waktu</h3>
        </div>
        <div class="flex items-center gap-3">
            <button
                class="bg-white text-black hover:bg-zinc-200 transition-colors px-3 py-1.5 rounded-full text-xs font-semibold">
                Batalkan
            </button>
            <button class="btn-open-mode-select" data-device-id="{{ $device_config->device_id }}" id="btn-open-mode-select" class="text-zinc-400 hover:text-white transition-colors">
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

    <div class="my-3 flex items-baseline gap-1 relative z-10">
        <span class="text-2xl text-white">
            @php
            $start = \Carbon\Carbon::parse($device_config->timer_start);
            $end = \Carbon\Carbon::parse($device_config->timer_end);

            $diff = $start->diff($end);
            @endphp

            {{ $start->format('H:i') }} - {{ $end->format('H:i') }}
        </span>
        <span class="text-sm text-zinc-400"></span>
    </div>

    <div class="mt-auto relative z-10">
        <div class="flex justify-between items-end mb-1">
            <span class="text-xs text-zinc-400">Limit timer waktu</span>
            <span class="text-sm text-white">@php
                use Carbon\Carbon;

                $start = Carbon::parse($device_config->timer_start);
                $end = Carbon::parse($device_config->timer_end);
                $now = Carbon::now();

                $totalDuration = $start->diffInSeconds($end);

                if ($now->lte($start)) {
                $progress = 0;
                } elseif ($now->gte($end)) {
                $progress = 100;
                } else {
                $elapsed = $start->diffInSeconds($now);
                $progress = round(($elapsed / $totalDuration) * 100, 1);
                }
                @endphp

                {{ $progress }}%</span>
        </div>

        <div class="w-full bg-zinc-800 h-1.5 rounded-full overflow-hidden">
            <div class="bg-gradient-to-r from-sky-500 to-cyan-200 h-1.5 rounded-full w-[{{ $progress }}%]"></div>
        </div>

        <div class="flex justify-between mt-1">
            <div class="flex flex-col gap-0.5">
                <span class="text-[11px] text-zinc-500">Volume terpompa</span>
                <div class="flex text-xs text-white ">
                    <span class="js-session-volume">–</span>
                    <p>&nbspL</p>
                </div>
            </div>
            <div class="flex flex-col gap-0.5 text-right">
                <span class="text-[11px] text-zinc-500">Sisa durasi</span>
                <span class="text-xs text-white">{{ $diff->h }} jam {{ $diff->i }} menit</span>
            </div>
        </div>
    </div>
</div>