<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../db_connect.php'; // Ensure the correct path

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Allowed email domains
    $allowed_domains = ['gmail.com', 'yahoo.com'];
    $email_parts = explode("@", $email);
    $domain = count($email_parts) == 2 ? $email_parts[1] : "";

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (!in_array($domain, $allowed_domains)) {
        $error = "Only Gmail and Yahoo addresses are allowed!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = "user"; // Default role

        // Check if email already exists
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            $error = "Email is already registered!";
        } else {
            // Insert new user with default role
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Signup successful! You can now login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $stmt->close();
        }
        $check_email->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Signup</title>
    <style>
        /* Google Font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f4f4f4;
        }

        .container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            color: #333;
        }

        h2 {
            font-size: 26px;
            margin-bottom: 15px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .button {
            width: 100%;
            padding: 12px;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            color: white;
            background: #007bff;
        }

        .button:hover {
            background: #0056b3;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        p {
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Signup</h2>

        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

        <form method="POST" action="">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>

            <button type="submit" class="button">Signup</button>
        </form>

        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>

</body>
</html>
