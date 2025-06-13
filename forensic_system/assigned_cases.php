<?php
require_once "config.php";
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'investigator') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$investigator_id = $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'Investigator');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['case_id'], $_POST['action'])) {
    $caseId = $_POST['case_id'];
    $action = $_POST['action'];

    $update = $pdo->prepare("UPDATE cases SET investigator_response = ? WHERE custom_case_id = ? AND assigned_to = ?");
    $update->execute([$action, $caseId, $investigator_id]);
}

$stmt = $pdo->prepare("SELECT * FROM cases WHERE assigned_to = ?");
$stmt->execute([$investigator_id]);
$cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Assigned Cases | Forensic Portal</title>
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

<?php include "includes/sidebar.php"; ?>
<?php include "includes/header.php"; ?>

<main class="content">
    <h2>Assigned Cases for <strong><?= $user_name ?></strong></h2>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Case ID</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Date Assigned</th>
                    <th>Your Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($cases) > 0): ?>
                    <?php foreach ($cases as $case): ?>
                        <tr>
                            <td><?= htmlspecialchars($case['custom_case_id']) ?></td>
                            <td style="text-align: left; max-width: 300px; word-wrap: break-word; white-space: normal;">
                                <?= htmlspecialchars($case['description']) ?>
                            </td>
                            <td><?= ucfirst(htmlspecialchars($case['status'])) ?></td>
                            <td><?= htmlspecialchars(date('d M Y', strtotime($case['created_at']))) ?></td>
                            <td>
                                <?php if ($case['investigator_response'] === 'pending'): ?>
                                    <form method="POST" class="d-flex gap-2 justify-content-center">
                                        <input type="hidden" name="case_id" value="<?= htmlspecialchars($case['custom_case_id']) ?>">
                                        <select name="action" class="form-select form-select-sm w-auto" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="accepted">Accept</option>
                                            <option value="reassign_requested">Request Reassign</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge bg-<?= $case['investigator_response'] === 'accepted' ? 'success' : 'warning' ?>">
                                        <?= ucfirst(str_replace('_', ' ', $case['investigator_response'])) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No assigned cases found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
