<?php
require_once "../config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

// Fetch all complaints
$stmt = $pdo->prepare("
    SELECT c.*, u.name AS user_name
    FROM complaints c
    JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at DESC
");
$stmt->execute();
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>New Complaints | Forensic Portal</title>
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

        table.table thead tr th,
table.table tbody td {
    padding: 6px 10px !important;
    font-size: 0.8rem !important;
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

        select.form-select {
            min-width: 110px;
            font-size: 0.9rem;
            border-radius: 8px;
            border: 1.5px solid #ced4da;
            transition: border-color 0.3s ease;
        }

        select.form-select:focus {
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
<?php include '../includes/sidebar.php'; ?>
<?php include '../includes/header.php'; ?>

<main class="content p-4">
    <h3> complaints</h3>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <?php if (count($complaints) === 0): ?>
            <div class="alert alert-info">No complaints found.</div>
        <?php else: ?>
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>User Name</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Attachment</th>
                        <th>Submitted At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $index => $c): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($c['user_name']) ?></td>
                        <td><?= htmlspecialchars($c['subject']) ?></td>
                        <td><?= nl2br(htmlspecialchars($c['message'])) ?></td>
                        <td>
                            <?php if (!empty($c['attachment'])): ?>
                                <a href="/complaints/<?= htmlspecialchars($c['attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    View File
                                </a>
                            <?php else: ?>
                                No Attachment
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y, H:i', strtotime($c['created_at'])) ?></td>
                        <td>
                            <?= htmlspecialchars($c['is_converted'] ? 'Converted' : $c['status']) ?>
                        </td>
                        <td>
                            <?php if (!$c['is_converted']): ?>
                                <div class="d-flex flex-row gap-2">
                                    <a href="convert_to_case.php?complaint_id=<?= $c['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Mark this complaint as a case?')">
                                        Convert to Case
                                    </a>
                                    <a href="reject_complaint.php?complaint_id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this complaint?')">
                                        Reject
                                    </a>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
