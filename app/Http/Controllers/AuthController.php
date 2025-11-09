<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    // Web Views
    public function showRegisterForm() { return view('register'); }
    public function showLoginForm() { return view('login'); }

    // Web Registration
    public function registerUser(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);

        auth()->login($user); // automatically login
        return redirect()->route('dashboard');
    }

    // Web Login
    public function loginUser(Request $request)
    {
        $credentials = $request->only('email','password');

        if(auth()->attempt($credentials)){
            return redirect()->route('dashboard');
        }

        return back()->with('error','Invalid credentials');
    }
    public function syncSession(Request $request)
    {
        // Store customer data in Laravel session
        session([
            'customer_id' => $request->id,
            'customer_name' => $request->name,
            'customer_email' => $request->email,
            'customer_latitude' => $request->latitude,
            'customer_longitude' => $request->longitude,
            'jwt_token' => $request->token,
        ]);

        // Optionally log in the customer (for Auth::check() and cart)
        $customer = Customer::find($request->id);
        if ($customer) {
            auth('customer')->login($customer);
        }

        return response()->json(['success' => true, 'message' => 'Session synced successfully']);
    }

    public function dashboard()
    {
        $user = auth()->user();
        return view('dashboard', compact('user'));
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login.form');
    }

    // API Registration
    public function apiRegister(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    // API Login
    public function apiLogin(Request $request)
    {
        $credentials = $request->only('email','password');

        try {
            if(!$token = JWTAuth::attempt($credentials)){
                return response()->json(['error'=>'Invalid credentials'],401);
            }
        } catch(JWTException $e){
            return response()->json(['error'=>'Could not create token'],500);
        }

        return response()->json(compact('token'));
    }

    // API: current user
    public function apiMe()
    {
        return response()->json(auth()->user());
    }

    // API: list users with pagination
    public function listUsers()
    {
        return response()->json(User::paginate(10));
    }

   

}
