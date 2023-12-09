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

    public function update($data, $id)
    {
        try {
            DB::beginTransaction();
            $userId = auth()->user()->id;
            $product = Product::where('id', $id)
                ->where('user_id', $userId)->firstOrFail();
            $product->title = $data['title'];
            $product->subtitle = $data['subtitle'];
            $product->description = $data['description'];
            $product->price = $data['price'];
            $product->save();
            DB::commit();
            return response()->json([
                'message' => 'Product updated'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    "message" => "Data not found",
                    "data" => null
                ], 404);
            }

            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getAll()
    {
        try {
            DB::beginTransaction();
            $product = Product::paginate(5);
            DB::commit();
            return response()->json([
                'products' => $product
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();


            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function addImage($data)
    {
        try {
            DB::beginTransaction();
            $filename = time() . Str::uuid() . '.' . $data['image']->getClientOriginalExtension();
            $path = Storage::putFileAs('public/products', $data['image'], $filename);
            ProductImage::create([
                'image_url' => $path,
                'product_id' => $data['idProduct']
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Image success create'
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            if ($path) {
                Storage::delete($path);
            }

            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $userId = auth()->user()->id;
            $product = Product::where('id', $id)
                ->where('user_id', $userId)->with('images')->firstOrFail();
            foreach ($product->images as $image) {
                Storage::delete($image->image_url);
            }
            $product->delete();
            DB::commit();
            return response()->json([
                'message' => 'Delete success'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

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
