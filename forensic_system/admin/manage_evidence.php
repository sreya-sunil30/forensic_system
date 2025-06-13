<?php
require_once "../config.php";
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'investigator')) {
    header("Location: /forensic_system/index.php"); 
    exit;
}

// Handle deletion
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM evidence WHERE id = ?")->execute([$deleteId]);
    $message = "Evidence record deleted successfully.";
}

// Handle upload if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_evidence'])) {
    $custom_case_id = $_POST['custom_case_id'] ?: null;
    $uploaded_by = $_SESSION['user_id'];
    $attachment_path = null;
    $sha256 = null;

    if (!empty($_FILES['evidence_file']['name'])) {
        $upload_dir = '../uploads/evidence/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['evidence_file']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['evidence_file']['tmp_name'], $target_file)) {
            $attachment_path = $target_file;
            $sha256 = hash_file('sha256', $target_file);
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO evidence 
            (custom_case_id, file_name, file_path, uploaded_by, uploaded_at, sha256_hash) 
        VALUES 
            (?, ?, ?, ?, NOW(), ?)
    ");
    $stmt->execute([$custom_case_id, $filename, $attachment_path, $uploaded_by, $sha256]);
    $message = "Evidence uploaded successfully.";
}

// Fetch filters
$filter_case_id = $_GET['filter_case_id'] ?? '';
$filter_user_id = $_GET['filter_user_id'] ?? '';

// Fetch evidence with filters
$sql = "SELECT e.*, c.custom_case_id, c.title 
        FROM evidence e 
        LEFT JOIN cases c ON e.custom_case_id = c.custom_case_id 
        WHERE 1=1";

$params = [];

if (!empty($filter_case_id)) {
    $sql .= " AND e.custom_case_id = ?";
    $params[] = $filter_case_id;
}
if (!empty($filter_user_id)) {
    $sql .= " AND e.uploaded_by = ?";
    $params[] = $filter_user_id;
}

$sql .= " ORDER BY e.uploaded_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$evidences = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cases = $pdo->query("SELECT custom_case_id, title FROM cases ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$uploaders = $pdo->query("
    SELECT DISTINCT u.id, u.name 
    FROM users u 
    JOIN evidence e ON u.id = e.uploaded_by
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Evidence - Forensic Portal</title>
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
    margin-left: 270px; /* was 250px – now enough to avoid sidebar overlap */
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Manage Evidence</h3>
<button class="btn btn-secondary" style="background-color: #2a6f97; color: #fff;" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bi bi-upload"></i> Upload Evidence
        </button>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Filters -->
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-5">
            <label for="filter_case_id" class="form-label">Filter by Case ID</label>
            <select name="filter_case_id" id="filter_case_id" class="form-select">
                <option value="">-- All Cases --</option>
                <?php foreach ($cases as $case): ?>
                    <option value="<?= htmlspecialchars($case['custom_case_id']) ?>" <?= $filter_case_id === $case['custom_case_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($case['custom_case_id'] . ' – ' . $case['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <label for="filter_user_id" class="form-label">Filter by Uploaded By</label>
            <select name="filter_user_id" id="filter_user_id" class="form-select">
                <option value="">-- All Users --</option>
                <?php foreach ($uploaders as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $filter_user_id == $u['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-outline-primary w-100">Apply Filters</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered align-middle bg-white">
            <thead class="table-light">
                <tr>
                    <th>Evidence ID</th>
                    <th>Case ID</th>
                    <th>Case Title</th>
                    <th>File Name</th>
                    <th>Uploaded By</th>
                    <th>Uploaded At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $userNames = [];
                foreach ($uploaders as $u) {
                    $userNames[$u['id']] = $u['name'];
                }
                ?>
                <?php foreach ($evidences as $e): ?>
                <tr>
                    <td><?= htmlspecialchars($e['id']) ?></td>
                    <td><?= htmlspecialchars($e['custom_case_id'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($e['title'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($e['file_name']) ?></td>
                    <td><?= htmlspecialchars($userNames[$e['uploaded_by']] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($e['uploaded_at']) ?></td>
                    <td>
                        <div class="btn-group" role="group">
    <?php if (!empty($e['file_path']) && file_exists("../uploads/evidence/" . basename($e['file_path']))) : ?>
        <a href="../uploads/evidence/<?= urlencode(basename($e['file_path'])) ?>" class="btn btn-primary btn-sm" target="_blank">View</a>
        <a href="../uploads/evidence/<?= urlencode(basename($e['file_path'])) ?>" class="btn btn-success btn-sm" download>Download</a>
    <?php else: ?>
        <span class="text-muted">No file</span>
    <?php endif; ?>
    <a href="?delete=<?= $e['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this evidence?')">
        <i class="bi bi-trash"></i>
    </a>
</div>

                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Evidence File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="upload_evidence" value="1">
                <div class="mb-3">
                    <label for="custom_case_id" class="form-label">Custom Case ID</label>
                    <select name="custom_case_id" class="form-select">
                        <option value="">-- Select Case (optional) --</option>
                        <?php foreach ($cases as $case): ?>
                            <option value="<?= htmlspecialchars($case['custom_case_id']) ?>">
                                <?= htmlspecialchars($case['custom_case_id'] . ' – ' . $case['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="evidence_file" class="form-label">Choose File</label>
                    <input type="file" name="evidence_file" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Upload</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
