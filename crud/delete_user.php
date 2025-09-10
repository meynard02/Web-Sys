<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user ID is provided
if (!isset($_POST['user_id'])) {
    echo "No user ID provided";
    exit();
}

$user_id = $_POST['user_id'];

// Mark user as deleted
$stmt = $conn->prepare("UPDATE user SET is_deleted = TRUE WHERE userID = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo "User deleted successfully!";
} else {
    echo "Failed to delete user: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
