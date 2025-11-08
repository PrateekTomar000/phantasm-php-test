<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function createForm()
    {
        return view('stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:stores',
            'contact' => 'required',
            'address' => 'nullable',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Store::create($request->all());

        return redirect()->back()->with('success', 'Store created successfully!');
    }

    // JSON for DataTables
    public function data()
    {
        $stores = Store::all();
        return response()->json(['data' => $stores]);
    }

    // Search store by ID
    public function search(Request $request)
    {
        $id = $request->query('id');
        $stores = $id ? Store::where('id', $id)->get() : Store::all();
        return response()->json(['data' => $stores]);
    }
}
