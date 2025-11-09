@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Your Orders</h2>

    <div class="alert alert-success">
        ✅ Payment Successful & Order Placed!
    </div>

    @foreach($orders as $order)
        <div class="card mb-3">
            <div class="card-header">
                Order #{{ $order->id }} — {{ ucfirst($order->status) }}
            </div>
            <div class="card-body">
                <p>Total Amount: ₹{{ $order->total_amount }}</p>
            </div>
        </div>
    @endforeach
</div>
@endsection
