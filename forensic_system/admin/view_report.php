<?php
require_once "../config.php";
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

if (!isset($_GET['report_id']) || empty($_GET['report_id'])) {
    echo "Invalid report ID.";
    exit;
}

$report_id = $_GET['report_id'];

$stmt = $pdo->prepare("
    SELECT r.*, u.name AS investigator_name, c.title AS case_title
    FROM reports r
    JOIN users u ON r.investigator_id = u.id
    JOIN cases c ON r.custom_case_id = c.custom_case_id
    WHERE r.id = ?
");
$stmt->execute([$report_id]);
$report = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$report) {
    echo "Report not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Report</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #ffffff;
            padding: 2rem;
        }
        .report-container {
            background: #fdfdfd;
            padding: 2rem;
            border: 1px solid #ccc;
            border-radius: 10px;
            max-width: 900px;
            margin: auto;
        }
        .header {
            border-bottom: 2px solid #2a6f97;
            margin-bottom: 1.5rem;
        }
        .header h2 {
            font-weight: bold;
            color: #2a6f97;
        }
        .section {
            margin-bottom: 1.5rem;
        }
        .section h5 {
            color: #343a40;
            font-weight: 600;
        }
        .btn-print {
            margin-top: 20px;
        }
        .file-link a {
            text-decoration: none;
        }
        @media print {
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="report-container">
    <div class="header text-center">
        <h2>Investigation Report</h2>
        <p><strong>Date:</strong> <?= date('d M Y', strtotime($report['report_date'])) ?></p>
    </div>

    <div class="section">
        <h5>Case ID:</h5>
        <p><?= htmlspecialchars($report['custom_case_id']) ?></p>
    </div>

    <div class="section">
        <h5>Case Title:</h5>
        <p><?= htmlspecialchars($report['case_title']) ?></p>
    </div>

    <div class="section">
        <h5>Investigator:</h5>
        <p><?= htmlspecialchars($report['investigator_name']) ?></p>
    </div>

    <div class="section">
        <h5>Description:</h5>
        <p><?= nl2br(htmlspecialchars($report['description'])) ?></p>
    </div>

    <div class="section">
        <h5>Conclusion:</h5>
        <p><?= nl2br(htmlspecialchars($report['conclusion'] ?? 'No conclusion provided.')) ?></p>
    </div>

    <?php if (!empty($report['file_path']) && file_exists("../" . $report['file_path'])): ?>
        <div class="section file-link">
            <h5>Attached File:</h5>
            <a href="../<?= htmlspecialchars($report['file_path']) ?>" class="btn btn-sm btn-success" target="_blank">
                <i class="bi bi-download"></i> Download File
            </a>
        </div>
    <?php endif; ?>

    <div class="text-center btn-print">
        <button class="btn btn-primary" style="background-color: #2a6f97; color: #fff;" onclick="window.print()">
            <i class="bi bi-printer"></i> Print Report
        </button>
    </div>
</div>

</body>
</html>
