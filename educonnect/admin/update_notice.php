<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure session starts properly
session_start();
require_once "../db_connect.php";

// Debugging: Check session values
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Ensure `id` is passed
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_notices.php");
    exit();
}

$notice_id = intval($_GET['id']);

// Fetch existing notice details
$query = $conn->prepare("SELECT title, description FROM notices WHERE id = ?");
$query->bind_param("i", $notice_id);
$query->execute();
$result = $query->get_result();
$notice = $result->fetch_assoc();
$query->close();

if (!$notice) {
    die("Notice not found.");
}

// Handle update submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);

    $stmt = $conn->prepare("UPDATE notices SET title = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $description, $notice_id);

    if ($stmt->execute()) {
        header("Location: manage_notices.php");
        exit();
    } else {
        die("MySQL Error: " . $stmt->error);
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Notice - EduConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            width: 500px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: white;
        }

        .btn-success {
            background-color: rgb(53, 101, 152);
            border: none;
            transition: 0.3s ease-in-out;
        }

        .btn-success:hover {
            background-color: rgb(41, 81, 121);
        }

        .btn-secondary {
            background-color: #6c757d;
            transition: 0.3s ease-in-out;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="card">
    <h3 class="text-center">✏️ Update Notice</h3>
    
    <form method="POST">
        <label class="fw-bold mt-2">Title</label>
        <input type="text" name="title" class="form-control mb-3" value="<?= htmlspecialchars($notice['title']) ?>" required>

        <label class="fw-bold">Description</label>
        <textarea name="description" class="form-control mb-3" rows="4" required><?= htmlspecialchars($notice['description']) ?></textarea>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success">✔ Save Changes</button>
            <a href="manage_notices.php" class="btn btn-secondary">❌ Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
