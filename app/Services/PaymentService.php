<?php

namespace App\Services;

use App\Contracts\PaymentServiceInterface;
use App\Jobs\SendEmailJob;
use App\Mail\PaymentNotification;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Utils\ChargeResult;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentService implements PaymentServiceInterface
{

    public function getAdapter(string $key): ?BaseAdapter
    {

        $namespace = (new \ReflectionClass(get_class($this)))->getNamespaceName();

        $adapter = $namespace .'\\'. Str::studly($key).'Adapter';
        return new $adapter();
    }

    public function getAdapterOf(User|Transaction $model): BaseAdapter
    {
        return $this->getAdapter($model->payment_provider);
    }

    public function charge(User $user, Subscription $subscription, float $price): ChargeResult
    {
        $adapter = $this->getAdapterOf($user);
        $charge = $adapter->charge($user, $subscription, $price);
        $this->notify($charge);
        return $charge;
    }

    public function notify(ChargeResult $result): void {

        SendEmailJob::dispatch($result->user->email, new PaymentNotification($result));

    }

}
