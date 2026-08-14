/**
 * public/js/chart-card.js
 *
 * Menggantikan <script> inline yang sebelumnya ada di chart-card.blade.php.
 * Semua state (rawSeriesData, currentRange, appliedFields, dst) sekarang
 * jadi properti instance ChartCard, bukan variabel global — supaya kartu
 * ini bisa dipakai berkali-kali di satu halaman tanpa saling tabrakan,
 * dan supaya JS-nya bisa di-cache browser (tidak ikut berubah tiap request
 * Blade).
 *
 * Kontrak yang dipertahankan dari versi lama (dipakai partial lain):
 *   - id "chart-card-container" pada elemen root
 *   - event "tf:open" (detail.context = 'chart') untuk membuka panel tanggal
 *   - event "tf:apply" (detail.context = 'chart') tidak dipakai chart (chart
 *     menangani apply lewat callback onApply saat register ke TimeFilterPanel)
 */
(function () {
    'use strict';

    class ChartCard {
        constructor(root) {
            this.root = root;
            this.config = JSON.parse(root.dataset.config);

            this.fieldMeta = this.config.fieldMeta;
            this.allFields = this.config.allFields;
            this.appliedFields = this.config.defaultFields.slice();
            this.selectedFields = this.config.defaultFields.slice(); // draft dropdown

            this.currentRange = '1H';
            this.customRange = null; // { start, end, resolution } saat range === 'CUSTOM'
            this.rawSeriesData = []; // data yang SEDANG ditampilkan di chart (sudah di-downsample)

            this.els = this.queryElements();
            this.apexChart = new ApexCharts(this.els.chart, this.buildChartOptions());
            this.apexChart.render();

            this.bindDropdown();
            this.bindRangeButtons();
            this.bindExportButton();
            this.bindTimeFilterIntegration();

            this.loadData();
            this.refreshTimer = setInterval(() => this.loadData(), this.config.refreshIntervalMs);
        }

        queryElements() {
            const q = (role) => this.root.querySelector(`[data-role="${role}"]`);
            return {
                chart: q('chart'),
                dropdownBtn: q('dropdown-btn'),
                dropdownMenu: q('dropdown-menu'),
                dropdownOverlay: q('dropdown-overlay'),
                dropdownIcon: q('dropdown-icon'),
                dropdownList: q('dropdown-list'),
                dropdownCancel: q('dropdown-cancel'),
                dropdownApply: q('dropdown-apply'),
                rangeGroup: q('range-filter-group'),
                exportBtn: q('export-btn'),
                exportLabel: q('export-label'),
            };
        }

        // ---------------------------------------------------------------
        // Util
        // ---------------------------------------------------------------

        getOrderedFields(fields) {
            return this.allFields.filter((f) => fields.indexOf(f) !== -1);
        }

        fmtLabel(tsMs) {
            const d = new Date(tsMs);
            const pad = (v) => (v < 10 ? '0' + v : '' + v);
            const time = pad(d.getHours()) + ':' + pad(d.getMinutes());
            const date =
                pad(d.getDate()) +
                ' ' +
                ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'][d.getMonth()];

            return this.currentRange === '1H' ? time : date + ' ' + time;
        }

        // Titik terdekat pada sebuah seri untuk timestamp tertentu.
        // Dipakai bareng oleh tooltip & export CSV supaya keduanya konsisten.
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

        // LTTB downsampling
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

        // Normalisasi ke 0-100% untuk sumbu Y, sekaligus menyimpan data mentah
        // (sudah di-downsample) yang dipakai tooltip & export.
        normalizeSeries(seriesArray) {
            this.rawSeriesData = seriesArray;
            return seriesArray.map((s) => {
                const values = s.data.map((p) => p.y);
                const minVal = Math.min.apply(null, values);
                const maxVal = Math.max.apply(null, values);
                const range = maxVal - minVal || 1;

                return {
                    name: s.name,
                    data: s.data.map((p) => ({
                        x: p.x,
                        y: parseFloat((((p.y - minVal) / range) * 100).toFixed(2)),
                    })),
                };
            });
        }

        // ---------------------------------------------------------------
        // ApexCharts
        // ---------------------------------------------------------------

        buildChartOptions() {
            const self = this;

            return {
                series: [],
                chart: {
                    id: `ptsp-chart-${this.config.deviceId}`,
                    height: 500,
                    type: 'area',
                    fontFamily: 'inherit',
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    background: 'transparent',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 500,
                        dynamicAnimation: { enabled: true },
                    },
                },
                theme: { mode: 'dark' },
                colors: this.config.defaultFields.map((f) => this.fieldMeta[f].color),
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
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
                    max: 100,
                    tickAmount: 4,
                    labels: {
                        show: true,
                        style: { colors: '#52525b', fontSize: '11px', fontWeight: 500 },
                        formatter: (val) => Math.round(val),
                    },
                },
                xaxis: {
                    type: 'datetime',
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: '#71717a', fontSize: '12px', fontWeight: 500 },
                        datetimeUTC: false,
                        offsetY: 4,
                    },
                    tooltip: { enabled: false },
                },
                grid: {
                    borderColor: '#3f3f46',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } },
                    padding: { top: 0, right: 16, bottom: 0, left: 8 },
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '14px',
                    fontFamily: 'inherit',
                    fontWeight: 400,
                    itemMargin: { horizontal: 12, vertical: 8 },
                    markers: { width: 12, height: 12, radius: 12, offsetX: 0, offsetY: 0 },
                    labels: { colors: '#a1a1aa', useSeriesColors: false },
                },
                markers: {
                    size: 0,
                    strokeWidth: 2.5,
                    strokeColors: '#1c1c1e',
                    fillOpacity: 1,
                    hover: { size: 6, sizeOffset: 0 },
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    x: { show: false },
                    custom(opts) {
                        return self.renderTooltip(opts);
                    },
                },
            };
        }

        renderTooltip(opts) {
            const { series, dataPointIndex, w } = opts;
            const ts = w.globals.seriesX[0][dataPointIndex];
            const lbl = this.fmtLabel(ts);
            const seriesNames = w.globals.seriesNames;

            let rows = '';
            for (let si = 0; si < series.length; si++) {
                const fieldKey = seriesNames[si];
                const meta = this.fieldMeta[fieldKey] || { label: fieldKey, unit: '', color: '#e4e4e7' };

                const rawArr = this.rawSeriesData[si] ? this.rawSeriesData[si].data : [];
                const ts0 = w.globals.seriesX[si][dataPointIndex];
                const nearest = this.findNearestPoint(rawArr, ts0);
                const rawVal = nearest ? nearest.y : null;

                if (rawVal != null) {
                    const displayVal = meta.unit === 'l/min' ? parseFloat(rawVal).toFixed(1) : Math.round(rawVal);
                    rows += `
                        <div style="border-left:3px solid ${meta.color};padding-left:10px;line-height:1.3;">
                            <div style="font-size:11px;color:#71717a;font-weight:600;margin-bottom:5px;text-transform:uppercase;letter-spacing:.05em;">${meta.label}</div>
                            <div style="font-weight:600;color:#e4e4e7;font-size:14px;">${displayVal} <span style="font-size:11px;color:#71717a;">${meta.unit}</span></div>
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
                const ordered = this.getOrderedFields(this.appliedFields);
                const params = new URLSearchParams({ fields: ordered.join(','), range: this.currentRange });

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
                    data: ChartCard.lttb(s.data, this.config.maxVisiblePoints),
                }));

                const finalSeries = this.normalizeSeries(downsampledData);
                const chartColors = fetchedData.map((s) => (this.fieldMeta[s.name] || {}).color || '#a1a1aa');

                this.apexChart.updateOptions({ colors: chartColors, series: finalSeries }, true, true);
                this.setExportEnabled(finalSeries.length > 0);
            } catch (error) {
                console.error('Gagal mengambil data dari API InfluxDB:', error);
                this.setExportEnabled(false);
            }
        }

        formatDateParam(d) {
            const pad = (v) => (v < 10 ? '0' + v : '' + v);
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        }

        // ---------------------------------------------------------------
        // Dropdown "Data"
        // ---------------------------------------------------------------

        bindDropdown() {
            const { dropdownBtn, dropdownOverlay, dropdownCancel, dropdownApply } = this.els;

            dropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.isDropdownOpen() ? this.closeDropdown(true) : this.openDropdown();
            });
            dropdownOverlay.addEventListener('click', () => this.closeDropdown(true));
            dropdownCancel.addEventListener('click', () => {
                this.selectedFields = this.appliedFields.slice();
                this.renderFieldOptions();
            });
            dropdownApply.addEventListener('click', () => {
                this.appliedFields = this.selectedFields.slice();
                this.closeDropdown(false);
                this.loadData();
            });
        }

        isDropdownOpen() {
            return !this.els.dropdownMenu.classList.contains('hidden');
        }

        openDropdown() {
            this.selectedFields = this.appliedFields.slice();
            this.renderFieldOptions();
            this.els.dropdownMenu.classList.remove('hidden');
            this.els.dropdownOverlay.classList.remove('hidden');
            this.els.dropdownIcon.style.transform = 'rotate(180deg)';
        }

        closeDropdown(discardDraft) {
            if (discardDraft) this.selectedFields = this.appliedFields.slice();
            this.els.dropdownMenu.classList.add('hidden');
            this.els.dropdownOverlay.classList.add('hidden');
            this.els.dropdownIcon.style.transform = 'rotate(0deg)';
        }

        renderFieldOptions() {
            const list = this.els.dropdownList;
            list.innerHTML = '';

            this.allFields.forEach((key) => {
                const meta = this.fieldMeta[key];
                const isChecked = this.selectedFields.indexOf(key) !== -1;

                const item = document.createElement('div');
                item.className =
                    'flex items-center gap-3 px-2.5 py-2 rounded-lg hover:bg-[#242424] cursor-pointer transition-colors';
                item.style.borderLeft = `3px solid ${meta.color}`;
                item.innerHTML = `
                    <span class="field-checkbox-box w-4 h-4 rounded-md border flex items-center justify-center transition-colors"
                        style="border-color:${meta.color};background:${isChecked ? meta.color : 'transparent'};">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round" style="opacity:${isChecked ? '1' : '0'}">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                    </span>
                    <span class="flex-1 text-[11px] font-semibold text-zinc-400 uppercase tracking-wide">${meta.label}</span>
                    <span class="text-[11px]" style="color:#52525b;">${meta.unit}</span>`;

                item.addEventListener('click', () => {
                    const idx = this.selectedFields.indexOf(key);
                    if (idx === -1) {
                        this.selectedFields.push(key);
                    } else {
                        if (this.selectedFields.length === 1) return; // minimal 1 data harus aktif
                        this.selectedFields.splice(idx, 1);
                    }
                    this.renderFieldOptions();
                });

                list.appendChild(item);
            });
        }

        // ---------------------------------------------------------------
        // Filter rentang waktu (1H / 1M / Custom)
        // ---------------------------------------------------------------

        bindRangeButtons() {
            this.els.rangeGroup.querySelectorAll('.range-filter-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const newRange = btn.getAttribute('data-range');
                    if (newRange === 'CUSTOM') {
                        document.dispatchEvent(new CustomEvent('tf:open', { detail: { context: 'chart' } }));
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

        // Dipanggil oleh TimeFilterPanel (lihat time-filter-panel.js) saat
        // context "chart" menekan tombol Terapkan pada panel tanggal custom.
        bindTimeFilterIntegration() {
            if (!window.TimeFilterPanel) {
                console.warn('TimeFilterPanel belum dimuat sebelum chart-card.js');
                return;
            }

            window.TimeFilterPanel.register('chart', {
                container: this.root,
                positionClasses: ['right-0'],
                hasResolutionOption: true,
                onApply: ({ start, end, resolution }) => {
                    this.customRange = { start, end, resolution };
                    this.currentRange = 'CUSTOM';

                    const customBtn = this.els.rangeGroup.querySelector('.range-filter-btn[data-range="CUSTOM"]');
                    this.setActiveRange('CUSTOM', customBtn);
                    this.loadData();
                },
            });
        }

        // ---------------------------------------------------------------
        // Export CSV
        //
        // Kita export `this.rawSeriesData`, yaitu data yang SUDAH dipakai
        // untuk menggambar chart (setelah downsampling LTTB, sebelum
        // dinormalisasi ke 0-100%). Ini sengaja TIDAK hit API lagi, karena:
        //   1) tujuannya adalah "data yang sedang ditampilkan di chart" —
        //      kalau fetch ulang, hasil downsampling bisa beda & tidak lagi
        //      1:1 dengan apa yang dilihat user di layar.
        //   2) datanya sudah ada di memori, jadi tidak ada request tambahan
        //      dan tidak ada risiko race condition dengan auto-refresh.
        // Kalau nanti butuh export resolusi penuh (bukan hasil downsample),
        // baru masuk akal bikin endpoint export terpisah di backend.
        // ---------------------------------------------------------------

        bindExportButton() {
            this.els.exportBtn.addEventListener('click', () => this.exportToCSV());
        }

        setExportEnabled(enabled) {
            this.els.exportBtn.disabled = !enabled;
        }

        csvEscape(value) {
            const str = String(value ?? '');
            return /[",\n]/.test(str) ? `"${str.replace(/"/g, '""')}"` : str;
        }

        exportToCSV() {
            if (!this.rawSeriesData.length || !this.rawSeriesData[0].data.length) return;

            const orderedFields = this.getOrderedFields(this.appliedFields);
            const baseSeries = this.rawSeriesData[0];

            const header = ['Timestamp', ...orderedFields.map((f) => `${this.fieldMeta[f].label} (${this.fieldMeta[f].unit})`)];

            const rows = baseSeries.data.map((point) => {
                const ts = point.x;
                const values = this.rawSeriesData.map((series) => {
                    const nearest = this.findNearestPoint(series.data, ts);
                    return nearest ? nearest.y : '';
                });
                return [new Date(ts).toISOString(), ...values];
            });

            const csvContent = [header, ...rows].map((row) => row.map(this.csvEscape).join(',')).join('\r\n');
            this.downloadCSV(csvContent);
        }

        downloadCSV(csvContent) {
            // BOM supaya Excel membuka file dengan encoding UTF-8 yang benar.
            const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const rangeLabel = this.currentRange.toLowerCase();
            const dateStamp = new Date().toISOString().slice(0, 10);

            const link = document.createElement('a');
            link.href = url;
            link.download = `device-${this.config.deviceId}-${rangeLabel}-${dateStamp}.csv`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        destroy() {
            clearInterval(this.refreshTimer);
            this.apexChart.destroy();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-chart-card]').forEach((root) => new ChartCard(root));
    });

    window.ChartCard = ChartCard;
})();