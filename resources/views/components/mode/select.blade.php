@props(['id', 'serial_number'])

<style>
    /* ---- Custom slider ---- */
    .custom-slider {
        position: relative;
        height: 24px;
        display: flex;
        align-items: center;
        cursor: pointer;
        touch-action: none;
    }

    .slider-track {
        position: absolute;
        left: 0;
        right: 0;
        height: 2px;
        background: #4b5563;
        border-radius: 999px;
    }

    .slider-fill {
        position: absolute;
        height: 2px;
        border-radius: 999px;
    }

    .slider-handle {
        position: absolute;
        top: 50%;
        width: 18px;
        height: 18px;
        margin-top: -9px;
        transform: translateX(-50%) scale(1);
        background: #ffffff;
        border-radius: 999px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        cursor: grab;
        touch-action: none;
        transition: transform 0.12s ease;
    }

    .slider-handle.is-active {
        transform: translateX(-50%) scale(0.78);
        cursor: grabbing;
    }

    .toggle-track {
        width: 44px;
        height: 24px;
        border-radius: 999px;
        position: relative;
        transition: background-color .15s ease;
        flex-shrink: 0;
    }

    .toggle-knob {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        border-radius: 999px;
        background: #fff;
        transition: transform .15s ease;
    }

    .tab-btn.active {
        background: #171717;
        color: #ffffff;
    }

    .tab-btn:not(.active) {
        color: #979797;
    }

    .mode-option.selected {
        border-color: rgba(255, 255, 255, 0.25);
        background: rgba(255, 255, 255, 0.04);
    }

    .value-input {
        background: transparent;
        border: none;
        outline: none;
        border-bottom: 1px solid transparent;
        transition: border-color .15s ease;
        padding-bottom: 2px;
    }

    .value-input:focus {
        border-bottom-color: #6b7280;
    }

    /* hide native number spinners just in case */
    .value-input::-webkit-outer-spin-button,
    .value-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* ---- Time (Waktu) inputs ---- */
    .time-input {
        width: 6ch;
        text-align: center;
        font: inherit;
        color: inherit;
    }
</style>

