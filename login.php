<?php
session_start();
include("includes/db_connect.php");
$page_title = "Login";
include("includes/header.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $hashed = md5($password);

    $sql = "SELECT id FROM users WHERE username='$username' AND password='$hashed'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION["user_id"] = $row["id"];
        $_SESSION["username"] = $username;
        header("Location: index.php");
        exit;
    } else {
        $message = "Invalid username or password.";
    }
}
?>

<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h2>User Login</h2>
    <?php if ($message != "") echo "<p><strong>$message</strong></p>"; ?>
    <form action="login.php" method="post">
        <label>Username:</label><br />
        <input type="text" name="username" required style="width: 30%;"/><br /><br />
        
        <label>Password:</label><br />
        <input type="password" name="password" required style="width: 30%;" /><br /><br />
        
        <input type="submit" value="Login" />
    </form>
</body>
</html>
