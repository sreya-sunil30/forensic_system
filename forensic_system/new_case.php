<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /forensic_system/index.php"); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Case</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="dashboard.php">Forensic Dashboard</a>
    <div class="ms-auto">
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>
<div class="container mt-5">
    <h2 class="mb-4">Create New Case</h2>
    <div class="card shadow p-4">
        <form method="POST" action="save_case.php">
            <div class="mb-3">
                <label for="case_title" class="form-label">Case Title</label>
                <input type="text" class="form-control" id="case_title" name="case_title" required>
            </div>
            <div class="mb-3">
                <label for="case_description" class="form-label">Description</label>
                <textarea class="form-control" id="case_description" name="case_description" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Case</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>
