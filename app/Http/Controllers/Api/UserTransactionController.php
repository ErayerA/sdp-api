<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserTransactionRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserTransactionController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserTransactionRequest $request, User $user): UserResource
    {
        // gather props sent within request
        $toFill = $request->validated();
        $user->loadMissing($user->allIncludes);

        $subscription = $user->subscriptions->where('id', $toFill['subscription_id'])->first();

        // while "price" is conventional to define the amount to pay, it is "amount" to define what is paid
        // so, it is not "price" but "amount" on a transaction model
        // replace "price" key with "amount"
        if (!array_key_exists('price', $toFill)) {
            $toFill['amount'] = (float)$subscription->plan->price;
        } else {
            $toFill['amount'] = (float)$toFill['price'];
            unset($toFill['price']);
        }
        //
        $toFill['user_id'] = $user->id;

        $service = new PaymentService();
        $result = $service->charge($user, $subscription, $toFill['amount']);
        $result = $result->toArray();

        $nextRenewal = $result['success'] ? Carbon::now()->addMonth()->format('Y-m-d') : $subscription->renewal_at;

        // In case transaction made with success...
        if ($result['success']){
            $toFill['payment_provider'] = $result['payment_provider'];
            if($createdTransaction = $subscription->transactions()->create($toFill)){
                $result['transaction_id'] = $createdTransaction->id;
                $subscription->renewal_at = $nextRenewal;
                $subscription->save();
            }
        }

        // Unsuccessful transaction can be watched by reading 'transaction' node of the returned json.

        return (new UserResource($user->fresh()->loadMissing($user->allIncludes)))->additional(
            ['transactionResult' => $result, "nextRenewalAt" => $nextRenewal]
        );
    }


}
