<?php

namespace App\Http\Requests;

use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->id === $this->user->id ||  $this->user()->isAdmin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plan_id' => [
                'required',
                'exists:plans,id',
                function ($attribute, $value, $fail) {
                    $userId = $this->route('user')->id;

                    $exists = Subscription::where('user_id', $userId)
                        ->where('plan_id', $value)
                        ->exists();

                    if ($exists) {
                        $fail('User already subscribed to this plan.');
                    }
                },
            ],
            'renewal_at' => [
                'nullable',
                'date_format:Y-m-d',
            ]
        ];
    }

    /**
     * Get validation messages
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'plan_id.required' => 'The plan_id field is required.',
            'plan_id.exists' => 'The plan_id field must correspond to an existing plan.',
            'renewal_at.date_format' => 'The renewal_at date format must be Y-m-d.',
        ];
    }
}
