<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EduConnect</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: #f4f4f4;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background-color:rgb(53, 101, 152);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h2 {
            margin: 0;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }
        .navbar a:hover {
            background: rgba(255, 255, 255, 0.4);
        }
        .dashboard {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
            flex: 1;
        }
        .action-btn {
            width: 250px;
            padding: 15px;
            font-size: 18px;
            margin: 10px;
            border-radius: 8px;
            border: none;
            background-color:rgb(53, 101, 152);
            color: white;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        .action-btn:hover {
            background-color:rgb(53, 101, 152);
        }
        .footer {
            background-color:rgb(53, 101, 152);
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Admin Dashboard</h2>
        <a href="../auth/login.php">Logout</a>
    </div>

    <div class="dashboard">
        <a href="manage_notices.php" class="action-btn">📢 Manage Notices</a>
        <a href="manage_assignments.php" class="action-btn">📚 Manage Assignments</a>
        <a href="manage_exams.php" class="action-btn">📝 Manage Exam Routines</a>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2025 EduConnect. All rights reserved.</p>
    </div>

</body>
</html>
