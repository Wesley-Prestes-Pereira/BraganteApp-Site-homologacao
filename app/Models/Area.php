<?php

namespace App\Models;

use Carbon\Carbon;
use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Area extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    public const CACHE_TAG = 'areas';

    protected $fillable = [
        'nome',
        'tipo_area_id',
        'descricao',
        'capacidade_pessoas',
        'modo_reserva',
        'duracao_slot_min',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo'              => 'boolean',
            'capacidade_pessoas' => 'integer',
            'duracao_slot_min'   => 'integer',
        ];
    }

    public function tipoArea(): BelongsTo
    {
        return $this->belongsTo(TipoArea::class);
    }

    public function diasDisponiveis(): HasMany
    {
        return $this->hasMany(AreaDiaDisponivel::class);
    }

    public function horariosConfig(): HasMany
    {
        return $this->hasMany(AreaHorario::class);
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function valores(): HasMany
    {
        return $this->hasMany(AreaValor::class);
    }

    public function taxas(): BelongsToMany
    {
        return $this->belongsToMany(Taxa::class, 'area_taxas')
            ->withPivot('obrigatoria')
            ->withTimestamps();
    }

    public function areaTaxas(): HasMany
    {
        return $this->hasMany(AreaTaxa::class);
    }

    public function temVinculo(): bool
    {
        return $this->reservas()->withTrashed()->exists();
    }

    protected function dias(): Attribute
    {
        return Attribute::get(fn() => $this->diasCached());
    }

    protected function icone(): Attribute
    {
        return Attribute::get(fn() => $this->tipoArea?->icone ?? 'fi-rr-marker');
    }

    protected function cor(): Attribute
    {
        return Attribute::get(fn() => $this->tipoArea?->cor ?? '#3b82f6');
    }

    protected function tipoNome(): Attribute
    {
        return Attribute::get(fn() => $this->tipoArea?->nome ?? 'Área');
    }

    protected function isDiaInteiro(): Attribute
    {
        return Attribute::get(fn() => $this->modo_reserva === 'DIA_INTEIRO');
    }

    public function diasCached(): array
    {
        return Cache::tags(self::CACHE_TAG)->remember(
            "area:{$this->id}:dias",
            3600,
            fn() => $this->diasDisponiveis()->pluck('dia_semana')->toArray(),
        );
    }

    public function configDiasCached(): array
    {
        return Cache::tags(self::CACHE_TAG)->remember(
            "area:{$this->id}:config_dias",
            3600,
            fn() => $this->diasDisponiveis()
                ->get()
                ->mapWithKeys(fn(AreaDiaDisponivel $d) => [
                    $d->dia_semana => [
                        'horario_abertura'   => $d->aberturaFormatada(),
                        'horario_fechamento' => $d->fechamentoFormatado(),
                    ],
                ])
                ->toArray(),
        );
    }

    public function horariosPorDia(string $diaSemana): array
    {
        return Cache::tags(self::CACHE_TAG)->remember(
            "area:{$this->id}:horarios:{$diaSemana}",
            3600,
            function () use ($diaSemana) {
                $diaConfig = $this->diasDisponiveis()
                    ->where('dia_semana', $diaSemana)
                    ->first();

                if (!$diaConfig || !$diaConfig->horario_abertura || !$diaConfig->horario_fechamento) {
                    return [];
                }

                $todosSlots = $this->gerarSlots(
                    $diaConfig->horario_abertura,
                    $diaConfig->horario_fechamento,
                    $this->duracao_slot_min ?: 60,
                );

                $bloqueados = $this->horariosConfig()
                    ->where('dia_semana', $diaSemana)
                    ->where('ativo', false)
                    ->pluck('horario')
                    ->map(fn($h) => $h instanceof \DateTimeInterface ? $h->format('H:i') : substr($h, 0, 5))
                    ->toArray();

                return array_values(array_diff($todosSlots, $bloqueados));
            },
        );
    }

    public function gerarSlots(string $abertura, string $fechamento, int $duracaoMin): array
    {
        $slots = [];
        $atual = Carbon::createFromFormat('H:i', substr($abertura, 0, 5));
        $fim = Carbon::createFromFormat('H:i', substr($fechamento, 0, 5));

        while ($atual <= $fim) {
            $slots[] = $atual->format('H:i');
            $atual->addMinutes($duracaoMin);
        }

        return $slots;
    }

    public function todosHorariosCached(): array
    {
        return Cache::tags(self::CACHE_TAG)->remember(
            "area:{$this->id}:horarios:todos",
            3600,
            function () {
                $resultado = [];
                foreach ($this->diasCached() as $dia) {
                    $resultado[$dia] = $this->horariosPorDia($dia);
                }
                return $resultado;
            },
        );
    }

    public function valorPara(string $tipoReserva, ?string $diaSemana = null): ?AreaValor
    {
        return Cache::tags(self::CACHE_TAG)->remember(
            "area:{$this->id}:valor:{$tipoReserva}:{$diaSemana}",
            3600,
            fn() => $this->valores()
                ->where('tipo_reserva', $tipoReserva)
                ->where('ativo', true)
                ->where(function ($q) use ($diaSemana) {
                    $q->where('dia_semana', $diaSemana)->orWhereNull('dia_semana');
                })
                ->orderByRaw('dia_semana IS NULL ASC')
                ->first(),
        );
    }

    public static function findCached(int $id): ?self
    {
        return Cache::tags(self::CACHE_TAG)->remember(
            "area:{$id}",
            3600,
            fn() => static::with('tipoArea')->find($id),
        );
    }

    public static function allCached()
    {
        return Cache::tags(self::CACHE_TAG)->remember(
            'areas:all',
            3600,
            fn() => static::with('tipoArea')
                ->where('ativo', true)
                ->orderBy('nome')
                ->get(),
        );
    }

    public static function limparCache(): void
    {
        Cache::tags(self::CACHE_TAG)->flush();
    }

    protected static function booted(): void
    {
        static::saved(fn() => static::limparCache());
        static::deleted(fn() => static::limparCache());
        static::restored(fn() => static::limparCache());
    }
}
