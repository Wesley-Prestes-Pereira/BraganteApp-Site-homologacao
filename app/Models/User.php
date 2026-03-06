<?php

namespace App\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Permission\{PermissionRegistrar, Traits\HasRoles};
use Illuminate\Database\Eloquent\{SoftDeletes, Factories\HasFactory};

class User extends Authenticatable implements AuditableContract
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, Auditable;

    public const CACHE_TAG = 'usuarios';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public static function findCached(int $id): ?self
    {
        return Cache::tags(self::CACHE_TAG)->remember(
            "user:{$id}",
            3600,
            fn() => static::find($id),
        );
    }

    public static function limparCache(int $userId): void
    {
        Cache::tags(self::CACHE_TAG)->forget("user:{$userId}");
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected static function booted(): void
    {
        static::saved(fn(self $u) => static::limparCache($u->id));
        static::deleted(fn(self $u) => static::limparCache($u->id));
        static::restored(fn(self $u) => static::limparCache($u->id));
    }
}