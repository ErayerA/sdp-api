<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Plan $plan
 */
class Subscription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_id',
        'renewal_at',
        'started_at',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo {
        return $this->belongsTo(Plan::class);
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }

    protected function casts():array
    {
        return [
            'started_at' => 'datetime:Y-m-d',
            'renewal_at' => 'datetime:Y-m-d',
        ];
    }
}
