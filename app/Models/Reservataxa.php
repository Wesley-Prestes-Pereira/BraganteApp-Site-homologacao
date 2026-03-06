<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ReservaTaxa extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'reserva_taxas';

    protected $fillable = [
        'reserva_id',
        'taxa_id',
        'valor_aplicado',
    ];

    protected function casts(): array
    {
        return [
            'valor_aplicado' => 'decimal:2',
        ];
    }

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    public function taxa(): BelongsTo
    {
        return $this->belongsTo(Taxa::class);
    }

    protected static function booted(): void
    {
        static::saved(fn() => Reserva::limparCache());
        static::deleted(fn() => Reserva::limparCache());
        static::restored(fn() => Reserva::limparCache());
    }
}
