<?php

namespace App\Http\Requests;

use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->id === $this->user->id || $this->user()->isAdmin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'subscription_id'=> [
                'required',
                'exists:subscriptions,id',
                function ($attribute, $value, $fail) {
                    $userId = $this->route('user')->id;

                    $exists = Subscription::where('user_id', $userId)
                        ->find($this->subscription_id);
                    if (!$exists) {
                        $fail('Subscription neither exists nor belongs to the user.');
                    }
                }
            ],
            'price' =>[
                'sometimes',
                'decimal:0,2',
                'min:0.01'
            ],
        ];
    }


    public function messages(): array{
        return [
            'subscription_id.required' => 'The subscription_id field is required.',
            'subscription_id.exists' => 'The subscription_id field must correspond to an existing subscription.',
            'price.decimal' => 'The price field must have 0-2 decimal places.',
            'price.min' => 'The lowest price value allowed is 0.01',
        ];
    }
}
