<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_subscribe_restful(): void
    {

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToSubscribe = User::factory()->create();
        $planToSubscribe = Plan::factory()->create();

        $uri = "/api/user/$userToSubscribe->id/subscriptions";

        $response = $this->actingAs($user,'api')->post( $uri, [
            'plan_id' => $planToSubscribe->id,
            'renewal_at'=> Carbon::now()->addMonth()->format("Y-m-d")
        ]);

        $response->assertStatus(200);

    }

    public function test_cannot_subscribe_by_missing_user(): void
    {

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $planToSubscribe = Plan::factory()->create();

        $uri = "/api/user/9000/subscriptions";

        $response = $this->actingAs($user,'api')->post( $uri, [
            'plan_id' => $planToSubscribe->id,
            'renewal_at'=> Carbon::now()->addMonth()->format("Y-m-d")
        ]);

        $response->assertStatus(404);

    }

    public function test_cannot_subscribe_with_missing_plan_id(): void
    {

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToSubscribe = User::factory()->create();

        $uri = "/api/user/$userToSubscribe->id/subscriptions";

        $response = $this->actingAs($user,'api')->post( $uri, [
            'renewal_at'=> Carbon::now()->addMonth()->format("Y-m-d")
        ]);

        $response->assertJsonValidationErrorFor('plan_id');
        $response->assertJsonMissingValidationErrors(['renewal_at']);

        $response->assertStatus(422);

    }

    public function test_cannot_subscribe_with_missing_plan(): void
    {

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToSubscribe = User::factory()->create();

        $uri = "/api/user/$userToSubscribe->id/subscriptions";

        $response = $this->actingAs($user,'api')->post( $uri, [
            'plan_id'=>900,
            'renewal_at'=> Carbon::now()->addMonth()->format("Y-m-d")
        ]);

        $response->assertJsonValidationErrorFor('plan_id');
        $response->assertJsonMissingValidationErrors(['renewal_at']);

        $response->assertStatus(422);

    }

    public function test_should_subscribe_with_default_renewal_at(): void
    {

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToSubscribe = User::factory()->create();
        $planToSubscribe = Plan::factory()->create();

        $uri = "/api/user/$userToSubscribe->id/subscriptions";

        $response = $this->actingAs($user,'api')->post( $uri, [
            'plan_id'=>$planToSubscribe->id,
        ]);

        $response->assertJsonPath('data.subscriptions.0.renewal_at', Carbon::now()->addMonth()->format("Y-m-d"));

        $response->assertStatus(200);

    }

    public function test_can_unsubscribe_restful(): void
    {

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToSubscribe = User::factory()->create();
        $planToSubscribe = Plan::factory()->create();
        $subscription = Subscription::create([
            'user_id' => $userToSubscribe->id,
            'plan_id' => $planToSubscribe->id,
            'started_at' => Carbon::now()->format("Y-m-d")
        ]);

        $uri = "/api/user/$userToSubscribe->id/subscriptions/$subscription->id";

        $response = $this->actingAs($user,'api')->delete($uri);
        $response->assertStatus(200);

        $this->assertSoftDeleted($subscription);

    }

    public function test_can_update_subscription_restful(): void
    {

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToSubscribe = User::factory()->create();
        $planToSubscribe = Plan::factory()->create();
        $subscription = Subscription::create([
            'user_id' => $userToSubscribe->id,
            'plan_id' => $planToSubscribe->id,
            'started_at' => Carbon::now()->format("Y-m-d"),
            'renewal_at' => Carbon::now()->format("Y-m-d")
        ]);

        $uri = "/api/user/$userToSubscribe->id/subscriptions/$subscription->id";

        $newDate = Carbon::now()->addMonth()->format("Y-m-d");

        $response = $this->actingAs($user,'api')->put($uri, [
            'renewal_at' => $newDate
        ]);
        $response->assertStatus(200);


    }

    public function test_cannot_update_irrevelant_user_subscription_pair(): void
    {

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToSubscribe = User::factory()->create();
        $planToSubscribe = Plan::factory()->create();
        $subscription = Subscription::create([
            'user_id' => $userToSubscribe->id,
            'plan_id' => $planToSubscribe->id,
            'started_at' => Carbon::now()->format("Y-m-d"),
            'renewal_at' => Carbon::now()->format("Y-m-d")
        ]);

        $uri = "/api/user/1/subscriptions/$subscription->id";

        $newDate = Carbon::now()->addMonth()->format("Y-m-d");

        $response = $this->actingAs($user,'api')->put($uri, [
            'renewal_at' => $newDate
        ]);
        $response->assertJsonValidationErrorFor('subscription_check');
        $response->assertStatus(422);
    }

    public function test_cannot_update_missing_subscription(): void
    {

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToSubscribe = User::factory()->create();

        $uri = "/api/user/$userToSubscribe->id/subscriptions/9000";

        $newDate = Carbon::now()->addMonth()->format("Y-m-d");

        $response = $this->actingAs($user,'api')->put($uri, [
            'renewal_at' => $newDate
        ]);
        $response->assertStatus(404);
    }

    public function test_cannot_unsubscribe_by_random_user(): void
    {

        $user = User::factory()->create();

        $userToSubscribe = User::factory()->create();
        $planToSubscribe = Plan::factory()->create();
        $subscription = Subscription::create([
            'user_id' => $userToSubscribe->id,
            'plan_id' => $planToSubscribe->id,
            'started_at' => Carbon::now()->format("Y-m-d")
        ]);

        $uri = "/api/user/$userToSubscribe->id/subscriptions/$subscription->id";

        $response = $this->actingAs($user,'api')->delete($uri);
        $response->assertForbidden();

    }
}
