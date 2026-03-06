<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Dompdf\Dompdf;
use App\Exports\ReservasExport;
use App\Models\{Area, Cliente, Reserva, TipoArea};
use Illuminate\Support\Facades\{Cache, DB};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\{JsonResponse, Request};

class ReservaController extends Controller
{
    private const DIAS_PT = [
        'DOMINGO' => 'Domingo',
        'SEGUNDA' => 'Segunda',
        'TERCA'   => 'Terça',
        'QUARTA'  => 'Quarta',
        'QUINTA'  => 'Quinta',
        'SEXTA'   => 'Sexta',
        'SABADO'  => 'Sábado',
    ];

    private const MESES_PT = [
        'Janeiro',
        'Fevereiro',
        'Março',
        'Abril',
        'Maio',
        'Junho',
        'Julho',
        'Agosto',
        'Setembro',
        'Outubro',
        'Novembro',
        'Dezembro',
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

    public function index(Request $request)
    {
        $areas = Area::allCached();

        $inicio = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $fim    = $inicio->copy()->endOfWeek(Carbon::SUNDAY);

        $query = Reserva::with(['cliente', 'area']);
        $this->aplicarFiltroPeriodo($query, $inicio, $fim);
        $this->aplicarOrdenacao($query);

        $reservasIniciais = $query->get()->map(fn(Reserva $r) => $this->formatarReserva($r));

        $todasAreasJson = $areas->map(fn(Area $a) => $this->formatarArea($a));

        return view('reservas.index', [
            'areasPorTipo'   => TipoArea::allCached()->map(fn($tipo) => [
                'tipo'  => $tipo,
                'areas' => $areas->filter(fn($a) => $a->tipo_area_id === $tipo->id)->values(),
            ])->filter(fn($g) => $g['areas']->isNotEmpty())->values(),
            'todasAreasJson'   => $todasAreasJson,
            'reservasIniciais' => $reservasIniciais,
            'areaIdInicial'    => $request->integer('area_id') ?: null,
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $request->validate([
            'area_id'  => 'nullable|integer|exists:areas,id',
            'view'     => 'required|in:day,week,month,year',
            'data_ref' => 'required|date',
            'tipo'     => 'nullable|in:FIXA,UNICA,MENSALISTA',
            'busca'    => 'nullable|string|max:191',
        ]);

        $cacheKey = 'reservas:data:' . md5(
            collect($request->only(['area_id', 'view', 'data_ref', 'tipo', 'busca']))->sortKeys()->toJson()
        );

        return response()->json(
            Cache::tags(Reserva::CACHE_TAG)->remember($cacheKey, 60, fn() => $this->montarRespostaData($request)),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validarReserva($request, true);
        $this->ajustarDatasPorTipo($validated);
        $this->validarAreaAtiva($validated['area_id']);
        $this->validarClienteAtivo($validated['cliente_id']);
        $this->validarDiaArea($validated);
        $this->validarHorariosArea($validated);

        $reserva = DB::transaction(function () use ($validated) {
            Reserva::where('area_id', $validated['area_id'])
                ->where('dia_semana', $validated['dia_semana'])
                ->lockForUpdate()
                ->get();

            $this->verificarConflito($validated);
            $this->preencherValores($validated);

            return Reserva::create($validated);
        });

        return response()->json($reserva->load(['cliente', 'area']), 201);
    }

    public function update(Request $request, Reserva $reserva): JsonResponse
    {
        $validated = $this->validarReserva($request, false);
        $validated['tipo'] ??= $reserva->tipo;
        $this->ajustarDatasPorTipo($validated);

        $areaId = $validated['area_id'] ?? $reserva->area_id;
        $this->validarAreaAtiva($areaId);

        if (isset($validated['cliente_id'])) {
            $this->validarClienteAtivo($validated['cliente_id']);
        }

        $merged = array_merge($reserva->only([
            'area_id',
            'dia_semana',
            'horario_inicio',
            'horario_fim',
            'tipo',
            'data_reserva',
            'data_inicio',
            'data_fim',
            'slots_ocupados',
        ]), $validated);

        $this->validarDiaArea($merged);
        $this->validarHorariosArea($merged);

        $reserva = DB::transaction(function () use ($reserva, $validated, $merged) {
            Reserva::where('area_id', $merged['area_id'])
                ->where('dia_semana', $merged['dia_semana'])
                ->lockForUpdate()
                ->get();

            $this->verificarConflito($merged, $reserva->id);
            $this->preencherValores($validated);

            $reserva->update($validated);

            return $reserva;
        });

        return response()->json($reserva->load(['cliente', 'area']));
    }

    public function destroy(Reserva $reserva): JsonResponse
    {
        $reserva->delete();

        return response()->json(['message' => 'Reserva excluída']);
    }

    public function restore(int $id): JsonResponse
    {
        $reserva = Reserva::onlyTrashed()->findOrFail($id);
        $reserva->restore();

        return response()->json(['message' => 'Reserva restaurada']);
    }

    public function verificarConflitoApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'area_id'        => 'required|exists:areas,id',
            'dia_semana'     => 'required|in:DOMINGO,SEGUNDA,TERCA,QUARTA,QUINTA,SEXTA,SABADO',
            'horario_inicio' => 'nullable|date_format:H:i',
            'horario_fim'    => 'nullable|date_format:H:i',
            'tipo'           => 'required|in:FIXA,UNICA,MENSALISTA',
            'data_reserva'   => 'nullable|date',
            'data_inicio'    => 'nullable|date',
            'data_fim'       => 'nullable|date',
            'reserva_id'     => 'nullable|integer|exists:reservas,id',
        ]);

        $conflitos = $this->buscarConflitos($validated, $validated['reserva_id'] ?? null);

        return response()->json([
            'tem_conflito' => $conflitos->isNotEmpty(),
            'conflitos'    => $conflitos->map(fn(Reserva $r) => [
                'id'              => $r->id,
                'cliente'         => $r->cliente?->nome ?? '—',
                'horario_inicio'  => $r->horario_inicio_formatado,
                'horario_fim'     => $r->horario_fim_formatado,
                'tipo'            => $r->tipo,
            ])->values(),
        ]);
    }

    public function exportarPdfFiltrado(Request $request)
    {
        [$reservas, $descricao, $inicio, $fim, $view] = $this->montarQueryExportacao($request);

        $titulo = $request->filled('area_id')
            ? (Area::findCached((int) $request->area_id)?->nome ?? 'Reservas')
            : 'Todas as Áreas';

        return $this->gerarPdf($reservas, $titulo, $descricao, $inicio, $fim, $view);
    }

    public function exportarXlsxFiltrado(Request $request)
    {
        [$reservas] = $this->montarQueryExportacao($request);

        $prefix = $request->filled('area_id')
            ? 'reservas_' . Str::slug(Area::findCached((int) $request->area_id)?->nome ?? '')
            : 'reservas';

        return Excel::download(
            new ReservasExport($reservas),
            "{$prefix}_" . now()->format('Y-m-d_His') . '.xlsx',
        );
    }

    private function validarReserva(Request $request, bool $obrigatorio): array
    {
        $regra = $obrigatorio ? 'required' : 'sometimes';

        return $request->validate([
            'area_id'          => "{$regra}|exists:areas,id",
            'cliente_id'       => "{$regra}|exists:clientes,id",
            'dia_semana'       => "{$regra}|in:DOMINGO,SEGUNDA,TERCA,QUARTA,QUINTA,SEXTA,SABADO",
            'tipo'             => "{$regra}|in:FIXA,UNICA,MENSALISTA",
            'horario_inicio'   => 'nullable|date_format:H:i',
            'horario_fim'      => 'nullable|date_format:H:i',
            'slots_ocupados'   => 'nullable|integer|min:1|max:24',
            'duracao_real_min' => 'nullable|integer|min:1|max:1440',
            'data_reserva'     => 'required_if:tipo,UNICA|nullable|date',
            'data_inicio'      => 'nullable|date',
            'data_fim'         => 'nullable|date|after_or_equal:data_inicio',
            'valor_unitario'   => 'nullable|numeric|min:0|max:99999.99',
            'desconto'         => 'nullable|numeric|min:0|max:99999.99',
            'num_pessoas'      => 'nullable|integer|min:1|max:9999',
            'hora_entrada'     => 'nullable|date_format:H:i',
            'hora_saida'       => 'nullable|date_format:H:i',
            'obs'              => 'nullable|string|max:1000',
        ], [
            'data_reserva.required_if' => 'A data da reserva é obrigatória para reservas únicas.',
        ]);
    }

    private function ajustarDatasPorTipo(array &$dados): void
    {
        match ($dados['tipo']) {
            'FIXA', 'MENSALISTA' => $dados['data_reserva'] = null,
            'UNICA' => $dados = [...$dados, 'data_inicio' => null, 'data_fim' => null],
        };
    }

    private function validarAreaAtiva(int $areaId): void
    {
        $area = Area::findCached($areaId);

        if (!$area || !$area->ativo) {
            throw ValidationException::withMessages([
                'area_id' => 'Esta área está desativada e não aceita novas reservas.',
            ]);
        }
    }

    private function validarClienteAtivo(int $clienteId): void
    {
        $cliente = Cliente::findCached($clienteId);

        if (!$cliente || !$cliente->ativo) {
            throw ValidationException::withMessages([
                'cliente_id' => 'Este cliente está desativado e não pode receber novas reservas.',
            ]);
        }
    }

    private function validarDiaArea(array $dados): void
    {
        $area = Area::findCached($dados['area_id']);
        if (!$area) return;

        if (!in_array($dados['dia_semana'], $area->dias)) {
            throw ValidationException::withMessages([
                'dia_semana' => 'A área "' . $area->nome . '" não funciona neste dia.',
            ]);
        }
    }

    private function validarHorariosArea(array $dados): void
    {
        $area = Area::findCached($dados['area_id']);
        if (!$area) return;

        if ($area->modo_reserva === 'DIA_INTEIRO') {
            return;
        }

        if (empty($dados['horario_inicio'])) {
            throw ValidationException::withMessages([
                'horario_inicio' => 'O horário de início é obrigatório para esta área.',
            ]);
        }

        $horariosDisponiveis = $area->horariosPorDia($dados['dia_semana']);

        $inicio = substr($dados['horario_inicio'], 0, 5);
        if (!in_array($inicio, $horariosDisponiveis)) {
            throw ValidationException::withMessages([
                'horario_inicio' => "O horário \"{$inicio}\" não está disponível para \"{$area->nome}\" neste dia.",
            ]);
        }

        if (!empty($dados['horario_fim']) && ($dados['slots_ocupados'] ?? 1) > 1) {
            $inicioIdx = array_search($inicio, $horariosDisponiveis);
            $slotsNecessarios = $dados['slots_ocupados'] ?? 1;

            for ($i = $inicioIdx; $i < $inicioIdx + $slotsNecessarios; $i++) {
                if (!isset($horariosDisponiveis[$i])) {
                    throw ValidationException::withMessages([
                        'horario_fim' => "Não há slots suficientes disponíveis para o período solicitado em \"{$area->nome}\".",
                    ]);
                }
            }
        }
    }

    private function verificarConflito(array $dados, ?int $reservaIdAtual = null): void
    {
        $conflitos = $this->buscarConflitos($dados, $reservaIdAtual);

        if ($conflitos->isNotEmpty()) {
            $detalhes = $conflitos->map(function (Reserva $r) {
                $cliente = $r->cliente?->nome ?? '—';
                $horario = $r->horario_inicio_formatado ?? 'dia inteiro';
                return "{$cliente} às {$horario}";
            })->implode(', ');

            throw ValidationException::withMessages([
                'horario_inicio' => "Conflito com reserva(s) existente(s): {$detalhes}",
            ]);
        }
    }

    private function buscarConflitos(array $dados, ?int $reservaIdAtual = null)
    {
        $area = Area::findCached($dados['area_id']);

        $query = Reserva::with('cliente')
            ->where('area_id', $dados['area_id'])
            ->where('dia_semana', $dados['dia_semana']);

        if ($reservaIdAtual) {
            $query->where('id', '!=', $reservaIdAtual);
        }

        if ($area && $area->modo_reserva === 'DIA_INTEIRO') {
            $this->aplicarFiltroConflitoDatas($query, $dados);
            return $query->get();
        }

        $inicio = $dados['horario_inicio'] ?? null;
        $fim = $dados['horario_fim'] ?? null;

        if (!$inicio) return collect();

        if ($fim && $inicio !== $fim) {
            $query->where(function ($q) use ($inicio, $fim) {
                $q->where(function ($r) use ($inicio, $fim) {
                    $r->where('horario_inicio', '<', $fim)
                        ->where('horario_fim', '>', $inicio);
                })->orWhere(function ($r) use ($inicio) {
                    $r->where('horario_inicio', $inicio)
                        ->whereNull('horario_fim');
                })->orWhere(function ($r) use ($inicio, $fim) {
                    $r->where('horario_inicio', '>=', $inicio)
                        ->where('horario_inicio', '<', $fim)
                        ->whereNull('horario_fim');
                });
            });
        } else {
            $query->where(function ($q) use ($inicio) {
                $q->where('horario_inicio', $inicio)
                    ->orWhere(function ($r) use ($inicio) {
                        $r->where('horario_inicio', '<', $inicio)
                            ->where('horario_fim', '>', $inicio);
                    });
            });
        }

        $this->aplicarFiltroConflitoDatas($query, $dados);

        return $query->get();
    }

    private function aplicarFiltroConflitoDatas($query, array $dados): void
    {
        if ($dados['tipo'] === 'UNICA' && !empty($dados['data_reserva'])) {
            $dataReserva = $dados['data_reserva'];
            $query->where(function ($q) use ($dataReserva) {
                $q->where(function ($u) use ($dataReserva) {
                    $u->where('tipo', 'UNICA')->where('data_reserva', $dataReserva);
                })->orWhere(function ($f) use ($dataReserva) {
                    $f->whereIn('tipo', ['FIXA', 'MENSALISTA'])
                        ->where(fn($p) => $p->whereNull('data_inicio')->orWhere('data_inicio', '<=', $dataReserva))
                        ->where(fn($p) => $p->whereNull('data_fim')->orWhere('data_fim', '>=', $dataReserva));
                });
            });
        }

        if (in_array($dados['tipo'], ['FIXA', 'MENSALISTA'])) {
            $inicio = $dados['data_inicio'] ?? '0001-01-01';
            $fim = $dados['data_fim'] ?? '9999-12-31';

            $query->where(function ($q) use ($inicio, $fim) {
                $q->where(function ($f) use ($inicio, $fim) {
                    $f->whereIn('tipo', ['FIXA', 'MENSALISTA'])
                        ->where(fn($p) => $p->whereNull('data_inicio')->orWhere('data_inicio', '<=', $fim))
                        ->where(fn($p) => $p->whereNull('data_fim')->orWhere('data_fim', '>=', $inicio));
                })->orWhere(function ($u) use ($inicio, $fim) {
                    $u->where('tipo', 'UNICA')
                        ->whereBetween('data_reserva', [$inicio, $fim]);
                });
            });
        }
    }

    private function preencherValores(array &$dados): void
    {
        $area = Area::findCached($dados['area_id']);
        if (!$area) return;

        if (!isset($dados['valor_unitario'])) {
            $areaValor = $area->valorPara($dados['tipo'], $dados['dia_semana'] ?? null);
            if ($areaValor) {
                $dados['valor_unitario'] = $areaValor->valor;
            }
        }

        $slots = $dados['slots_ocupados'] ?? 1;
        $unitario = $dados['valor_unitario'] ?? null;

        if ($unitario) {
            $dados['valor_total'] = $unitario * $slots;
        }

        $total = $dados['valor_total'] ?? 0;
        $taxas = $dados['valor_taxas'] ?? 0;
        $desconto = $dados['desconto'] ?? 0;
        $dados['valor_final'] = $total + $taxas - $desconto;

        if ($area->modo_reserva === 'DIA_INTEIRO') {
            $dados['horario_inicio'] = null;
            $dados['horario_fim'] = null;
            $dados['slots_ocupados'] = 1;
        }

        if (isset($dados['duracao_real_min']) && $dados['duracao_real_min'] && $dados['duracao_real_min'] % 60 !== 0) {
            $horas = intdiv($dados['duracao_real_min'], 60);
            $minutos = $dados['duracao_real_min'] % 60;
            $sobra = ($slots * 60) - $dados['duracao_real_min'];
            $dados['obs_sistema'] = "Duração real: {$horas}h{$minutos}min ({$sobra}min não utilizados)";
        }
    }

    private function montarRespostaData(Request $request): array
    {
        $ref = Carbon::parse($request->data_ref);
        $view = $request->view;

        [$inicio, $fim] = $this->calcularPeriodo($view, $ref);

        $query = Reserva::with(['cliente', 'area' => fn($q) => $q->withTrashed()]);

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $this->aplicarFiltroPeriodo($query, $inicio, $fim);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $this->aplicarFiltroBusca($query, $request);
        $this->aplicarOrdenacao($query);

        return [
            'reservas' => $query->get()->map(fn(Reserva $r) => $this->formatarReserva($r)),
            'periodo'  => [
                'inicio' => $inicio->format('Y-m-d'),
                'fim'    => $fim->format('Y-m-d'),
            ],
        ];
    }

    private function formatarArea(Area $area): array
    {
        return [
            'id'           => $area->id,
            'nome'         => $area->nome,
            'tipo'         => $area->tipoArea?->nome ?? 'Área',
            'modo_reserva' => $area->modo_reserva,
            'dias'         => $area->dias,
            'horarios'     => $area->todosHorariosCached(),
        ];
    }

    private function formatarReserva(Reserva $reserva): array
    {
        return [
            'id'               => $reserva->id,
            'area_id'          => $reserva->area_id,
            'area_nome'        => $reserva->area?->nome,
            'cliente_id'       => $reserva->cliente_id,
            'cliente_nome'     => $reserva->cliente?->nome ?? '—',
            'cliente_telefone' => $reserva->cliente?->telefone,
            'dia_semana'       => $reserva->dia_semana,
            'tipo'             => $reserva->tipo,
            'horario_inicio'   => $reserva->horario_inicio_formatado,
            'horario_fim'      => $reserva->horario_fim_formatado,
            'slots_ocupados'   => $reserva->slots_ocupados,
            'duracao_real_min' => $reserva->duracao_real_min,
            'data_reserva'     => $reserva->data_reserva?->format('Y-m-d'),
            'data_formatada'   => $reserva->data_formatada,
            'data_inicio'      => $reserva->data_inicio?->format('Y-m-d'),
            'data_fim'         => $reserva->data_fim?->format('Y-m-d'),
            'valor_unitario'   => $reserva->valor_unitario,
            'valor_total'      => $reserva->valor_total,
            'valor_taxas'      => $reserva->valor_taxas,
            'desconto'         => $reserva->desconto,
            'valor_final'      => $reserva->valor_final,
            'num_pessoas'      => $reserva->num_pessoas,
            'obs'              => $reserva->obs,
            'obs_sistema'      => $reserva->obs_sistema,
        ];
    }

    private function aplicarOrdenacao($query): void
    {
        $query->orderBy('area_id')->orderBy('dia_semana')->orderBy('horario_inicio');
    }

    private function aplicarFiltroBusca($query, Request $request): void
    {
        if ($request->filled('busca')) {
            $termo = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $request->busca) . '%';
            $query->where(function ($q) use ($termo) {
                $q->where('obs', 'like', $termo)
                    ->orWhereHas('cliente', fn($c) => $c->where('nome', 'like', $termo)->orWhere('telefone', 'like', $termo));
            });
        }
    }

