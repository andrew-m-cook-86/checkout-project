<?php

declare(strict_types=1);

namespace App\Models\Payout;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $table = 'payouts';

    protected $fillable = [
        'order_id',
        'user_id',
        'total',
        'currency_id',
        'status',
    ];
}
