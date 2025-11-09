<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected function customerId() {
        return auth('customer')->id(); // Use 'customer' guard
    }
    public function index(){
        $orders = Order::where('customer_id', $this->customerId())
                        ->with('items.product')
                        ->get();

        return view('orders', compact('orders'));
    }

}
