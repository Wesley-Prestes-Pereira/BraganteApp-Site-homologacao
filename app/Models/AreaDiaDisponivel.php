<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaDiaDisponivel extends Model
{
    protected $table = 'area_dias_disponiveis';

    protected $fillable = [
        'area_id',
        'dia_semana',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    protected static function booted(): void
    {
        static::saved(fn() => Area::limparCache());
        static::deleted(fn() => Area::limparCache());
    }
}