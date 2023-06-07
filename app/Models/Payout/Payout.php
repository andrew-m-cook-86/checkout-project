<?php

declare(strict_types=1);

namespace App\Models\Payout;

use App\Models\Currency;
use App\Models\Instruction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payout extends Model
{
    protected $table = 'payouts';

    protected $fillable = [
        'order_id',
        'user_id',
        'total',
        'currency_id',
        'status',
        'completed_at',
        'last_attempted_at'
    ];

    protected $casts = [
        'total' => 'float',
    ];

    /**
     * Get the post that owns the comment.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the post that owns the comment.
     */
    public function instructions(): BelongsToMany
    {
        return $this->belongsToMany(Instruction::class);
    }
}
