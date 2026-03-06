<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Reserva extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    public const CACHE_TAG = 'reservas';

    protected $fillable = [
        'area_id',
        'cliente_id',
        'dia_semana',
        'tipo',
        'horario_inicio',
        'horario_fim',
        'slots_ocupados',
        'duracao_real_min',
        'data_reserva',
        'data_inicio',
        'data_fim',
        'valor_unitario',
        'valor_total',
        'valor_taxas',
        'desconto',
        'valor_final',
        'num_pessoas',
        'hora_entrada',
        'hora_saida',
        'obs',
        'obs_sistema',
    ];

    protected function casts(): array
    {
        return [
            'data_reserva'     => 'date',
            'data_inicio'      => 'date',
            'data_fim'         => 'date',
            'valor_unitario'   => 'decimal:2',
            'valor_total'      => 'decimal:2',
            'valor_taxas'      => 'decimal:2',
            'desconto'         => 'decimal:2',
            'valor_final'      => 'decimal:2',
            'slots_ocupados'   => 'integer',
            'duracao_real_min' => 'integer',
            'num_pessoas'      => 'integer',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function reservaTaxas(): HasMany
    {
        return $this->hasMany(ReservaTaxa::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }

    public function temVinculo(): bool
    {
        return $this->pagamentos()->withTrashed()->exists()
            || $this->reservaTaxas()->exists();
    }

    protected function nomeCliente(): Attribute
    {
        return Attribute::get(fn() => $this->cliente?->nome ?? '—');
    }

    protected function telefoneCliente(): Attribute
    {
        return Attribute::get(fn() => $this->cliente?->telefone);
    }

    protected function dataFormatada(): Attribute
    {
        return Attribute::get(fn() => $this->data_reserva?->format('d/m/Y'));
    }

    protected function horarioInicioFormatado(): Attribute
    {
        return Attribute::get(function () {
            $h = $this->horario_inicio;
            if (!$h) return null;
            return $h instanceof \DateTimeInterface ? $h->format('H:i') : substr($h, 0, 5);
        });
    }

    protected function horarioFimFormatado(): Attribute
    {
        return Attribute::get(function () {
            $h = $this->horario_fim;
            if (!$h) return null;
            return $h instanceof \DateTimeInterface ? $h->format('H:i') : substr($h, 0, 5);
        });
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