<?php
declare(strict_types=1);

namespace App\Contracts\Interfaces\Repositories;

use App\Contracts\Data\Order\CreateOrderDataObject;
use App\Models\Order;

interface OrderRepositoryInterface
{
    public function createOrder(CreateOrderDataObject $dataObject) : Order;
}
