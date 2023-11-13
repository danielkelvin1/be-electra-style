<?php

namespace App\Service;

use App\Models\Adress;
use App\Models\Product;
use App\Models\ProductImage;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function create($data)
    {
        try {
            DB::beginTransaction();
            $id = auth()->user()->id;
            $title = $data['title'];
            $subtitle = $data['subtitle'];
            $description = $data['description'];
            $price = $data['price'];
            $image = $data['image'];
            $product = Product::create([
                'title' => $title,
                'subtitle' => $subtitle,
                'description' => $description,
                'price' => $price,
                'user_id' => $id
            ]);
            foreach ($image as $imageProduct) {
                $filename = time()  . Str::uuid() . '.' . $imageProduct->getClientOriginalExtension();
                $path = Storage::putFileAs('public/products', $imageProduct, $filename);
                ProductImage::create([
                    'image_url' => $path,
                    'product_id' => $product->id
                ]);
            }
            DB::commit();

            return response()->json([
                'message' => 'create product success',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            if ($e instanceof QueryException) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'data' => null
                ], 501);
            }

            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function deleteImage($id)
    {
        try {
            DB::beginTransaction();
            $productImage = ProductImage::where('id', $id)->firstOrFail();
            Storage::delete($productImage->image_url);
            $productImage->delete();
            DB::commit();
            return response()->json([
                'message' => 'Delete image product success'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Data not found',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
