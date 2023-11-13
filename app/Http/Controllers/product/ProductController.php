<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use App\Http\Requests\product\AddProductRequest;
use App\Service\ProductService;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {
    }

    public function addProduct(AddProductRequest $req)
    {
        $data = $req->only(['title', 'subtitle', 'description', 'price', 'image']);
        return $this->service->create($data);
    }

    public function deleteImageProduct($id)
    {
        return $this->service->deleteImage($id);
    }
}
