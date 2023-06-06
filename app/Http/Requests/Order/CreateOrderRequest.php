<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use App\Enums\CurrencyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'currency' => 'required|in:' . CurrencyEnum::namesToString(),
            'total' => 'required|decimal:2',
            'transaction_id' => 'required|string',
            'items' => 'required|array',
            'items*.name' => 'required|string',
            'items*.price' => 'required|decimal:2',
            'items*.id' => 'required|integer'
        ];
    }
}
