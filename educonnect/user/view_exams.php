<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "../db_connect.php";

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all upcoming exams
$query = "SELECT subject, exam_date, start_time FROM exam_routines WHERE exam_date >= CURDATE() ORDER BY exam_date ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Routine - EduConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: rgb(243, 232, 213); }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        .btn-primary { background-color: rgb(53, 101, 152); border: none; }
        .btn-primary:hover { background-color: rgb(40, 80, 120); }
        .navbar { background-color: rgb(53, 101, 152); color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>📅 Exam Routine</h2>
    <a href="user_dashboard.php" class="btn btn-dark">Back to Dashboard</a>
</div>

<div class="container mt-4">
    <h4>Upcoming Exam Schedule</h4>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Exam Date</th>
                    <th>Start Time</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                        <td><?= htmlspecialchars($row['exam_date']) ?></td>
                        <td><?= htmlspecialchars($row['start_time']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">No upcoming exams scheduled.</p>
    <?php endif; ?>

</div>

</body>
</html>
