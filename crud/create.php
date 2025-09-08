<?php 
session_start();
include 'db.php';

// If user is already logged in, redirect to read.php
if (isset($_SESSION['user_id'])) {
    header("Location: read.php");
    exit();
}

// Process form submission
if (isset($_POST['submit'])) {
    $lname = $_POST['lname'];
    $gname = $_POST['gname'];
    $mi = $_POST['mi'];
    $uname = $_POST['uname'];
    $pwd = $_POST['password'];
    $hash = password_hash($pwd, PASSWORD_DEFAULT);

    // Check if username already exists
    $check_sql = "SELECT * FROM user WHERE Username = '$uname'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $error = "Username already taken!";
    } else {
        $sql = "INSERT INTO user (userID, Lname, Gname, MI, Username, Password, Created_at) 
                VALUES (NULL, '$lname', '$gname', '$mi', '$uname', '$hash', NOW())";

        if ($conn->query($sql)) {
            $success = "User added successfully! <a href='read.php'>View Users</a>";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .header h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .form-container {
            padding: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            border-color: #4b6cb7;
            outline: none;
        }
        
        .availability-status {
            margin-top: 5px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .available {
            color: #28a745;
        }
        
        .taken {
            color: #dc3545;
        }
        
        .checking {
            color: #ffc107;
        }
        
        .status-icon {
            margin-right: 5px;
            font-size: 16px;
        }
        
        button {
            background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 14px 20px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .message {
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
            text-align: center;
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
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        
        .login-link a {
            color: #4b6cb7;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Create User Account</h2>
        </div>
        
        <div class="form-container">
            <?php if (isset($success)): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" id="registrationForm">
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="lname" required>
                </div>
                
                <div class="form-group">
                    <label>Given Name:</label>
                    <input type="text" name="gname" required>
                </div>
                
                <div class="form-group">
                    <label>Middle Initial:</label>
                    <input type="text" name="mi" maxlength="2" required>
                </div>
                
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="uname" id="uname" required autocomplete="off">
                    <div id="usernameStatus" class="availability-status"></div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required minlength="8"
                    autocomplete="new-password" placeholder="At least 8 characters">
                </div>
                
                <button type="submit" name="submit">Add User</button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.getElementById('uname');
            const usernameStatus = document.getElementById('usernameStatus');
            let checkTimeout = null;
            
            // Username availability check
            usernameInput.addEventListener('input', function() {
                const username = this.value.trim();
                
                // Clear previous timeout if exists
                if (checkTimeout) {
                    clearTimeout(checkTimeout);
                }
                
                if (username.length < 3) {
                    usernameStatus.innerHTML = '';
                    return;
                }
                
                usernameStatus.innerHTML = '<span class="status-icon">⏳</span> Checking availability...';
                usernameStatus.className = 'availability-status checking';
                
                // Set a timeout to simulate AJAX request to server
                checkTimeout = setTimeout(() => {
                    // Create AJAX request to check username
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'check_username.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            
                            if (response.available) {
                                usernameStatus.innerHTML = '<span class="status-icon">✅</span> Username is available';
                                usernameStatus.className = 'availability-status available';
                            } else {
                                usernameStatus.innerHTML = '<span class="status-icon">❌</span> Username is already taken';
                                usernameStatus.className = 'availability-status taken';
                            }
                        }
                    };
                    
                    xhr.send('username=' + encodeURIComponent(username));
                }, 800);
            });
        });
    </script>
</body>
</html>