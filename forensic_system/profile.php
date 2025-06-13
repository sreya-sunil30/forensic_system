<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'investigator') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['edit_name'])) {
        $new_name = trim($_POST['new_name']);
        if (!empty($new_name)) {
            $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->execute([$new_name, $user_id]);
            $success = "Name updated successfully.";
            $user['name'] = $new_name;
        } else {
            $error = "Name cannot be empty.";
        }
    }

    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $stored_hash = $stmt->fetchColumn();

        if (!password_verify($current_password, $stored_hash)) {
            $error = "Current password is incorrect.";
        } elseif (empty($new_password) || $new_password !== $confirm_password) {
            $error = "New passwords do not match or are empty.";
        } else {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$new_hash, $user_id]);
            $success = "Password changed successfully.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Investigator Profile | Forensic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: url('https://www.transparenttextures.com/patterns/white-diamond.png') repeat, #f4f4f4;
        color: #343a40;
    }

    .content {
        padding: 2rem;
        margin-left: 270px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
        overflow-x: auto;
    }

    h2 {
        font-weight: 600;
        color: #212529;
    }

    .alert-info {
        background-color: #e0f7fa;
        border-left: 5px solid #00acc1;
        color: #006064;
    }

    @media (max-width: 768px) {
        .content {
            margin-left: 0;
            padding: 1rem;
        }
    }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <main class="content flex-grow-1">
        <?php include 'includes/header.php'; ?>

        <div class="navbar">Investigator Profile</div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 p-4">
            <h5 class="mb-3">Profile Details</h5>
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

            <div class="mt-3">
                <button class="btn btn-xs btn-secondary me-2" style="background-color: #2a6f97; color: #fff; " data-bs-toggle="modal" data-bs-target="#editNameModal">
                    <i class="bi bi-pencil-square"></i> Edit Name
                </button>
                <button class="btn btn-xs btn-light border" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="bi bi-key-fill"></i> Change Password
                </button>
            </div>
        </div>
    </main>
</div>

<!-- Edit Name Modal -->
<div class="modal fade" id="editNameModal" tabindex="-1" aria-labelledby="editNameModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editNameModalLabel">Edit Name</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="new_name" class="form-label">New Name</label>
          <input type="text" class="form-control" id="new_name" name="new_name" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="edit_name" style="background-color: #2a6f97; color: #fff; " class="btn btn-xs btn-secondary">Update Name</button>
      </div>
    </form>
  </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="current_password" class="form-label">Current Password</label>
          <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="mb-3">
          <label for="new_password" class="form-label">New Password</label>
          <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="change_password" style="background-color: #2a6f97; color: #fff; " class="btn btn-xs btn-light border">Change Password</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
