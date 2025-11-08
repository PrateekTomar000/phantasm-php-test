<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomerController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:customers',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customer = Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Issue token (auth()->login() works with api guard)
        $token = auth('api')->login($customer);

        return response()->json([
            'customer' => $customer,
            'token'    => $token
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // This attempts using the api guard → customers provider
        $token = auth('api')->attempt($request->only('email', 'password'));

        if (!$token) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'success'  => true,
            'token'    => $token,
            'customer' => auth('api')->user()
        ]);
    }

    public function profile()
    {
        // Automatically uses auth:api (no need for parseToken)
        return response()->json(auth('api')->user());
    }

    public function updateAddress(Request $request)
    {
        $customer = auth('api')->user();

        $customer->latitude = $request->latitude;
        $customer->longitude = $request->longitude;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'customer' => $customer
        ]);
    }
}
