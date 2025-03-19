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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_SESSION['user_id'];
    $subjects = $_POST['subject'];
    $exam_dates = $_POST['exam_date'];
    $start_times = $_POST['start_time'];

    foreach ($subjects as $index => $subject) {
        $exam_date = $exam_dates[$index];
        $start_time = $start_times[$index];

        // Validate date (No past dates)
        if (strtotime($exam_date) < strtotime(date('Y-m-d'))) {
            continue; // Skip past date entries
        }

        // Insert into database
        $query = "INSERT INTO exam_routines (subject, exam_date, start_time, posted_by) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $subject, $exam_date, $start_time, $admin_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = "DELETE FROM exam_routines WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all exam routines
$query = "SELECT * FROM exam_routines ORDER BY exam_date ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exam Routines - EduConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function addSubjectField() {
            let container = document.getElementById("subjects-container");
            let row = document.createElement("div");
            row.classList.add("row", "subject-row", "mb-3");
            
            row.innerHTML = `
                <div class="col-md-4">
                    <input type="text" name="subject[]" class="form-control" placeholder="Subject" required>
                </div>
                <div class="col-md-4">
                    <input type="date" name="exam_date[]" class="form-control" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4">
                    <input type="time" name="start_time[]" class="form-control" required>
                </div>
            `;
            container.appendChild(row);
        }
    </script>
    <style>
        body { background: rgb(243, 232, 213); }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        .btn-primary { background-color: rgb(53, 101, 152); border: none; }
        .btn-primary:hover { background-color: rgb(40, 80, 120); }
        .navbar { background-color: rgb(53, 101, 152); color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>📅 Manage Exam Routines</h2>
    <a href="admin_dashboard.php" class="btn btn-dark">Back to Dashboard</a>
</div>

<div class="container mt-4">
    <h4>Add New Exam Routine</h4>
    
    <form method="POST" action="manage_exams.php">
        <div id="subjects-container">
            <div class="row subject-row mb-3">
                <div class="col-md-4">
                    <input type="text" name="subject[]" class="form-control" placeholder="Subject" required>
                </div>
                <div class="col-md-4">
                    <input type="date" name="exam_date[]" class="form-control" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4">
                    <input type="time" name="start_time[]" class="form-control" required>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary" onclick="addSubjectField()">+ Add More Subjects</button>
        <button type="submit" class="btn btn-primary mt-3">Add Exams</button>
    </form>

    <h4 class="mt-5">Existing Exam Routines</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Exam Date</th>
                <th>Start Time</th>
                <th>Actions</th>
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
                    <td>
                        <a href="manage_exams.php?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
