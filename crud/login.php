<?php
// login.php
include 'db.php';
session_start();

// If user is already logged in, redirect to read.php
if (isset($_SESSION['user_id'])) {
    header("Location: read.php");
    exit();
}

$error_message = "";

// Process form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data safely
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username !== '' && $password !== '') {
        // Query to check user - using prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT userID, Lname, Gname, MI, Username, Password FROM user WHERE Username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify password (using password_verify since create.php uses password_hash)
            if (password_verify($password, $row['Password'])) {
                $_SESSION['user_id'] = $row['userID'];
                $_SESSION['username'] = $row['Username'];
                $_SESSION['gname'] = $row['Gname'];
                $_SESSION['lname'] = $row['Lname'];
                
                // Redirect to read.php
                header("Location: read.php");
                exit();
            } else {
                $error_message = "Invalid password!";
            }
        } else {
            $error_message = "User not found!";
        }
        $stmt->close();
    } else {
        $error_message = "Please enter both username and password!";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: #d9534f;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <input type="submit" value="Login">
        </form>
        
        <div class="register-link">
            Don't have an account? <a href="create.php">Register here</a>
        </div>
    </div>
</body>
</html>