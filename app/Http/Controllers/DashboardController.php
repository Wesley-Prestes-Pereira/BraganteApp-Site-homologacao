<?php

namespace App\Http\Controllers;

use App\Models\{Area, Reserva, TipoArea};
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    private const DIAS_MAP = [
        'SEGUNDA' => 'Seg',
        'TERCA'   => 'Ter',
        'QUARTA'  => 'Qua',
        'QUINTA'  => 'Qui',
        'SEXTA'   => 'Sex',
        'SABADO'  => 'Sáb',
        'DOMINGO' => 'Dom',
    ];

    private const DIA_CARBON = [
        'DOMINGO' => Carbon::SUNDAY,
        'SEGUNDA' => Carbon::MONDAY,
        'TERCA'   => Carbon::TUESDAY,
        'QUARTA'  => Carbon::WEDNESDAY,
        'QUINTA'  => Carbon::THURSDAY,
        'SEXTA'   => Carbon::FRIDAY,
        'SABADO'  => Carbon::SATURDAY,
    ];

    private const TODOS = ['SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO', 'DOMINGO'];
    private const SEMANA = ['SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA'];
    private const FDS = ['SABADO', 'DOMINGO'];

    public function index()
    {
        $areas = Area::with('tipoArea')
            ->withCount(['reservas' => fn($q) => $q->withoutTrashed()])
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        $tiposArea = TipoArea::where('ativo', true)->orderBy('nome')->get();

        $stats = $this->calcularStats();

        $gruposPorTipo = $tiposArea->map(fn($tipo) => (object) [
            'tipo'  => $tipo,
            'areas' => $areas->filter(fn($a) => $a->tipo_area_id === $tipo->id)->values(),
        ])->filter(fn($g) => $g->areas->isNotEmpty())->values();

        $areaData = $areas->flatMap(function ($a) use ($stats) {
            $as = $stats['porArea'][$a->id] ?? ['total' => 0, 'fixas' => 0, 'unicas' => 0, 'mensalistas' => 0];
            $dias = $a->diasCached();
            $slotsPerDay = $a->modo_reserva === 'DIA_INTEIRO' ? 1 : count($a->horariosPorDia($dias[0] ?? 'SEGUNDA'));

            return [
                $a->id => [
                    'id'          => $a->id,
                    'nome'        => $a->nome,
                    'tipo'        => $a->tipoArea?->nome ?? 'Área',
                    'icone'       => $a->tipoArea?->icone ?? 'fi-rr-marker',
                    'cor'         => $a->tipoArea?->cor ?? '#3b82f6',
                    'dias'        => $this->formatDias($dias),
                    'modo'        => $a->modo_reserva,
                    'slots'       => $slotsPerDay * count($dias),
                    'total'       => $as['total'],
                    'fixas'       => $as['fixas'],
                    'unicas'      => $as['unicas'],
                    'mensalistas' => $as['mensalistas'],
                    'url'         => route('reservas.index', ['area_id' => $a->id]),
                ],
            ];
        })->toArray();

        return view('dashboard.index', [
            'gruposPorTipo' => $gruposPorTipo,
            ...$stats,
            'areaStats'     => $stats['porArea'],
            'areaData'      => $areaData,
        ]);
    }

    private function calcularStats(): array
    {
        return Cache::tags(Reserva::CACHE_TAG)->remember('dashboard:stats', 120, function () {
            $hoje = Carbon::today();

            $diaSemana = array_search($hoje->dayOfWeek, self::DIA_CARBON);
            if ($diaSemana === false) {
                $diaSemana = 'SEGUNDA';
            }

            $reservas = Reserva::query();
            $totalReservas = (clone $reservas)->count();
            $reservasFixas = (clone $reservas)->where('tipo', 'FIXA')->count();
            $reservasMensalistas = (clone $reservas)->where('tipo', 'MENSALISTA')->count();

            $reservasHoje = Reserva::where(function ($q) use ($hoje, $diaSemana) {
                $q->where(function ($u) use ($hoje) {
                    $u->where('tipo', 'UNICA')->where('data_reserva', $hoje->format('Y-m-d'));
                })->orWhere(function ($f) use ($hoje, $diaSemana) {
                    $f->whereIn('tipo', ['FIXA', 'MENSALISTA'])
                        ->where('dia_semana', $diaSemana)
                        ->where(fn($p) => $p->whereNull('data_inicio')->orWhere('data_inicio', '<=', $hoje))
                        ->where(fn($p) => $p->whereNull('data_fim')->orWhere('data_fim', '>=', $hoje));
                });
            })->count();

            $porArea = Reserva::selectRaw('area_id, tipo, count(*) as total')
                ->groupBy('area_id', 'tipo')
                ->get()
                ->groupBy('area_id')
                ->map(fn($group) => [
                    'total'       => $group->sum('total'),
                    'fixas'       => $group->where('tipo', 'FIXA')->sum('total'),
                    'unicas'      => $group->where('tipo', 'UNICA')->sum('total'),
                    'mensalistas' => $group->where('tipo', 'MENSALISTA')->sum('total'),
                ])
                ->toArray();

            return [
                'totalReservas'       => $totalReservas,
                'reservasFixas'       => $reservasFixas,
                'reservasMensalistas' => $reservasMensalistas,
                'reservasHoje'        => $reservasHoje,
                'totalUsuarios'       => \App\Models\User::count(),
                'totalClientes'       => \App\Models\Cliente::where('ativo', true)->count(),
                'porArea'             => $porArea,
            ];
        });
    }

    private function formatDias(array $dias): string
    {
        if (count($dias) === 7 && !array_diff(self::TODOS, $dias)) return 'Segunda a Domingo';
        if (count($dias) === 5 && !array_diff(self::SEMANA, $dias)) return 'Segunda a Sexta';
        if (count($dias) === 2 && !array_diff(self::FDS, $dias)) return 'Sábado e Domingo';

        return implode(', ', array_map(fn($d) => self::DIAS_MAP[$d] ?? $d, $dias));
    }
}