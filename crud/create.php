


<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
</head>
<body>
    <h2>Add User</h2>
    <form method="POST" action="">
        <label>Last Name:</label><br>
        <input type="text" name="lname" required><br><br>
        
        <label>Given Name:</label><br>
        <input type="text" name="gname" required><br><br>
        
        <label>Middle Initial:</label><br>
        <input type="text" name="mi" maxlength="2" required><br><br>
        
        <label>Username:</label><br>
        <input type="text" name="uname" required><br><br>
        
        <label for="pw">Password</label><br>
        <input id="pw" name="password" type="password" required minlength="8"
        autocomplete="new-password" placeholder="At least 8 characters"><br><br> 
        
        <input type="submit" name="submit" value="Add User">
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $lname = $_POST['lname'];
        $gname = $_POST['gname'];
        $mi = $_POST['mi'];
        $uname = $_POST['uname'];
        $pwd = $_POST['password'];
        $hash = password_hash($pwd, PASSWORD_DEFAULT);

        $sql = "INSERT INTO user (userID, Lname, Gname, MI, Username, Password, Created_at) 
                VALUES (NULL, '$lname', '$gname', '$mi', '$uname', '$hash', NOW())";

        if ($conn->query($sql)) {
            echo "User added successfully! <a href='read.php'>View Users</a>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
    ?>
</body>
</html>
