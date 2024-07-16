<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'user_id'=>$this->user_id,
            'plan_id'=>$this->plan_id,
            'started_at' => $this->started_at ? $this->started_at->format('Y-m-d') : null,
            'renewal_at' => $this->renewal_at ? $this->renewal_at->format('Y-m-d') : null,
            'transactions'=> TransactionResource::collection($this->whenLoaded('transactions')),
            'plan'=> new PlanResource($this->whenLoaded('plan')),
            'user'=> new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at ? $this->created_at->format($this->datetimeFormat) : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format($this->datetimeFormat) : null,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format($this->datetimeFormat) : null
        ];
    }
}
