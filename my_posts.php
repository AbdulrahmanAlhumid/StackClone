<?php
session_start();
include("includes/db_connect.php");
$page_title = "View Question";
 include("includes/header.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
?>

<html>
<head>
    <title>My Questions and Answers</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h2>My Questions</h2>
    <ul>
    <?php
    $qsql = "SELECT id, title, created_at FROM questions WHERE user_id = $user_id ORDER BY created_at DESC";
    $qres = mysqli_query($conn, $qsql);
    while ($row = mysqli_fetch_assoc($qres)) {
        echo '<li><a href="view_question.php?id=' . $row["id"] . '">' . htmlspecialchars($row["title"]) . '</a> <small>(' . $row["created_at"] . ')</small></li>';
    }
    ?>
    </ul>

    <h2>My Answers</h2>
    <ul>
    <?php
    $asql = "SELECT a.content, a.created_at, q.id AS qid, q.title
             FROM answers a
             JOIN questions q ON a.question_id = q.id
             WHERE a.user_id = $user_id
             ORDER BY a.created_at DESC";
    $ares = mysqli_query($conn, $asql);
    while ($row = mysqli_fetch_assoc($ares)) {
        echo '<li><a href="view_question.php?id=' . $row["qid"] . '">' . htmlspecialchars($row["title"]) . '</a><br />';
        echo '<small>' . nl2br(htmlspecialchars($row["content"])) . '</small><br />';
        echo '<small>Posted on ' . $row["created_at"] . '</small></li><br />';
    }
    ?>
    </ul>
    <br><hr>
    <p><a href="index.php"><button>← Back to Home</button></a></p>
</body>
</html>
