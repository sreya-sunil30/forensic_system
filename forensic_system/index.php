<?php
require_once "config.php";
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            $redirect = match ($user['role']) {
                'admin'        => 'admin/admin_dashboard.php',
                'investigator' => 'investigator_dashboard.php',
                default        => 'user_dashboard.php'
            };

            header("Location: $redirect");
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login | Forensic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #a9d6e5; /* light grey background */
            font-family: 'Segoe UI', sans-serif;
        }

        .login-box {
            background-color: #61a5c2; /* very light grey / almost white */
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.12);
            max-width: 420px;
        }

        .form-control {
            background-color: #fff; /* white input background */
            border: 1.5px solid #012a4a; /* subtle grey border */
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-control:focus {
            border-color: #012a4a; /* medium grey focus */
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25);
            background-color: #fff;
        }

        .btn-login {
            background-color: #012a4a; /* medium grey button */
            color: #f8f9fa; /* off-white text */
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .btn-login:hover {
            background-color: #012a4a; /* darker grey on hover */
        }

        .link {
            color: #012a4a;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        .link:hover {
            color: #013a63;
            text-decoration: underline;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">

    <div class="login-box">
        <h2 class="text-center mb-4" style="color: #013a63;">
    <i class="bi bi-shield-lock"></i> Forensic Login
</h2>


        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?= implode("<br>", array_map('htmlspecialchars', $errors)) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required placeholder="Enter email" autofocus>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Enter password">
            </div>
            <button type="submit" class="btn btn-login w-100">Login</button>
        </form>

        <div class="text-center mt-3">
            <a href="register.php" class="link">Don't have an account? Register</a>
        </div>
    </div>

</body>
</html>
