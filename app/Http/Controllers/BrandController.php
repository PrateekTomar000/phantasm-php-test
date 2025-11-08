<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function create(Request $request)
    {
        $request->validate(['name' => 'required|unique:brands']);
        return Brand::create(['name' => $request->name]);
    }

    public function getAll()
    {
        return Brand::all();
    }

    public function getById($id)
    {
        return Brand::findOrFail($id);
    }
}
