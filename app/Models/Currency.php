<?php
declare(strict_types=1);

namespace App\Models;

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
     * Get the post that owns the comment.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