    private function aplicarFiltroPeriodo($query, Carbon $inicio, Carbon $fim): void
    {
        $inicioStr = $inicio->format('Y-m-d');
        $fimStr    = $fim->format('Y-m-d');

        $query->where(function ($q) use ($inicioStr, $fimStr) {
            $q->where(function ($fixa) use ($inicioStr, $fimStr) {
                $fixa->whereIn('tipo', ['FIXA', 'MENSALISTA'])
                    ->where(fn($p) => $p->whereNull('data_inicio')->orWhere('data_inicio', '<=', $fimStr))
                    ->where(fn($p) => $p->whereNull('data_fim')->orWhere('data_fim', '>=', $inicioStr));
            })
                ->orWhere(fn($u) => $u->where('tipo', 'UNICA')->whereBetween('data_reserva', [$inicioStr, $fimStr]));
        });
    }

    private function calcularPeriodo(string $view, Carbon $ref): array
    {
        return match ($view) {
            'day'   => [$ref->copy()->startOfDay(), $ref->copy()->endOfDay()],
            'week'  => [($s = $ref->copy()->startOfWeek(Carbon::MONDAY)), $s->copy()->endOfWeek(Carbon::SUNDAY)],
            'month' => [$ref->copy()->startOfMonth(), $ref->copy()->endOfMonth()],
            'year'  => [$ref->copy()->startOfYear(), $ref->copy()->endOfYear()],
        };
    }

