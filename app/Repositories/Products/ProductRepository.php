<?php
declare(strict_types=1);

namespace App\Repositories\Products;

use App\Contracts\Interfaces\Repositories\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(private ProductDbRepository $dbRepository){}

    public function list(): LengthAwarePaginator
    {
        return $this->dbRepository->list();
    }

}
