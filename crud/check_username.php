<?php
include 'db.php';

if (isset($_POST['username'])) {
    $username = trim($_POST['username']);
    
    // Check if username exists
    $stmt = $conn->prepare("SELECT userID FROM user WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo json_encode(['available' => false]);
    } else {
        echo json_encode(['available' => true]);
    }
    $stmt->close();
} else {
    echo json_encode(['available' => false]);
}

$conn->close();
?>