<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Register (JWT)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Customer Register (JWT)</h3>

                    <div id="alertBox"></div>

                    <form id="registerForm">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" id="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" id="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" id="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" id="password_confirmation" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Register</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        Already have an account? <a href="{{ url('/customer/login') }}">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const payload = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value
    };

    try {
        const res = await fetch('/api/customer/register', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (res.ok) {
            localStorage.setItem('customerToken', data.token);
            document.getElementById('alertBox').innerHTML =
                '<div class="alert alert-success">Registered successfully!</div>';
            window.location.href = '/customer/dashboard';
        } else {
            document.getElementById('alertBox').innerHTML =
                `<div class="alert alert-danger">${JSON.stringify(data)}</div>`;
        }
    } catch (err) {
        document.getElementById('alertBox').innerHTML =
            '<div class="alert alert-danger">Server error</div>';
    }
});
</script>
</body>
</html>
