<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'investigator') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch assigned cases for filter dropdown
$stmt = $pdo->prepare("SELECT custom_case_id, title FROM cases WHERE assigned_to = ?");
$stmt->execute([$user_id]);
$cases = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize filters with default values
$filter_case_id = $_GET['case_id'] ?? '';
$filter_start_date = $_GET['start_date'] ?? '';
$filter_end_date = $_GET['end_date'] ?? '';
$filter_keyword = trim($_GET['keyword'] ?? '');

// Build WHERE conditions for filtering
$whereClauses = ["r.investigator_id = ?"];
$params = [$user_id];

if ($filter_case_id) {
    $whereClauses[] = "r.custom_case_id = ?";
    $params[] = $filter_case_id;
}
if ($filter_start_date) {
    $whereClauses[] = "r.report_date >= ?";
    $params[] = $filter_start_date . " 00:00:00";
}
if ($filter_end_date) {
    $whereClauses[] = "r.report_date <= ?";
    $params[] = $filter_end_date . " 23:59:59";
}
if ($filter_keyword) {
    $whereClauses[] = "r.description LIKE ?";
    $params[] = "%" . $filter_keyword . "%";
}

$whereSQL = implode(" AND ", $whereClauses);

// Fetch filtered reports
$sql = "
    SELECT r.id, r.custom_case_id, r.description, r.report_date, r.file_name, r.file_path,
           c.title AS case_title
    FROM reports r
    JOIN cases c ON r.custom_case_id = c.custom_case_id
    WHERE $whereSQL
    ORDER BY r.report_date DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reports History | Forensic Portal</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('https://www.transparenttextures.com/patterns/white-diamond.png') repeat, #f4f4f4;
            color: #343a40;
        }
        .content {
            padding: 2rem;
            margin-left: 270px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }
        h2 {
            font-weight: 600;
            color: #212529;
            margin-bottom: 1.5rem;
        }
        form.bg-white {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgb(0 0 0 / 0.05);
            margin-bottom: 2rem;
        }
        .btn-download {
            background-color: #198754;
            color: #fff;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.9rem;
            text-decoration: none;
        }
        .btn-download:hover {
            background-color: #157347;
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table {
            width: 100% !important;
            table-layout: auto;
            min-width: 700px;
            border-collapse: separate !important;
            border-spacing: 0 12px !important;
            font-size: 0.95rem;
        }
        table thead tr {
            background: linear-gradient(90deg, #4e9af1, #56ccf2);
            color: #fff;
            font-weight: 600;
        }
        table thead th {
            border: none;
            padding: 14px 20px;
            text-align: center;
        }
        table tbody tr {
            background: #fefefe;
            box-shadow: 0 3px 10px rgb(0 0 0 / 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        table tbody tr:hover {
            background: #e7f1ff;
            box-shadow: 0 8px 24px rgb(78 154 241 / 0.25);
            transform: translateY(-3px);
        }
        table tbody td {
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
            }
            table {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<main class="content">
    <h2>Reports History</h2>

    <form method="GET" class="bg-white row g-3 align-items-end">
        <div class="col-md-3">
            <label for="case_id" class="form-label">Filter by Case</label>
            <select name="case_id" id="case_id" class="form-select">
                <option value="">-- All Cases --</option>
                <?php foreach ($cases as $case): ?>
                    <option value="<?= htmlspecialchars($case['custom_case_id']) ?>"
                        <?= ($filter_case_id === $case['custom_case_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($case['custom_case_id'] . " — " . $case['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($filter_start_date) ?>" />
        </div>

        <div class="col-md-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($filter_end_date) ?>" />
        </div>

        <div class="col-md-3">
            <label for="keyword" class="form-label">Keyword in Description</label>
            <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Search text..." value="<?= htmlspecialchars($filter_keyword) ?>" />
        </div>

        <div class="col-md-12 d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
            <a href="reports_history.php" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
        </div>
    </form>

    <?php if (empty($reports)): ?>
        <div class="alert alert-info mt-3">No reports found with current filters.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Case ID</th>
                        <th>Case Title</th>
                        <th>Description</th>
                        <th>Report Date</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $index => $report): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($report['custom_case_id']) ?></td>
                            <td><?= htmlspecialchars($report['case_title']) ?></td>
                            <td style="text-align:left; max-width: 350px; word-wrap: break-word; white-space: normal;"><?= nl2br(htmlspecialchars($report['description'])) ?></td>
                            <td><?= date('d M Y, H:i', strtotime($report['report_date'])) ?></td>
                            <td>
                                <?php if (!empty($report['file_path']) && file_exists("../" . $report['file_path'])): ?>
                                    <a href="../<?= htmlspecialchars($report['file_path']) ?>" class="btn btn-sm btn-download" target="_blank" download>
                                        <i class="bi bi-download"></i> Download
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
    <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
