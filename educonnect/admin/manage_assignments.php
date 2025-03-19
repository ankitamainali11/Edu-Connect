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
$message = "";

// Handle Assignment Submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_assignment'])) {
    $subject = trim($_POST["subject"]);
    $topic = trim($_POST["topic"]);
    $description = trim($_POST["description"]);
    $due_date = $_POST["due_date"];

    // Validate due date (should not be in the past)
    if (strtotime($due_date) < strtotime(date("Y-m-d"))) {
        $message = "<div class='alert alert-danger'>Due date cannot be in the past!</div>";
    } else {
        // File Upload Handling
        $file_path = NULL;
        if (!empty($_FILES["file"]["name"])) {
            $upload_dir = "../uploads/assignments/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = time() . "_" . basename($_FILES["file"]["name"]);
            $target_file = $upload_dir . $file_name;
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $allowed_types = ["pdf", "doc", "docx", "xls", "xlsx"];
            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                    $file_path = $file_name;
                } else {
                    $message = "<div class='alert alert-danger'>File upload failed!</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>Invalid file type! Only PDF, DOC, DOCX, XLS, XLSX allowed.</div>";
            }
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO assignments (subject, topic, description, due_date, file_path, posted_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $subject, $topic, $description, $due_date, $file_path, $admin_id);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Assignment added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}

// Fetch assignments
$query = "SELECT assignments.id, assignments.subject, assignments.topic, assignments.description, assignments.due_date, assignments.file_path, assignments.created_at, users.full_name 
          FROM assignments 
          JOIN users ON assignments.posted_by = users.id 
          ORDER BY assignments.created_at DESC";

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
    <title>Manage Assignments - EduConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: rgb(243, 232, 213);
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
        .table {
            margin-top: 20px;
            border-collapse: collapse;
        }
        .table th {
            background-color: rgb(53, 101, 152);
            color: white;
        }
        .footer {
            background-color: rgb(53, 101, 152);
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>📄 Manage Assignments</h2>
    <a href="../admin/admin_dashboard.php" class="btn btn-dark">Back to Dashboard</a>
</div>

<div class="container mt-4">
    <?= $message ?>

    <h4>Add a New Assignment</h4>
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <input type="text" name="subject" class="form-control mb-2" placeholder="Subject" required>
        <input type="text" name="topic" class="form-control mb-2" placeholder="Topic" required>
        <textarea name="description" class="form-control mb-2" rows="3" placeholder="Description" required></textarea>
        <input type="date" name="due_date" class="form-control mb-2" required>
        <input type="file" name="file" class="form-control mb-2">
        <button type="submit" name="submit_assignment" class="btn btn-primary">Post Assignment</button>
    </form>

    <h4>Existing Assignments</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Topic</th>
                <th>Description</th>
                <th>Due Date</th>
                <th>File</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= htmlspecialchars($row['topic']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= htmlspecialchars($row['due_date']) ?></td>
                    <td>
                        <?php if ($row['file_path']) : ?>
                            <a href="../uploads/assignments/<?= $row['file_path'] ?>" download class="btn btn-sm btn-success">Download</a>
                        <?php else : ?>
                            No File
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="update_assignment.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Update</a>
                        <a href="delete_assignment.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this assignment?')">Delete</a>
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
