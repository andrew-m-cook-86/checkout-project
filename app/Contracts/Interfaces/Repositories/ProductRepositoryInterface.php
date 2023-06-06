<?php
declare(strict_types=1);

namespace App\Contracts\Interfaces\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function list(): LengthAwarePaginator;
}
