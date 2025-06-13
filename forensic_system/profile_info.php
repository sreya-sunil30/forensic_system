<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Update name
if (isset($_POST['update_name'])) {
    $new_name = trim($_POST['new_name']);
    if (!empty($new_name)) {
        $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->execute([$new_name, $user_id]);
        $_SESSION['name'] = $new_name;
        $message = "Name updated successfully.";
    }
}

// Change password
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $hashed = $stmt->fetchColumn();

    if (password_verify($current, $hashed)) {
        if ($new === $confirm && strlen($new) >= 6) {
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([password_hash($new, PASSWORD_DEFAULT), $user_id]);
            $message = "Password changed successfully.";
        } else {
            $message = "New passwords do not match or are too short.";
        }
    } else {
        $message = "Current password is incorrect.";
    }
}

// Fetch user details
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Profile | Forensic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .wrapper {
            min-height: 100vh;
        }
        .content {
            margin-left: 250px; /* Adjust to match sidebar */
            padding: 20px;
        }
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="wrapper d-flex">
    <main class="content flex-grow-1">
        <div class="container py-4">
            <?php if ($message): ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm bg-white p-4">
                <h4 class="mb-4">My Profile</h4>
                <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

                <div class="mt-4">
                    <button class="btn btn-primary me-2" style="background-color: #2a6f97; color: #fff;" data-bs-toggle="modal" data-bs-target="#editNameModal">
                        <i class="bi bi-pencil-square"></i> Edit Name
                    </button>
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="bi bi-key"></i> Change Password
                    </button>
                </div>
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
        <input type="hidden" name="update_name" value="1">
        <div class="mb-3">
          <label for="new_name" class="form-label">New Name</label>
          <input type="text" class="form-control" name="new_name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" style="background-color: #2a6f97; color: #fff;" class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
        <input type="hidden" name="change_password" value="1">
        <div class="mb-3">
          <label class="form-label">Current Password</label>
          <input type="password" class="form-control" name="current_password" required>
        </div>
        <div class="mb-3">
          <label class="form-label">New Password</label>
          <input type="password" class="form-control" name="new_password" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm New Password</label>
          <input type="password" class="form-control" name="confirm_password" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" style="background-color: #2a6f97; color: #fff;" class="btn btn-primary">Change Password</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
