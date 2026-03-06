<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Cliente extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    public const CACHE_TAG = 'clientes';

    protected $fillable = [
        'nome',
        'telefone',
        'email',
        'cpf',
        'ativo',
        'obs',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }

    public function temVinculo(): bool
    {
        return $this->reservas()->withTrashed()->exists()
            || $this->pagamentos()->withTrashed()->exists();
    }

    public static function findCached(int $id): ?self
    {
        return Cache::tags(self::CACHE_TAG)->remember(
            "cliente:{$id}",
            3600,
            fn() => static::find($id),
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