<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'currency_id' => $this->currency_id,
            'user_id' => $this->user_id,
            'total' => $this->total,
            'items' => OrderProduct::collection($this->orderProducts),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
