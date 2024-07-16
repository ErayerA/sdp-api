<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{

    use RefreshDatabase;

    public function test_can_register_user_restful(): void
    {
        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $response = $this->actingAs($user, 'api')
            ->post('/api/register',[
                'email' => 'new.user@mukellef.co',
                'password' => '12345678',
                'name' => 'test_can_register_user_restful'
            ]);

        $response->assertStatus(201);
    }

    public function test_can_return_expected_user_structure(): void
    {
        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $planToSubscribe = Plan::factory()->create();
        $userToShow = User::factory()->create();

        $subscription = Subscription::create([
            'user_id' => $userToShow->id,
            'plan_id' => $planToSubscribe->id,
            'started_at' => Carbon::now()->format("Y-m-d")
        ]);

        Transaction::create([
            'user_id' => $userToShow->id,
            'subscription_id' => $subscription->id,
            'amount' => 100,
            'payment_provider' => 'iyzico',
        ]);


        $response = $this->actingAs($user, 'api')
            ->post('/api/register',[
                'email' => 'new.user@mukellef.co',
                'password' => '12345678',
                'name' => 'test_can_register_user_restful'
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'subscriptions'=>[],    // this key would not exist when there are no related models
                'transactions'=>[],     // this key would not exist when there are no related models
                'plans'=>[],            // this key would not exist when there are no related models
                'created_at',
                'updated_at',
                'deleted_at'
            ]
        ]);
    }

    public function test_cannot_save_with_invalid_email(): void
    {
        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $response = $this->actingAs($user, 'api')
            ->post('/api/register',[
                'email' => 'new.user',
                'password' => '12345678',
                'name' => 'Invalid Email User'
            ]);

        $response->assertJsonValidationErrorFor('email');
        $response->assertJsonMissingValidationErrors(['password', 'name']);
        $response->assertStatus(422);

    }

    public function test_cannot_save_with_invalid_password(): void
    {
        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $response = $this->actingAs($user, 'api')
            ->post('/api/register',[
                'email' => 'new.user@mukellef.co',
                'password' => '1234567',
                'name' => 'Invalid Short Password'
            ]);

        $response->assertJsonValidationErrorFor('password');
        $response->assertJsonMissingValidationErrors(['email', 'name']);
        $response->assertStatus(422);
    }

    public function test_cannot_save_with_invalid_name(): void
    {
        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $response = $this->actingAs($user, 'api')
            ->post('/api/register',[
                'email' => 'new.user@mukellef.co',
                'password' => '12345678',
                'name' => '12'
            ]);

        $response->assertJsonValidationErrorFor('name');
        $response->assertJsonMissingValidationErrors(['email', 'password']);
        $response->assertStatus(422);
    }

    public function test_cannot_save_with_existing_email(): void
    {
        $user = User::whereEmail('mukellef@mukellef.co')->first();

        $response = $this->actingAs($user, 'api')
            ->post('/api/register',[
                'email' => 'mukellef@mukellef.co',
                'password' => '12345678',
                'name' => 'Existing Email User'
            ]);

        $response->assertJsonValidationErrorFor('email');
        $response->assertJsonMissingValidationErrors(['password', 'name']);
        $response->assertStatus(422);
    }

    public function test_cannot_save_by_random_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->post('/api/register',[
                'email' => 'aaa@mukellef.co',
                'password' => '12345678',
                'name' => 'Breaching User'
            ]);

        $response->assertForbidden();
    }

    public function test_cannot_save_unguarded(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeader('Accept', 'application/json')->post('/api/register',[
                'email' => 'aaa@mukellef.co',
                'password' => '12345678',
                'name' => 'Breaching User'
            ]);

        $response->assertUnauthorized();
    }
}
