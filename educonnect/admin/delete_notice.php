<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "../db_connect.php";

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Validate notice ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_notices.php");
    exit();
}

$notice_id = intval($_GET['id']);

// Delete notice
$stmt = $conn->prepare("DELETE FROM notices WHERE id = ?");
$stmt->bind_param("i", $notice_id);

if ($stmt->execute()) {
    header("Location: manage_notices.php?deleted=true");
    exit();
} else {
    die("Error deleting notice: " . $stmt->error);
}

$stmt->close();
?>
