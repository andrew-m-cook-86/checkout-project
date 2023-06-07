<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Payout\Payout;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'currency';

    protected $fillable = [
      'name',
      'prefix',
    ];

    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return HasMany
     */
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    /**
     * @return HasMany
     */
    public function instructions(): HasMany
    {
        return $this->hasMany(Instruction::class);
    }

    /**
     * @return HasMany
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }
}
