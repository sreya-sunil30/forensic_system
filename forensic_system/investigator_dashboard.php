<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'investigator') {
    header("Location: /forensic_system/index.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Investigator Dashboard | Forensic Portal</title>
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
        margin-left: 270px; /* Sidebar width */
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
        }
    }
    </style>
</head>
<body>

<div class="d-flex">
    <?php include "includes/sidebar.php"; ?>

    <main class="content flex-grow-1">
        <?php include "includes/header.php"; ?>

        <h1 class="mb-4">Investigator Dashboard</h1>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Open Assigned Cases</h5>
                        <?php
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cases WHERE assigned_to = ? AND status = 'open'");
                        $stmt->execute([$_SESSION['user_id']]);
                        $openCases = $stmt->fetchColumn();
                        ?>
                        <p class="card-text fs-2 fw-bold text-secondary"><?= $openCases ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Reports Submitted</h5>
                        <?php
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE investigator_id = ?");
                        $stmt->execute([$_SESSION['user_id']]);
                        $reports = $stmt->fetchColumn();
                        ?>
                        <p class="card-text fs-2 fw-bold text-secondary"><?= $reports ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Closed Cases</h5>
                        <?php
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cases WHERE assigned_to = ? AND status = 'closed'");
                        $stmt->execute([$_SESSION['user_id']]);
                        $closedCases = $stmt->fetchColumn();
                        ?>
                        <p class="card-text fs-2 fw-bold text-secondary"><?= $closedCases ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
