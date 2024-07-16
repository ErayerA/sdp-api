<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @property array $allIncludes
 * @property bool $isAdmin
 * @property string $email
 * @method static create(mixed $validated)
 * @method static whereEmail(string $string)
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'payment_provider'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subscriptions(): HasMany {
        return $this->hasMany(Subscription::class);
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'subscriptions', 'user_id', 'plan_id');
    }
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    protected function allIncludes():Attribute {

        return Attribute::make(
            get: fn () => static::getAllIncludes(),
        );
    }

    protected function isAdmin():Attribute {
        return Attribute::make(
            get: fn()=>$this->attributes['email']==='mukellef@mukellef.co'
        );
    }


    public static function getAllIncludes ():array {
        return [
            'subscriptions' => function($q) { $q->with(['plan','transactions']); },
            'plans',
            'transactions'
        ];
    }
}
