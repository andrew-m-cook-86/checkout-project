<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lanos\CashierConnect\Contracts\StripeAccount;
use Laravel\Cashier\Billable as CashierBillable;
use Lanos\CashierConnect\Billable as ConnectBillable;

class Vendor extends Model implements StripeAccount
{
    use CashierBillable;
    use ConnectBillable;

    use HasFactory;

    /**
     * Get the post that owns the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that owns the comment.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function instructions(): HasMany
    {
        return $this->hasMany(Instruction::class);
    }
}
