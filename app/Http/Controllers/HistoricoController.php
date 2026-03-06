<?php

namespace App\Http\Controllers;

use App\Exports\AuditsExport;
use App\Models\{Area, Cliente, Pagamento, Taxa, TipoArea, User};
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use OwenIt\Auditing\Models\Audit;

class HistoricoController extends Controller
{
    private const CACHE_TAG = 'historico';

    private const HIDDEN_FIELDS = [
        'remember_token',
        'password',
        'email_verified_at',
    ];

    private const FIELD_LABELS = [
        'area_id'            => 'Área',
        'tipo_area_id'       => 'Tipo de Área',
        'cliente_id'         => 'Cliente',
        'taxa_id'            => 'Taxa',
        'reserva_id'         => 'Reserva',
        'dia_semana'         => 'Dia',
        'horario'            => 'Horário',
        'horario_inicio'     => 'Horário Início',
        'horario_fim'        => 'Horário Fim',
        'cliente'            => 'Cliente',
        'telefone'           => 'Telefone',
        'tipo'               => 'Tipo',
        'tipo_reserva'       => 'Tipo de Reserva',
        'tipo_cobranca'      => 'Tipo de Cobrança',
        'modo_reserva'       => 'Modo de Reserva',
        'modo_cobranca'      => 'Modo de Cobrança',
        'obs'                => 'Observações',
        'obs_sistema'        => 'Obs. Sistema',
        'data_reserva'       => 'Data da Reserva',
        'data_inicio'        => 'Início Vigência',
        'data_fim'           => 'Fim Vigência',
        'data_vencimento'    => 'Data Vencimento',
        'data_pagamento'     => 'Data Pagamento',
        'name'               => 'Nome',
        'email'              => 'Email',
        'nome'               => 'Nome',
        'icone'              => 'Ícone',
        'cor'                => 'Cor',
        'descricao'          => 'Descrição',
        'capacidade_pessoas' => 'Capacidade',
        'ativo'              => 'Ativo',
        'obrigatoria'        => 'Obrigatória',
        'valor'              => 'Valor',
        'valor_unitario'     => 'Valor Unitário',
        'valor_total'        => 'Valor Total',
        'valor_taxas'        => 'Valor Taxas',
        'valor_final'        => 'Valor Final',
        'valor_aplicado'     => 'Valor Aplicado',
        'desconto'           => 'Desconto',
        'slots_ocupados'     => 'Slots Ocupados',
        'duracao_real_min'   => 'Duração Real (min)',
        'num_pessoas'        => 'Nº Pessoas',
        'hora_entrada'       => 'Hora Entrada',
        'hora_saida'         => 'Hora Saída',
        'status'             => 'Status',
        'forma_pagamento'    => 'Forma Pagamento',
        'referencia_mes'     => 'Mês Referência',
        'cpf'                => 'CPF',
    ];

    private const MODEL_MAP = [
        'reserva'    => 'App\\Models\\Reserva',
        'usuario'    => 'App\\Models\\User',
        'area'       => 'App\\Models\\Area',
        'cliente'    => 'App\\Models\\Cliente',
        'taxa'       => 'App\\Models\\Taxa',
        'valor'      => 'App\\Models\\AreaValor',
        'pagamento'  => 'App\\Models\\Pagamento',
        'tipo_area'  => 'App\\Models\\TipoArea',
        'horario'    => 'App\\Models\\AreaHorario',
        'dia'        => 'App\\Models\\AreaDiaDisponivel',
        'area_taxa'  => 'App\\Models\\AreaTaxa',
    ];

    private const MODEL_LABELS = [
        'App\\Models\\Reserva'          => 'Reserva',
        'App\\Models\\User'             => 'Usuário',
        'App\\Models\\Area'             => 'Área',
        'App\\Models\\Cliente'          => 'Cliente',
        'App\\Models\\Taxa'             => 'Taxa',
        'App\\Models\\AreaValor'        => 'Valor de Área',
        'App\\Models\\Pagamento'        => 'Pagamento',
        'App\\Models\\TipoArea'         => 'Tipo de Área',
        'App\\Models\\AreaHorario'      => 'Horário de Área',
        'App\\Models\\AreaDiaDisponivel' => 'Dia Disponível',
        'App\\Models\\'         => 'Taxa de Área',
        'App\\Models\\ReservaTaxa'      => 'Taxa de Reserva',
        'App\\Models\\Recurso'          => 'Área',
    ];

