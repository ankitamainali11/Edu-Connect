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

$user_id = $_SESSION['user_id'];

// Fetch user name
$user_query = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();
$user_name = $user['full_name'] ?? 'User';
$user_query->close();

// Fetch latest notices
$notices_query = "SELECT notices.title, notices.description, notices.created_at, users.full_name 
                  FROM notices 
                  JOIN users ON notices.posted_by = users.id 
                  ORDER BY notices.created_at DESC";
$notices_result = $conn->query($notices_query);

// Fetch assignments
$assignments_query = "SELECT subject, topic, description, due_date, file_path FROM assignments ORDER BY due_date ASC";
$assignments_result = $conn->query($assignments_query);

// Fetch exam routines (REMOVED `end_time`)
$exams_query = "SELECT subject, exam_date, start_time FROM exam_routines ORDER BY exam_date ASC";
$exams_result = $conn->query($exams_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background: rgb(243, 232, 213);
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: rgb(53, 101, 152);
            color: white;
            padding: 20px;
            min-height: 100vh;
            position: fixed;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        .sidebar a:hover {
            background: rgb(41, 81, 121);
        }
        .content {
            margin-left: 270px;
            flex-grow: 1;
            padding: 20px;
        }
        .navbar {
            background: rgb(53, 101, 152);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer {
            background: rgb(243, 232, 213);
            color: black;
            text-align: center;
            padding: 15px;
            margin-top: 20px;
        }
        .card {
            margin-bottom: 15px;
            border-left: 5px solid #007bff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>EduConnect</h2>
    <a href="?section=notices">📢 Notices</a>
    <a href="?section=assignments">📚 Assignments</a>
    <a href="?section=exams">📅 Exam Routine</a>
    <a href="../auth/logout.php" class="btn btn-danger w-100">Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <div class="navbar">
        <h2>👤 Welcome, <?= htmlspecialchars($user_name) ?>!</h2>
    </div>

    <div class="container mt-4">
        <?php 
        $section = $_GET['section'] ?? 'notices';
        
        if ($section === 'notices'): ?>
            <h4>📢 Latest Notices</h4>
            <?php while ($notice = $notices_result->fetch_assoc()) : ?>
                <div class="card p-3">
                    <h5 class="card-title"><?= htmlspecialchars($notice['title']) ?></h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($notice['description'])) ?></p>
                    <p class="text-muted">
                        <strong>Posted by:</strong> <?= htmlspecialchars($notice['full_name']) ?><br>
                        <strong>On:</strong> <?= $notice['created_at'] ?>
                    </p>
                </div>
            <?php endwhile; ?>

        <?php elseif ($section === 'assignments'): ?>
            <h4>📚 Assignments</h4>
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
                    <?php while ($assignment = $assignments_result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= htmlspecialchars($assignment['subject']) ?></td>
                            <td><?= htmlspecialchars($assignment['topic']) ?></td>
                            <td><?= nl2br(htmlspecialchars($assignment['description'])) ?></td>
                            <td><?= htmlspecialchars($assignment['due_date']) ?></td>
                            <td>
                                <?php if (!empty($assignment['file_path'])) : ?>
                                    <a href="../uploads/assignments/<?= htmlspecialchars($assignment['file_path']) ?>" class="btn btn-primary btn-sm" download>Download</a>
                                <?php else : ?>
                                    <span class="text-muted">No file</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php elseif ($section === 'exams'): ?>
            <h4>📅 Exam Routine</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Start Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($exam = $exams_result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= htmlspecialchars($exam['subject']) ?></td>
                            <td><?= htmlspecialchars($exam['exam_date']) ?></td>
                            <td><?= htmlspecialchars($exam['start_time']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>&copy; 2025 EduConnect. All rights reserved.</p>
    </div>
</div>

</body>
</html>
