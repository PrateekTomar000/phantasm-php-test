@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="container mt-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Customer Dashboard</h2>

        <div>
            <!-- Cart Button -->
            <button class="btn btn-primary position-relative" data-bs-toggle="modal" data-bs-target="#cartModal" onclick="loadCart()">
                Cart
                <span id="cartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    0
                </span>
            </button>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>

    <!-- Welcome -->
    <div id="welcomeBox" class="alert alert-primary">Loading customer info...</div>

    <!-- PRODUCTS SECTION -->
    <h4 class="mt-5 mb-3">Products</h4>
    <div id="productContainer" class="row g-4"></div>

</div>

<!-- CART MODAL -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cartModalLabel">My Cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="cartContainer">
        Loading cart...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <a href="{{ route('cart.index') }}" class="btn btn-primary">Go to Cart Page</a>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function() {

    // Load profile
    async function loadProfile() {
        const res = await fetch("/api/customer/profile", {
            headers: { "Authorization": "Bearer " + localStorage.getItem("customerToken") }
        });
        const data = await res.json();
        document.getElementById("welcomeBox").innerHTML = `Welcome <strong>${data.name}</strong> (${data.email})`;
    }

    // Load products
    async function loadProducts() {
        const res = await fetch("/customer/products/data");
        const products = await res.json();

        let html = '';
        products.forEach(p => {
            html += `
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        ${p.image ? `<img src="/storage/${p.image}" class="card-img-top">` : ""}
                        <div class="card-body">
                            <h5 class="card-title">${p.name}</h5>
                            <p class="text-muted">${p.sku}</p>
                            <p>${p.description ?? ""}</p>
                            <p class="fw-bold">₹${p.price}</p>
                            <input type="number" min="1" value="1" id="qty-${p.id}" class="form-control mb-2">
                            <button class="btn btn-primary w-100" onclick="addToCart(${p.id})">Add to Cart</button>
                        </div>
                    </div>
                </div>
            `;
        });

        document.getElementById("productContainer").innerHTML = html;
    }

    // Add product to cart
    window.addToCart = async function(productId) {
        const quantity = document.getElementById(`qty-${productId}`).value;

        const res = await fetch(`/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ quantity: quantity })
        });

        const data = await res.json();
        if (data.success) {
            alert("Added to cart!");
            loadCartCount();
        }
    }

    // Load cart content into modal
    window.loadCart = async function() {
        const res = await fetch("/cart");
        const html = await res.text(); // Blade partial
        document.getElementById("cartContainer").innerHTML = html;
        loadCartCount();
    }

    // Load cart count
    async function loadCartCount() {
        const res = await fetch("/cart");
        const html = await res.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const rows = doc.querySelectorAll('tbody tr').length;
        document.getElementById('cartCount').innerText = rows;
    }

    loadProfile();
    loadProducts();
    loadCartCount();

});
</script>
@endsection
