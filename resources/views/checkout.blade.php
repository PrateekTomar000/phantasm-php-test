@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mt-4">
    <h2>Checkout</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cartItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>₹{{ $item->price }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ $item->price * $item->quantity }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-end fw-bold">Total:</td>
                <td class="fw-bold">₹{{ $total }}</td>
            </tr>
        </tbody>
    </table>

    <form method="POST" action="{{ route('cart.placeOrder') }}">
        @csrf
        <button type="submit" class="btn btn-success">Place Order</button>
    </form>
</div>
@endsection
