<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\User;

class UserSubscriptionRelevance implements ValidationRule
{
    protected $userId;
    protected $subscriptionId;

    public function __construct($user, $subscription)
    {
        $this->user = $user;
        $this->subscription = $subscription;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::find($this->user->id);

        if (!$user || !$user->subscriptions()->where('id', $this->subscription->id)->exists()) {
            $fail('The selected user and subscription are not related.');
        }
    }
}
