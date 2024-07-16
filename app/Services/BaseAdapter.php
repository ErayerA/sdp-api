<?php

namespace App\Services;

use App\Contracts\PaymentAdapterServiceInterface;
use App\Models\Subscription;
use App\Models\User;
use App\Utils\ChargeResult;

abstract class BaseAdapter implements PaymentAdapterServiceInterface
{
    public string $key;

    public function getKey(): string
    {
        return $this->key;
    }

    protected function setKey(string $key): void
    {
        $this->key = $key;
    }
    abstract public function charge(User $user, Subscription $subscription, float $price) : ChargeResult;

}
