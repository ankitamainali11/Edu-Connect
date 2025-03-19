<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "../db_connect.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch assignments
$query = "SELECT id, subject, topic, description, due_date, file_path FROM assignments ORDER BY due_date ASC";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching assignments: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assignments - EduConnect</title>
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
            max-width: 900px;
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
        .table {
            margin-top: 20px;
        }
        .btn-primary {
            background-color: rgb(53, 101, 152);
            border: none;
        }
        .btn-primary:hover {
            background-color: rgb(40, 80, 120);
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>📚 Assignments</h2>
    <a href="../user/user_dashboard.php" class="btn btn-dark">Back to Dashboard</a>
</div>

<div class="container mt-4">
    <h4>Available Assignments</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Topic</th>
                <th>Description</th>
                <th>Due Date</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
            <?php $count = 1; ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= htmlspecialchars($row['topic']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                    <td><?= htmlspecialchars($row['due_date']) ?></td>
                    <td>
                        <?php if (!empty($row['file_path'])) : ?>
                            <a href="../uploads/assignments/<?= htmlspecialchars($row['file_path']) ?>" class="btn btn-primary btn-sm" download>Download</a>
                        <?php else : ?>
                            <span class="text-muted">No file</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="footer">
    <p>&copy; 2025 EduConnect. All rights reserved.</p>
</div>

</body>
</html>
