<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($page_title)) {
    $page_title = "StackOverflow";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
<div id="header" style="background-color: #004080; padding: 15px; border-radius: 5px;">
    <h1 style="margin: 0; color:#e0e0e0;">StackOverflow<?php if (isset($_SESSION["user_id"])):?>, Welcome <?php echo htmlspecialchars($_SESSION["username"]); endif;?></h1>
</div>

<div id="nav" style="background-color: #e0e0e0; padding: 8px; margin-bottom: 20px; border-radius: 5px;">
    <a href="index.php">Home</a> |
    <?php if (isset($_SESSION["user_id"])): ?>
        <a href="add_question.php">Ask a question</a> |
        <a href="my_posts.php">View Posts</a> |
        <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION["username"]); ?>)</a>
    <?php else: ?>
        <a href="register.php">Register</a> |
        <a href="login.php">Login</a>
    <?php endif; ?>
</div>
