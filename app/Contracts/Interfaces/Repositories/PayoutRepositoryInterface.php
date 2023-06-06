<?php
declare(strict_types=1);

namespace App\Contracts\Interfaces\Repositories;

use App\Models\Order;

interface PayoutRepositoryInterface
{
    public function create(Order $order, ...$props) : void;
}
