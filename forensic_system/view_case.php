<?php
session_start();
require_once "config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$cases = $pdo->query("SELECT * FROM cases ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Cases</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-sm p-4">
        <h3 class="mb-4">All Cases</h3>

        <table class="table table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th>case ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cases as $case): ?>
                    <tr>
                        <td><?= htmlspecialchars($case['custom_case_id']) ?></td>
                        <td><?= htmlspecialchars($case['case_title']) ?></td>
                        <td><?= htmlspecialchars($case['description']) ?></td>
                        <td><?= htmlspecialchars($case['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
