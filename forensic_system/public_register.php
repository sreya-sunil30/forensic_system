<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'investigator') {
    header("Location: /forensic_system/index.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Investigator Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow mx-auto" style="max-width: 650px;">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="bi bi-search me-2"></i>Investigator Dashboard</h4>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($_SESSION['role']) ?></p>

            <div class="list-group">
                <a href="view_case.php" class="list-group-item list-group-item-action"><i class="bi bi-journal-text me-2"></i>Assigned Cases</a>
                <a href="investigate.php" class="list-group-item list-group-item-action"><i class="bi bi-clipboard-data me-2"></i>Investigate Evidence</a>
                <a href="report.php" class="list-group-item list-group-item-action"><i class="bi bi-file-earmark-text me-2"></i>Submit Report</a>
                <a href="logout.php" class="list-group-item list-group-item-action text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