<div id="mode-select-card" class="hidden w-full max-w-[350px] lg:max-w-md bg-[#171717] rounded-2xl p-6 sm:p-7 fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[1113]">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#262626] flex items-center justify-center text-zinc-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                    <path
                        d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9c.26.604.852 1 1.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z" />
                </svg>
            </div>
            <h3 class="text-lg text-white">Pengaturan Mode <br class="block lg:hidden">Pompa Air</h3>
        </div>
        <button id="btn-close-mode-select" class="">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.6673 16.6667L3.33398 3.33334M16.6673 3.33334L3.33398 16.6667" stroke="#C2C2C2" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
    </div>
    <!-- Tabs -->
    <div class="flex justify-center my-5">
        <div class="inline-flex bg-[#242424] rounded-full p-1 gap-0.5">
            <button data-tab="manual"
                class="tab-btn active px-4 py-1.5 rounded-full text-sm  transition whitespace-nowrap">Manual</button>
            <button data-tab="volume"
                class="tab-btn px-4 py-1.5 rounded-full text-sm  transition whitespace-nowrap">Volume</button>
            <button data-tab="waktu"
                class="tab-btn px-4 py-1.5 rounded-full text-sm  transition whitespace-nowrap">Waktu</button>
        </div>
    </div>

    <!-- Panel: Manual -->
    <div id="panel-manual" class="tab-panel space-y-3">
        <label
            class="mode-option selected flex items-center justify-between rounded-2xl border border-white/10 p-4 cursor-pointer transition">
            <div>
                <p class="text-white ">Mode: On</p>
                <p class="text-neutral-400 text-sm mt-1 leading-snug">Pompa beroperasi tanpa henti<br>hingga Anda
                    mematikannya</p>
            </div>
            <button type="button" data-mode="On" class="toggle-btn toggle-track bg-neutral-600">
                <span class="toggle-knob" style="transform: translateX(20px)"></span>
            </button>
        </label>

        <label
            class="mode-option flex items-center justify-between rounded-2xl border border-white/10 p-4 cursor-pointer transition">
            <div>
                <p class="text-white ">Mode: Off</p>
                <p class="text-neutral-400 text-sm mt-1 leading-snug">Pompa dinonaktifkan dan berhenti<br>beroperasi
                    sepenuhnya</p>
            </div>
            <button type="button" data-mode="Off" class="toggle-btn toggle-track bg-neutral-800">
                <span class="toggle-knob" style="transform: translateX(0px)"></span>
            </button>
        </label>
    </div>

    <!-- Panel: Volume -->
    <div id="panel-volume" class="tab-panel hidden">
        <div class="flex items-start justify-between mb-8">
            <div>
                <p class="flex items-center gap-2 text-white  mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-neutral-300" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M12 2s6 7.5 6 12a6 6 0 11-12 0c0-4.5 6-12 6-12z" />
                    </svg>
                    Pengaturan Volume
                </p>
                <p class="text-neutral-400 text-sm">Estimasi : <span id="volEstimasi">0 Menit</span></p>
            </div>
            <div class="flex items-baseline justify-end gap-2 min-w-0">
                <input id="volValue" type="text" inputmode="numeric" pattern="[0-9]*"
                    class="value-input text-white text-3xl sm:text-4xl  text-right" value="0"
                    aria-label="Ketik nilai volume dalam liter">
                <span class="text-white text-lg font-medium">L</span>
            </div>
            <span id="volMeasure" class="text-3xl sm:text-4xl "
                style="position:fixed; top:-9999px; left:-9999px; visibility:hidden; white-space:pre;"></span>
        </div>

        <div class="custom-slider mb-3" id="volumeSlider">
            <div class="slider-track"></div>
            <div class="slider-fill bg-teal-400" id="volumeFill"></div>
            <div class="slider-handle" id="volumeHandle"></div>
        </div>
        <div class="flex justify-between text-neutral-500 text-sm mb-8">
            <span>0</span><span>100.000</span><span>200.000</span>
        </div>

        <div>
            <div class="flex items-center gap-2 text-neutral-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 16v-4M12 8h.01" />
                </svg>
                <span class="">Peringatan</span>
            </div>
            <p class="text-neutral-500 text-sm">Pompa akan mati setelah batas volume terpenuhi</p>
        </div>
    </div>

    <!-- Panel: Waktu -->
    <div id="panel-waktu" class="tab-panel hidden">
        <div class="flex items-start justify-between mb-8">
            <div>
                <p class="flex items-center gap-2 text-white  mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-neutral-300" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 6v6l4 2" />
                    </svg>
                    Pengaturan Durasi
                </p>
                <p class="text-neutral-400 text-xs">Estimasi : <span id="waktuEstimasi">154 L - 270</span> Menit</p>
            </div>
            <p class="text-white text-2xl sm:text-2xl  whitespace-nowrap flex items-baseline gap-1">
                <input id="waktuStartInput" type="text" inputmode="numeric"
                    class="value-input time-input" value="07:00" aria-label="Ketik waktu mulai (HH:mm)">
                <span>-</span>
                <input id="waktuEndInput" type="text" inputmode="numeric"
                    class="value-input time-input" value="17:00" aria-label="Ketik waktu selesai (HH:mm)">
            </p>
        </div>

        <div class="custom-slider mb-3" id="waktuSlider">
            <div class="slider-track"></div>
            <div class="slider-fill bg-teal-400" id="waktuFill"></div>
            <div class="slider-handle" id="waktuHandleStart"></div>
            <div class="slider-handle" id="waktuHandleEnd"></div>
        </div>
        <div class="flex justify-between text-neutral-500 text-sm mb-8">
            <span id="waktuTick0">07:00</span><span id="waktuTick1">09:30</span><span
                id="waktuTick2">12:00</span><span id="waktuTick3">14:30</span><span id="waktuTick4">17:00</span>
        </div>

        <div>
            <div class="flex items-center gap-2 text-neutral-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 16v-4M12 8h.01" />
                </svg>
                <span class="">Peringatan</span>
            </div>
            <p class="text-neutral-500 text-sm">Pompa akan mati setelah batas durasi terpenuhi</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="flex items-center gap-3 mt-8">
        <button id="btn-cancel-mode"
            class="flex-1 rounded-full border border-white/15 text-neutral-300 py-1 hover:bg-white/5 transition">Batalkan</button>
        <button id="btn-apply-mode"
            class="flex-1 rounded-full bg-white text-neutral-900 py-1 flex items-center justify-center gap-1.5 hover:bg-neutral-200 transition">
            Terapkan
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 18l6-6-6-6" />
            </svg>
        </button>
    </div>
    <p id="modeApplyError" class="text-red-400 text-sm text-center mt-2"></p>

</div>

