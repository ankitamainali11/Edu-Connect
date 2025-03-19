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

$admin_id = $_SESSION['user_id'];

// Fetch admin name
$admin_query = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
$admin_query->bind_param("i", $admin_id);
$admin_query->execute();
$admin_result = $admin_query->get_result();
$admin = $admin_result->fetch_assoc();
$admin_name = $admin['full_name'] ?? 'Unknown Admin';
$admin_query->close();

// Handle new notice submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);

    $stmt = $conn->prepare("INSERT INTO notices (title, description, posted_by) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $description, $admin_id);

    if ($stmt->execute()) {
        header("Location: manage_notices.php");
        exit();
    } else {
        die("MySQL Error: " . $stmt->error);
    }

    $stmt->close();
}

// Fetch existing notices with admin name
$query = "SELECT notices.id, notices.title, notices.description, notices.created_at, users.full_name 
          FROM notices 
          JOIN users ON notices.posted_by = users.id 
          ORDER BY notices.created_at DESC";

$result = $conn->query($query);

if (!$result) {
    die("Error fetching notices: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notices - EduConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: rgb(243, 232, 213);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background-color: rgb(53, 101, 152);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .footer {
            background-color: rgb(53, 101, 152);
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: auto;
        }
        .card {
            margin-bottom: 15px;
            border-left: 5px solid rgb(53, 101, 152);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-primary, .btn-warning, .btn-danger {
            border: none;
        }
        .btn-danger:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>📢 Manage Notices</h2>
    <a href="../admin/admin_dashboard.php" class="btn btn-dark">Back to Dashboard</a>
</div>

<div class="container mt-4">
    <h4>Add a New Notice</h4>
    <form method="POST" class="mb-4">
        <input type="text" name="title" class="form-control mb-2" placeholder="Notice Title" required>
        <textarea name="description" class="form-control mb-2" rows="3" placeholder="Notice Description" required></textarea>
        <input type="text" class="form-control mb-2" value="<?= htmlspecialchars($admin_name) ?>" readonly>
        <button type="submit" class="btn btn-primary">Post Notice</button>
    </form>

    <h4>Existing Notices</h4>
    <?php while ($row = $result->fetch_assoc()) : ?>
        <div class="card p-3">
            <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
            <p class="card-text"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            <p class="text-muted">
                <strong>Posted by:</strong> <?= htmlspecialchars($row['full_name']) ?><br>
                <strong>On:</strong> <?= $row['created_at'] ?>
            </p>

            <div class="d-flex">
                <a href="update_notice.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm me-2">✏ Edit</a>
                <a href="delete_notice.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this notice?');">🗑 Delete</a>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<div class="footer">
    <p>&copy; 2025 EduConnect. All rights reserved.</p>
</div>

</body>
</html>
