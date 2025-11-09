@extends('layouts.app')

@section('title', 'Payment')

@section('content')

<div class="container mt-4">
    <h2>Complete Payment</h2>

    <p>Total Amount: ₹{{ $total }}</p>

    <button id="rzp-button" class="btn btn-primary">Pay Now</button>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
document.getElementById('rzp-button').onclick = function(e){
    var options = {
        key: "{{ env('RAZORPAY_KEY') }}",
        amount: {{ $razorpayOrder['amount'] }},
        currency: "INR",
        name: "PHANTASMSOLUTIONSPVTLTD",
        order_id: "{{ $razorpayOrder['id'] }}",
        handler: function (response){

            fetch("{{ route('verify.payment') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(response)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    alert("Payment Successful!");
                    window.location.href = "{{ route('orders.index') }}";
                } else {
                    alert("Payment Failed: " + data.message);
                }
            });
        },
        theme: { color: "#3399cc" }
    };

    var rzp = new Razorpay(options);
    rzp.open();
    e.preventDefault();
}
</script>

@endsection
