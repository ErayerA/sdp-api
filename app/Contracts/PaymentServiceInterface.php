<?php

namespace App\Contracts;

use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BaseAdapter;
use App\Utils\ChargeResult;

interface PaymentServiceInterface
{
    public function charge(User $user, Subscription $subscription, float $price): ChargeResult;
    public function getAdapterOf(User | Transaction $model): BaseAdapter;
    public function getAdapter(string $key): ?BaseAdapter;

    function notify(ChargeResult $result): void;
}
