<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if ID parameter is provided
if (!isset($_GET['id'])) {
    header("Location: read.php");
    exit();
}

$user_id = $_GET['id'];
$error = '';
$success = '';

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM user WHERE userID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // User not found
    header("Location: read.php");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lname = htmlspecialchars(trim($_POST['lname']));
    $gname = htmlspecialchars(trim($_POST['gname']));
    $mi = htmlspecialchars(trim($_POST['mi']));
    $uname = htmlspecialchars(trim($_POST['uname']));
    
    // Check if username is being changed and if it's already taken (excluding current user)
    if ($uname !== $user['Username']) {
        $check_sql = "SELECT * FROM user WHERE Username = ? AND userID != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $uname, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Username already taken!";
        } else {
            $update_sql = "UPDATE user SET Lname = ?, Gname = ?, MI = ?, Username = ? WHERE userID = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssssi", $lname, $gname, $mi, $uname, $user_id);
            
            if ($update_stmt->execute()) {
                $success = "User updated successfully!";
            } else {
                $error = "Error updating user: " . $conn->error;
            }
            $update_stmt->close();
        }
        $check_stmt->close();
    } else {
        // Username not changed, update other fields
        $update_sql = "UPDATE user SET Lname = ?, Gname = ?, MI = ? WHERE userID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $lname, $gname, $mi, $user_id);
        
        if ($update_stmt->execute()) {
            $success = "User updated successfully!";
        } else {
            $error = "Error updating user: " . $conn->error;
        }
        $update_stmt->close();
    }
    
    // Refresh user data
    $stmt = $conn->prepare("SELECT * FROM user WHERE userID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #fca311;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --border-radius: 12px;
            --box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--dark);
        }
        
        .container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .header {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
        }
        
        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--success), var(--warning));
        }
        
        .header h2 {
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .form-container {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
            font-size: 14px;
        }
        
        input {
            width: 100%;
            padding: 14px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: var(--transition);
            background-color: #f8f9fa;
        }
        
        input:focus {
            border-color: var(--primary);
            outline: none;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }
        
        .btn {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 16px 20px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .message {
            padding: 12px 16px;
            margin: 15px 0;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: var(--gray);
        }
        
        .back-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .form-group i {
            position: absolute;
            left: 15px;
            top: 43px;
            color: var(--gray);
            font-size: 18px;
        }
        
        .form-group input {
            padding-left: 45px;
        }
        
        @media (max-width: 576px) {
            .container {
                max-width: 100%;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Edit User</h2>
            <p>Update user information</p>
        </div>
        
        <div class="form-container">
            <?php if ($success): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Last Name:</label>
                    <i class="fas fa-user"></i>
                    <input type="text" name="lname" value="<?php echo $user['Lname']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Given Name:</label>
                    <i class="fas fa-user"></i>
                    <input type="text" name="gname" value="<?php echo $user['Gname']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Middle Initial:</label>
                    <i class="fas fa-user"></i>
                    <input type="text" name="mi" maxlength="2" value="<?php echo $user['MI']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Username:</label>
                    <i class="fas fa-at"></i>
                    <input type="text" name="uname" value="<?php echo $user['Username']; ?>" required>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Update User
                </button>
            </form>
            
            <div class="back-link">
                <a href="read.php"><i class="fas fa-arrow-left"></i> Back to User List</a>
            </div>
        </div>
    </div>
</body>
</html>