    private const EVENT_LABELS = [
        'created'  => 'Criação',
        'updated'  => 'Atualização',
        'deleted'  => 'Exclusão',
        'restored' => 'Restauração',
    ];

    private const FK_RESOLVERS = [
        'area_id'      => [Area::class, 'nome'],
        'tipo_area_id' => [TipoArea::class, 'nome'],
        'cliente_id'   => [Cliente::class, 'nome'],
        'taxa_id'      => [Taxa::class, 'nome'],
    ];

    private array $fkCache = [];

    public function index(Request $request)
    {
        $query = $this->buildQuery($request);

        if ($request->export === 'xlsx') {
            return Excel::download(
                new AuditsExport($query->get()->map(fn($a) => $this->processAudit($a))),
                'historico_' . now()->format('Y-m-d_His') . '.xlsx',
            );
        }

        if ($request->export === 'pdf') {
            return $this->exportPdf($query->get());
        }

        $audits = $query->paginate(25)->through(fn($a) => $this->processAudit($a));
        $hoje = now()->format('Y-m-d');
        $ontem = now()->subDay()->format('Y-m-d');

        $grouped = $audits->groupBy(fn($a) => $a->created_at->format('Y-m-d'))
            ->map(function ($items, $date) use ($hoje, $ontem) {
                return (object) [
                    'date'  => $date,
                    'label' => match ($date) {
                        $hoje  => 'Hoje',
                        $ontem => 'Ontem',
                        default => Carbon::parse($date)->translatedFormat('d \\d\\e F, Y'),
                    },
                    'items' => $items,
                ];
            });

        return view('historico.index', [
            'audits'      => $audits,
            'grouped'     => $grouped,
            'usuarios'    => User::orderBy('name')->get(['id', 'name']),
            'stats'       => $this->getStats(),
            'eventConfig' => [
                'created'  => ['icon' => 'fi-rr-plus', 'color' => 'green'],
                'updated'  => ['icon' => 'fi-rr-pencil', 'color' => 'blue'],
                'deleted'  => ['icon' => 'fi-rr-trash', 'color' => 'red'],
                'restored' => ['icon' => 'fi-rr-undo', 'color' => 'amber'],
            ],
            'modelConfig' => [
                'reserva'   => ['icon' => 'fi-rr-calendar'],
                'usuario'   => ['icon' => 'fi-rr-user'],
                'area'      => ['icon' => 'fi-rr-marker'],
                'cliente'   => ['icon' => 'fi-rr-user'],
                'taxa'      => ['icon' => 'fi-rr-dollar'],
                'valor'     => ['icon' => 'fi-rr-coins'],
                'pagamento' => ['icon' => 'fi-rr-receipt'],
                'tipo_area' => ['icon' => 'fi-rr-layers'],
                'horario'   => ['icon' => 'fi-rr-clock'],
            ],
        ]);
    }

    private function buildQuery(Request $request)
    {
        $query = Audit::with('user')->orderByDesc('created_at');

        $this->excludeNoise($query);

        if ($request->filled('modelo') && isset(self::MODEL_MAP[$request->modelo])) {
            $query->where('auditable_type', self::MODEL_MAP[$request->modelo]);
        }

        if ($request->filled('evento')) {
            $query->where('event', $request->evento);
        }

        if ($request->filled('usuario_id')) {
            $query->where('user_id', $request->usuario_id);
        }

        if ($request->filled('data_inicio')) {
            $query->where('created_at', '>=', Carbon::parse($request->data_inicio)->startOfDay());
        }

        if ($request->filled('data_fim')) {
            $query->where('created_at', '<=', Carbon::parse($request->data_fim)->endOfDay());
        }

        if ($request->filled('busca')) {
            $termo = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $request->busca) . '%';
            $query->where(function ($q) use ($termo) {
                $q->where('old_values', 'like', $termo)
                    ->orWhere('new_values', 'like', $termo);
            });
        }

