<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class AreaValor extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'area_valores';

    protected $fillable = [
        'area_id',
        'tipo_reserva',
        'modo_cobranca',
        'valor',
        'dia_semana',
        'descricao',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'ativo' => 'boolean',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function temVinculo(): bool
    {
        return Reserva::withTrashed()
            ->where('area_id', $this->area_id)
            ->where('tipo', $this->tipo_reserva)
            ->where('valor_unitario', $this->valor)
            ->exists();
    }

    protected static function booted(): void
    {
        static::saved(fn() => Area::limparCache());
        static::deleted(fn() => Area::limparCache());
        static::restored(fn() => Area::limparCache());
    }
}