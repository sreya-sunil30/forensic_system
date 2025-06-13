<?php
require_once "../config.php";
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /forensic_system/index.php"); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'], $_POST['conclusion'])) {
    $report_id = (int)$_POST['report_id'];
    $conclusion = trim($_POST['conclusion']);

    $stmt = $pdo->prepare("UPDATE reports SET conclusion = ? WHERE id = ?");
    $stmt->execute([$conclusion, $report_id]);

    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
}
?>
