<?php

namespace App\Services;

use App\Contracts\PaymentAdapterServiceInterface;
use App\Models\Subscription;
use App\Models\User;
use App\Utils\ChargeResult;

class IyzicoAdapter extends BaseAdapter
{
    public function __construct()
    {
        $this->key = 'iyzico';
    }

    public function charge(User $user, Subscription $subscription, float $price): ChargeResult
    {
        // Iyzico specific payment integration & charging logic here
        return new ChargeResult($user, $subscription, $price, $this->key, true);
    }

}
