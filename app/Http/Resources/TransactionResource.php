<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public $datetimeFormat = "Y-m-d H:i:s";
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'user_id'=> $this->user_id,
            'subscription_id'=> $this->subscription_id,
            'user'=> new UserResource($this->whenLoaded('user')),
            'subscription'=> new SubscriptionResource($this->whenLoaded('subscription')),
            'amount'=> $this->amount,
            'payment_provider' => $this->payment_provider,
            'created_at' => $this->created_at ? $this->created_at->format($this->datetimeFormat) : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format($this->datetimeFormat) : null,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format($this->datetimeFormat) : null
        ];
    }
}
