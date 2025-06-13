<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'investigator') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

$success = false;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $custom_case_id = $_POST['custom_case_id'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $conclusion = trim($_POST['conclusion'] ?? '');
    $report_date = date("Y-m-d H:i:s");

    // File upload
    $uploadedFilePath = null;
    if (!empty($_FILES['report_file']['name'])) {
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $fileType = $_FILES['report_file']['type'];
        $fileSize = $_FILES['report_file']['size'];
        $tmpName = $_FILES['report_file']['tmp_name'];

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Unsupported file type. Allowed: PDF, JPG, PNG, DOC, DOCX.";
        } elseif ($fileSize > 10 * 1024 * 1024) {
            $errors[] = "File size exceeds 10MB limit.";
        } else {
            $ext = pathinfo($_FILES['report_file']['name'], PATHINFO_EXTENSION);
            $safeName = uniqid('report_', true) . "." . $ext;
            $uploadDir = "../uploads/reports/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $targetPath = $uploadDir . $safeName;
            if (move_uploaded_file($tmpName, $targetPath)) {
                $uploadedFilePath = "uploads/reports/" . $safeName;
            } else {
                $errors[] = "Failed to upload file.";
            }
        }
    }

    if (!$custom_case_id) $errors[] = "Please select a case.";
    if (!$description) $errors[] = "Please enter a description.";
    if (!$conclusion) $errors[] = "Please provide a conclusion for the report.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO reports (custom_case_id, investigator_id, description, conclusion, report_date, file_name, file_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $fileName = $_FILES['report_file']['name'] ?? null;
        $stmt->execute([
            $custom_case_id,
            $_SESSION['user_id'],
            $description,
            $conclusion,
            $report_date,
            $fileName,
            $uploadedFilePath
        ]);
        $success = true;
    }
}

// Fetch assigned cases
$stmt = $pdo->prepare("SELECT custom_case_id, title FROM cases WHERE assigned_to = ?");
$stmt->execute([$_SESSION['user_id']]);
$cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Submit Report | Forensic Portal</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<main class="content" style="margin-left:270px; padding:2rem;">
    <h2>Submit Investigation Report</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">Report submitted successfully!</div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-danger"><ul><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="custom_case_id" class="form-label">Select Case</label>
            <select name="custom_case_id" id="custom_case_id" class="form-select" required>
                <option value="">-- Choose a case --</option>
                <?php foreach ($cases as $case): ?>
                    <option value="<?= htmlspecialchars($case['custom_case_id']) ?>"><?= htmlspecialchars($case['custom_case_id'] . " — " . $case['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Report Description</label>
            <textarea name="description" id="description" rows="6" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="conclusion" class="form-label">Conclusion</label>
            <textarea name="conclusion" id="conclusion" rows="4" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="report_file" class="form-label">Upload Report File (optional)</label>
            <input type="file" name="report_file" id="report_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
        </div>

<button type="submit" class="btn" style="background-color: #013a63; color: #fff;">Submit Report</button>
    </form>    
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
