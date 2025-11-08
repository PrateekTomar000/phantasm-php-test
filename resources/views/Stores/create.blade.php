@extends('layouts.app')

@section('title', 'Create Store')

@section('content')
<div class="card mb-4">
    <div class="card-header"><h4>Create Store</h4></div>
    <div class="card-body">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('stores.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Code</label>
                <input type="text" name="code" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Contact</label>
                <input type="text" name="contact" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Address</label>
                <input type="text" name="address" class="form-control">
            </div>

            <div class="mb-3">
                <label>Latitude</label>
                <input type="text" name="latitude" class="form-control">
            </div>

            <div class="mb-3">
                <label>Longitude</label>
                <input type="text" name="longitude" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Create Store</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h4>All Stores</h4></div>
    <div class="card-body">
        <input type="text" id="searchId" placeholder="Search by ID" class="form-control mb-3">
        <table id="storesTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#storesTable').DataTable({
        ajax: '/admin/stores/data',
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'code' },
            { data: 'contact' },
            { data: 'address' },
            { data: 'latitude' },
            { data: 'longitude' }
        ]
    });

    // Search by ID
    $('#searchId').on('keyup', function() {
        var id = $(this).val();
        table.ajax.url('/admin/stores/search?id=' + id).load();
    });
});
</script>
@endsection
