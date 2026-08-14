/**
 * public/js/time-filter-panel.js
 *
 * Panel "Pengaturan Filter Waktu" dipakai bersama oleh beberapa kartu
 * (chart Tren Aktual, kartu Kinerja Pompa Air, dan kartu lain di masa depan).
 *
 * Sebelumnya logic ini hardcode untuk dua context ('chart' & 'kinerja').
 * Sekarang setiap kartu cukup memanggil TimeFilterPanel.register(context, {...})
 * saat inisialisasi, jadi menambah kartu baru tidak perlu menyentuh file ini lagi.
 *
 * DOM yang WAJIB ada di halaman (partial panel terpisah, di luar file ini):
 *   #time-filter-panel, #overlay-timepicker, #tf-month-label, #tf-days-grid,
 *   #tf-resolution-wrapper, #tf-prev-month, #tf-next-month, #tf-apply-btn,
 *   #btn-close-time-filter-chart, .tf-resolution-btn[data-resolution],
 *   .tf-preset-chip[data-preset]
 *
 * Kompatibilitas mundur: selain memanggil callback onApply yang didaftarkan
 * lewat register(), panel ini tetap men-dispatch event "tf:apply" di
 * document supaya partial lama yang masih dengarkan event tersebut (mis.
 * kartu Kinerja) tidak perlu diubah.
 */
(function () {
    'use strict';

    const MONTH_NAMES_ID = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];
    const ALL_POSITION_CLASSES = ['right-0', 'right-6', '-translate-x-full'];

    class TimeFilterPanelController {
        constructor() {
            this.contexts = {}; // { [name]: { container, positionClasses, hasResolutionOption, onApply, applied } }
            this.activeContext = null;

            this.today = new Date();
            this.today.setHours(0, 0, 0, 0);
            this.viewDate = new Date();
            this.draft = { start: null, end: null };
            this.draftResolution = 'detail';

            this.ready = false;
        }

        // Dipanggil oleh masing-masing kartu (chart-card.js, kinerja-card.js, ...)
        register(name, { container, positionClasses = ['right-0'], hasResolutionOption = false, onApply = null }) {
            this.contexts[name] = {
                container,
                positionClasses,
                hasResolutionOption,
                onApply,
                applied: { start: null, end: null, resolution: 'detail' },
            };
            this.init(); // idempotent
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

            if (!this.els.panel) return; // partial panel belum ada di halaman ini, tunggu register berikutnya

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

        // -------------------------------------------------------------
        // Open / close
        // -------------------------------------------------------------

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

            // Kartu "chart" punya z-index tinggi supaya dropdown-nya di atas kartu lain;
            // turunkan sementara kalau panel dibuka dari context lain (mis. kinerja)
            // supaya panel tidak tertutup kartu chart.
            const chartCtx = this.contexts['chart'];
            if (contextName !== 'chart' && chartCtx?.container) {
                chartCtx.container.classList.remove('z-[1111]');
            }
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

            const chartCtx = this.contexts['chart'];
            chartCtx?.container?.classList.add('z-[1111]');
        }

        applyDraft() {
            if (!this.draft.start || !this.draft.end) return; // minimal 2 tanggal harus dipilih

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

            // Kompatibilitas mundur untuk partial yang masih dengarkan CustomEvent.
            document.dispatchEvent(new CustomEvent('tf:apply', { detail: payload }));
        }

        // -------------------------------------------------------------
        // Kalender
        // -------------------------------------------------------------

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
            return { start, end };
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
                cells.push({ date: new Date(year, month - 1, daysInPrevMonth - i), currentMonth: false });
            }
            for (let d = 1; d <= daysInMonth; d++) {
                cells.push({ date: new Date(year, month, d), currentMonth: true });
            }
            while (cells.length < 42) {
                const lastDate = cells[cells.length - 1].date;
                const nextDate = new Date(lastDate);
                nextDate.setDate(lastDate.getDate() + 1);
                cells.push({ date: nextDate, currentMonth: false });
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
})();