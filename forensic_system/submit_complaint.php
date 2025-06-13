<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $custom_case_id = null;

    if (!empty($_POST['custom_case_id'])) {
        $input_case_id = trim($_POST['custom_case_id']);
        $check = $pdo->prepare("SELECT COUNT(*) FROM cases WHERE custom_case_id = ?");
        $check->execute([$input_case_id]);
        if ($check->fetchColumn() > 0) {
            $custom_case_id = $input_case_id;
        }
    }

    $message = $_POST['message'];
    $attachment_path = null;

    if (!empty($_FILES['attachment']['name'])) {
        $upload_dir = '../uploads/complaints/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['attachment']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $attachment_path = $target_file;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO complaints 
            (user_id, subject, custom_case_id, message, attachment, status, created_at) 
        VALUES 
            (?, ?, ?, ?, ?, 'Pending', NOW())
    ");
    $stmt->execute([$user_id, $subject, $custom_case_id, $message, $attachment_path]);

    header("Location: user_dashboard.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>

    <title>Submit Complaint | Forensic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .wrapper {
            min-height: 100vh;
        }
        .content {
            margin-left: 250px; /* Match sidebar width */
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
            <div class="card shadow-sm bg-white p-4">
                <h4 class="mb-4">Submit Complaint</h4>
                <form method="post" action="submit_complaint.php" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Complaint Details</label>
                        <textarea name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="attachment" class="form-label">Attachment (optional)</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="user_dashboard.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
