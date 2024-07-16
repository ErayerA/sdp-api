<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function subscriptions(): HasMany {
        return $this->hasMany(Subscription::class);
    }
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'plan_id', 'user_id');
    }
}
