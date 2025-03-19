<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduConnect - Home</title>

    <!-- Internal CSS -->
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
            background-color:rgb(53, 101, 152);

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

        h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        p {
            font-size: 16px;
            font-weight: 300;
            margin-bottom: 20px;
        }

        .button {
            display: block;
            width: 100%;
            padding: 12px;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            text-decoration: none;
            text-align: center;
            color: white;
            margin-bottom: 10px;
        }

        .login {
            background: #007bff;
        }

        .signup {
            background: #28a745;
        }

        .button:hover {
            opacity: 0.8;
        }

    </style>
</head>
<body>

    <div class="container">
        <h1>Welcome to EduConnect</h1>
        <p>Your gateway to school updates, assignments, and exam schedules.</p>
        
        <!-- Login and Signup Buttons -->
        <a href="auth/login.php" class="button login">Login</a>
        <a href="auth/signup.php" class="button signup">Signup</a>
    </div>

</body>
</html>
