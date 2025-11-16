<?php
//- Please add your database credentials here
$servername = "localhost";
$username = "your_username"; // Replace with your database username
$password = "your_password"; // Replace with your database password
$dbname = "pet_clinic_pos";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
