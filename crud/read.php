<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
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
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .user-info {
            color: #666;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        a {
            color: #337ab7;
            text-decoration: none;
            margin-right: 10px;
        }
        a:hover {
            text-decoration: underline;
        }
        .logout {
            color: #d9534f;
        }
        .actions {
            white-space: nowrap;
        }
        #logout-warning {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ffcccc;
            padding: 10px 15px;
            border: 1px solid #ff0000;
            border-radius: 5px;
            display: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div id="logout-warning">
        You will be logged out due to inactivity in <span id="countdown">5</span> seconds.
    </div>

    <div class="header-container">
        <h2>User List</h2>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['gname'] . ' ' . $_SESSION['lname']; ?>!
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>

    <a href="create.php">Add New User</a><br><br>

    <table id="userTable">
        <tr>
            <th>User ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Password Hash</th>
            <th>Created At</th>
            <th class="actions">Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM user");
        while ($row = $result->fetch_assoc()) {
            echo "<tr id='row-{$row['userID']}'>
                    <td>{$row['userID']}</td>
                    <td>{$row['Lname']}, {$row['Gname']} {$row['MI']}.</td>
                    <td>{$row['Username']}</td>
                    <td title='{$row['Password']}'>" . substr($row['Password'], 0, 20) . "...</td>
                    <td>{$row['Created_at']}</td>
                    <td class='actions'>
                        <a href='update.php?id={$row['userID']}'>Edit</a>
                        <!-- new update: delete function -->
                        <a href='#' onclick=\"return localDelete('{$row['userID']}');\">Delete</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>

    <script>
        // new update: delete function with localStorage persistence
        function localDelete(id) {
            if (confirm("Are you sure you want to delete this user?")) {
                const row = document.getElementById("row-" + id);
                if (row) {
                    row.remove(); // tanggalin sa DOM

                    // save deleted ID sa localStorage
                    let deleted = JSON.parse(localStorage.getItem("deletedUsers")) || [];
                    if (!deleted.includes(id)) {
                        deleted.push(id);
                        localStorage.setItem("deletedUsers", JSON.stringify(deleted));
                    }
                }
            }
            return false; // para hindi mag-redirect
        }

        // new update: apply deletion kapag nag-refresh
        window.onload = function() {
            // session timeout system start
            startTimer();

            // alisin lahat ng na-delete dati
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

        document.addEventListener('mousemove', resetTimer);
        document.addEventListener('keypress', resetTimer);
        document.addEventListener('click', resetTimer);
        document.addEventListener('scroll', resetTimer);
        document.addEventListener('touchstart', resetTimer);
    </script>
</body>
</html>
