<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use App\Http\Requests\product\AddImageProductRequest;
use App\Http\Requests\product\AddProductRequest;
use App\Http\Requests\product\UpdateProductRequest;
use App\Service\ProductService;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {
    }

    public function addProduct(AddProductRequest $req)
    {
        if (Gate::allows('isSeller')) {
            $data = $req->only(['title', 'subtitle', 'description', 'price', 'image']);
            return $this->service->create($data);
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function deleteImageProduct($id)
    {
        if (Gate::allows('isSeller')) {
            return $this->service->deleteImage($id);
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function editProduct(UpdateProductRequest $req, $id)
    {
        if (Gate::allows('isSeller')) {
            $data = $req->only(['title', 'subtitle', 'description', 'price']);
            return $this->service->update($data, $id);
        } else {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    public function addImageProduct(AddImageProductRequest $req)
    {
        if (Gate::allows('isSeller')) {
            $data = $req->only(['image', 'idProduct']);
            return $this->service->addImage($data);
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function getAllProduct()
    {
    }

    public function deleteProduct($id)
    {
        if (Gate::allows('isSeller')) {
            return $this->service->delete($id);
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }
}
