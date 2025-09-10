<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Show success message if redirected from create.php
if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Session timeout handling (5 minutes = 300 seconds)
$inactive = 300;

if (isset($_SESSION['last_activity'])) {
    $session_life = time() - $_SESSION['last_activity'];
    if ($session_life > $inactive) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit();
    }
}
$_SESSION['last_activity'] = time();

// Fetch users from database
$result = $conn->query("SELECT * FROM user");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
            background-color: #f5f7fb;
            color: var(--dark);
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        h2 {
            color: var(--primary);
            font-weight: 600;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--gray);
        }
        
        .user-info .welcome {
            font-style: italic;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background: #c1121f;
            transform: translateY(-2px);
        }
        
        .btn-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .btn-edit {
            background: var(--success);
            color: white;
        }
        
        .btn-delete {
            background: var(--danger);
            color: white;
        }
        
        .table-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        th {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            font-weight: 500;
            position: sticky;
            top: 0;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e9ecef;
        }
        
        .actions {
            white-space: nowrap;
            display: flex;
            gap: 8px;
        }
        
        #logout-warning {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px 20px;
            border-left: 4px solid var(--warning);
            border-radius: 8px;
            box-shadow: var(--box-shadow);
            display: none;
            z-index: 1000;
            animation: slideInRight 0.3s ease;
            max-width: 350px;
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .warning-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .warning-icon {
            color: var(--warning);
            font-size: 24px;
        }
        
        .warning-text {
            flex: 1;
        }
        
        .countdown {
            font-weight: 600;
            color: var(--danger);
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #c3e6cb;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .user-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            th, td {
                padding: 12px 15px;
            }
            
            .actions {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div id="logout-warning">
        <div class="warning-content">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="warning-text">
                You will be logged out due to inactivity in <span id="countdown" class="countdown">5</span> seconds.
            </div>
        </div>
    </div>

    <div class="container">
        <div class="header-container">
            <h2>User Management System</h2>
            <div class="user-info">
                <div class="welcome">
                    Welcome, <?php echo $_SESSION['gname'] . ' ' . $_SESSION['lname']; ?>!
                </div>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <a href="create.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Add New User
        </a>
        
        <br><br>

        <div class="table-container">
            <table id="userTable">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Password Hash</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr id='row-{$row['userID']}'>
                                <td>{$row['userID']}</td>
                                <td>{$row['Lname']}, {$row['Gname']} {$row['MI']}.</td>
                                <td>{$row['Username']}</td>
                                <td title='{$row['Password']}'>" . substr($row['Password'], 0, 20) . "...</td>
                                <td>{$row['Created_at']}</td>
                                <td class='actions'>
                                    <a href='update.php?id={$row['userID']}' class='btn btn-action btn-edit'>
                                        <i class='fas fa-edit'></i> Edit
                                    </a>
                                    <a href='#' onclick=\"return localDelete('{$row['userID']}');\" class='btn btn-action btn-delete'>
                                        <i class='fas fa-trash'></i> Delete
                                    </a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Delete function with localStorage persistence
        function localDelete(id) {
            if (confirm("Are you sure you want to delete this user?")) {
                const row = document.getElementById("row-" + id);
                if (row) {
                    row.style.opacity = "0.5";
                    row.style.transition = "opacity 0.3s ease";
                    
                    setTimeout(() => {
                        row.remove(); // Remove from DOM
                    }, 300);

                    // Save deleted ID to localStorage
                    let deleted = JSON.parse(localStorage.getItem("deletedUsers")) || [];
                    if (!deleted.includes(id)) {
                        deleted.push(id);
                        localStorage.setItem("deletedUsers", JSON.stringify(deleted));
                    }
                }
            }
            return false; // Prevent redirect
        }

        // Apply deletion on page load
        window.onload = function() {
            // Session timeout system start
            startTimer();

            // Remove all previously deleted users
            let deleted = JSON.parse(localStorage.getItem("deletedUsers")) || [];
            deleted.forEach(function(id) {
                const row = document.getElementById("row-" + id);
                if (row) {
                    row.remove();
                }
            });
        };

        let logoutTimer;
        let countdownTimer;
        const inactivityTime = 5000; // 5 seconds inactivity before warning
        const countdownTime = 5;     // 5 seconds countdown before logout

        function startTimer() {
            clearTimeout(logoutTimer);
            clearInterval(countdownTimer);
            document.getElementById('logout-warning').style.display = 'none';
            logoutTimer = setTimeout(showLogoutWarning, inactivityTime);
        }

        function showLogoutWarning() {
            let seconds = countdownTime;
            const warningElement = document.getElementById('logout-warning');
            const countdownElement = document.getElementById('countdown');

            warningElement.style.display = 'block';
            countdownElement.textContent = seconds;

            countdownTimer = setInterval(function() {
                seconds--;
                countdownElement.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(countdownTimer);
                    window.location.href = 'logout.php';
                }
            }, 1000);
        }

        function resetTimer() {
            startTimer();
        }

        // Reset timer on user activity
        document.addEventListener('mousemove', resetTimer);
        document.addEventListener('keypress', resetTimer);
        document.addEventListener('click', resetTimer);
        document.addEventListener('scroll', resetTimer);
        document.addEventListener('touchstart', resetTimer);
    </script>
</body>
</html>