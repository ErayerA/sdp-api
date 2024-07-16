<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'price' => (float)$this->price,
            'users'=> UserResource::collection($this->whenLoaded('users')),
            'subscriptions'=> SubscriptionResource::collection($this->whenLoaded('subscriptions')),
            'created_at' => $this->created_at ? $this->created_at->format($this->datetimeFormat) : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format($this->datetimeFormat) : null,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format($this->datetimeFormat) : null
        ];
    }
}
