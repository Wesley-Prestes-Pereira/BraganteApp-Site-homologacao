<?php

namespace App\Models;

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
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo'              => 'boolean',
            'capacidade_pessoas' => 'integer',
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

    public function horariosPorDia(string $diaSemana): array
    {
        return Cache::tags(self::CACHE_TAG)->remember(
            "area:{$this->id}:horarios:{$diaSemana}",
            3600,
            fn() => $this->horariosConfig()
                ->where('dia_semana', $diaSemana)
                ->where('ativo', true)
                ->orderBy('horario')
                ->pluck('horario')
                ->map(fn($h) => $h instanceof \DateTimeInterface ? $h->format('H:i') : substr($h, 0, 5))
                ->toArray(),
        );
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