<?php
include 'db.php';

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    
    // Check if username exists
    $sql = "SELECT * FROM user WHERE Username = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo json_encode(['available' => false]);
    } else {
        echo json_encode(['available' => true]);
    }
} else {
    echo json_encode(['available' => false]);
}

$conn->close();
?>