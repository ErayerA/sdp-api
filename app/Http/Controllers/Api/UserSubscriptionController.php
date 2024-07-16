<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteUserSubscriptionRequest;
use App\Http\Requests\StoreUserSubscriptionRequest;
use App\Http\Requests\UpdateUserSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\UserResource;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class UserSubscriptionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(User $user, StoreUserSubscriptionRequest $request): UserResource
    {
        $props = $request->validated();

        // From case request: BEKLENEN ENDPOINT'LER: ● POST /user/ #id /subscription : renewal_at alanı alır.

        // Why would renewal_at be posted at all into store method?
        // Isn't it supposed to be computed after subscription created ?
        // Is it planned to be equal to "today" so initialization payment would be charged as it was a renewal?

        // Or is it just "Use first pay later"? => MY CONCLUSION

        if(!in_array('renewal_at', $props)) {
            //
            $props['renewal_at'] = Carbon::now()->addMonth()->format('Y-m-d');
        }
        $props['started_at'] = Carbon::now()->format('Y-m-d');

        $user->subscriptions()->create($props);

        return new UserResource($user->fresh()->loadMissing($user->allIncludes));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserSubscriptionRequest $request, User $user, Subscription $subscription)
    {
        $props = $request->validated();
        if(count($props)) {
            $subscription->update($props);
        }
        return new UserResource($user->fresh()->loadMissing($user->allIncludes));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteUserSubscriptionRequest $request, User $user, Subscription $subscription): UserResource
    {
        $subscription->delete();
        return new UserResource($user->fresh()->loadMissing($user->allIncludes));


    }
}
