<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Login (JWT)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Customer Login (JWT)</h3>

                    <div id="alertBox"></div>

                    <form id="loginForm">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        Don’t have an account? <a href="/customer/register">Register</a>

                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$('#loginForm').on('submit', function(e) {
    e.preventDefault();

    let email = $('#email').val();
    let password = $('#password').val();

    $.ajax({
        url: '/api/customer/login',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ email: email, password: password }),
        success: function(response) {
            if (response.success) {
                // Save JWT token in localStorage
                localStorage.setItem('customerToken', response.token);

                // Send customer + token to Laravel session
                $.ajax({
                    url: '/session/sync',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        token: response.token,
                        id: response.customer.id,
                        name: response.customer.name,
                        email: response.customer.email,
                        latitude: response.customer.latitude,
                        longitude: response.customer.longitude
                    },
                    success: function(res) {
                        window.location.href = '/customer/dashboard';
                    },
                    error: function() {
                        alert('Session sync failed.');
                    }
                });
            } else {
                $('#alertBox').html('<div class="alert alert-danger">' + (response.error || 'Login failed') + '</div>');
            }
        },
        error: function() {
            $('#alertBox').html('<div class="alert alert-danger">Server error</div>');
        }
    });
});
</script>

</body>
</html>
