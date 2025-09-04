<?php
$host = "localhost";
$user = "root"; // change if using different user
$pass = "";     // change if password is set
$dbname = "student";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>