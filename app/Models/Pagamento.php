<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Pagamento extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    public const CACHE_TAG = 'pagamentos';

    protected $fillable = [
        'cliente_id',
        'reserva_id',
        'tipo',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'status',
        'forma_pagamento',
        'referencia_mes',
        'obs',
    ];

    protected function casts(): array
    {
        return [
            'valor'           => 'decimal:2',
            'data_vencimento' => 'date',
            'data_pagamento'  => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    protected function valorFormatado(): Attribute
    {
        return Attribute::get(fn() => 'R$ ' . number_format((float) $this->valor, 2, ',', '.'));
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