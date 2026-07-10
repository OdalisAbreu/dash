<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — Reporte comercial {{ $year }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .display-font { font-family: 'Space Grotesk', 'Inter', ui-sans-serif, system-ui, sans-serif; }
        table.dt th { cursor: pointer; user-select: none; }
        ::-webkit-scrollbar { height: 8px; width: 8px; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#EEF1F6] text-[#1A2333] min-h-screen" x-data="dashboard()" x-cloak>

    <div class="max-w-[1240px] mx-auto px-6 pt-7 pb-16">

        {{-- Header --}}
        <div class="flex items-baseline justify-between flex-wrap gap-2 mb-6">
            <div>
                <div class="text-xs font-bold tracking-[0.12em] text-[#3E63DD] uppercase">Reporte comercial</div>
                <h1 class="display-font text-[28px] sm:text-[30px] font-bold mt-1 mb-0.5 tracking-tight">
                    Actividad de ventas — {{ $year }}
                </h1>
                <div class="text-[13.5px] text-[#64748B]">Seguimiento de facturación, oportunidades perdidas, pendientes y pipeline generado.</div>
            </div>
            <a href="{{ route('filament.admin.pages.dashboard') }}" class="text-[13px] font-semibold text-[#3E63DD] hover:underline">Acceso administrador →</a>
        </div>

        {{-- Meta anual --}}
        <div class="bg-[#1A2333] rounded-2xl px-6 py-5 mb-5">
            <div class="grid gap-5 mb-4" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                <div>
                    <div class="text-[11.5px] font-bold text-[#94A3B8] uppercase tracking-wider mb-1.5">Meta {{ $year }}</div>
                    <div class="display-font text-[26px] font-bold text-white tabular-nums">{{ money($metaAnual) }}</div>
                </div>
                <div>
                    <div class="text-[11.5px] font-bold text-[#94A3B8] uppercase tracking-wider mb-1.5">Logro YTD</div>
                    <div class="display-font text-[26px] font-bold text-[#34D399] tabular-nums">{{ money($logroYTD) }}</div>
                </div>
                <div>
                    <div class="text-[11.5px] font-bold text-[#94A3B8] uppercase tracking-wider mb-1.5">Falta para la meta</div>
                    <div class="display-font text-[26px] font-bold text-[#F5A623] tabular-nums">{{ money($faltaMeta) }}</div>
                </div>
                <div>
                    <div class="text-[11.5px] font-bold text-[#94A3B8] uppercase tracking-wider mb-1.5">Pipeline necesario para la meta</div>
                    <div class="display-font text-[26px] font-bold text-[#93C5FD] tabular-nums">{{ $pipelineNecesario === null ? '—' : money($pipelineNecesario) }}</div>
                    <div class="text-[10.5px] text-[#64748B] mt-0.5">a tasa de cierre actual: {{ number_format($winRateGlobal, 0) }}%</div>
                </div>
            </div>
            <div class="h-3 rounded-full bg-white/10 overflow-hidden">
                <div class="h-full rounded-full" style="width: {{ $pctMeta }}%; background: linear-gradient(90deg, #34D399, #0F9D6E);"></div>
            </div>
            <div class="text-xs text-[#94A3B8] mt-2">{{ number_format($pctMeta, 1) }}% de la meta anual alcanzado ({{ $year }})</div>
        </div>

        {{-- Filters --}}
        <div class="flex gap-3.5 flex-wrap mb-5">
            <div class="bg-white border border-[#E2E8F0] rounded-2xl px-4 py-3 flex-[1_1_320px]">
                <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Mes</div>
                <div class="flex gap-2 flex-wrap">
                    <template x-for="m in monthsAll" :key="m.num">
                        <div @click="toggleMonth(m.num)" x-text="m.label"
                             class="cursor-pointer select-none transition-all px-3.5 py-1.5 rounded-full text-[13px] font-semibold"
                             :class="selectedMonths.includes(m.num) ? 'bg-[#1A2333] text-white border border-[#1A2333]' : 'bg-[#F1F4F9] text-[#64748B] border border-[#E2E8F0]'">
                        </div>
                    </template>
                </div>
            </div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl px-4 py-3 flex-[2_1_500px]">
                <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Variable</div>
                <div class="flex gap-2 flex-wrap">
                    <template x-for="c in categories" :key="c.key">
                        <div @click="toggleCat(c.key)"
                             class="cursor-pointer select-none transition-all px-3.5 py-1.5 rounded-full text-[13px] font-semibold flex items-center gap-1.5"
                             :style="selectedCats.includes(c.key) ? `background:${c.color}1A;color:${c.color};border:1px solid ${c.color}55` : 'background:#F1F4F9;color:#64748B;border:1px solid #E2E8F0'">
                            <span class="w-2 h-2 rounded-full" :style="`background:${selectedCats.includes(c.key) ? c.color : '#CBD5E1'}`"></span>
                            <span x-text="c.label"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="grid gap-3.5 mb-5" style="grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));">
            <div class="bg-white border border-[#E2E8F0] rounded-2xl px-4 py-3.5">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#0F9D6E1A;color:#0F9D6E;">$</div>
                    <div class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wide">Facturado</div>
                </div>
                <div class="display-font text-[22px] font-bold tabular-nums" x-text="money(kpis().facturado)"></div>
            </div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl px-4 py-3.5">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#E14F441A;color:#E14F44;">↓</div>
                    <div class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wide">Perdido</div>
                </div>
                <div class="display-font text-[22px] font-bold tabular-nums" x-text="money(kpis().perdido)"></div>
            </div>
            <div class="bg-white rounded-2xl px-4 py-3.5 cursor-pointer transition-all"
                 :class="pendienteOpen ? 'border border-[#D89A2C]' : 'border border-[#E2E8F0]'"
                 :style="pendienteOpen ? 'box-shadow:0 0 0 3px #D89A2C22' : ''"
                 @click="pendienteOpen = !pendienteOpen">
                <div class="flex items-center justify-between mb-2.5">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#D89A2C1A;color:#D89A2C;">⏱</div>
                        <div class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wide">Pendiente por facturar</div>
                    </div>
                    <span x-text="pendienteOpen ? '▲' : '▼'" class="text-[#94A3B8] text-xs"></span>
                </div>
                <div class="display-font text-[22px] font-bold tabular-nums">{{ money($totalPendiente) }}</div>
                <div class="text-[10.5px] text-[#94A3B8] mt-1" x-text="pendienteOpen ? 'Ocultar detalle' : 'Ver detalle'"></div>
            </div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl px-4 py-3.5">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#3E63DD1A;color:#3E63DD;">↑</div>
                    <div class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wide">Pipeline nuevo generado</div>
                </div>
                <div class="display-font text-[22px] font-bold tabular-nums" x-text="money(kpis().pipeline)"></div>
            </div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl px-4 py-3.5">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#6D28D91A;color:#6D28D9;">◎</div>
                    <div class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wide">Pipeline total (proceso + pend.)</div>
                </div>
                <div class="display-font text-[22px] font-bold tabular-nums">{{ money($pipelineTotalNeto) }}</div>
            </div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl px-4 py-3.5">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#0EA5B71A;color:#0EA5B7;">📅</div>
                    <div class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wide">Citas (total / nuevas)</div>
                </div>
                <div class="display-font text-[22px] font-bold tabular-nums" x-text="kpis().citasTotales + ' / ' + kpis().citasNuevas"></div>
            </div>
        </div>

        {{-- Pendiente detail --}}
        <div x-show="pendienteOpen" x-cloak class="mb-5">
            <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden">
                <div class="px-4.5 py-3.5 flex items-center justify-between border-b border-[#E2E8F0]">
                    <div class="flex items-center gap-2.5">
                        <div class="display-font text-[15px] font-bold">Pendiente por facturar — detalle</div>
                        <div class="text-xs text-[#64748B] bg-[#F1F4F9] px-2 py-0.5 rounded-full">{{ $pendienteActual->count() }} clientes</div>
                    </div>
                    <div class="display-font text-lg font-bold text-[#D89A2C]">{{ money($totalPendiente) }}</div>
                </div>
                <div class="overflow-x-auto">
                    <table class="dt border-collapse w-full">
                        <thead>
                            <tr class="text-left text-[11px] tracking-wider uppercase text-[#64748B]">
                                <th class="px-3 py-2.5 border-b-2 border-[#E2E8F0]">Cliente</th>
                                <th class="px-3 py-2.5 border-b-2 border-[#E2E8F0]">Servicio / Programa</th>
                                <th class="px-3 py-2.5 border-b-2 border-[#E2E8F0] text-right">Monto</th>
                                <th class="px-3 py-2.5 border-b-2 border-[#E2E8F0]">Estado / Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendienteActual as $p)
                            <tr class="hover:bg-[#F7F9FC]">
                                <td class="px-3 py-2.5 border-b border-[#EDF1F5] font-semibold text-[13.5px]">{{ $p['cliente'] }}</td>
                                <td class="px-3 py-2.5 border-b border-[#EDF1F5] text-[#475569] text-[13.5px]">{{ $p['servicio'] }}</td>
                                <td class="px-3 py-2.5 border-b border-[#EDF1F5] text-right font-semibold tabular-nums text-[13.5px]">{{ money($p['monto']) }}</td>
                                <td class="px-3 py-2.5 border-b border-[#EDF1F5] text-[#64748B] text-[13.5px]">{{ $p['estado'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Win rate --}}
        <div class="bg-white border border-[#E2E8F0] rounded-2xl px-5 py-4 mb-5">
            <div class="flex justify-between items-center mb-2">
                <div class="text-[13px] font-bold">Tasa de cierre (Facturado vs. Perdido) — meses seleccionados</div>
                <div class="display-font text-xl font-bold" :class="winRate() >= 50 ? 'text-[#0F9D6E]' : 'text-[#E14F44]'" x-text="winRate().toFixed(0) + '%'"></div>
            </div>
            <div class="h-2.5 rounded-full bg-[#F1F4F9] overflow-hidden flex">
                <div class="bg-[#0F9D6E]" :style="`width:${winRate()}%`"></div>
                <div class="bg-[#E14F44]" :style="`width:${100 - winRate()}%`"></div>
            </div>
        </div>

        {{-- Bar chart --}}
        <div class="bg-white border border-[#E2E8F0] rounded-2xl px-4.5 py-4 mb-5">
            <div class="display-font text-[15px] font-bold mb-2.5">Evolución mensual por variable</div>
            <div style="height: 300px;"><canvas id="barChart"></canvas></div>
        </div>

        {{-- Citas chart --}}
        <div class="bg-white border border-[#E2E8F0] rounded-2xl px-4.5 py-4 mb-5">
            <div class="display-font text-[15px] font-bold mb-2.5">Citas por mes (total vs. nuevas)</div>
            <div style="height: 220px;"><canvas id="citasChart"></canvas></div>
        </div>

        {{-- Detail table --}}
        <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden">
            <div class="px-4.5 py-3.5 flex items-center justify-between border-b border-[#E2E8F0] cursor-pointer" @click="tableOpen = !tableOpen">
                <div class="flex items-center gap-2.5">
                    <div class="display-font text-[15px] font-bold">Detalle de oportunidades</div>
                    <div class="text-xs text-[#64748B] bg-[#F1F4F9] px-2 py-0.5 rounded-full" x-text="filteredDetalle().length + ' registros'"></div>
                </div>
                <div class="flex items-center gap-3">
                    <div x-show="tableOpen" @click.stop class="relative">
                        <input x-model="search" placeholder="Buscar cliente o servicio..."
                               class="pl-3 pr-3 py-1.5 rounded-lg border border-[#E2E8F0] text-[13px] outline-none w-[230px]">
                    </div>
                    <span x-text="tableOpen ? '▲' : '▼'" class="text-[#64748B]"></span>
                </div>
            </div>
            <div x-show="tableOpen" x-cloak class="overflow-x-auto">
                <table class="dt border-collapse w-full">
                    <thead>
                        <tr class="text-left text-[11px] tracking-wider uppercase text-[#64748B]">
                            <th class="px-3 py-2.5 border-b-2 border-[#E2E8F0]" @click="setSort('mes')">Mes</th>
                            <th class="px-3 py-2.5 border-b-2 border-[#E2E8F0]" @click="setSort('categoria')">Variable</th>
                            <th class="px-3 py-2.5 border-b-2 border-[#E2E8F0]" @click="setSort('cliente')">Cliente</th>
                            <th class="px-3 py-2.5 border-b-2 border-[#E2E8F0]" @click="setSort('servicio')">Servicio / Programa</th>
                            <th class="px-3 py-2.5 border-b-2 border-[#E2E8F0] text-right" @click="setSort('monto')">Monto</th>
                            <th class="px-3 py-2.5 border-b-2 border-[#E2E8F0]" @click="setSort('estado')">Estado / Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(d, i) in filteredDetalle()" :key="i">
                            <tr class="hover:bg-[#F7F9FC]">
                                <td class="px-3 py-2.5 border-b border-[#EDF1F5] text-[13.5px]" x-text="d.mesLabel"></td>
                                <td class="px-3 py-2.5 border-b border-[#EDF1F5] text-[13.5px]">
                                    <span class="text-[11.5px] font-semibold px-2.5 py-1 rounded-full"
                                          :style="`background:${catMap()[d.categoria].color}1A;color:${catMap()[d.categoria].color}`"
                                          x-text="catMap()[d.categoria].label"></span>
                                </td>
                                <td class="px-3 py-2.5 border-b border-[#EDF1F5] font-semibold text-[13.5px]" x-text="d.cliente"></td>
                                <td class="px-3 py-2.5 border-b border-[#EDF1F5] text-[#475569] text-[13.5px]" x-text="d.servicio"></td>
                                <td class="px-3 py-2.5 border-b border-[#EDF1F5] text-right font-semibold tabular-nums text-[13.5px]" x-text="money(d.monto)"></td>
                                <td class="px-3 py-2.5 border-b border-[#EDF1F5] text-[#64748B] text-[13.5px]" x-text="d.estado"></td>
                            </tr>
                        </template>
                        <tr x-show="filteredDetalle().length === 0">
                            <td colspan="6" class="text-center py-7 text-[#94A3B8]">No hay registros para los filtros seleccionados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-[11.5px] text-[#94A3B8] mt-3 text-center">
            Datos administrados internamente. Última actualización: {{ now()->format('d/m/Y') }}.
        </div>
    </div>

    <script>
        function dashboard() {
            return {
                monthsAll: @json(collect($activeMonths)->map(fn($n) => ['num' => $n, 'label' => $monthLabels[$n]])->values()),
                categories: [
                    { key: 'facturado', label: 'Ganado', color: '#0F9D6E' },
                    { key: 'perdido', label: 'Perdido', color: '#E14F44' },
                    { key: 'pipeline', label: 'Pipeline nuevo', color: '#3E63DD' },
                ],
                summary: @json(collect($summary)->keyBy(fn($v,$k)=>$k)),
                detalle: @json($detalle),
                selectedMonths: @json($activeMonths),
                selectedCats: ['facturado', 'perdido', 'pipeline'],
                search: '',
                sortField: 'monto',
                sortDir: 'desc',
                tableOpen: true,
                pendienteOpen: false,
                barChart: null,
                citasChart: null,

                catMap() {
                    return Object.fromEntries(this.categories.map(c => [c.key, c]));
                },

                toggleMonth(m) {
                    this.selectedMonths = this.selectedMonths.includes(m)
                        ? this.selectedMonths.filter(x => x !== m)
                        : [...this.selectedMonths, m];
                },
                toggleCat(k) {
                    this.selectedCats = this.selectedCats.includes(k)
                        ? this.selectedCats.filter(x => x !== k)
                        : [...this.selectedCats, k];
                },

                kpis() {
                    const acc = { facturado: 0, perdido: 0, pipeline: 0, citasTotales: 0, citasNuevas: 0 };
                    this.selectedMonths.forEach(m => {
                        const r = this.summary[m];
                        if (!r) return;
                        acc.facturado += r.facturado;
                        acc.perdido += r.perdido;
                        acc.pipeline += r.pipeline;
                        acc.citasTotales += r.citasTotales;
                        acc.citasNuevas += r.citasNuevas;
                    });
                    return acc;
                },

                winRate() {
                    const k = this.kpis();
                    return (k.facturado + k.perdido) > 0 ? (k.facturado / (k.facturado + k.perdido) * 100) : 0;
                },

                setSort(field) {
                    if (this.sortField === field) {
                        this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortField = field;
                        this.sortDir = 'desc';
                    }
                },

                filteredDetalle() {
                    let rows = this.detalle.filter(d => this.selectedMonths.includes(d.mes) && this.selectedCats.includes(d.categoria));
                    if (this.search.trim()) {
                        const q = this.search.toLowerCase();
                        rows = rows.filter(d => d.cliente.toLowerCase().includes(q) || d.servicio.toLowerCase().includes(q) || d.estado.toLowerCase().includes(q));
                    }
                    const field = this.sortField, dir = this.sortDir;
                    rows = [...rows].sort((a, b) => {
                        let av = a[field], bv = b[field];
                        if (typeof av === 'string') { av = av.toLowerCase(); bv = bv.toLowerCase(); }
                        if (av < bv) return dir === 'asc' ? -1 : 1;
                        if (av > bv) return dir === 'asc' ? 1 : -1;
                        return 0;
                    });
                    return rows;
                },

                money(n) {
                    if (n === null || n === undefined) return '—';
                    return '$' + Math.round(n).toLocaleString('en-US');
                },
                moneyShort(n) {
                    if (Math.abs(n) >= 1000) return '$' + (n / 1000).toFixed(0) + 'k';
                    return '$' + n;
                },

                renderCharts() {
                    const months = this.monthsAll.filter(m => this.selectedMonths.includes(m.num));
                    const labels = months.map(m => m.label);
                    const catList = this.categories.filter(c => this.selectedCats.includes(c.key));

                    const barCtx = document.getElementById('barChart');
                    if (this.barChart) this.barChart.destroy();
                    this.barChart = new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: catList.map(c => ({
                                label: c.label,
                                data: months.map(m => this.summary[m.num][c.key]),
                                backgroundColor: c.color,
                                borderRadius: 5,
                                maxBarThickness: 38,
                            })),
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top', labels: { font: { size: 12.5 } } },
                                tooltip: {
                                    callbacks: { label: (ctx) => `${ctx.dataset.label}: ${this.money(ctx.raw)}` },
                                },
                            },
                            scales: {
                                x: { grid: { display: false }, ticks: { font: { size: 12.5 }, color: '#64748B' } },
                                y: { grid: { color: '#EDF1F5' }, ticks: { callback: (v) => this.moneyShort(v), font: { size: 12 }, color: '#64748B' } },
                            },
                        },
                    });

                    const citasCtx = document.getElementById('citasChart');
                    if (this.citasChart) this.citasChart.destroy();
                    this.citasChart = new Chart(citasCtx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [
                                { label: 'Citas totales', data: months.map(m => this.summary[m.num].citasTotales), backgroundColor: '#0EA5B7', borderRadius: 5, maxBarThickness: 44 },
                                { label: 'Citas nuevas', data: months.map(m => this.summary[m.num].citasNuevas), backgroundColor: '#93C5FD', borderRadius: 5, maxBarThickness: 44 },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top', labels: { font: { size: 12.5 } } },
                            },
                            scales: {
                                x: { grid: { display: false }, ticks: { font: { size: 12.5 }, color: '#64748B' } },
                                y: { grid: { color: '#EDF1F5' }, ticks: { font: { size: 12 }, color: '#64748B' } },
                            },
                        },
                    });
                },

                init() {
                    this.renderCharts();
                    this.$watch('selectedMonths', () => this.renderCharts());
                    this.$watch('selectedCats', () => this.renderCharts());
                },
            };
        }
    </script>
</body>
</html>
