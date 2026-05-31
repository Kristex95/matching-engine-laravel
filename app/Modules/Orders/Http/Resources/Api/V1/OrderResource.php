<?php

declare(strict_types=1);

namespace App\Modules\Orders\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_id'      => $this->resource->uuid,
            'account_id'    => $this->resource->account_id,
            'side'          => $this->resource->side,
            'type'          => $this->resource->type,
            'currency'      => $this->resource->currency,
            'price'         => $this->resource->price,
            'amount'        => $this->resource->amount,
            'filled_amount' => $this->resource->filled_amount,
            'status'        => $this->resource->status,
            'created_at'    => $this->resource->created_at?->toIso8601String(),
            'updated_at'    => $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
