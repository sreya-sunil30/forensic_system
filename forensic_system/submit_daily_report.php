<?php
require_once "config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'investigator') {
header("Location: /forensic_system/index.php"); 
    exit;
}

$investigator_id = $_SESSION['user_id'];
$message = "";

// Fetch assigned cases for this investigator
$stmt = $pdo->prepare("SELECT custom_case_id, title FROM cases WHERE assigned_to = ?");
$stmt->execute([$investigator_id]);
$cases = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $case_id = $_POST['custom_case_id'];
    $summary = trim($_POST['activity_summary']);
    $report_date = $_POST['report_date'];
    
    // Suspects
    $suspects = [];
    if (!empty($_POST['suspect_name'])) {
        for ($i = 0; $i < count($_POST['suspect_name']); $i++) {
            if ($_POST['suspect_name'][$i] !== '') {
                $suspects[] = [
                    'name' => $_POST['suspect_name'][$i],
                    'age' => $_POST['suspect_age'][$i],
                    'remarks' => $_POST['suspect_remarks'][$i],
                ];
            }
        }
    }

    // Evidence Upload
    $evidence_files = [];
    if (!empty($_FILES['evidence']['name'][0])) {
        foreach ($_FILES['evidence']['tmp_name'] as $index => $tmpName) {
            $originalName = $_FILES['evidence']['name'][$index];
            $targetPath = "uploads/" . uniqid() . "_" . basename($originalName);
            if (move_uploaded_file($tmpName, $targetPath)) {
                $evidence_files[] = $targetPath;
            }
        }
    }

    $stmt = $pdo->prepare("INSERT INTO investigator_daily_reports (custom_case_id, investigator_id, activity_summary, suspects, evidence_files, report_date)
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $case_id,
        $investigator_id,
        $summary,
        json_encode($suspects),
        json_encode($evidence_files),
        $report_date
    ]);

    $message = "Report submitted successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Submit Daily Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "includes/header.php"; ?>
<?php include "includes/sidebar.php"; ?>

<main class="main-content" style="margin-left: 260px; padding: 2rem;">
    <div class="container-fluid">
        <h2 class="mb-4">Submit Daily Activity Report</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>

        <form method="post" action="submit_daily_report.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Case</label>
                <select name="custom_case_id" class="form-select" required>
                    <option value="">Select a case</option>
                    <?php foreach ($cases as $case): ?>
                        <option value="<?= $case['custom_case_id'] ?>"><?= $case['custom_case_id'] ?> - <?= htmlspecialchars($case['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Report Date</label>
                <input type="date" name="report_date" class="form-control" required value="<?= date('Y-m-d') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Activity Summary</label>
                <textarea name="activity_summary" class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Suspect(s)</label>
                <div id="suspect-wrapper">
                    <div class="row mb-2">
                        <div class="col-md-4 mb-2">
                            <input type="text" name="suspect_name[]" class="form-control" placeholder="Name">
                        </div>
                        <div class="col-md-3 mb-2">
                            <input type="number" name="suspect_age[]" class="form-control" placeholder="Age">
                        </div>
                        <div class="col-md-5 mb-2">
                            <input type="text" name="suspect_remarks[]" class="form-control" placeholder="Remarks">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addSuspect()">+ Add Suspect</button>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload Evidence</label>
                <input type="file" name="evidence[]" class="form-control" multiple>
            </div>

            <button type="submit" style="background-color: #013a63; color: #fff" class="btn btn-primary">Submit Report</button>
        </form>
    </div>
</main>

<script>
function addSuspect() {
    const wrapper = document.getElementById('suspect-wrapper');
    const div = document.createElement('div');
    div.className = "row mb-2";
    div.innerHTML = `
        <div class="col-md-4 mb-2">
            <input type="text" name="suspect_name[]" class="form-control" placeholder="Name">
        </div>
        <div class="col-md-3 mb-2">
            <input type="number" name="suspect_age[]" class="form-control" placeholder="Age">
        </div>
        <div class="col-md-5 mb-2">
            <input type="text" name="suspect_remarks[]" class="form-control" placeholder="Remarks">
        </div>`;
    wrapper.appendChild(div);
}
</script>
</body>

</html>
