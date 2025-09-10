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
    $username = trim($_POST['username'] ?? '');
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
                $_SESSION['last_activity'] = time();
                
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
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
        }
        
        .login-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-header {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
        }
        
        .login-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--success), var(--warning));
        }
        
        .login-header h2 {
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .login-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .login-body {
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
            padding: 14px 15px 14px 45px;
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
        
        .form-group i {
            position: absolute;
            left: 15px;
            top: 43px;
            color: var(--gray);
            font-size: 18px;
        }
        
        .password-container {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 14px;
            cursor: pointer;
            color: var(--gray);
            background: transparent;
            border: none;
            font-size: 18px;
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
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            margin: 15px 0;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            border: 1px solid #f5c6cb;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: var(--gray);
        }
        
        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 576px) {
            .login-container {
                max-width: 100%;
            }
            
            .login-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Welcome Back</h2>
            <p>Sign in to your account</p>
        </div>
        
        <div class="login-body">
            <?php if (!empty($error_message)): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Username:</label>
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" required placeholder="Enter your username">
                </div>

                <div class="form-group">
                    <label>Password:</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" required placeholder="Enter your password">
                    <button type="button" class="toggle-password" id="togglePassword">
                        <i class="far fa-eye"></i>
                    </button>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="register-link">
                Don't have an account? <a href="create.php">Register here</a>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        togglePassword.addEventListener("click", function () {
            const type = passwordInput.type === "password" ? "text" : "password";
            passwordInput.type = type;
            this.innerHTML = type === "password" ? '<i class="far fa-eye"></i>' : '<i class="far fa-eye-slash"></i>';
        });
    </script>
</body>
</html>