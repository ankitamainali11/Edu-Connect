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

if (!isset($_GET['id'])) {
    header("Location: manage_assignments.php");
    exit();
}

$assignment_id = intval($_GET['id']);

// Fetch file path before deleting
$query = $conn->prepare("SELECT file_path FROM assignments WHERE id = ?");
$query->bind_param("i", $assignment_id);
$query->execute();
$result = $query->get_result();
$assignment = $result->fetch_assoc();
$query->close();

if (!$assignment) {
    die("Assignment not found.");
}

// Delete file if exists
if ($assignment['file_path']) {
    unlink("../uploads/assignments/" . $assignment['file_path']);
}

// Delete from database
$stmt = $conn->prepare("DELETE FROM assignments WHERE id = ?");
$stmt->bind_param("i", $assignment_id);

if ($stmt->execute()) {
    header("Location: manage_assignments.php");
    exit();
} else {
    die("Error deleting assignment: " . $stmt->error);
}

$stmt->close();
?>
