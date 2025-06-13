<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: /forensic_system/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Complaints without a conclusion
$stmtActive = $pdo->prepare("
    SELECT com.*
    FROM complaints com
    LEFT JOIN cases c ON com.custom_case_id = c.custom_case_id
    LEFT JOIN reports r ON c.custom_case_id = r.custom_case_id
    WHERE com.user_id = ? AND r.conclusion IS NULL
    ORDER BY com.created_at DESC
");
$stmtActive->execute([$user_id]);
$activeComplaints = $stmtActive->fetchAll(PDO::FETCH_ASSOC);

// Complaints with a conclusion (Closed)
$stmtClosed = $pdo->prepare("
    SELECT com.*, c.title AS case_title, r.conclusion, r.report_date, u.name AS investigator_name
    FROM complaints com
    JOIN cases c ON com.custom_case_id = c.custom_case_id
    JOIN reports r ON c.custom_case_id = r.custom_case_id
    JOIN users u ON r.investigator_id = u.id
    WHERE com.user_id = ? AND r.conclusion IS NOT NULL
    ORDER BY r.report_date DESC
");
$stmtClosed->execute([$user_id]);
$closedCases = $stmtClosed->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Dashboard | Forensic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <?php require_once "includes/header.php"; ?>
    <?php require_once "includes/sidebar.php"; ?>

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

.table-responsive {
    width: 100%;
    overflow-x: auto;  
}

table {
    width: 100% !important;
    min-width: 900px; 
    table-layout: auto;
}

table.table {
    border-collapse: separate !important;
    border-spacing: 0 12px !important;
    width: 100%;
    font-size: 0.95rem;
}

table.table thead tr {
    background: linear-gradient(90deg, #4e9af1, #56ccf2);
    color: #fff;
    font-weight: 600;
    box-shadow: 0 3px 6px rgb(0 0 0 / 0.15);
}

table.table thead tr th {
    border: none;
    padding: 14px 20px;
    text-align: center;
}

table.table tbody tr {
    background: #fefefe;
    box-shadow: 0 3px 10px rgb(0 0 0 / 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

table.table tbody tr:hover {
    background: #e7f1ff;
    box-shadow: 0 8px 24px rgb(78 154 241 / 0.25);
    transform: translateY(-3px);
}

table.table tbody td {
    padding: 14px 20px;
    text-align: center;
    vertical-align: middle;
    border-top: none;
    border-bottom: none;
    color: #333;
}

@media (max-width: 768px) {
    .content {
        margin-left: 0;
        padding: 1rem;
        padding-left: 270px;
    }
}
</style>

</head>
<body>

<div class="content">
    <div class="d-flex justify-content-between mb-3">
        <h2>Your Complaints</h2>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white shadow-sm">
            <thead class="table-light">
                <tr>
                    <th>S No</th>
                    <th>Subject</th>
                    <th>Case ID</th>
                    <th>Status</th>
                    <th>Submitted At</th>
                    <th>Attachment</th>
                </tr>
            </thead>
            <tbody>
                <?php $serial = 1; foreach ($activeComplaints as $comp): ?>
                    <tr>
                        <td><?= $serial++ ?></td>
                        <td><?= htmlspecialchars($comp['subject']) ?></td>
                        <td><?= $comp['custom_case_id'] ?? '-' ?></td>
                        <td>
                            <span class="badge bg-<?= $comp['custom_case_id'] ? 'success' : 'secondary' ?>">
                                <?= $comp['custom_case_id'] ? 'Accepted' : 'Pending' ?>
                            </span>
                        </td>
                        <td><?= $comp['created_at'] ?></td>
                        <td>
                            <?php if (!empty($comp['attachment'])): ?>
                                <a href="<?= htmlspecialchars($comp['attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-arrow-down"></i> View
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No file</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($closedCases)): ?>
    <div class="mt-5">
        <h2>Closed Cases</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle bg-white shadow-sm">
                <thead class="table-light">
                    <tr>
                        <th>S No</th>
                        <th>Case Title</th>
                        <th>Case ID</th>
                        <th>Investigator</th>
                        <th>Report Date</th>
                        <th>Conclusion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $n = 1; foreach ($closedCases as $case): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= htmlspecialchars($case['case_title']) ?></td>
                            <td><?= htmlspecialchars($case['custom_case_id']) ?></td>
                            <td><?= htmlspecialchars($case['investigator_name']) ?></td>
                            <td><?= date('d M Y, h:i A', strtotime($case['report_date'])) ?></td>
                            <td><?= nl2br(htmlspecialchars($case['conclusion'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
