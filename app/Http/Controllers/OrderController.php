<?php

namespace App\Http\Controllers;

use App\Contracts\Data\Order\CreateOrderDataObject;
use App\Contracts\Interfaces\Repositories\OrderRepositoryInterface;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Resources\Order as OrderResource;

class OrderController extends Controller
{
    public function __construct(private readonly OrderRepositoryInterface $orderRepository){}

    public function store(CreateOrderRequest $request, CreateOrderDataObject $dataObject): OrderResource
    {
        $order = $this->orderRepository->createOrder($dataObject::fromRequest($request));

        return new OrderResource($order);
    }
}
