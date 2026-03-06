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
        'horario_abertura',
        'horario_fechamento',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function aberturaFormatada(): ?string
    {
        return $this->horario_abertura ? substr($this->horario_abertura, 0, 5) : null;
    }

    public function fechamentoFormatado(): ?string
    {
        return $this->horario_fechamento ? substr($this->horario_fechamento, 0, 5) : null;
    }

    protected static function booted(): void
    {
        static::saved(fn() => Area::limparCache());
        static::deleted(fn() => Area::limparCache());
    }
}
