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
$message = "";

// Fetch existing assignment
$query = $conn->prepare("SELECT subject, topic, description, due_date, file_path FROM assignments WHERE id = ?");
$query->bind_param("i", $assignment_id);
$query->execute();
$result = $query->get_result();
$assignment = $result->fetch_assoc();
$query->close();

if (!$assignment) {
    die("Assignment not found.");
}

// Handle update submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = trim($_POST["subject"]);
    $topic = trim($_POST["topic"]);
    $description = trim($_POST["description"]);
    $due_date = $_POST["due_date"];

    // Validate due date
    if (strtotime($due_date) < strtotime(date("Y-m-d"))) {
        $message = "<div class='alert alert-danger'>Due date cannot be in the past!</div>";
    } else {
        $file_path = $assignment['file_path'];

        // Handle file upload
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
                    // Delete old file if exists
                    if ($assignment['file_path']) {
                        unlink($upload_dir . $assignment['file_path']);
                    }
                    $file_path = $file_name;
                } else {
                    $message = "<div class='alert alert-danger'>File upload failed!</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>Invalid file type! Only PDF, DOC, DOCX, XLS, XLSX allowed.</div>";
            }
        }

        // Update database
        $stmt = $conn->prepare("UPDATE assignments SET subject = ?, topic = ?, description = ?, due_date = ?, file_path = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $subject, $topic, $description, $due_date, $file_path, $assignment_id);

        if ($stmt->execute()) {
            header("Location: manage_assignments.php");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Assignment - EduConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            border-radius: 5px;
        }
        .btn {
            border-radius: 5px;
        }
        .file-preview {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h3 class="text-center text-primary">Update Assignment</h3>
    <?= $message ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label"><strong>Subject</strong></label>
            <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($assignment['subject']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label"><strong>Topic</strong></label>
            <input type="text" name="topic" class="form-control" value="<?= htmlspecialchars($assignment['topic']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label"><strong>Description</strong></label>
            <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($assignment['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label"><strong>Due Date</strong></label>
            <input type="date" name="due_date" class="form-control" value="<?= htmlspecialchars($assignment['due_date']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label"><strong>Upload New File (Optional)</strong></label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
        </div>

        <?php if ($assignment['file_path']) : ?>
            <div class="file-preview">
                <p>Current File: <a href="../uploads/assignments/<?= $assignment['file_path'] ?>" class="btn btn-sm btn-info" download>Download File</a></p>
            </div>
        <?php endif; ?>

        <div class="text-center">
            <button type="submit" class="btn btn-success">Update Assignment</button>
            <a href="manage_assignments.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
