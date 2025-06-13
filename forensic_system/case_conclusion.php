<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT r.custom_case_id, c.title AS case_title, r.conclusion, r.report_date, u.name AS investigator_name
    FROM reports r
    JOIN cases c ON r.custom_case_id = c.custom_case_id
    JOIN users u ON r.investigator_id = u.id
    WHERE c.created_by = ?
    ORDER BY r.report_date DESC
");
$stmt->execute([$user_id]);

$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Case Conclusions</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('https://www.transparenttextures.com/patterns/white-diamond.png') repeat, #f4f4f4;
            color: #333;
        }
        .content {
            padding: 2rem;
            margin-left: 270px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        h3 {
            font-weight: 600;
            color: #014f86;
            margin-bottom: 1.5rem;
        }
        .report-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-left: 5px solid #2a6f97;
            border-radius: 10px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
        }
        .report-card h5 {
            margin-bottom: 0.5rem;
            color: #2a6f97;
        }
        .report-card p {
            margin-bottom: 0.3rem;
        }
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<main class="content">
    <h3>Investigation Conclusions</h3>

    <?php if (empty($reports)): ?>
        <div class="alert alert-info">No investigation conclusions available yet.</div>
    <?php else: ?>
        <?php foreach ($reports as $report): ?>
            <div class="report-card">
                <h5><?= htmlspecialchars($report['case_title']) ?> (<?= htmlspecialchars($report['custom_case_id']) ?>)</h5>
                <p><strong>Investigator:</strong> <?= htmlspecialchars($report['investigator_name']) ?></p>
                <p><strong>Report Date:</strong> <?= date('d M Y, h:i A', strtotime($report['report_date'])) ?></p>
                <p><strong>Conclusion:</strong><br><?= nl2br(htmlspecialchars($report['conclusion'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

</body>
</html>
