<?php

namespace App\Utils;

use App\Models\Subscription;
use App\Models\User;
use App\Services\BaseAdapter;

class ChargeResult
{
    public ?User $user = null;
    public ?Subscription $subscription = null;
    public float $price = 0.00;
    public ?string $paymentProvider = null;
    public ?\Exception $exception = null;
    public bool $success = false;
    public function __construct(User $user = null, Subscription $subscription = null, float $price=0.00, string $paymentProvider = null,   bool $success = null, $exception = null)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->price = $price;
        $this->paymentProvider = $paymentProvider;
        $this->success = (bool)$success;
        $this->exception = $exception;
    }

    public function toArray(): array {
        return [
            'success' => $this->success,
            'subscription' => $this->subscription->toArray(),
            'price' => $this->price,
            'paymentProvider' => $this->paymentProvider,
            'exception' => $this->exception,
        ];
    }

    public function notificationReady(): bool {
        return $this->user && $this->subscription;
    }

}
