@extends('layouts.app')

@section('title', 'Your Orders')

@section('content')
<div class="container mt-4">
    <h2>Your Orders</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($orders->isEmpty())
        <div class="alert alert-info">You have no orders yet.</div>
    @else
        @foreach($orders as $order)
            <div class="card mb-3">
                <div class="card-header">
                    Order #{{ $order->id }} - ₹{{ $order->total }} - Status: {{ $order->status }}
                </div>
                <div class="card-body">
                    <ul>
                        @foreach($order->items as $item)
                        <li>{{ $item->product->name }} x {{ $item->quantity }} = ₹{{ $item->price * $item->quantity }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