    private function montarQueryExportacao(Request $request): array
    {
        $request->validate([
            'area_id'  => 'nullable|exists:areas,id',
            'view'     => 'nullable|in:day,week,month,year',
            'data_ref' => 'nullable|date',
            'tipo'     => 'nullable|in:FIXA,UNICA,MENSALISTA',
            'busca'    => 'nullable|string|max:191',
        ]);

        $query     = Reserva::with(['area' => fn($q) => $q->withTrashed(), 'cliente' => fn($q) => $q->withTrashed()]);
        $descParts = [];
        $inicio    = null;
        $fim       = null;
        $view      = $request->view;

        if ($request->filled('view') && $request->filled('data_ref')) {
            $ref = Carbon::parse($request->data_ref);
            [$inicio, $fim] = $this->calcularPeriodo($view, $ref);
            $this->aplicarFiltroPeriodo($query, $inicio, $fim);

            $descParts[] = match ($view) {
                'day'   => $ref->format('d/m/Y') . ' (' . (self::DIAS_PT[array_search($ref->dayOfWeek, self::DIA_CARBON) ?: ''] ?? '') . ')',
                'week'  => 'Semana ' . $inicio->format('d/m') . ' — ' . $fim->format('d/m/Y'),
                'month' => self::MESES_PT[$ref->month - 1] . '/' . $ref->year,
                'year'  => 'Ano ' . $ref->year,
            };
        }

        if ($request->filled('area_id')) $query->where('area_id', $request->area_id);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
            $descParts[] = 'Tipo: ' . $request->tipo;
        }

        $this->aplicarFiltroBusca($query, $request);
        if ($request->filled('busca')) {
            $descParts[] = 'Busca: "' . $request->busca . '"';
        }

        $this->aplicarOrdenacao($query);

        return [
            $query->get(),
            $descParts ? implode(' · ', $descParts) : 'Todas as reservas',
            $inicio,
            $fim,
            $view,
        ];
    }

    private function gerarPdf($reservas, string $titulo, string $descricao, ?Carbon $inicio, ?Carbon $fim, ?string $view)
    {
        $diasDatas = [];
        if ($inicio && $fim && in_array($view, ['day', 'week'])) {
            $cursor = $inicio->copy();
            while ($cursor->lte($fim)) {
                $key = array_search($cursor->dayOfWeek, self::DIA_CARBON);
                if ($key) {
                    $diasDatas[$key] = $cursor->format('d/m/Y');
                }
                $cursor->addDay();
            }
        }

        $html = view('reservas.pdf', compact('reservas', 'titulo', 'descricao', 'diasDatas', 'view'))->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => false]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'reservas_' . Str::slug($titulo) . '_' . now()->format('Y-m-d') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
