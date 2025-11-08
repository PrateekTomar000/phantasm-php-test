@extends('layouts.app')

@section('title', 'Your Cart')

@section('content')
<div class="container mt-4">
    <h2>Your Cart</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($cartItems->isEmpty())
        <div class="alert alert-info">Your cart is empty.</div>
    @else
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>₹{{ $item->price }}</td>
                    <td>
                        <form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex align-items-center">
                            @csrf
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="decreaseQty({{ $item->id }})">-</button>
                            <input type="text" name="quantity" id="qty-{{ $item->id }}" value="{{ $item->quantity }}" class="form-control text-center mx-1" style="width:50px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="increaseQty({{ $item->id }})">+</button>
                            <button type="submit" class="btn btn-sm btn-primary ms-2">Update</button>
                        </form>
                    </td>
                    <td>₹{{ $item->price * $item->quantity }}</td>
                    <td>
                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="text-end fw-bold">Total:</td>
                    <td colspan="2" class="fw-bold">₹{{ $cartItems->sum(fn($i) => $i->price * $i->quantity) }}</td>
                </tr>
            </tbody>
        </table>

        <a href="{{ route('cart.checkout') }}" class="btn btn-success">Proceed to Checkout</a>
    @endif
</div>

@endsection

@section('scripts')
<script>
window.increaseQty = function(id) {
    const input = document.getElementById(`qty-${id}`);
    input.value = parseInt(input.value) + 1;
}

window.decreaseQty = function(id) {
    const input = document.getElementById(`qty-${id}`);
    if(parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
}
</script>
@endsection
