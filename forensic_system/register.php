<?php
require_once "config.php";
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$name, $email, $hashed]);

            // Auto-login the user
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['role'] = 'user';

            header("Location: user_dashboard.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Email already registered.";
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register | Forensic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
        background-color: #a9d6e5; /* same as login */
        font-family: 'Segoe UI', sans-serif;
    }

    .register-box {
        background-color: #61a5c2; /* same as login card */
        border-radius: 12px;
        padding: 2.5rem;
        box-shadow: 0 0 15px rgba(0,0,0,0.12);
        max-width: 500px;
        margin: 3rem auto;
    }

    .form-control {
        background-color: #fff;
        border: 1.5px solid #012a4a;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .form-control:focus {
        border-color: #012a4a;
        box-shadow: 0 0 0 0.2rem rgba(1, 42, 74, 0.25);
        background-color: #fff;
    }

    .btn-register {
        background-color: #012a4a;
        color: #f8f9fa;
        font-weight: 600;
        transition: background-color 0.3s;
    }
    .btn-register:hover {
        background-color: #013a63;
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
<body>

    <div class="register-box">
        <h2 class="text-center mb-4" style="color: #013a63;">
            <i class="bi bi-person-plus-fill me-2"></i> Register Account
        </h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?= implode("<br>", array_map('htmlspecialchars', $errors)) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-register w-100">Register</button>
        </form>

        <div class="text-center mt-3">
            <a href="index.php" class="link">Back to Login</a>
        </div>
    </div>

</body>
</html>
