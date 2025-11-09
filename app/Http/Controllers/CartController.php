<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

use Razorpay\Api\Api;

class CartController extends Controller
{
    // Use customer guard
    protected function customerId() {
        return auth('customer')->id(); // Use 'customer' guard
    }

    public function index() {
        $cartItems = Cart::where('customer_id', $this->customerId())
                        ->with('product')
                        ->get();
                        

        return view('cart.index', compact('cartItems'));
    }

    public function add(Request $request, Product $product) {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = Cart::firstOrNew([
            'customer_id' => $this->customerId(),
            'product_id' => $product->id
        ]);

        $cart->quantity = ($cart->quantity ?? 0) + $request->quantity;
        $cart->price = $product->price;
        $cart->save();

        // Update session
        $sessionCart = session()->get('cart', []);
        $sessionCart[$product->id] = $cart->quantity;
        session()->put('cart', $sessionCart);

        return response()->json(['success' => true, 'quantity' => $cart->quantity]);
    }

    public function update(Request $request, $productId) {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $product = Product::findOrFail($productId);

        $cart = Cart::firstOrNew([
            'customer_id' => $this->customerId(),
            'product_id' => $product->id
        ]);

        $cart->quantity = $request->quantity;
        $cart->price = $product->price;
        $cart->save();

        // Update session
        $sessionCart = session()->get('cart', []);
        $sessionCart[$productId] = $cart->quantity;
        session()->put('cart', $sessionCart);

        return response()->json(['success' => true, 'quantity' => $cart->quantity]);
    }

    public function update1(Request $request, Cart $cart) {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart->quantity = $request->quantity;
        $cart->save();

        $sessionCart = session()->get('cart', []);
        $sessionCart[$cart->product_id] = $cart->quantity;
        session()->put('cart', $sessionCart);

        if($request->ajax()){
            return response()->json(['success' => true, 'quantity' => $cart->quantity]);
        }

        return redirect()->back()->with('success', 'Cart updated!');
    }

    public function remove(Request $request, Cart $cart) {
        $cart->delete();

        $sessionCart = session()->get('cart', []);
        unset($sessionCart[$cart->product_id]);
        session()->put('cart', $sessionCart);

        if($request->ajax()){
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Item removed from cart!');
    }

    public function count() {
        $count = count(session()->get('cart', []));
        return response()->json(data: ['count' => $count]);
    }

    public function sessionCart() {
        $cartItems = Cart::where('customer_id', $this->customerId())
                        ->pluck('quantity','product_id')
                        ->toArray();
        return response()->json($cartItems);
    }

    public function checkout() {
        $cartItems = Cart::where('customer_id', $this->customerId())
                        ->with('product')->get();

        if($cartItems->isEmpty()){
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $total = $cartItems->sum(fn($i) => $i->price * $i->quantity);
        return view('checkout', compact('cartItems', 'total'));
    }

    // public function placeOrder(Request $request) {
    //     $cartItems = Cart::where('customer_id', $this->customerId())->get();

    //     if($cartItems->isEmpty()){
    //         return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
    //     }

    //     $total = $cartItems->sum(fn($i) => $i->price * $i->quantity);

    //     $order = \App\Models\Order::create([
    //         'customer_id' => $this->customerId(),
    //         'total_amount' => $total,
    //         'status' => 'pending'
    //     ]);

    //     foreach($cartItems as $item){
    //         \App\Models\OrderItem::create([
    //             'order_id' => $order->id,
    //             'product_id' => $item->product_id,
    //             'quantity' => $item->quantity,
    //             'price' => $item->price
    //         ]);
    //     }

    //     // Clear cart
    //     Cart::where('customer_id', $this->customerId())->delete();
    //     session()->forget('cart');

    //     return redirect()->route('orders.index')->with('success', 'Order placed successfully!');
    // }


public function placeOrder(Request $request) {
    $cartItems = Cart::where('customer_id', $this->customerId())->get();

    if($cartItems->isEmpty()){
        return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
    }

    $total = $cartItems->sum(fn($i) => $i->price * $i->quantity);

    $order = \App\Models\Order::create([
        'customer_id' => $this->customerId(),
        'total_amount' => $total,
        'status' => 'pending'
    ]);

    foreach($cartItems as $item){
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'price' => $item->price
        ]);
    }

    // **Create Razorpay order**
    $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    $razorpayOrder = $api->order->create([
        'receipt' => 'order_rcptid_'.$order->id,
        'amount' => $total * 100, // amount in paise
        'currency' => 'INR',
        'payment_capture' => 1
    ]);

    $order->update(['razorpay_order_id' => $razorpayOrder['id']]);

    // **Send Razorpay order info to frontend**
    return view('checkout_payment', [
        'order' => $order,
        'razorpayOrder' => $razorpayOrder,
        'cartItems' => $cartItems,
        'total' => $total
    ]);
}

public function verifyPayment(Request $request)
{
    try {
        $signature = $request->razorpay_signature;
        $paymentId = $request->razorpay_payment_id;
        $orderId = $request->razorpay_order_id;

        $order = \App\Models\Order::where('razorpay_order_id', $orderId)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found']);
        }

        // Verify signature
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $attributes = [
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $signature
        ];

        $api->utility->verifyPaymentSignature($attributes);

        // If signature is valid — update order status
        $order->update([
            'payment_id' => $paymentId,
            'status' => 'paid'
        ]);

        // Clear cart
        Cart::where('customer_id', $this->customerId())->delete();
        session()->forget('cart');

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Payment verification failed: ' . $e->getMessage()
        ]);
    }
}


}
