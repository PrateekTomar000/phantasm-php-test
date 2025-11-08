@extends('layouts.app')

@section('title', 'Create Product')

@section('content')
<div class="container mt-5">
    <div class="card shadow border-0 rounded-4">
        <div class="card-header"><h4>Create Product</h4></div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form id="productForm" method="POST" action="{{ route('products.store') }}">
                @csrf
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>SKU</label>
                    <input type="text" name="sku" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Brand</label>
                    <select name="brand_id" id="brandSelect" class="form-control" required></select>
                </div>

                <div class="mb-3">
                    <label>Category</label>
                    <select name="category_id" id="categorySelect" class="form-control" required></select>
                </div>

                <button type="submit" class="btn btn-primary">Create Product</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch brands dynamically
    fetch('/brands')
        .then(res => res.json())
        .then(data => {
            const brandSelect = document.getElementById('brandSelect');
            data.forEach(brand => {
                const option = document.createElement('option');
                option.value = brand.id;
                option.text = brand.name;
                brandSelect.add(option);
            });
        });

    // Fetch categories dynamically
    fetch('/categories')
        .then(res => res.json())
        .then(data => {
            const categorySelect = document.getElementById('categorySelect');
            data.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.text = cat.name;
                categorySelect.add(option);
            });
        });
});
</script>
@endsection
