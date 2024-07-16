<?php

namespace App\Http\Requests;

use App\Rules\UserSubscriptionRelevance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return  $this->user()->id === $this->user->id ||  $this->user()->isAdmin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $user = $this->route('user');
        $subscription = $this->route('subscription');

        return [
            'renewal_at' =>'nullable|date_format:Y-m-d',
            'subscription_check' => [new UserSubscriptionRelevance($user, $subscription)],
        ];
    }
        public function validationData()
        {
            return array_merge($this->all(), [
                'subscription_check' => true,
            ]);
        }
}
