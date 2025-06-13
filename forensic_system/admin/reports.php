<?php
require_once "../config.php";
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$stmt = $pdo->query("
    SELECT reports.id, reports.custom_case_id, reports.description, reports.report_date, reports.file_name, reports.file_path,
           reports.conclusion,
           users.name AS investigator_name, cases.title AS case_title
    FROM reports
    JOIN users ON reports.investigator_id = users.id
    JOIN cases ON reports.custom_case_id = cases.custom_case_id
    ORDER BY reports.report_date DESC
");

$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Investigation Reports | Forensic Portal</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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

        h3 {
            font-weight: 600;
            color: #212529;
            margin-bottom: 1.5rem;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100% !important;
            table-layout: auto;
            min-width: 700px;
        }

        table.table {
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

        .btn-download {
            background-color: #198754;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .btn-download:hover {
            background-color: #157347;
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

<?php include '../includes/sidebar.php'; ?>
<?php include '../includes/header.php'; ?>

<main class="content p-4">
    <h3>Investigation Reports</h3>

    <?php if (empty($reports)): ?>
        <div class="alert alert-info">No reports found.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-light">
                    <tr>
                        <th>S No</th>
                        <th>Case ID</th>
                        <th>Case Title</th>
                        <th>Investigator</th>
                        <th>Description</th>
                        <th>Report Date</th>
                        <th>File</th>
                        <th>conclusion</th>
                        <th>Generate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $index => $report): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($report['custom_case_id']) ?></td>
                            <td><?= htmlspecialchars($report['case_title']) ?></td>
                            <td><?= htmlspecialchars($report['investigator_name']) ?></td>
                            <td style="text-align:left"><?= nl2br(htmlspecialchars($report['description'])) ?></td>
                            <td><?= date('d M Y') ?></td>
                            <td>
                                <?php if (!empty($report['file_path']) && file_exists("../" . $report['file_path'])): ?>
                                    <a href="../<?= htmlspecialchars($report['file_path']) ?>" class="btn btn-sm btn-download" target="_blank" download>
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No file</span>
                                <?php endif; ?>
                            </td>

    <td>
    <div id="conclusion-view-<?= $report['id'] ?>">
        <div style="white-space: pre-wrap;"><?= htmlspecialchars($report['conclusion'] ?? 'No conclusion') ?></div>
        <button type="button" class="btn btn-sm mt-1" style="background-color:#013a63; color:#fff;" onclick="toggleEdit(<?= $report['id'] ?>)">Edit</button>
    </div>

    <form id="conclusion-form-<?= $report['id'] ?>" method="POST" action="update_conclusion.php" style="display:none; flex-direction: column; gap: 5px;">
        <textarea name="conclusion" class="form-control form-control-sm" rows="2" required><?= htmlspecialchars($report['conclusion'] ?? '') ?></textarea>
        <input type="hidden" name="report_id" value="<?= $report['id'] ?>" />
        <div class="mt-1">
            <button type="submit" class="btn btn-sm" style="background-color:#013a63; color:#fff;">Save</button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="cancelEdit(<?= $report['id'] ?>)">Cancel</button>
        </div>
    </form>
</td>

                            <td>
                                <a href="view_report.php?report_id=<?= $report['id'] ?>" style="background-color: #013a63; color: #fff;" target="_blank" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-printer"></i> Generate
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<script>
    function toggleEdit(id) {
        document.getElementById('conclusion-view-' + id).style.display = 'none';
        document.getElementById('conclusion-form-' + id).style.display = 'flex';
    }

    function cancelEdit(id) {
        document.getElementById('conclusion-form-' + id).style.display = 'none';
        document.getElementById('conclusion-view-' + id).style.display = 'block';
    }
</script>

</body>
</html>
