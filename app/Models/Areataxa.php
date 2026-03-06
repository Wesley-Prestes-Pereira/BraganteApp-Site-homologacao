<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class AreaTaxa extends Model implements AuditableContract
{
    use Auditable;

    protected $table = 'area_taxas';

    protected $fillable = [
        'area_id',
        'taxa_id',
        'obrigatoria',
    ];

    protected function casts(): array
    {
        return [
            'obrigatoria' => 'boolean',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function taxa(): BelongsTo
    {
        return $this->belongsTo(Taxa::class);
    }

    protected static function booted(): void
    {
        static::saved(fn() => Area::limparCache());
        static::deleted(fn() => Area::limparCache());
    }
}