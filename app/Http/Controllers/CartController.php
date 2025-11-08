<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index() {
        $cartItems = Cart::where('customer_id', Auth::id())
                        ->with('product')
                        ->get();

        return view('cart.index', compact('cartItems'));
    }

    public function add(Request $request, Product $product) {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::firstOrNew([
            'customer_id' => Auth::id(),
            'product_id' => $product->id
        ]);

        $cart->quantity = ($cart->quantity ?? 0) + $request->quantity;
        $cart->price = $product->price;
        $cart->save();

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function update(Request $request, Cart $cart) {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart->quantity = $request->quantity;
        $cart->save();

        return redirect()->back()->with('success', 'Cart updated!');
    }

    public function remove(Cart $cart) {
        $cart->delete();
        return redirect()->back()->with('success', 'Item removed from cart!');
    }

    public function checkout() {
        $cartItems = Cart::where('customer_id', Auth::id())
                        ->with('product')
                        ->get();

        // Here you can add your payment integration (Razorpay) later

        return view('cart.checkout', compact('cartItems'));
    }
}
