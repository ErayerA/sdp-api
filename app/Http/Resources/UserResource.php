<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'subscriptions'=> SubscriptionResource::collection($this->whenLoaded('subscriptions')),
            'transactions'=> TransactionResource::collection($this->whenLoaded('transactions')),
            'plans'=> PlanResource::collection($this->whenLoaded('plans')),
            'created_at' => $this->created_at ? $this->created_at->format($this->datetimeFormat) : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format($this->datetimeFormat) : null,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format($this->datetimeFormat) : null
        ];
    }
}
