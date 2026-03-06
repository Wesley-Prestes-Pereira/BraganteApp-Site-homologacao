<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany};
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Taxa extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    public const CACHE_TAG = 'taxas';

    protected $fillable = [
        'nome',
        'valor',
        'tipo_cobranca',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'ativo' => 'boolean',
        ];
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'area_taxas')
            ->withPivot('obrigatoria')
            ->withTimestamps();
    }

    public function reservaTaxas(): HasMany
    {
        return $this->hasMany(ReservaTaxa::class);
    }

    public function temVinculo(): bool
    {
        return $this->reservaTaxas()->exists();
    }

    public static function allCached()
    {
        return Cache::tags(self::CACHE_TAG)->remember(
            'taxas:all',
            3600,
            fn() => static::where('ativo', true)->orderBy('nome')->get(),
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