<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProduct extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total' => $this->total,
            'name' => $this->whenLoaded('product') ? $this->product->name : null,
        ];
    }
}
