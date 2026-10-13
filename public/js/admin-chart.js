(function () {
    'use strict';

    // =================================================================
    // TIME FILTER PANEL
    // (diambil dari public/js/time-filter-panel.js, tanpa perubahan logic —
    // tetap dipertahankan generic/reusable-nya via register(context, {...})
    // seandainya nanti ada kartu lain yang butuh panel yang sama)
    //
    // DOM yang WAJIB ada (partial "kartu time filter" yang sudah kamu buat):
    //   #time-filter-panel, #overlay-timepicker, #tf-month-label, #tf-days-grid,
    //   #tf-resolution-wrapper, #tf-prev-month, #tf-next-month, #tf-apply-btn,
    //   #btn-close-time-filter-chart, .tf-resolution-btn[data-resolution],
    //   .tf-preset-chip[data-preset]
    // =================================================================

    const MONTH_NAMES_ID = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];
    const ALL_POSITION_CLASSES = ['right-0', 'right-6', '-translate-x-full'];

    class TimeFilterPanelController {
        constructor() {
            this.contexts = {};
            this.activeContext = null;
            this.today = new Date();
            this.today.setHours(0, 0, 0, 0);
            this.viewDate = new Date();
            this.draft = {
                start: null,
                end: null
            };
            this.draftResolution = 'detail';
            this.ready = false;
        }

        register(name, {
            container,
            positionClasses = ['right-0'],
            hasResolutionOption = false,
            onApply = null
        }) {
            this.contexts[name] = {
                container,
                positionClasses,
                hasResolutionOption,
                onApply,
                applied: {
                    start: null,
                    end: null,
                    resolution: 'detail'
                },
            };
            this.init();
        }

        init() {
            if (this.ready) return;
            this.els = {
                panel: document.getElementById('time-filter-panel'),
                overlay: document.getElementById('overlay-timepicker'),
                monthLabel: document.getElementById('tf-month-label'),
                daysGrid: document.getElementById('tf-days-grid'),
                resolutionWrapper: document.getElementById('tf-resolution-wrapper'),
                prevMonthBtn: document.getElementById('tf-prev-month'),
                nextMonthBtn: document.getElementById('tf-next-month'),
                applyBtn: document.getElementById('tf-apply-btn'),
                closeBtn: document.getElementById('btn-close-time-filter-chart'),
                resolutionButtons: document.querySelectorAll('.tf-resolution-btn'),
                presetChips: document.querySelectorAll('.tf-preset-chip'),
            };

            if (!this.els.panel) return;

            this.bindEvents();
            this.ready = true;
        }

        bindEvents() {
            const e = this.els;

            e.resolutionButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    this.draftResolution = btn.getAttribute('data-resolution');
                    this.syncResolutionButtons();
                });
            });

            e.presetChips.forEach((chip) => {
                chip.addEventListener('click', () => {
                    const range = this.computePresetRange(chip.getAttribute('data-preset'));
                    this.draft.start = range.start;
                    this.draft.end = range.end;
                    this.viewDate = new Date(range.end);
                    this.renderCalendar();
                });
            });

            e.prevMonthBtn.addEventListener('click', () => {
                this.viewDate.setMonth(this.viewDate.getMonth() - 1);
                this.renderCalendar();
            });
            e.nextMonthBtn.addEventListener('click', () => {
                this.viewDate.setMonth(this.viewDate.getMonth() + 1);
                this.renderCalendar();
            });

            e.closeBtn.addEventListener('click', () => this.close());
            e.overlay.addEventListener('click', () => this.close());
            e.applyBtn.addEventListener('click', () => this.applyDraft());

            document.addEventListener('click', (evt) => {
                if (e.panel.classList.contains('hidden')) return;
                if (e.panel.contains(evt.target)) return;
                if (evt.target.closest('[data-range="CUSTOM"], [data-range="custom"]')) return;
                this.close();
            });

            document.addEventListener('tf:open', (evt) => this.open(evt.detail && evt.detail.context));
        }

        open(contextName) {
            const ctx = this.contexts[contextName];
            if (!ctx) {
                console.warn(`TimeFilterPanel: context "${contextName}" belum di-register`);
                return;
            }

            this.activeContext = contextName;
            this.draft.start = ctx.applied.start;
            this.draft.end = ctx.applied.end;
            this.draftResolution = ctx.applied.resolution || 'detail';
            this.syncResolutionButtons();

            const panel = this.els.panel;
            if (window.innerWidth < 1024) {
                panel.classList.remove('absolute', 'right-0');
                panel.classList.add('fixed', 'top-1/2', 'left-1/2', '-translate-x-1/2', '-translate-y-1/2');
            } else {
                panel.classList.remove(...ALL_POSITION_CLASSES);
                panel.classList.add(...ctx.positionClasses);
            }

            this.els.resolutionWrapper.classList.toggle('hidden', !ctx.hasResolutionOption);

            this.viewDate = ctx.applied.start ? new Date(ctx.applied.start) : new Date();
            this.renderCalendar();
            panel.classList.remove('hidden');
            this.els.overlay.classList.remove('hidden');
        }

        close() {
            const ctx = this.contexts[this.activeContext];
            if (ctx) {
                this.draft.start = ctx.applied.start;
                this.draft.end = ctx.applied.end;
                this.draftResolution = ctx.applied.resolution || this.draftResolution;
                this.syncResolutionButtons();
            }
            this.els.panel.classList.add('hidden');
            this.els.overlay.classList.add('hidden');
        }

        applyDraft() {
            if (!this.draft.start || !this.draft.end) return;

            const ctx = this.contexts[this.activeContext];
            ctx.applied.start = this.draft.start;
            ctx.applied.end = this.draft.end;
            if (ctx.hasResolutionOption) ctx.applied.resolution = this.draftResolution;

            this.els.panel.classList.add('hidden');
            this.els.overlay.classList.add('hidden');

            const payload = {
                context: this.activeContext,
                start: ctx.applied.start,
                end: ctx.applied.end,
                resolution: ctx.applied.resolution,
                startParam: this.formatDateTimeParam(ctx.applied.start, false),
                stopParam: this.formatDateTimeParam(ctx.applied.end, true),
            };

            if (typeof ctx.onApply === 'function') ctx.onApply(payload);
            document.dispatchEvent(new CustomEvent('tf:apply', {
                detail: payload
            }));
        }

        syncResolutionButtons() {
            this.els.resolutionButtons.forEach((b) => {
                const active = b.getAttribute('data-resolution') === this.draftResolution;
                b.classList.toggle('text-white', active);
                b.classList.toggle('bg-[#171717]', active);
                b.classList.toggle('shadow', active);
                b.classList.toggle('text-zinc-400', !active);
            });
        }

        sameDate(a, b) {
            return (
                a && b &&
                a.getFullYear() === b.getFullYear() &&
                a.getMonth() === b.getMonth() &&
                a.getDate() === b.getDate()
            );
        }

        formatDateParam(d) {
            const pad = (v) => (v < 10 ? '0' + v : '' + v);
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        }

        formatDateTimeParam(d, endOfDay) {
            return `${this.formatDateParam(d)} ${endOfDay ? '23:59:59' : '00:00:00'}`;
        }

        computePresetRange(preset) {
            const end = new Date(this.today);
            let start;
            if (preset === 'month') {
                start = new Date(this.today.getFullYear(), this.today.getMonth(), 1);
            } else {
                start = new Date(this.today);
                start.setDate(start.getDate() - (parseInt(preset, 10) - 1));
            }
            return {
                start,
                end
            };
        }

        updatePresetChips() {
            this.els.presetChips.forEach((chip) => {
                const range = this.computePresetRange(chip.getAttribute('data-preset'));
                const isActive = this.sameDate(this.draft.start, range.start) && this.sameDate(this.draft.end, range.end);
                chip.classList.toggle('border-blue-600', isActive);
                chip.classList.toggle('text-white', isActive);
                chip.classList.toggle('bg-[#3f3f46]', isActive);
                chip.classList.toggle('border-[#3f3f46]', !isActive);
                chip.classList.toggle('text-zinc-400', !isActive);
            });
        }

        buildMonthCells(year, month) {
            const firstDay = new Date(year, month, 1);
            const startWeekday = firstDay.getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();
            const cells = [];

            for (let i = startWeekday - 1; i >= 0; i--) {
                cells.push({
                    date: new Date(year, month - 1, daysInPrevMonth - i),
                    currentMonth: false
                });
            }
            for (let d = 1; d <= daysInMonth; d++) {
                cells.push({
                    date: new Date(year, month, d),
                    currentMonth: true
                });
            }
            while (cells.length < 42) {
                const lastDate = cells[cells.length - 1].date;
                const nextDate = new Date(lastDate);
                nextDate.setDate(lastDate.getDate() + 1);
                cells.push({
                    date: nextDate,
                    currentMonth: false
                });
            }
            return cells;
        }

        renderCalendar() {
            const year = this.viewDate.getFullYear();
            const month = this.viewDate.getMonth();
            this.els.monthLabel.textContent = `${MONTH_NAMES_ID[month]} ${year}`;

            const cells = this.buildMonthCells(year, month);
            const grid = this.els.daysGrid;
            grid.innerHTML = '';

            cells.forEach((cell, idx) => {
                const col = idx % 7;
                const isStart = this.sameDate(cell.date, this.draft.start);
                const isEnd = this.sameDate(cell.date, this.draft.end);
                const inRange = this.draft.start && this.draft.end && cell.date > this.draft.start && cell.date < this.draft.end;
                const isFuture = cell.date > this.today;

                const wrap = document.createElement('div');
                wrap.className = 'py-1';
                if (!isFuture && (inRange || (isStart && this.draft.end) || (isEnd && this.draft.start))) {
                    wrap.classList.add('bg-[#3f3f46]');
                    if (col === 0) wrap.classList.add('rounded-l-full');
                    if (col === 6) wrap.classList.add('rounded-r-full');
                }

                const num = document.createElement('div');
                num.textContent = cell.date.getDate();
                num.className = 'w-9 h-9 mx-auto flex items-center justify-center rounded-full select-none transition-colors';

                if (isFuture) {
                    num.classList.add('text-zinc-700', 'cursor-not-allowed');
                } else if (isStart || isEnd) {
                    num.classList.add('bg-blue-600', 'text-white', 'font-semibold', 'cursor-pointer');
                } else if (!cell.currentMonth) {
                    num.classList.add('text-zinc-600', 'hover:bg-[#3f3f46]', 'cursor-pointer');
                } else {
                    num.classList.add('text-zinc-200', 'hover:bg-[#3f3f46]', 'cursor-pointer');
                }

                if (!isFuture) {
                    num.addEventListener('click', (evt) => {
                        evt.stopPropagation();
                        this.handleDayClick(cell.date);
                    });
                }

                wrap.appendChild(num);
                grid.appendChild(wrap);
            });

            this.updateNavButtons();
            this.updatePresetChips();
        }

        updateNavButtons() {
            const isCurrentMonth =
                this.viewDate.getFullYear() === this.today.getFullYear() && this.viewDate.getMonth() === this.today.getMonth();
            this.els.nextMonthBtn.classList.toggle('opacity-30', isCurrentMonth);
            this.els.nextMonthBtn.classList.toggle('cursor-not-allowed', isCurrentMonth);
            this.els.nextMonthBtn.classList.toggle('pointer-events-none', isCurrentMonth);
        }

        handleDayClick(date) {
            if (!this.draft.start || (this.draft.start && this.draft.end)) {
                this.draft.start = date;
                this.draft.end = null;
            } else if (date < this.draft.start) {
                this.draft.end = this.draft.start;
                this.draft.start = date;
            } else {
                this.draft.end = date;
            }
            this.renderCalendar();
        }
    }

    window.TimeFilterPanel = new TimeFilterPanelController();

    // =================================================================
    // ADMIN OPERATIONAL DURATION CHART
    //
    // DOM yang WAJIB ada di div chart:
    //   <div data-admin-duration-chart data-config='{"endpoint":"...","refreshIntervalMs":30000,"maxVisiblePoints":200}'>
    //       <div data-role="chart"></div>
    //       <div data-role="range-filter-group">
    //           <button class="range-filter-btn" data-range="1H">...</button>
    //           <button class="range-filter-btn" data-range="1M">...</button>
    //           <button class="range-filter-btn" data-range="CUSTOM">...</button>
    //       </div>
    //   </div>
    // Panel filter waktu (kartu terpisah yang sudah kamu buat) dipakai lewat
    // TimeFilterPanel di atas, context "admin-duration".
    // =================================================================

    // Palet warna untuk series per-device (dipilih agar kontras di tema gelap).
    const DEVICE_COLOR_PALETTE = [
        '#38bdf8', '#a78bfa', '#fb923c', '#4ade80', '#f472b6',
        '#facc15', '#2dd4bf', '#f87171', '#818cf8', '#c084fc',
    ];
    const TOTAL_COLOR = '#e4e4e7';

    class AdminDurationChart {
        constructor(root) {
            this.root = root;
            this.config = JSON.parse(root.dataset.config);

            this.currentRange = '1H';
            this.customRange = null; // { start, end, resolution } saat range === 'CUSTOM'
            this.rawSeriesData = []; // data mentah (menit) yang sedang ditampilkan, dipakai tooltip

            this.els = {
                chart: root.querySelector('[data-role="chart"]'),
                rangeGroup: root.querySelector('[data-role="range-filter-group"]'),
            };

            this.apexChart = new ApexCharts(this.els.chart, this.buildChartOptions());
            this.apexChart.render();

            this.bindRangeButtons();
            this.bindTimeFilterIntegration();

            this.loadData();
            this.refreshTimer = setInterval(() => this.loadData(), this.config.refreshIntervalMs);
        }

        // ---------------------------------------------------------------
        // Util
        // ---------------------------------------------------------------

        fmtLabel(tsMs) {
            const d = new Date(tsMs);
            const pad = (v) => (v < 10 ? '0' + v : '' + v);
            const time = pad(d.getHours()) + ':' + pad(d.getMinutes());
            const date =
                pad(d.getDate()) +
                ' ' + ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'][d.getMonth()];

            return this.currentRange === '1H' ? time : date + ' ' + time;
        }

        // Format menit -> "Xj Ym" biar enak dibaca (durasi bisa besar untuk range 1M/harian).
        fmtDuration(minutes) {
            const total = Math.round(minutes);
            const h = Math.floor(total / 60);
            const m = total % 60;
            return h > 0 ? `${h}j ${m}m` : `${m}m`;
        }

        findNearestPoint(seriesPoints, targetX) {
            if (!seriesPoints || seriesPoints.length === 0) return null;
            let best = seriesPoints[0];
            let bestDiff = Math.abs(best.x - targetX);
            for (let i = 1; i < seriesPoints.length; i++) {
                const diff = Math.abs(seriesPoints[i].x - targetX);
                if (diff < bestDiff) {
                    bestDiff = diff;
                    best = seriesPoints[i];
                }
            }
            return best;
        }

        // LTTB downsampling (sama seperti chart-card.js)
        static lttb(data, threshold) {
            const n = data.length;
            if (threshold <= 0 || n <= threshold) return data;

            const sampled = [data[0]];
            let a = 0;
            const bucketSize = (n - 2) / (threshold - 2);

            for (let i = 0; i < threshold - 2; i++) {
                const avgStart = Math.floor((i + 1) * bucketSize) + 1;
                const avgEnd = Math.min(Math.floor((i + 2) * bucketSize) + 1, n);
                let avgX = 0;
                let avgY = 0;
                const avgLen = avgEnd - avgStart;
                for (let j = avgStart; j < avgEnd; j++) {
                    avgX += data[j].x;
                    avgY += data[j].y;
                }
                avgX /= avgLen;
                avgY /= avgLen;

                const rangeStart = Math.floor(i * bucketSize) + 1;
                const rangeEnd = Math.min(Math.floor((i + 1) * bucketSize) + 1, n);
                let maxArea = -1;
                let maxPoint = rangeStart;
                const ax = data[a].x;
                const ay = data[a].y;

                for (let k = rangeStart; k < rangeEnd; k++) {
                    const area = Math.abs((ax - avgX) * (data[k].y - ay) - (ax - data[k].x) * (avgY - ay)) * 0.5;
                    if (area > maxArea) {
                        maxArea = area;
                        maxPoint = k;
                    }
                }
                sampled.push(data[maxPoint]);
                a = maxPoint;
            }
            sampled.push(data[n - 1]);
            return sampled;
        }

        // ---------------------------------------------------------------
        // ApexCharts
        // ---------------------------------------------------------------

        buildChartOptions() {
            const self = this;

            return {
                series: [],
                chart: {
                    id: 'admin-duration-chart',
                    height: '85%',
                    type: 'area',
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                    background: 'transparent',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 500,
                        dynamicAnimation: {
                            enabled: true
                        },
                    },
                },
                theme: {
                    mode: 'dark'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        type: 'vertical',
                        shadeIntensity: 0,
                        opacityFrom: 0.12,
                        opacityTo: 0.0,
                        stops: [0, 100],
                    },
                },
                yaxis: {
                    min: 0,
                    labels: {
                        show: true,
                        style: {
                            colors: '#52525b',
                            fontSize: '11px',
                            fontWeight: 500
                        },
                        formatter: (val) => this.fmtDuration(val),
                    },
                },
                xaxis: {
                    type: 'datetime',
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#71717a',
                            fontSize: '12px',
                            fontWeight: 500
                        },
                        datetimeUTC: false,
                        offsetY: 4,
                    },
                    tooltip: {
                        enabled: false
                    },
                },
                grid: {
                    borderColor: '#3f3f46',
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    },
                    padding: {
                        top: 0,
                        right: 16,
                        bottom: 0,
                        left: 8
                    },
                },
                legend: {
                    show: true,
                    offsetY: 25,
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '14px',
                    fontFamily: 'inherit',
                    fontWeight: 400,
                    itemMargin: {
                        horizontal: 12,
                        vertical: 8
                    },
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 12,
                        offsetX: 0,
                        offsetY: 0
                    },
                    labels: {
                        colors: '#a1a1aa',
                        useSeriesColors: false
                    },
                },
                markers: {
                    size: 0,
                    strokeWidth: 2.5,
                    strokeColors: '#1c1c1e',
                    fillOpacity: 1,
                    hover: {
                        size: 6,
                        sizeOffset: 0
                    },
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    x: {
                        show: false
                    },
                    custom(opts) {
                        return self.renderTooltip(opts);
                    },
                },
            };
        }

        renderTooltip(opts) {
            const {
                series,
                dataPointIndex,
                w
            } = opts;
            const ts = w.globals.seriesX[0][dataPointIndex];
            const lbl = this.fmtLabel(ts);
            const seriesNames = w.globals.seriesNames;
            const seriesColors = w.globals.colors;

            let rows = '';
            for (let si = 0; si < series.length; si++) {
                const name = seriesNames[si];
                const color = seriesColors[si] || '#e4e4e7';

                const rawArr = this.rawSeriesData[si] ? this.rawSeriesData[si].data : [];
                const ts0 = w.globals.seriesX[si][dataPointIndex];
                const nearest = this.findNearestPoint(rawArr, ts0);
                const rawVal = nearest ? nearest.y : null;

                if (rawVal != null) {
                    rows += `
                        <div style="border-left:3px solid ${color};padding-left:10px;line-height:1.3;">
                            <div style="font-size:11px;color:#71717a;font-weight:600;margin-bottom:5px;text-transform:uppercase;letter-spacing:.05em;">${name}</div>
                            <div style="font-weight:600;color:#e4e4e7;font-size:14px;">${this.fmtDuration(rawVal)}</div>
                        </div>`;
                }
            }

            return `
                <div style="background:#2c2c2e;border:1px solid #3f3f46;border-radius:16px;padding:14px 16px;min-width:160px;box-shadow:0 10px 40px rgba(0,0,0,0.4);">
                    <div style="font-size:12px;color:#e4e4e7;font-weight:600;margin-bottom:12px;">${lbl}</div>
                    <div style="display:flex;flex-direction:column;gap:12px;">${rows}</div>
                </div>`;
        }

        // ---------------------------------------------------------------
        // Data loading
        // ---------------------------------------------------------------

        async loadData() {
            try {
                const params = new URLSearchParams({
                    range: this.currentRange
                });

                if (this.currentRange === 'CUSTOM' && this.customRange?.start && this.customRange?.end) {
                    params.set('start', this.formatDateParam(this.customRange.start));
                    params.set('end', this.formatDateParam(this.customRange.end));
                    params.set('resolution', this.customRange.resolution || 'detail');
                }

                const response = await fetch(`${this.config.endpoint}?${params.toString()}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const fetchedData = await response.json();
                const downsampledData = fetchedData.map((s) => ({
                    name: s.name,
                    data: AdminDurationChart.lttb(s.data, this.config.maxVisiblePoints),
                }));

                this.rawSeriesData = downsampledData;

                // Total selalu index 0 dari response (sesuai backend) -> warna khusus,
                // stroke lebih tebal biar menonjol dari series per-device.
                const colors = downsampledData.map((s, i) =>
                    s.name === 'Total' ? TOTAL_COLOR : DEVICE_COLOR_PALETTE[i % DEVICE_COLOR_PALETTE.length]
                );
                const strokeWidths = downsampledData.map((s) => (s.name === 'Total' ? 4 : 2));

                this.apexChart.updateOptions({
                    colors,
                    stroke: {
                        curve: 'smooth',
                        width: strokeWidths
                    },
                    series: downsampledData
                },
                    true,
                    true
                );
            } catch (error) {
                console.error('Gagal mengambil data durasi operasional admin:', error);
            }
        }

        formatDateParam(d) {
            const pad = (v) => (v < 10 ? '0' + v : '' + v);
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        }

        // ---------------------------------------------------------------
        // Filter rentang waktu (1H / 1M / Custom)
        // ---------------------------------------------------------------

        bindRangeButtons() {
            this.els.rangeGroup.querySelectorAll('.range-filter-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const newRange = btn.getAttribute('data-range');
                    if (newRange === 'CUSTOM') {
                        document.dispatchEvent(new CustomEvent('tf:open', {
                            detail: {
                                context: 'admin-duration'
                            }
                        }));
                        return;
                    }
                    if (newRange === this.currentRange) return;
                    this.setActiveRange(newRange, btn);
                    this.loadData();
                });
            });
        }

        setActiveRange(range, activeBtn) {
            this.currentRange = range;
            this.els.rangeGroup.querySelectorAll('.range-filter-btn').forEach((b) => {
                b.classList.remove('text-white', 'bg-[#171717]', 'shadow');
                b.classList.add('text-zinc-400');
            });
            activeBtn.classList.remove('text-zinc-400');
            activeBtn.classList.add('text-white', 'bg-[#171717]', 'shadow');
        }

        bindTimeFilterIntegration() {
            if (!window.TimeFilterPanel) {
                console.warn('TimeFilterPanel belum dimuat sebelum admin-duration-chart');
                return;
            }

            window.TimeFilterPanel.register('admin-duration', {
                container: this.root,
                positionClasses: ['right-0'],
                hasResolutionOption: true, // endpoint admin support "detail" & "harian"
                onApply: ({
                    start,
                    end,
                    resolution
                }) => {
                    this.customRange = {
                        start,
                        end,
                        resolution
                    };
                    this.currentRange = 'CUSTOM';

                    const customBtn = this.els.rangeGroup.querySelector('.range-filter-btn[data-range="CUSTOM"]');
                    this.setActiveRange('CUSTOM', customBtn);
                    this.loadData();
                },
            });
        }

        destroy() {
            clearInterval(this.refreshTimer);
            this.apexChart.destroy();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-admin-duration-chart]').forEach((root) => new AdminDurationChart(root));
    });

    window.AdminDurationChart = AdminDurationChart;
})();
