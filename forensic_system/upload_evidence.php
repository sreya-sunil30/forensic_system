<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Fetch user info
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$userProfile = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch case list
$stmt = $pdo->prepare("SELECT custom_case_id, title FROM cases ORDER BY custom_case_id ASC");
$stmt->execute();
$cases = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Upload logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_evidence'])) {
    $custom_case_id = $_POST['custom_case_id'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if (!empty($_FILES['evidence_file']['name'])) {
        $upload_dir = 'uploads/evidence/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $original_name = basename($_FILES['evidence_file']['name']);
        $file_name = time() . '_' . $original_name;
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['evidence_file']['tmp_name'], $file_path)) {
            $sha256 = hash_file('sha256', $file_path);

            $stmt = $pdo->prepare("INSERT INTO evidence (file_name, file_path, uploaded_by, uploaded_at, sha256_hash, custom_case_id, description)
                                   VALUES (?, ?, ?, NOW(), ?, ?, ?)");
            $stmt->execute([$file_name, $file_path, $user_id, $sha256, $custom_case_id, $description]);

            $success = "Evidence uploaded successfully.";
        } else {
            $error = "Failed to upload file.";
        }
    } else {
        $error = "Please select a file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Upload Evidence | Forensic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { height: 100vh; background-color: #e9ecef; padding-top: 1.5rem; min-width: 250px; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
        .sidebar a { color: #495057; font-weight: 600; padding: 0.75rem 1.25rem; display: flex; align-items: center; text-decoration: none; border-left: 4px solid transparent; transition: background-color 0.3s, border-color 0.3s; margin-bottom: 0.25rem; border-radius: 0.25rem 0 0 0.25rem; }
        .sidebar a:hover, .sidebar a.active { background-color: #dee2e6; border-left-color: #6c757d; color: #343a40; }
        .sidebar a i { margin-right: 0.75rem; font-size: 1.2rem; }
        .content { padding: 2rem; width: 100%; }
        .navbar { background-color: #e9ecef; padding: 1rem 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem; border-radius: 0.5rem; font-weight: 600; color: #495057; }
    </style>
</head>
<body>
<div class="d-flex">
    <nav class="sidebar d-flex flex-column">
        <a href="user_dashboard.php"><i class="bi bi-house-door-fill"></i> Dashboard</a>
        <a href="profile_info.php"><i class="bi bi-person-circle"></i> Profile</a>
        <a href="upload_evidence.php" class="active"><i class="bi bi-upload me-2"></i>Upload Evidence</a>
        <a href="logout.php" class="mt-auto"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>

    <main class="content">
        <div class="navbar">Upload Evidence</div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="card shadow-sm bg-white p-4">
            <h4 class="mb-4">Upload New Evidence</h4>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="custom_case_id" class="form-label">Case ID</label>
                    <select name="custom_case_id" id="custom_case_id" class="form-select" required>
                        <option value="">-- Select Case --</option>
                        <?php foreach ($cases as $case): ?>
                            <option value="<?= htmlspecialchars($case['custom_case_id']) ?>">
                                <?= htmlspecialchars($case['custom_case_id'] . ' - ' . $case['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description (optional)</label>
                    <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="evidence_file" class="form-label">Select Evidence File</label>
                    <input type="file" name="evidence_file" id="evidence_file" class="form-control" required>
                </div>

                <button type="submit" name="upload_evidence" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Upload
                </button>
            </form>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
