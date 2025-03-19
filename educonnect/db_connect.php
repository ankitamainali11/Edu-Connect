<?php
$servername = "localhost"; 
$username = "root"; 
$password = "123"; 
$database = "educonnect_db"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