        return $query;
    }

    private function excludeNoise($query): void
    {
        $query->where(function ($q) {
            $q->where('new_values', 'not like', '%remember_token%')
                ->orWhere('event', '!=', 'updated');
        });
    }

    private function getStats(): array
    {
        return Cache::tags(self::CACHE_TAG)->remember('historico:stats', 60, function () {
            $base = Audit::where(function ($q) {
                $q->where('new_values', 'not like', '%remember_token%')
                    ->orWhere('event', '!=', 'updated');
            });

            return [
                'total'      => (clone $base)->count(),
                'hoje'       => (clone $base)->where('created_at', '>=', Carbon::today())->count(),
                'semana'     => (clone $base)->where('created_at', '>=', Carbon::now()->startOfWeek(Carbon::MONDAY))->count(),
                'por_evento' => (clone $base)->selectRaw('event, count(*) as total')->groupBy('event')->pluck('total', 'event')->toArray(),
            ];
        });
    }

    private function processAudit(Audit $audit): object
    {
        $old = $this->filterValues($audit->old_values ?? []);
        $new = $this->filterValues($audit->new_values ?? []);

        $modelLabel = self::MODEL_LABELS[$audit->auditable_type] ?? class_basename($audit->auditable_type);
        $modelType = array_search($audit->auditable_type, self::MODEL_MAP) ?: 'outro';

        return (object) [
            'id'            => $audit->id,
            'event'         => $audit->event,
            'event_label'   => self::EVENT_LABELS[$audit->event] ?? ucfirst($audit->event),
            'model_type'    => $modelType,
            'model_label'   => $modelLabel,
            'model_id'      => $audit->auditable_id,
            'user_name'     => $audit->user?->name ?? 'Sistema',
            'user_initials' => $this->initials($audit->user?->name ?? 'SI'),
            'description'   => $this->describe($audit->event, $modelLabel, $old, $new, $audit->auditable_id),
            'changes'       => $this->buildChanges($audit->event, $old, $new),
            'ip'            => $audit->ip_address,
            'created_at'    => $audit->created_at,
            'time_ago'      => $audit->created_at->diffForHumans(),
        ];
    }

    private function filterValues(array $values): array
    {
        return collect($values)->reject(fn($v, $k) => in_array($k, self::HIDDEN_FIELDS))->toArray();
    }

    private function resolveFk(string $field, $value): ?string
    {
        if (!$value || !isset(self::FK_RESOLVERS[$field])) return null;

        $cacheKey = "{$field}:{$value}";

        if (isset($this->fkCache[$cacheKey])) {
            return $this->fkCache[$cacheKey];
        }

        [$modelClass, $displayField] = self::FK_RESOLVERS[$field];

        $record = $modelClass::withTrashed()->find($value);
        $resolved = $record ? $record->{$displayField} : null;

        $this->fkCache[$cacheKey] = $resolved;

        return $resolved;
    }

    private function formatFieldValue(string $field, $value): string
    {
        if (is_null($value)) return '—';
        if (is_bool($value)) return $value ? 'Sim' : 'Não';
        if (is_array($value)) return implode(', ', $value);

        if ($field === 'ativo') return $value ? 'Sim' : 'Não';
        if ($field === 'obrigatoria') return $value ? 'Sim' : 'Não';

        $resolved = $this->resolveFk($field, $value);
        if ($resolved) return "{$resolved}";

        if (in_array($field, ['valor', 'valor_unitario', 'valor_total', 'valor_taxas', 'valor_final', 'desconto', 'valor_aplicado']) && is_numeric($value)) {
            return 'R$ ' . number_format((float) $value, 2, ',', '.');
        }

        return Str::limit((string) $value, 100);
    }

    private function describe(string $event, string $model, array $old, array $new, int $id): string
    {
        return match ($event) {
            'created'  => $this->describeCreated($model, $new),
            'updated'  => $this->describeUpdated($model, $old, $new),
            'deleted'  => $this->describeDeleted($model, $old),
            'restored' => $this->describeRestored($model, $old, $new, $id),
            default    => ucfirst($event) . " em {$model} #{$id}",
        };
    }

    private function describeCreated(string $model, array $values): string
    {
        return match ($model) {
            'Reserva' => 'Nova reserva: ' . $this->resolveClienteNome($values)
                . $this->resolveAreaNome($values)
                . (isset($values['horario_inicio']) ? " às {$values['horario_inicio']}" : '')
                . (isset($values['dia_semana']) ? " ({$values['dia_semana']})" : ''),
            'Usuário' => 'Novo usuário: ' . ($values['name'] ?? $values['email'] ?? '?'),
            'Área' => 'Nova área: ' . ($values['nome'] ?? '?'),
            'Cliente' => 'Novo cliente: ' . ($values['nome'] ?? '?'),
            'Taxa' => 'Nova taxa: ' . ($values['nome'] ?? '?'),
            'Valor de Área' => 'Novo valor: ' . $this->resolveAreaNome($values)
                . ' ' . ($values['tipo_reserva'] ?? '')
                . (isset($values['valor']) ? ' R$ ' . number_format((float) $values['valor'], 2, ',', '.') : ''),
            'Pagamento' => 'Novo pagamento: ' . $this->resolveClienteNome($values)
                . (isset($values['valor']) ? ' R$ ' . number_format((float) $values['valor'], 2, ',', '.') : '')
                . ' (' . ($values['tipo'] ?? '?') . ')',
            'Tipo de Área' => 'Novo tipo: ' . ($values['nome'] ?? '?'),
            'Horário de Área' => 'Novo horário: ' . $this->resolveAreaNome($values)
                . ' ' . ($values['dia_semana'] ?? '') . ' ' . ($values['horario'] ?? ''),
            default => "Novo registro de {$model}",
        };
    }

    private function describeUpdated(string $model, array $old, array $new): string
    {
        $campos = array_keys(array_merge($old, $new));
        $labels = collect($campos)->map(fn($c) => self::FIELD_LABELS[$c] ?? $c)->take(3)->implode(', ');
        $extra = count($campos) > 3 ? ' (+' . (count($campos) - 3) . ')' : '';
        return "{$model} atualizado: {$labels}{$extra}";
    }

    private function describeDeleted(string $model, array $values): string
    {
        return match ($model) {
            'Reserva' => 'Reserva excluída: ' . $this->resolveClienteNome($values) . $this->resolveAreaNome($values),
            'Usuário' => 'Usuário excluído: ' . ($values['name'] ?? $values['email'] ?? '?'),
            'Área' => 'Área excluída: ' . ($values['nome'] ?? '?'),
            'Cliente' => 'Cliente excluído: ' . ($values['nome'] ?? '?'),
            'Taxa' => 'Taxa excluída: ' . ($values['nome'] ?? '?'),
            'Pagamento' => 'Pagamento excluído: ' . $this->resolveClienteNome($values),
            default => "{$model} excluído",
        };
    }

    private function describeRestored(string $model, array $old, array $new, int $id): string
    {
        $nome = $new['nome'] ?? $old['nome'] ?? $new['name'] ?? $old['name'] ?? "#{$id}";
        return "{$model} restaurado: {$nome}";
    }

    private function resolveClienteNome(array $values): string
    {
        if (isset($values['cliente_id'])) {
            $nome = $this->resolveFk('cliente_id', $values['cliente_id']);
            if ($nome) return $nome;
        }
        return $values['cliente'] ?? $values['nome'] ?? '?';
    }

    private function resolveAreaNome(array $values): string
    {
        if (isset($values['area_id'])) {
            $nome = $this->resolveFk('area_id', $values['area_id']);
            if ($nome) return " em {$nome}";
        }
        return '';
    }

    private function buildChanges(string $event, array $old, array $new): array
    {
        $changes = [];

        if ($event === 'created') {
            foreach ($new as $field => $value) {
                $changes[] = [
                    'field' => self::FIELD_LABELS[$field] ?? $field,
                    'type'  => 'added',
                    'new'   => $this->formatFieldValue($field, $value),
                ];
            }
        } elseif ($event === 'deleted') {
            foreach ($old as $field => $value) {
                $changes[] = [
                    'field' => self::FIELD_LABELS[$field] ?? $field,
                    'type'  => 'removed',
                    'old'   => $this->formatFieldValue($field, $value),
                ];
            }
        } else {
            foreach (array_keys(array_merge($old, $new)) as $field) {
                if (($old[$field] ?? null) !== ($new[$field] ?? null)) {
                    $changes[] = [
                        'field' => self::FIELD_LABELS[$field] ?? $field,
                        'type'  => 'changed',
                        'old'   => $this->formatFieldValue($field, $old[$field] ?? null),
                        'new'   => $this->formatFieldValue($field, $new[$field] ?? null),
                    ];
                }
            }
        }

        return $changes;
    }

    private function initials(?string $name): string
    {
        if (!$name) return 'SI';
        $parts = explode(' ', $name);
        return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
    }

    private function exportPdf($audits)
    {
        $processed = $audits->map(fn($a) => $this->processAudit($a));
        $html = view('historico.pdf', [
            'audits'       => $processed,
            'totalCriados' => $processed->where('event', 'created')->count(),
            'totalEditados' => $processed->where('event', 'updated')->count(),
            'totalExcluidos' => $processed->where('event', 'deleted')->count(),
            'totalRestaurados' => $processed->where('event', 'restored')->count(),
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => false]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'historico_' . now()->format('Y-m-d_His') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