<script>
    /* ---------------- State pilihan mode aktif ---------------- */
    let activeTab = 'manual'; // 'manual' | 'volume' | 'waktu'
    let manualMode = 'on'; // 'on' | 'off' (hanya relevan saat activeTab === 'manual')

    /* ---------------- Tabs ---------------- */
    const tabButtons = document.querySelectorAll('.tab-btn');
    const panels = {
        manual: document.getElementById('panel-manual'),
        volume: document.getElementById('panel-volume'),
        waktu: document.getElementById('panel-waktu'),
    };
    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            activeTab = btn.dataset.tab;
            tabButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            Object.entries(panels).forEach(([key, el]) => {
                el.classList.toggle('hidden', key !== btn.dataset.tab);
            });
        });
    });

    /* ---------------- Manual mode toggle (radio-style) ---------------- */
    const modeOptions = document.querySelectorAll('.mode-option');
    modeOptions.forEach(option => {
        option.addEventListener('click', (e) => {
            e.preventDefault();
            const targetMode = option.querySelector('.toggle-btn').dataset.mode;
            manualMode = targetMode;
            modeOptions.forEach(opt => {
                const btn = opt.querySelector('.toggle-btn');
                const knob = btn.querySelector('.toggle-knob');
                const isOn = btn.dataset.mode === targetMode;
                opt.classList.toggle('selected', isOn);
                btn.classList.toggle('bg-neutral-600', isOn);
                btn.classList.toggle('bg-neutral-800', !isOn);
                knob.style.transform = isOn ? 'translateX(20px)' : 'translateX(0px)';
            });
        });
    });

    /* ---------------- Helpers ---------------- */
    function clamp(v, min, max) {
        return Math.min(Math.max(v, min), max);
    }

    // Binds drag handling to the WHOLE track (not just the handle), so a click/tap
    // anywhere on the slider jumps straight to that point and dragging continues from there.
    function attachTrackDrag(trackEl, {
        onStart,
        onMove,
        onEnd
    } = {}) {
        let dragging = false;
        const pctFromClientX = (clientX) => {
            const rect = trackEl.getBoundingClientRect();
            return clamp((clientX - rect.left) / rect.width, 0, 1);
        };
        const begin = (clientX) => {
            dragging = true;
            const pct = pctFromClientX(clientX);
            if (onStart) onStart(pct);
            onMove(pct);
        };
        const moveTo = (clientX) => {
            if (dragging) onMove(pctFromClientX(clientX));
        };
        const stop = () => {
            if (dragging) {
                dragging = false;
                if (onEnd) onEnd();
            }
        };

        trackEl.addEventListener('mousedown', e => {
            e.preventDefault();
            begin(e.clientX);
        });
        document.addEventListener('mousemove', e => moveTo(e.clientX));
        document.addEventListener('mouseup', stop);

        trackEl.addEventListener('touchstart', e => begin(e.touches[0].clientX), {
            passive: true
        });
        document.addEventListener('touchmove', e => moveTo(e.touches[0].clientX), {
            passive: true
        });
        document.addEventListener('touchend', stop);
    }

    /* ---------------- Volume slider (0 - 200.000 L) ---------------- */
    const VOL_MAX = 200000; // liter
    const VOL_STEP = 100; // snap increments, in liter (fine-grained but readable at this scale)
    const VOL_RATE = 500; // asumsi debit pompa, liter per menit — sesuaikan dengan debit riil pompa
    const volumeSlider = document.getElementById('volumeSlider');
    const volumeFill = document.getElementById('volumeFill');
    const volumeHandle = document.getElementById('volumeHandle');
    const volValueEl = document.getElementById('volValue');
    const volMeasureEl = document.getElementById('volMeasure');
    const volEstimasiEl = document.getElementById('volEstimasi');

    let volumeValue = 0; // Liter (belum diatur — tampil 0 sampai slider/input digeser)

    function formatDurationMinutes(totalMinutes) {
        const jam = Math.floor(totalMinutes / 60);
        const menit = totalMinutes % 60;
        if (jam <= 0) return `${menit} Menit`;
        if (menit === 0) return `${jam} Jam`;
        return `${jam} Jam ${menit} Menit`;
    }

    // Sizes the input's own width to match its current text, so the (otherwise full-width)
    // border-bottom underline visually hugs just the digits instead of a fixed-width box.
    function resizeVolInput() {
        volMeasureEl.textContent = volValueEl.value || '0';
        volValueEl.style.width = (volMeasureEl.offsetWidth + 2) + 'px';
    }

    function renderVolume() {
        const pct = (volumeValue / VOL_MAX) * 100;
        volumeFill.style.width = pct + '%';
        volumeHandle.style.left = pct + '%';
        if (document.activeElement !== volValueEl) {
            volValueEl.value = volumeValue.toLocaleString('id-ID');
        }
        volEstimasiEl.textContent = formatDurationMinutes(Math.round(volumeValue / VOL_RATE));
        resizeVolInput();
    }

    function parseVolumeInput(str) {
        const digitsOnly = str.replace(/[^0-9]/g, '');
        return digitsOnly ? parseInt(digitsOnly, 10) : 0;
    }

    function commitVolumeInput() {
        let val = parseVolumeInput(volValueEl.value);
        val = clamp(val, 0, VOL_MAX);
        // Nilai ketikan dipakai apa adanya, tidak dibulatkan ke VOL_STEP
        // (VOL_STEP hanya dipakai untuk snapping saat menggeser slider).
        volumeValue = val;
        renderVolume();
    }

    // While typing: keep only digits, don't reformat yet (so the cursor/typing feels natural)
    volValueEl.addEventListener('input', () => {
        volValueEl.value = volValueEl.value.replace(/[^0-9]/g, '');
        resizeVolInput();
    });
    // On focus: show raw digits (no thousands separator) so editing is easy, and select all
    volValueEl.addEventListener('focus', () => {
        volValueEl.value = String(volumeValue);
        resizeVolInput();
        volValueEl.select();
    });
    // Commit + reformat with thousands separator when done editing
    volValueEl.addEventListener('blur', commitVolumeInput);
    volValueEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') volValueEl.blur();
    });

    attachTrackDrag(volumeSlider, {
        onStart: () => volumeHandle.classList.add('is-active'),
        onMove: (pct) => {
            volumeValue = Math.round((pct * VOL_MAX) / VOL_STEP) * VOL_STEP;
            renderVolume();
        },
        onEnd: () => volumeHandle.classList.remove('is-active')
    });
    renderVolume();

    /* ---------------- Waktu (time range) slider ---------------- */
    // Jendela waktu SELALU tetap 07:00 - 17:00 setiap hari. Tidak lagi terikat pada
    // waktu sekarang ("now") — start boleh diatur ke jam berapa pun di dalam jendela ini,
    // termasuk jam yang sudah lewat pada hari berjalan.
    const START_HOUR = 7; // batas bawah jendela waktu (tetap)
    const END_HOUR = 17; // batas atas jendela waktu (tetap)
    const WINDOW_START_MIN = START_HOUR * 60; // 420, posisi 0% slider
    const WINDOW_END_MIN = END_HOUR * 60; // 1020, posisi 100% slider
    const DOMAIN_SPAN_MIN = WINDOW_END_MIN - WINDOW_START_MIN;
    const MIN_GAP = 30; // minimum gap between start & end, in minutes
    const STEP = 1; // snap step, in minutes (per-minute precision)
    const TIME_RATE = 500; // liter per menit, derived to match reference design

    const waktuSlider = document.getElementById('waktuSlider');
    const waktuFill = document.getElementById('waktuFill');
    const handleStart = document.getElementById('waktuHandleStart');
    const handleEnd = document.getElementById('waktuHandleEnd');
    const waktuStartInput = document.getElementById('waktuStartInput');
    const waktuEndInput = document.getElementById('waktuEndInput');
    const waktuEstimasiEl = document.getElementById('waktuEstimasi');
    const tickEls = [0, 1, 2, 3, 4].map(i => document.getElementById('waktuTick' + i));

    function snap(v) {
        return Math.round(v / STEP) * STEP;
    }

    // minOfDay: minutes since 00:00 (0-1439) -> "HH:mm"
    function minutesToLabel(minOfDay) {
        const h = Math.floor(minOfDay / 60) % 24;
        const m = minOfDay % 60;
        return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
    }

    // "HH:mm" / "HHmm" / "H" typed input -> minutes since 00:00, or null if unparsable
    function parseTimeInput(str) {
        const digits = (str || '').replace(/[^0-9]/g, '');
        if (!digits) return null;
        let h, m;
        if (digits.length <= 2) {
            h = parseInt(digits, 10);
            m = 0;
        } else {
            h = parseInt(digits.slice(0, digits.length - 2), 10);
            m = parseInt(digits.slice(-2), 10);
        }
        if (isNaN(h) || isNaN(m)) return null;
        h = clamp(h, 0, 23);
        m = clamp(m, 0, 59);
        return h * 60 + m;
    }

    // Initial state: default jam 07:00 - 17:00 (full window)
    let startMin = WINDOW_START_MIN;
    let endMin = WINDOW_END_MIN;

    function renderWaktu() {
        const startPct = clamp(((startMin - WINDOW_START_MIN) / DOMAIN_SPAN_MIN) * 100, 0, 100);
        const endPct = clamp(((endMin - WINDOW_START_MIN) / DOMAIN_SPAN_MIN) * 100, 0, 100);
        handleStart.style.left = startPct + '%';
        handleEnd.style.left = endPct + '%';
        waktuFill.style.left = startPct + '%';
        waktuFill.style.width = (endPct - startPct) + '%';

        if (document.activeElement !== waktuStartInput) {
            waktuStartInput.value = minutesToLabel(startMin);
        }
        if (document.activeElement !== waktuEndInput) {
            waktuEndInput.value = minutesToLabel(endMin);
        }

        const durationMin = endMin - startMin;
        const liters = Math.round(durationMin * TIME_RATE);
        waktuEstimasiEl.textContent = liters + ' L - ' + durationMin;

        // Tick labels tetap statis karena jendela sudah fixed, tapi dihitung ulang
        // supaya tetap konsisten kalau DOMAIN_SPAN_MIN berubah di masa depan.
        tickEls.forEach((el, i) => {
            el.textContent = minutesToLabel(WINDOW_START_MIN + (DOMAIN_SPAN_MIN / 4) * i);
        });
    }

    // Untuk input ketikan: TIDAK dibulatkan/dipaksa ke kelipatan MIN_GAP (30 menit).
    // Nilai yang diketik dipakai apa adanya, hanya dibatasi ke jendela 07:00-17:00 dan
    // dijaga agar start tetap sebelum end (minimal 1 menit selisih).
    function commitStartInput() {
        let val = parseTimeInput(waktuStartInput.value);
        if (val === null) val = startMin;
        val = clamp(val, WINDOW_START_MIN, WINDOW_END_MIN - 1);
        startMin = Math.min(val, endMin - 1);
        renderWaktu();
    }

    function commitEndInput() {
        let val = parseTimeInput(waktuEndInput.value);
        if (val === null) val = endMin;
        val = clamp(val, WINDOW_START_MIN + 1, WINDOW_END_MIN);
        endMin = Math.max(val, startMin + 1);
        renderWaktu();
    }

    // While typing: keep only digits/colon, don't reformat yet
    [waktuStartInput, waktuEndInput].forEach(input => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/[^0-9:]/g, '');
        });
        input.addEventListener('focus', () => input.select());
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') input.blur();
        });
    });
    waktuStartInput.addEventListener('blur', commitStartInput);
    waktuEndInput.addEventListener('blur', commitEndInput);

    let activeHandle = null; // 'start' | 'end', decided per drag by whichever handle is closer

    attachTrackDrag(waktuSlider, {
        onStart: (pct) => {
            const candidate = WINDOW_START_MIN + snap(pct * DOMAIN_SPAN_MIN);
            const distStart = Math.abs(candidate - startMin);
            const distEnd = Math.abs(candidate - endMin);
            activeHandle = distStart <= distEnd ? 'start' : 'end';
            (activeHandle === 'start' ? handleStart : handleEnd).classList.add('is-active');
        },
        onMove: (pct) => {
            const candidate = WINDOW_START_MIN + snap(pct * DOMAIN_SPAN_MIN);
            if (activeHandle === 'start') {
                startMin = clamp(candidate, WINDOW_START_MIN, endMin - MIN_GAP);
            } else {
                endMin = clamp(candidate, startMin + MIN_GAP, WINDOW_END_MIN);
            }
            renderWaktu();
        },
        onEnd: () => {
            handleStart.classList.remove('is-active');
            handleEnd.classList.remove('is-active');
            activeHandle = null;
        }
    });

    renderWaktu();

    /* ==================================================================
       KONEKSI KE API — update mode pompa di tabel devices_config
       ================================================================== */
    const modeModal = document.getElementById('mode-select-card');
    const DEVICE_ID = "{{ $id }}";
    const serial_number = "{{ $serial_number }}";
    const btnApplyMode = document.getElementById('btn-apply-mode');
    const btnCancelMode = document.getElementById('btn-cancel-mode');
    const applyErrorEl = document.getElementById('modeApplyError');
    const modeOverlay = document.getElementById('overlay');

    // Susun payload sesuai tab yang sedang aktif saat "Terapkan" ditekan.
    function buildModePayload() {
        if (activeTab === 'manual') {
            return {
                mode: manualMode
            }; // 'on' | 'off'
        }
        if (activeTab === 'volume') {
            return {
                mode: 'Timer Volume',
                volume_limit: volumeValue
            };
        }
        // activeTab === 'waktu'
        return {
            mode: 'Timer Waktu',
            timer_start: minutesToLabel(startMin), // format "HH:mm"
            timer_end: minutesToLabel(endMin),
        };
    }

    async function applyMode() {
        const deviceId = DEVICE_ID;
        if (!deviceId) {
            console.error('device-id belum di-set pada #mode-select-card (lihat openModePompaModal)');
            return;
        }

        const payload = buildModePayload();
        const originalLabel = btnApplyMode.innerHTML;
        btnApplyMode.disabled = true;
        btnApplyMode.innerHTML = 'Menyimpan...';
        applyErrorEl.textContent = '';

        try {
            const res = await fetch(`/device/mode/${deviceId}/${serial_number}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify(payload),
            });
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || 'Gagal menyimpan mode pompa');

            modeModal.classList.add('hidden');
            modeOverlay?.classList.add('hidden');
            // Broadcast supaya bagian lain dashboard (kartu status, gauge, dll) bisa refresh.

            document.dispatchEvent(new CustomEvent('pump-mode-updated', {
                detail: {
                    device_id: deviceId,
                    html: json.html
                }
            }));
        } catch (err) {
            applyErrorEl.textContent = err.message;
        } finally {
            btnApplyMode.disabled = false;
            btnApplyMode.innerHTML = originalLabel;
        }
    }

    btnApplyMode.addEventListener('click', applyMode);
    btnCancelMode.addEventListener('click', () => {
        modeModal.classList.add('hidden');
        modeOverlay?.classList.add('hidden');
    });

    /* ---------------- Buka modal + prefill dari config device saat ini ----------------
       Panggil fungsi ini dari luar (mis. saat tombol "Atur Mode" pada kartu device
       diklik), contoh:
           openModePompaModal(42, {
               mode: 'timer_volume',
               volume_limit: 15000,
               timer_start: '07:00',
               timer_end: '10:00',
           });
    -------------------------------------------------------------------------------- */
    function openModePompaModal(config = {}) {
        modeModal.dataset.deviceId = deviceId;
        applyErrorEl.textContent = '';

        const mode = config.mode || 'Off';

        if (mode === 'Timer Volume') {
            activeTab = 'volume';
            volumeValue = clamp(parseInt(config.volume_limit || 0, 10), 0, VOL_MAX);
            renderVolume();
        } else if (mode === 'Timer Waktu') {
            activeTab = 'waktu';
            if (config.timer_start && config.timer_end) {
                const parsedStart = parseTimeInput(config.timer_start);
                const parsedEnd = parseTimeInput(config.timer_end);
                if (parsedStart !== null) {
                    startMin = clamp(parsedStart, WINDOW_START_MIN, WINDOW_END_MIN - MIN_GAP);
                }
                if (parsedEnd !== null) {
                    endMin = clamp(parsedEnd, startMin + MIN_GAP, WINDOW_END_MIN);
                }
            }
            renderWaktu();
        } else {
            activeTab = 'manual';
            manualMode = mode === 'On' ? 'On' : 'Off';
            modeOptions.forEach(opt => {
                const btn = opt.querySelector('.toggle-btn');
                const knob = btn.querySelector('.toggle-knob');
                const isOn = btn.dataset.mode === manualMode;
                opt.classList.toggle('selected', isOn);
                btn.classList.toggle('bg-neutral-600', isOn);
                btn.classList.toggle('bg-neutral-800', !isOn);
                knob.style.transform = isOn ? 'translateX(20px)' : 'translateX(0px)';
            });
        }

        // Sinkronkan tampilan tab sesuai activeTab hasil prefill di atas
        tabButtons.forEach(b => b.classList.toggle('active', b.dataset.tab === activeTab));
        Object.entries(panels).forEach(([key, el]) => {
            el.classList.toggle('hidden', key !== activeTab);
        });

        modeModal.classList.remove('hidden');
    }

    /* ---------------- Close button ---------------- */
    document.getElementById('btn-close-mode-select').addEventListener('click', () => {
        modeModal.classList.add('hidden');
    });
</script>