<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Payout\Payout;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Instruction extends Model
{
    use HasFactory;

    protected $table = 'instructions';

    protected $fillable = [
        'currency_id',
        'amount',
        'status',
        'vendor_id',
        'completed_at',
        'last_attempted_at'
    ];

    public function payouts() : BelongsToMany
    {
        return $this->belongsToMany(Payout::class);
    }

    public function vendor() : BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
