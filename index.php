<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000;
            color: #fff;
        }
        .register-title {
            color: #00ff00;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }
        .form-container {
            background-color: #444;
            border-radius: 15px;
            padding: 2rem;
        }
        .btn-register {
            background-color: #00ff00;
            border: none;
            color: #000;
        }
        .btn-register:hover {
            background-color: #00cc00;
        }
        .btn-login {
            background-color: transparent;
            border: 1px solid #00ff00;
            color: #00ff00;
        }
        .btn-login:hover {
            background-color: #00ff00;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <h2 class="text-center register-title">REGISTER</h2>
                <div class="form-container">
                    <form action="/Photogram/_include/Register/db_connect.php" method="post">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username.." required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email Address.." required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-register">Register</button>
                            <a href="_pages/Login.php" class="btn btn-login">Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>