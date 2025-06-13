<?php
if (!isset($_SESSION)) session_start();


$role = $_SESSION['role'] ?? '';
$name = $_SESSION['name'] ?? '';

if ($role === 'admin') {
    $welcomeText = "Welcome Admin";
} elseif ($role === 'investigator') {
    $welcomeText = "Welcome Investigator, " . htmlspecialchars($name);
} elseif ($role === 'user') {
    $welcomeText = "Welcome User, " . htmlspecialchars($name);
} else {
    $welcomeText = "Welcome Guest";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Forensic Portal</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    body {
      margin: 0;
      padding-top: 60px; /* space for fixed header */
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f5f5;
      color: #333;
    }

    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 60px;
      background-color: #014f86;
      box-shadow: 0 2px #2a6f97(56, 6, 11);
      color: #ccc;
      font-weight: 600;
      letter-spacing: 0.05em;
      user-select: none;
      z-index: 1030;

      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 1.5rem;
    }

    .navbar .brand {
      display: flex;
      align-items: center;
      font-size: 1.25rem;
      color: #ccc;
    }

    .navbar .brand i {
      font-size: 1.6rem;
      color: #0b525b
      margin-right: 0.5rem;
    }

    .navbar .user-info {
      display: flex;
      align-items: center;
      color: #ccc;
      font-weight: 500;
      font-size: 0.95rem;
    }

    .navbar .user-info #username {
      margin-left: 0.5rem;
      white-space: nowrap;
    }

    .navbar .user-info img {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      border: 2px solid #3e1f47;
      object-fit: cover;
      margin-left: 0.7rem;
    }
  </style>
</head>
<body>

  <header class="navbar">
    <div class="brand">
      <i class="bi bi-shield-lock-fill"></i>
      Digital Forensics System
    </div>

    <div class="user-info">
      <?= $welcomeText ?>
    </div>
  </header>

</body>
</html>
