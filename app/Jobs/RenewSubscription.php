<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class RenewSubscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?Subscription $subscription = null;

    /**
     * Create a new job instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscription = $this->subscription;
        $service = new PaymentService();
        $result = $service->charge($subscription->user, $subscription, 100);
        $result = $result->toArray();

        $nextRenewal = $result['success'] ? Carbon::now()->addMonth()->format('Y-m-d') : $subscription->renewal_at;

        // In case transaction made with success...
        if ($result['success'] && $createdTransaction = $subscription->transactions()->create([
                'user_id' => $subscription->user->id,
                'subscription_id' => $subscription->id,
                'amount' => 100,
                'payment_provider'=>$result['payment_provider'],
            ])) {
            $result['transaction_id'] = $createdTransaction->id;
            $subscription->renewal_at = $nextRenewal;
            $subscription->save();
        }
    }
}
