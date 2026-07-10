<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\MonthlyStat;
use App\Models\Opportunity;

class DashboardController extends Controller
{
    private const MONTH_LABELS = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        0 => 'Otros',
    ];

    public function index()
    {
        $year = now()->year;

        $goalAmount = (float) (Goal::where('year', $year)->value('amount') ?? 0);

        $opportunities = Opportunity::with('client')->where('year', $year)->get();

        $stats = MonthlyStat::where('year', $year)->get()->keyBy('month');

        $monthOrder = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 0];

        $summary = [];
        foreach ($monthOrder as $num) {
            $rows = $opportunities->where('month', $num);
            $summary[$num] = [
                'label' => self::MONTH_LABELS[$num],
                'facturado' => (float) $rows->where('category', 'facturado')->sum('amount'),
                'perdido' => (float) $rows->where('category', 'perdido')->sum('amount'),
                'pipeline' => (float) $rows->where('category', 'pipeline')->sum('amount'),
                'citasTotales' => (int) ($stats[$num]->total_appointments ?? 0),
                'citasNuevas' => (int) ($stats[$num]->new_appointments ?? 0),
            ];
        }

        // Only expose months that actually have any data, so the filter row isn't 13 empty chips.
        $activeMonths = array_values(array_filter($monthOrder, function ($num) use ($summary) {
            $s = $summary[$num];

            return $s['facturado'] > 0 || $s['perdido'] > 0 || $s['pipeline'] > 0
                || $s['citasTotales'] > 0 || $s['citasNuevas'] > 0;
        }));

        if (empty($activeMonths)) {
            $activeMonths = $monthOrder;
        }

        $detalle = $opportunities->map(fn (Opportunity $o) => [
            'mes' => $o->month,
            'mesLabel' => self::MONTH_LABELS[$o->month],
            'categoria' => $o->category,
            'cliente' => $o->client->name,
            'servicio' => $o->service ?? '—',
            'monto' => (float) $o->amount,
            'estado' => $o->status ?? '—',
        ])->values();

        $pendienteActual = $opportunities->where('pending_invoice', true)->map(fn (Opportunity $o) => [
            'cliente' => $o->client->name,
            'servicio' => $o->service ?? '—',
            'monto' => (float) $o->amount,
            'estado' => $o->status ?? '—',
        ])->values();

        $totalPendiente = (float) $pendienteActual->sum('monto');

        $logroYTD = (float) $opportunities->where('category', 'facturado')->sum('amount');
        $perdidoYTD = (float) $opportunities->where('category', 'perdido')->sum('amount');
        $faltaMeta = max($goalAmount - $logroYTD, 0);
        $pctMeta = $goalAmount > 0 ? min($logroYTD / $goalAmount * 100, 100) : 0;
        $winRateGlobal = ($logroYTD + $perdidoYTD) > 0 ? $logroYTD / ($logroYTD + $perdidoYTD) * 100 : 0;
        $pipelineNecesario = $winRateGlobal > 0 ? $faltaMeta / ($winRateGlobal / 100) : null;

        $pipelineEnProceso = (float) $opportunities->where('category', 'pipeline')
            ->filter(fn (Opportunity $o) => str_starts_with($o->status ?? '', 'En proceso'))
            ->sum('amount');
        $pipelineTotalNeto = $pipelineEnProceso + $totalPendiente;

        return view('dashboard', [
            'year' => $year,
            'monthLabels' => self::MONTH_LABELS,
            'activeMonths' => $activeMonths,
            'summary' => $summary,
            'detalle' => $detalle,
            'pendienteActual' => $pendienteActual,
            'totalPendiente' => $totalPendiente,
            'metaAnual' => $goalAmount,
            'logroYTD' => $logroYTD,
            'faltaMeta' => $faltaMeta,
            'pctMeta' => $pctMeta,
            'winRateGlobal' => $winRateGlobal,
            'pipelineNecesario' => $pipelineNecesario,
            'pipelineTotalNeto' => $pipelineTotalNeto,
        ]);
    }
}
