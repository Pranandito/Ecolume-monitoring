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
    </style>

    <div id="mode-select-card" class="hidden w-full max-w-md bg-[#171717] rounded-2xl p-6 sm:p-7 fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[1112]">
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
                <h3 class="text-lg text-white">Pengaturan Mode Pompa Air</h3>
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
                    <p class="text-neutral-400 text-sm">Estimasi : <span id="waktuEstimasi">154 L - 270</span> Menit</p>
                </div>
                <p class="text-white text-2xl sm:text-3xl  whitespace-nowrap"><span id="waktuStartLabel">06:00</span> -
                    <span id="waktuEndLabel">12:00</span>
                </p>
            </div>

            <div class="custom-slider mb-3" id="waktuSlider">
                <div class="slider-track"></div>
                <div class="slider-fill bg-teal-400" id="waktuFill"></div>
                <div class="slider-handle" id="waktuHandleStart"></div>
                <div class="slider-handle" id="waktuHandleEnd"></div>
            </div>
            <div class="flex justify-between text-neutral-500 text-sm mb-8">
                <span id="waktuTick0">06:00</span><span id="waktuTick1">09:00</span><span
                    id="waktuTick2">12:00</span><span id="waktuTick3">15:00</span><span id="waktuTick4">18:00</span>
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
        // Jendela waktu yang diizinkan selalu di dalam 06:00 - 18:00.
        // Left edge (0%) adalah titik awal jendela (lihat computeWaktuWindow di bawah): mengikuti
        // waktu sekarang selama masih di dalam 06:00-18:00 (tidak bisa diatur mundur), atau 06:00
        // jika sekarang masih sebelum 06:00. Right edge (100%) selalu 18:00. Jika sekarang sudah
        // lewat 18:00, seluruh jendela diulang menjadi 06:00 - 18:00 untuk besok.
        const START_HOUR = 6; // batas bawah jendela waktu
        const END_HOUR = 18; // batas atas jendela waktu
        const MIN_GAP = 30; // minimum gap between start & end, in minutes
        const STEP = 1; // snap step, in minutes (per-minute precision)
        const TIME_RATE = 154 / 270; // liter per menit, derived to match reference design

        const waktuSlider = document.getElementById('waktuSlider');
        const waktuFill = document.getElementById('waktuFill');
        const handleStart = document.getElementById('waktuHandleStart');
        const handleEnd = document.getElementById('waktuHandleEnd');
        const startLabelEl = document.getElementById('waktuStartLabel');
        const endLabelEl = document.getElementById('waktuEndLabel');
        const waktuEstimasiEl = document.getElementById('waktuEstimasi');
        const tickEls = [0, 1, 2, 3, 4].map(i => document.getElementById('waktuTick' + i));

        function nowEpochMinutes() {
            return Math.floor(Date.now() / 60000); // integer minutes since epoch, i.e. "now"
        }

        // Menentukan jendela waktu yang berlaku (selalu di dalam START_HOUR-END_HOUR) beserta titik
        // awal slider (anchor), berdasarkan waktu sekarang:
        // - Sebelum 06:00 hari ini -> jendela = 06:00-18:00 hari ini, anchor = 06:00
        // - Antara 06:00 - 18:00   -> jendela = 06:00-18:00 hari ini, anchor = waktu sekarang (tidak bisa mundur)
        // - Sudah lewat 18:00      -> jendela diulang menjadi 06:00-18:00 untuk besok, anchor = 06:00 besok
        function computeWaktuWindow(nowEpochMin) {
            const startToday = new Date(nowEpochMin * 60000);
            startToday.setHours(START_HOUR, 0, 0, 0);
            const endToday = new Date(nowEpochMin * 60000);
            endToday.setHours(END_HOUR, 0, 0, 0);
            let startBoundMin = Math.floor(startToday.getTime() / 60000);
            let endBoundMin = Math.floor(endToday.getTime() / 60000);

            let anchorMin;
            if (nowEpochMin >= endBoundMin) {
                startBoundMin += 24 * 60;
                endBoundMin += 24 * 60;
                anchorMin = startBoundMin;
            } else if (nowEpochMin <= startBoundMin) {
                anchorMin = startBoundMin;
            } else {
                anchorMin = nowEpochMin;
            }
            return {
                anchorMin,
                endBoundMin
            };
        }

        function snap(v) {
            return Math.round(v / STEP) * STEP;
        }

        function minutesToLabel(epochMin) {
            const d = new Date(epochMin * 60000);
            return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
        }

        // anchorMin = the absolute minute that currently sits at position 0% (titik awal jendela).
        // endBoundMin = the absolute minute pinned at position 100% (selalu jam 18:00).
        // domainSpanMin is derived from the two, so it naturally shrinks as "now" approaches 18:00.
        let {
            anchorMin,
            endBoundMin
        } = computeWaktuWindow(nowEpochMinutes());
        let domainSpanMin = endBoundMin - anchorMin;

        // Initial state: start pinned to anchor (position 0), default 4h30m duration (matches reference),
        // but never past the fixed 18:00 bound.
        let startMin = anchorMin;
        let endMin = Math.min(startMin + 270, endBoundMin);
        if (endMin < startMin + MIN_GAP) endMin = Math.min(startMin + MIN_GAP, endBoundMin);

        function renderWaktu() {
            const startPct = clamp(((startMin - anchorMin) / domainSpanMin) * 100, 0, 100);
            const endPct = clamp(((endMin - anchorMin) / domainSpanMin) * 100, 0, 100);
            handleStart.style.left = startPct + '%';
            handleEnd.style.left = endPct + '%';
            waktuFill.style.left = startPct + '%';
            waktuFill.style.width = (endPct - startPct) + '%';
            startLabelEl.textContent = minutesToLabel(startMin);
            endLabelEl.textContent = minutesToLabel(endMin);
            const durationMin = endMin - startMin;
            const liters = Math.round(durationMin * TIME_RATE);
            waktuEstimasiEl.textContent = liters + ' L - ' + durationMin;

            // Tick labels are dynamic: 0%, 25%, 50%, 75%, 100% between "now" and the fixed 18:00 bound
            tickEls.forEach((el, i) => {
                el.textContent = minutesToLabel(anchorMin + (domainSpanMin / 4) * i);
            });
        }

        let activeHandle = null; // 'start' | 'end', decided per drag by whichever handle is closer

        attachTrackDrag(waktuSlider, {
            onStart: (pct) => {
                const candidate = anchorMin + snap(pct * domainSpanMin);
                const distStart = Math.abs(candidate - startMin);
                const distEnd = Math.abs(candidate - endMin);
                activeHandle = distStart <= distEnd ? 'start' : 'end';
                (activeHandle === 'start' ? handleStart : handleEnd).classList.add('is-active');
            },
            onMove: (pct) => {
                const candidate = anchorMin + snap(pct * domainSpanMin);
                if (activeHandle === 'start') {
                    // Can't drag start earlier than "now" (anchorMin = position 0)
                    startMin = clamp(candidate, anchorMin, endMin - MIN_GAP);
                } else {
                    // Can't drag end later than the fixed 18:00 bound (endBoundMin = position 100%)
                    endMin = clamp(candidate, startMin + MIN_GAP, endBoundMin);
                }
                renderWaktu();
            },
            onEnd: () => {
                handleStart.classList.remove('is-active');
                handleEnd.classList.remove('is-active');
                activeHandle = null;
            }
        });

        // Refresh the time window periodically so position 0 keeps tracking "now" while inside
        // 06:00-18:00, and so the window correctly rolls over to tomorrow's 06:00-18:00 once
        // it's past 18:00. If start has fallen behind the (possibly new) anchor, snap it forward.
        function tickClock() {
            ({
                anchorMin,
                endBoundMin
            } = computeWaktuWindow(nowEpochMinutes()));
            domainSpanMin = endBoundMin - anchorMin;
            if (startMin < anchorMin) {
                startMin = anchorMin;
                if (endMin < startMin + MIN_GAP) {
                    endMin = Math.min(startMin + MIN_GAP, endBoundMin);
                }
            }
            if (endMin > endBoundMin) endMin = endBoundMin;
            renderWaktu();
        }
        renderWaktu();
        setInterval(tickClock, 15000);

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
                timer_start: startLabelEl.textContent, // format "HH:mm"
                timer_end: endLabelEl.textContent,
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
                   timer_start: '06:00',
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
                    const [sh, sm] = config.timer_start.split(':').map(Number);
                    const [eh, em] = config.timer_end.split(':').map(Number);
                    const base = new Date(anchorMin * 60000);
                    base.setHours(sh, sm, 0, 0);
                    startMin = clamp(Math.floor(base.getTime() / 60000), anchorMin, endBoundMin - MIN_GAP);
                    base.setHours(eh, em, 0, 0);
                    endMin = clamp(Math.floor(base.getTime() / 60000), startMin + MIN_GAP, endBoundMin);
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