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

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        const res = await fetch('/api/customer/login', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({email, password})
        });

        const data = await res.json();

        if (res.ok) {
            localStorage.setItem('customerToken', data.token);
            document.getElementById('alertBox').innerHTML =
                '<div class="alert alert-success">Login successful!</div>';
            window.location.href = '/customer/dashboard';
        } else {
            document.getElementById('alertBox').innerHTML =
                `<div class="alert alert-danger">${data.error || 'Login failed'}</div>`;
        }
    } catch (err) {
        document.getElementById('alertBox').innerHTML =
            '<div class="alert alert-danger">Server error</div>';
    }
});
</script>
</body>
</html>
