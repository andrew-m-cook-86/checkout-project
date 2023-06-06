<?php

namespace App\Http\Controllers;

use App\Contracts\Interfaces\Repositories\ProductRepositoryInterface;
use App\Http\Resources\Product as ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly ProductRepositoryInterface $productRepository)
    {
    }

    public function index(Request $request)
    {
        $products = $this->productRepository->list();

        return view('products', compact('products'));
    }
}
