<?php
require_once "../config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$message = "";

// Handle creating new case
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_case'])) {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title && $description) {
            do {
                $custom_case_id = "CASE-" . strtoupper(substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 6));
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM cases WHERE custom_case_id = ?");
                $stmt->execute([$custom_case_id]);
                $count = $stmt->fetchColumn();
            } while ($count > 0);

            $stmt = $pdo->prepare("INSERT INTO cases (custom_case_id, title, description, status) VALUES (?, ?, ?, 'open')");
            $stmt->execute([$custom_case_id, $title, $description]);
            $message = "New case created successfully.";
        } else {
            $message = "Please fill in all fields.";
        }
    }

    if (isset($_POST['custom_case_id'])) {
        $caseId = $_POST['custom_case_id'];
        $status = $_POST['status'] ?? '';
        $assignedTo = $_POST['assigned_to'] ?? null;
        $deadline = $_POST['deadline'] ?? null;

        if (!in_array($status, ['open', 'in progress', 'closed'])) {
            $message = "Invalid status selected.";
        } else {
            $stmt = $pdo->prepare("UPDATE cases SET status = ?, assigned_to = ?, deadline = ? WHERE custom_case_id = ?");
            $stmt->execute([$status, $assignedTo ?: null, $deadline, $caseId]);
            $message = "Case updated successfully.";
        }
    }
}

if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM cases WHERE custom_case_id = ?");
    $stmt->execute([$deleteId]);
    $message = "Case deleted successfully.";
}

$investigators = $pdo->query("SELECT id, name FROM users WHERE role = 'investigator'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Cases | Forensic Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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

        .main-content {
            margin-left: 250px; 
            padding: 20px;
            overflow-x: auto; 
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

        .btn-update {
            background-color: #1f78d1;
            border: none;
            color: #fff;
            padding: 0.45rem 1.1rem;
            font-size: 0.95rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(31, 120, 209, 0.2);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-update:hover {
            background-color: #155d9c;
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: #e53935;
            color: #fff;
            border: none;
            padding: 0.45rem 1.1rem;
            font-size: 0.95rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(229, 57, 53, 0.2);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-danger:hover {
            background-color: #b71c1c;
            transform: translateY(-1px);
        }

        select.form-select, input[type="date"] {
            min-width: 110px;
            font-size: 0.9rem;
            border-radius: 8px;
            border: 1.5px solid #ced4da;
            transition: border-color 0.3s ease;
        }

        select.form-select:focus, input[type="date"]:focus {
            border-color: #4e9af1;
            box-shadow: 0 0 5px #4e9af1aa;
            outline: none;
        }

        .btn-sm i {
            margin-right: 6px;
            font-size: 0.9rem;
        }

        .text-muted {
            font-style: italic;
            color: #2a6f97 !important;
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
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="content container-fluid">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h3>Manage Cases</h3>
            <!-- <button class="btn" style="background-color: #2a6f97; color: #fff;" data-bs-toggle="modal" data-bs-target="#createCaseModal">
                + New Case
            </button> -->
        </div>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Case ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Assigned</th>
                        <th>Deadline</th>
                        <th>Update</th>
                        <th>Investigator Response</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM cases ORDER BY custom_case_id DESC");
                    while ($case = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $assignedName = 'Unassigned';
                        if (!empty($case['assigned_to'])) {
                            $stmt2 = $pdo->prepare("SELECT name FROM users WHERE id = ?");
                            $stmt2->execute([$case['assigned_to']]);
                            $user = $stmt2->fetch();
                            if ($user) $assignedName = $user['name'];
                        }

                        echo "<tr>
                            <td>" . htmlspecialchars($case['custom_case_id']) . "</td>
                            <td>" . htmlspecialchars($case['title']) . "</td>
                            <td>" . htmlspecialchars($case['description']) . "</td>
                            <td>" . htmlspecialchars($case['status']) . "</td>
                            <td>" . htmlspecialchars($assignedName) . "</td>
                            <td>" . (!empty($case['deadline']) ? htmlspecialchars($case['deadline']) : '<span class="text-muted">N/A</span>') . "</td>
                            <td>
                                <form method='post' class='d-flex gap-2 align-items-center'>
                                    <input type='hidden' name='custom_case_id' value='" . htmlspecialchars($case['custom_case_id']) . "'>
                                    <select name='status' class='form-select'>
                                        <option value='open'" . ($case['status'] === 'open' ? ' selected' : '') . ">Open</option>
                                        <option value='in progress'" . ($case['status'] === 'in progress' ? ' selected' : '') . ">In Progress</option>
                                        <option value='closed'" . ($case['status'] === 'closed' ? ' selected' : '') . ">Closed</option>
                                    </select>
                                    <select name='assigned_to' class='form-select'>
                                        <option value=''>Unassigned</option>";
                                        foreach ($investigators as $inv) {
                                            $sel = ($case['assigned_to'] == $inv['id']) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($inv['id']) . "' $sel>" . htmlspecialchars($inv['name']) . "</option>";
                                        }
                        echo        "</select>
                                    <input type='date' name='deadline' class='form-control' value='" . htmlspecialchars($case['deadline'] ?? '') . "'>
                                    <button class='btn btn-success btn-sm' style='background-color: #2a6f97; color: #fff;'><i class='bi bi-check2'></i></button>
                                </form>
                            </td>
                            <td>";
                            
                            $response = $case['investigator_response'] ?? 'pending';
                            $badgeClass = ($response === 'accepted') ? 'success' : (($response === 'reassign_requested') ? 'warning' : 'secondary');

                            echo "<span class='badge bg-$badgeClass'>" . ucfirst(str_replace('_', ' ', htmlspecialchars($response))) . "</span>";

                        echo    "</td>
                            <td>
                                <a href='?delete=" . urlencode($case['custom_case_id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Delete this case?\")'>
                                    <i class='bi bi-trash'></i>
                                </a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
