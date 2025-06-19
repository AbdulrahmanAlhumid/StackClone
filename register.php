<?php
include("includes/db_connect.php");
$page_title = "Register";
include("includes/header.php");

$username = "";
$email = "";
$password = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $hashed = md5($password); // simple 2009-style hashing

    // Check if username or email is already taken
    $check_sql = "SELECT username, email FROM users WHERE username='$username' OR email='$email'";
    $result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        $existing = mysqli_fetch_assoc($result);
        if ($existing['username'] === $username) {
            $message = "Username already exists!";
        } elseif ($existing['email'] === $email) {
            $message = "Email is already registered!";
        } else {
            $message = "Username or email already in use!";
        }
    } else {
        $insert_sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed')";
        if (mysqli_query($conn, $insert_sql)) {
            $message = "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h2>User Registration</h2>
    <?php if ($message != "") echo "<p><strong>$message</strong></p>"; ?>
    <form action="register.php" method="post">
        <label>Username:</label><br />
        <input type="text" name="username" required style="width: 30%;"  /><br /><br />
        
        <label>Email:</label><br />
        <input type="email" name="email" required style="width: 30%;" /><br /><br />
        
        <label>Password:</label><br />
        <input type="password" name="password" required style="width: 30%;" /><br /><br />
        
        <input type="submit" value="Register" />
    </form>
</body>
</html>
