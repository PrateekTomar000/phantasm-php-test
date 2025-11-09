@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="container mt-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Customer Dashboard</h2>

        <div>
            <!-- Cart Button -->
            <a href="{{ route('cart.index') }}" class="btn btn-primary position-relative">
                Cart
                <span id="cartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    0
                </span>
            </a>

            <!-- Logout -->
            <button id="logout-btn" class="btn btn-danger">Logout</button>
        </div>
    </div>

    <!-- Welcome -->
    <div id="welcomeBox" class="alert alert-primary">Loading customer info...</div>

    <!-- Notification -->
    <div id="notification" class="alert alert-success d-none position-fixed top-0 end-0 m-3" style="z-index:9999;"></div>

    <!-- PRODUCTS SECTION -->
    <h4 class="mt-5 mb-3">Products</h4>
    <div id="productContainer" class="row g-4"></div>

</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {

    // Load profile
    function loadProfile() {
        $.ajax({
            url: '/api/customer/profile',
            headers: { "Authorization": "Bearer " + localStorage.getItem("customerToken") },
            method: 'GET',
            success: function(data) {
                $('#welcomeBox').html(`Welcome <strong>${data.name}</strong> (${data.email})`);
            }
        });
    }

    // Load cart session
    function loadCartSession() {
        return $.ajax({
            url: '/cart/session',
            method: 'GET'
        });
    }

    // Load products
    function loadProducts() {
        $.when($.getJSON('/customer/products/data'), loadCartSession())
        .done(function(productsRes, cartSessionRes){
            let products = productsRes[0];
            let cartSession = cartSessionRes[0];
            let html = '';

            $.each(products, function(i, p){
                let inCart = cartSession[p.id] || 0;
                let cartBtnHtml = '';

                if(inCart > 0){
                    cartBtnHtml = `
                        <div class="input-group">
                            <button class="btn btn-danger minus-btn" data-id="${p.id}">-</button>
                            <input type="text" class="form-control text-center cartQty" data-id="${p.id}" value="${inCart}" readonly>
                            <button class="btn btn-success plus-btn" data-id="${p.id}">+</button>
                        </div>`;
                } else {
                    cartBtnHtml = `
                        <input type="number" min="1" value="1" class="form-control qty-input mb-2" data-id="${p.id}">
                        <button class="btn btn-primary w-100 add-btn" data-id="${p.id}">Add to Cart</button>`;
                }

                html += `
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            ${p.image ? `<img src="/storage/${p.image}" class="card-img-top">` : ""}
                            <div class="card-body">
                                <h5 class="card-title">${p.name}</h5>
                                <p class="text-muted">${p.sku}</p>
                                <p>${p.description || ""}</p>
                                <p class="fw-bold">₹${p.price}</p>
                                <div id="cartBtn-${p.id}">${cartBtnHtml}</div>
                            </div>
                        </div>
                    </div>`;
            });

            $('#productContainer').html(html);
        });
    }

    // Show notification
    function showNotification(msg){
        let notif = $('#notification');
        notif.text(msg).removeClass('d-none');
        setTimeout(() => notif.addClass('d-none'), 2000);
    }

    // Add to cart
    $(document).on('click', '.add-btn', function(){
        let productId = $(this).data('id');
        let qty = parseInt($(`.qty-input[data-id=${productId}]`).val());

        $.ajax({
            url: `/cart/add/${productId}`,
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            contentType: 'application/json',
            data: JSON.stringify({ quantity: qty }),
            success: function(data){
                if(data.success){
                    $(`#cartBtn-${productId}`).html(`
                        <div class="input-group">
                            <button class="btn btn-danger minus-btn" data-id="${productId}">-</button>
                            <input type="text" class="form-control text-center cartQty" data-id="${productId}" value="${data.quantity}" readonly>
                            <button class="btn btn-success plus-btn" data-id="${productId}">+</button>
                        </div>
                    `);
                    showNotification('Product added to cart!');
                    updateCartCount();
                }
            }
        });
    });

    // Minus button
    $(document).on('click', '.minus-btn', function(){
        let productId = $(this).data('id');
        let qtyInput = $(`.cartQty[data-id=${productId}]`);
        let newQty = parseInt(qtyInput.val()) - 1;
        if(newQty < 1) return;

        $.ajax({
            url: `/cart/update/${productId}`,
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            contentType: 'application/json',
            data: JSON.stringify({ quantity: newQty }),
            success: function(data){
                if(data.success){
                    qtyInput.val(newQty);
                    updateCartCount();
                }
            }
        });
    });

    // Plus button
    $(document).on('click', '.plus-btn', function(){
        let productId = $(this).data('id');
        let qtyInput = $(`.cartQty[data-id=${productId}]`);
        let newQty = parseInt(qtyInput.val()) + 1;

        $.ajax({
            url: `/cart/update/${productId}`,
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            contentType: 'application/json',
            data: JSON.stringify({ quantity: newQty }),
            success: function(data){
                if(data.success){
                    qtyInput.val(newQty);
                    updateCartCount();
                }
            }
        });
    });

    // Update cart badge
    function updateCartCount(){
        $.getJSON('/cart/count', function(data){
            $('#cartCount').text(data.count);
        });
    }

    // Initial load
    loadProfile();
    loadProducts();
    updateCartCount();

});
</script>
<script>
$(document).ready(function() {
    var token = localStorage.getItem('customerToken');
    if (!token) {
        window.location.href = 'login';
    }

    // Recheck whenever user navigates back
    $(window).on('pageshow', function(event) {
        if (event.originalEvent.persisted || performance.getEntriesByType("navigation")[0].type === 'back_forward') {
            if (!localStorage.getItem('customerToken')) {
                window.location.href = 'login';
            }
        }
    });
});

$(document).on('click', '#logout-btn', function(e) {
    e.preventDefault();
    const token = localStorage.getItem('customerToken');

    $.ajax({
        url: '/api/customer/logout',
        type: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function() {
            localStorage.removeItem('customerToken');
            window.location.href = 'login';
        },
        error: function() {
            localStorage.removeItem('customerToken');
            window.location.href = 'login';
        }
    });
});

</script>

@endsection
