<?php 
// read.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; 
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
    </style>
</head>
<body>
    <div class="header-container">
        <h2>User List</h2>
        <div class="user-info">
            Welcome, <?php echo $_SESSION['gname'] . ' ' . $_SESSION['lname']; ?>! 
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>
    
    <a href="create.php">Add New User</a><br><br>
    
    <table>
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
            echo "<tr>
                    <td>{$row['userID']}</td>
                    <td>{$row['Lname']}, {$row['Gname']} {$row['MI']}.</td>
                    <td>{$row['Username']}</td>
                    <td title='{$row['Password']}'>" . substr($row['Password'], 0, 20) . "...</td>
                    <td>{$row['Created_at']}</td>
                    <td class='actions'>
                        <a href='update.php?id={$row['userID']}'>Edit</a>
                        <a href='delete.php?id={$row['userID']}' onclick=\"return confirm('Are you sure you want to delete this user?');\">Delete</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>
</body>
</html>