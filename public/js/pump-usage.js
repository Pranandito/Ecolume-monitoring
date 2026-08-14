/**
 * public/js/pump-usage-bar.js
 *
 * Bar "penggunaan pompa" — SENGAJA independen dari chart-card.js.
 * Fetch datanya sendiri ke endpoint line-chart yang sama, tapi tidak
 * berbagi state apa pun dengan ChartCard (tidak butuh rawSeriesData-nya,
 * tidak butuh field yang sedang dipilih user di dropdown "Data").
 */
(function () {
    'use strict';

    const STATUS_COLOR = { penuh: '#6a5acd', sedang: '#4ea8de', mati: '#3f3f46' };
    const STATUS_LABEL = { penuh: 'Operasi Penuh', sedang: 'Operasi Sedang', mati: 'Mati' };

    class PumpUsageBar {
        constructor(root) {
            this.root = root;
            this.config = JSON.parse(root.dataset.config);

            this.barEl = root.querySelector('[data-role="bar"]');
            this.tooltipEl = root.querySelector('[data-role="tooltip"]');

            if (!this.barEl || !this.tooltipEl) {
                console.warn('Pump usage bar: elemen bar/tooltip tidak ditemukan di DOM.');
                return;
            }

            this.load();
            this.refreshTimer = setInterval(() => this.load(), this.config.refreshIntervalMs);
        }

        pad(v) {
            return v < 10 ? '0' + v : '' + v;
        }

        fmtTime(d) {
            return this.pad(d.getHours()) + ':' + this.pad(d.getMinutes());
        }

        classifyValue(y) {
            return y >= this.config.fullThreshold ? 'penuh' : 'sedang';
        }

        buildSegments(points, windowStart, windowEnd) {
            const raw = [];

            if (points.length === 0) {
                return [{ start: windowStart, end: windowEnd, status: 'mati' }];
            }

            if (points[0].x > windowStart) {
                raw.push({ start: windowStart, end: points[0].x, status: 'mati' });
            }

            for (let i = 0; i < points.length; i++) {
                const cur = points[i];
                const next = points[i + 1];
                const segEnd = next ? next.x : windowEnd;
                const gap = segEnd - cur.x;

                if (gap > this.config.gapMatiMs) {
                    raw.push({ start: cur.x, end: cur.x + this.config.gapMatiMs, status: this.classifyValue(cur.y) });
                    raw.push({ start: cur.x + this.config.gapMatiMs, end: segEnd, status: 'mati' });
                } else {
                    raw.push({ start: cur.x, end: segEnd, status: this.classifyValue(cur.y) });
                }
            }

            const merged = [];
            raw.forEach((seg) => {
                if (seg.end <= seg.start) return;
                const last = merged[merged.length - 1];
                if (last && last.status === seg.status) {
                    last.end = seg.end;
                } else {
                    merged.push({ ...seg });
                }
            });
            return merged;
        }

        renderSegments(segments, windowStart, windowEnd) {
            const totalMs = windowEnd - windowStart;
            this.barEl.innerHTML = '';

            segments.forEach((seg) => {
                const pct = ((seg.end - seg.start) / totalMs) * 100;
                if (pct <= 0) return;

                const div = document.createElement('div');
                div.className = 'h-full';
                div.style.width = pct + '%';
                div.style.backgroundColor = STATUS_COLOR[seg.status];
                div.style.cursor = 'pointer';
                div.addEventListener('mousemove', (e) => this.showTooltip(e, seg));
                div.addEventListener('mouseleave', () => this.hideTooltip());

                this.barEl.appendChild(div);
            });
        }

        showTooltip(e, seg) {
            this.tooltipEl.innerHTML = `
                <div style="font-weight:600;color:#e4e4e7;margin-bottom:2px;">${STATUS_LABEL[seg.status]}</div>
                <div style="color:#a1a1aa;">${this.fmtTime(new Date(seg.start))} – ${this.fmtTime(new Date(seg.end))}</div>`;
            this.tooltipEl.style.left = e.clientX + 12 + 'px';
            this.tooltipEl.style.top = e.clientY - 36 + 'px';
            this.tooltipEl.classList.remove('hidden');
        }

        hideTooltip() {
            this.tooltipEl.classList.add('hidden');
        }

        async load() {
            try {
                const params = new URLSearchParams({ fields: this.config.field, range: this.config.range });
                const response = await fetch(`${this.config.endpoint}?${params.toString()}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const fetchedData = await response.json();
                const series = fetchedData.find((s) => s.name === this.config.field);
                const allPoints = series ? series.data.slice().sort((a, b) => a.x - b.x) : [];

                const refDate = allPoints.length ? new Date(allPoints[allPoints.length - 1].x) : new Date();
                const dayStart = new Date(refDate.getFullYear(), refDate.getMonth(), refDate.getDate()).getTime();

                const windowStart = dayStart + 6 * 60 * 60 * 1000;
                const windowEnd = dayStart + 18 * 60 * 60 * 1000;

                const points = allPoints.filter((p) => p.x >= windowStart && p.x <= windowEnd);

                this.renderSegments(this.buildSegments(points, windowStart, windowEnd), windowStart, windowEnd);
            } catch (error) {
                console.error('Gagal mengambil data penggunaan pompa:', error);
            }
        }

        destroy() {
            clearInterval(this.refreshTimer);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-pump-usage-bar]').forEach((root) => new PumpUsageBar(root));
    });

    window.PumpUsageBar = PumpUsageBar;
})();