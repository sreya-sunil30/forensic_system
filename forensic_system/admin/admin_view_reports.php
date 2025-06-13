<?php
require_once "../config.php";
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
header("Location: /forensic_system/index.php"); 
    exit;
}

// Filters
$caseFilter = $_GET['case'] ?? '';
$investigatorFilter = $_GET['investigator'] ?? '';

// Query
$query = "SELECT r.*, u.name AS investigator_name, c.title AS case_title 
          FROM investigator_daily_reports r
          JOIN users u ON r.investigator_id = u.id
          JOIN cases c ON r.custom_case_id = c.custom_case_id
          WHERE 1=1";

$params = [];
if ($caseFilter) {
    $query .= " AND r.custom_case_id = ?";
    $params[] = $caseFilter;
}
if ($investigatorFilter) {
    $query .= " AND r.investigator_id = ?";
    $params[] = $investigatorFilter;
}
$query .= " ORDER BY r.report_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cases = $pdo->query("SELECT custom_case_id, title FROM cases ORDER BY custom_case_id")->fetchAll(PDO::FETCH_ASSOC);
$investigators = $pdo->query("SELECT id, name FROM users WHERE role = 'investigator'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Investigator Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>

<div class="container-fluid" style="margin-left:270px; padding-top: 20px;">
    <div class="p-3 bg-light rounded shadow-sm" style="min-height: 100vh; overflow-x: auto;">
    <h2 class="mb-4">Investigator Daily Reports</h2>

    <form class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Filter by Case</label>
            <select name="case" class="form-select">
                <option value="">All</option>
                <?php foreach ($cases as $case): ?>
                    <option value="<?= $case['custom_case_id'] ?>" <?= $case['custom_case_id'] === $caseFilter ? 'selected' : '' ?>>
                        <?= $case['custom_case_id'] ?> - <?= htmlspecialchars($case['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Filter by Investigator</label>
            <select name="investigator" class="form-select">
                <option value="">All</option>
                <?php foreach ($investigators as $inv): ?>
                    <option value="<?= $inv['id'] ?>" <?= $inv['id'] == $investigatorFilter ? 'selected' : '' ?>>
                        <?= htmlspecialchars($inv['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100" style="background-color: #013a63; color: #fff">Apply Filters</button>
        </div>
    </form>

    <?php if ($reports): ?>
        <div class="table-responsive">
            <table class="table table-bordered bg-white shadow-sm">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Investigator</th>
                        <th>Case</th>
                        <th>Summary</th>
                        <th>Suspects</th>
                        <th>Evidence</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $r): ?>
                        <tr>
                            <td><?= $r['report_date'] ?></td>
                            <td><?= htmlspecialchars($r['investigator_name']) ?></td>
                            <td><?= htmlspecialchars($r['custom_case_id']) ?> - <?= htmlspecialchars($r['case_title']) ?></td>
                            <td><?= nl2br(htmlspecialchars($r['activity_summary'])) ?></td>
                            <td>
                                <?php 
                                    $suspects = json_decode($r['suspects'], true);
                                    if ($suspects) {
                                        foreach ($suspects as $s) {
                                            echo "<strong>Name:</strong> " . htmlspecialchars($s['name']) . "<br>";
                                            echo "<strong>Age:</strong> " . htmlspecialchars($s['age']) . "<br>";
                                            echo "<strong>Remarks:</strong> " . htmlspecialchars($s['remarks']) . "<hr>";
                                        }
                                    } else {
                                        echo "<em>No suspects</em>";
                                    }
                                ?>
                            </td>
                            <td>
                                <?php 
                                    $files = json_decode($r['evidence_files'], true);
                                    if ($files) {
                                        foreach ($files as $file) {
                                            echo "<a href='../$file' target='_blank' style='color: #013a63;'>View</a><br>";
                                        }
                                    } else {
                                        echo "<em>No files</em>";
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">No reports found.</p>
    <?php endif; ?>
</div>
</body>
</html>
