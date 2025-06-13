<?php
require_once "../config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

// Check if complaint_id is passed
if (isset($_GET['complaint_id']) && is_numeric($_GET['complaint_id'])) {
    $complaint_id = $_GET['complaint_id'];

    // Update complaint status to 'Rejected'
    $stmt = $pdo->prepare("UPDATE complaints SET status = 'Rejected', is_converted = 1 WHERE id = ?");
    $stmt->execute([$complaint_id]);

    $_SESSION['msg'] = "Complaint $complaint_id has been rejected.";
} else {
    $_SESSION['msg'] = "Invalid complaint.";
}

header("Location: new_complaints.php");
exit;
