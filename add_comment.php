<?php
session_start();
include("includes/db_connect.php");


if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$content = mysqli_real_escape_string($conn, $_POST["content"]);

$question_id = isset($_POST["question_id"]) ? intval($_POST["question_id"]) : "NULL";
$answer_id = isset($_POST["answer_id"]) ? intval($_POST["answer_id"]) : "NULL";

// Basic protection to avoid invalid use
if ($question_id == "NULL" && $answer_id == "NULL") {
    die("Invalid request.");
}

$sql = "INSERT INTO comments (question_id, answer_id, user_id, content)
        VALUES ($question_id, $answer_id, $user_id, '$content')";

mysqli_query($conn, $sql);

if ($question_id != "NULL") {
    header("Location: view_question.php?id=$question_id");
} else {
    // find the question for that answer
    $qsql = "SELECT question_id FROM answers WHERE id=$answer_id LIMIT 1";
    $qres = mysqli_query($conn, $qsql);
    $qrow = mysqli_fetch_assoc($qres);
    header("Location: view_question.php?id=" . $qrow["question_id"]);
}
exit;
?>
