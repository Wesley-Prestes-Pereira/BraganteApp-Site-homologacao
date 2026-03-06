<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class TipoArea extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $table = 'tipos_area';

    public const CACHE_TAG = 'tipos_area';

    protected $fillable = [
        'nome',
        'icone',
        'cor',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    public function temVinculo(): bool
    {
        return $this->areas()->withTrashed()->exists();
    }

    public function temReservas(): bool
    {
        return $this->areas()->withTrashed()
            ->whereHas('reservas', fn($q) => $q->withTrashed())
            ->exists();
    }

    public static function allCached()
    {
        return Cache::tags(self::CACHE_TAG)->remember(
            'tipos_area:all',
            3600,
            fn() => static::where('ativo', true)->orderBy('nome')->get(),
        );
    }

    public static function limparCache(): void
    {
        Cache::tags(self::CACHE_TAG)->flush();
        Area::limparCache();
    }

    protected static function booted(): void
    {
        static::saved(fn() => static::limparCache());
        static::deleted(fn() => static::limparCache());
        static::restored(fn() => static::limparCache());
    }
}
