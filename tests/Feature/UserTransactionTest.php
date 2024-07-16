<?php

namespace Tests\Feature;

use App\Jobs\SendEmailJob;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UserTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_charge_restful(): void
    {
        Queue::fake();

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToCharge = User::factory()->create();
        $planToSubscribe = Plan::factory()->create();
        $subscriptionToBeChargedFor = Subscription::create([
            'user_id' => $userToCharge->id,
            'plan_id' => $planToSubscribe->id,
            'started_at' => Carbon::now()->format("Y-m-d")
        ]);

        $uri = "/api/user/$userToCharge->id/transactions";

        $response = $this->actingAs($user,'api')->post( $uri, [
            'subscription_id' => $subscriptionToBeChargedFor->id,
            'price' => 100,
        ]);

        $response->assertStatus(200);

    }

    public function test_cannot_charge_with_missing_subscription(): void
    {
        Queue::fake();

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToCharge = User::factory()->create();

        $uri = "/api/user/$userToCharge->id/transactions";

        $response = $this->actingAs($user,'api')->post( $uri, [
            'subscription_id' => 9000,
            'price' => 100,
        ]);

        $response->assertStatus(422);

    }

    public function test_cannot_charge_with_missing_subscription_id(): void
    {
        Queue::fake();

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToCharge = User::factory()->create();

        $uri = "/api/user/$userToCharge->id/transactions";

        $response = $this->actingAs($user,'api')->post( $uri, [
            'price' => 100,
        ]);

        $response->assertStatus(422);

    }

    public function test_cannot_charge_with_missing_user(): void
    {
        Queue::fake();

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToCharge = User::factory()->create();
        $planToSubscribe = Plan::factory()->create();
        $subscriptionToBeChargedFor = Subscription::create([
            'user_id' => $userToCharge->id,
            'plan_id' => $planToSubscribe->id,
            'started_at' => Carbon::now()->format("Y-m-d")
        ]);

        $uri = "/api/user/9000/transactions";

        $response = $this->actingAs($user,'api')->post( $uri, [
            'subscription_id' => $subscriptionToBeChargedFor->id,
            'price' => 100,
        ]);

        $response->assertStatus(404);

    }

    public function test_cannot_charge_irrelevant_user(): void
    {
        Queue::fake();

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToCharge = User::factory()->create();
        $planToSubscribe = Plan::factory()->create();
        $subscriptionToBeChargedFor = Subscription::create([
            'user_id' => $userToCharge->id,
            'plan_id' => $planToSubscribe->id,
            'started_at' => Carbon::now()->format("Y-m-d")
        ]);

        $uri = "/api/user/1/transactions";

        $response = $this->actingAs($user,'api')->post( $uri, [
            'subscription_id' => $subscriptionToBeChargedFor->id,
            'price' => 100,
        ]);

        $response->assertJsonValidationErrorFor('subscription_id');

        $response->assertStatus(422);

    }

    public function test_can_notify_user_with_email(): void
    {
        Queue::fake();

        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $userToCharge = User::factory()->create();
        $planToSubscribe = Plan::factory()->create();
        $subscriptionToBeChargedFor = Subscription::create([
            'user_id' => $userToCharge->id,
            'plan_id' => $planToSubscribe->id,
            'started_at' => Carbon::now()->format("Y-m-d")
        ]);

        $uri = "/api/user/$userToCharge->id/transactions";

        $response = $this->actingAs($user,'api')->post( $uri, [
            'subscription_id' => $subscriptionToBeChargedFor->id,
            'price' => 100,
        ]);
        Queue::assertPushed(SendEmailJob::class, 1);

        $response->assertStatus(200);
    }
}
