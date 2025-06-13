<?php
require_once "../config.php";
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

if (!isset($_GET['complaint_id'])) {
    $_SESSION['msg'] = "Invalid request.";
    header("Location: new_complaints.php");
    exit;
}

$complaint_id = intval($_GET['complaint_id']);

// Step 1: Fetch complaint details
$stmt = $pdo->prepare("SELECT * FROM complaints WHERE id = ?");
$stmt->execute([$complaint_id]);
$complaint = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$complaint) {
    $_SESSION['msg'] = "Complaint not found.";
    header("Location: new_complaints.php");
    exit;
}

$title = $complaint['subject'];
$description = $complaint['message'];
$user_id = $complaint['user_id']; // used for created_by

// Step 2: Generate a unique alphanumeric custom case ID
function generateUniqueCaseId($pdo) {
    do {
        $custom_case_id = "CASE-" . strtoupper(substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 6));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cases WHERE custom_case_id = ?");
        $stmt->execute([$custom_case_id]);
        $count = $stmt->fetchColumn();
    } while ($count > 0);
    return $custom_case_id;
}

$custom_case_id = generateUniqueCaseId($pdo);

// Step 3: Insert into `cases` table using created_by
$stmt = $pdo->prepare("INSERT INTO cases (custom_case_id, title, description, created_by, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->execute([$custom_case_id, $title, $description, $user_id]);

// Step 4: Mark complaint as converted and store custom_case_id
$stmt = $pdo->prepare("UPDATE complaints SET is_converted = 1, custom_case_id = ? WHERE id = ?");
$stmt->execute([$custom_case_id, $complaint_id]);

$_SESSION['msg'] = "Complaint converted to case successfully.";
header("Location: new_complaints.php");
exit;
?>
