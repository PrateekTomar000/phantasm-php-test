<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Dashboard</h3>
                    <p>Welcome, {{ $user->name }}!</p>
                    <p>Email: {{ $user->email }}</p>

                    <!-- Add Product Link -->
                    <a href="{{ url('/admin/products/create') }}" class="btn btn-primary mb-2">Add Product</a>

                    <!-- Logout -->
                    <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
