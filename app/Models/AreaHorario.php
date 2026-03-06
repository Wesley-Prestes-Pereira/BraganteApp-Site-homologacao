<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class AreaHorario extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'area_horarios';

    protected $fillable = [
        'area_id',
        'dia_semana',
        'horario',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo'   => 'boolean',
            'horario' => 'string',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function horarioFormatado(): string
    {
        $h = $this->horario;
        return $h instanceof \DateTimeInterface ? $h->format('H:i') : substr($h, 0, 5);
    }

    protected static function booted(): void
    {
        static::saved(fn() => Area::limparCache());
        static::deleted(fn() => Area::limparCache());
        static::restored(fn() => Area::limparCache());
    }
}