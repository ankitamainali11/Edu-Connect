<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../db_connect.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Admin Credentials (Predefined)
    $admin_email = "admin@educonnect.com"; // Change if needed
    $admin_password = "admin123"; // Change if needed

    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['user_id'] = 1; // Admin ID (Fixed)
        $_SESSION['role'] = 'admin';
        header("Location: ../admin/admin_dashboard.php");
        exit();
    } else {
        // Check User in Database
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role; // Set session role dynamically

                // Redirect based on role
                if ($role === 'admin') {
                    header("Location: ../admin/admin_dashboard.php");
                } else {
                    header("Location: ../user/user_dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "No account found with this email!";
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
    <title>EduConnect - Login</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(53, 101, 152);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 320px;
            text-align: center;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #333;
        }

        p {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }

        input {
            width: calc(100% - 16px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input:focus {
            border-color: #555;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            cursor: pointer;
            border-radius: 6px;
            margin-top: 10px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>EduConnect</h2>

        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign In</button>
        </form>

        <p>Don't have an account? <a href="signup.php">Signup</a></p>
    </div>

</body>
</html>
