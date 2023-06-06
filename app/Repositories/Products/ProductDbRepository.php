<?php
declare(strict_types=1);

namespace App\Repositories\Products;

use App\Contracts\Interfaces\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductDbRepository implements ProductRepositoryInterface
{
    public function __construct(private readonly Product $product)
    {

    }

    public function list(): LengthAwarePaginator
    {
        return $this->product->newQuery()->with(['currency'])->paginate(Product::PAGINATION_LIMIT);
    }

    public function listProductsById(array $ids) : Collection
    {
        return $this->product->newQuery()->with(['currency'])->whereIn('id', $ids)->get();
    }
}
