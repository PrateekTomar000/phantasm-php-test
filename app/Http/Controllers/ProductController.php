<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
  public function store(Request $request)
{
    $request->validate([
        'name'        => 'required',
        'sku'         => 'required|unique:products',
        'price'       => 'required|numeric',
        'status'      => 'required|in:active,inactive',
        'brand_id'    => 'required|exists:brands,id',
        'category_id' => 'required|exists:categories,id',
        'images'      => 'nullable|string',
    ]);

     $product = Product::create([
            'name' => $request->name,
            'sku' => $request->sku,
            'description' => $request->description,
            'price' => $request->price,
            'status' => $request->status,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
        ]);

        return redirect()->back()->with('success', 'Product created successfully!');
}


    public function getAll()
    {
        return Product::with('brand', 'category')->get();
    }

    public function getById($id)
    {
        return Product::with('brand', 'category')->findOrFail($id);
    }
}
