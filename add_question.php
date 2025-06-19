<?php
session_start();
include("includes/db_connect.php");
$page_title = "Post a Question";
 include("includes/header.php");

// Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $content = mysqli_real_escape_string($conn, $_POST["content"]);
    $user_id = $_SESSION["user_id"];

    $sql = "INSERT INTO questions (user_id, title, content) VALUES ('$user_id', '$title', '$content')";
    if (mysqli_query($conn, $sql)) {
        $message = "Question posted successfully.";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<html>
<head>
    <title>Post a Question</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h2>Ask a Question</h2>
    <?php if ($message != "") echo "<p><strong>$message</strong></p>"; ?>
    <form action="add_question.php" method="post">
        <label>Title:</label><br />
        <input type="text" name="title" style="width: 50%;" required/><br /><br />
        
        <label>Content:</label><br />
        <textarea name="content" rows="8" cols="60" required></textarea><br /><br />
        
        <input type="submit" value="Post Question" />
    </form>
    <br><hr>
    <p><a href="index.php"><button>← Back to Home</button></a></p>
</body>
</html>
