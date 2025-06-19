<?php
session_start();
include("includes/db_connect.php");

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized");
}

$answer_id = intval($_POST["answer_id"]);
// Normalize to +1 or -1
$rating  = ($_POST["rating"] == 1) ? 1 : -1;
$user_id = $_SESSION["user_id"];

// 1) Check existing vote
$check_sql    = "
  SELECT id, rating
    FROM ratings
   WHERE user_id   = $user_id
     AND answer_id = $answer_id
";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) === 0) {
    // → No record: INSERT the vote
    $insert_sql = "
      INSERT INTO ratings (answer_id, user_id, rating)
      VALUES ($answer_id, $user_id, $rating)
    ";
    mysqli_query($conn, $insert_sql);

} else {
    // → Record exists
    $row = mysqli_fetch_assoc($check_result);

    if ($row['rating'] === $rating) {
        // Same vote clicked again → do NOTHING (stay at +1 or -1)

    } else {
        // Opposite vote clicked → DELETE existing to go back to 0
        $delete_sql = "
          DELETE FROM ratings
           WHERE id = {$row['id']}
        ";
        mysqli_query($conn, $delete_sql);
    }
}

// 2) Redirect back to the question page
$qid_res     = mysqli_query($conn, "SELECT question_id FROM answers WHERE id = $answer_id");
$qrow        = mysqli_fetch_assoc($qid_res);
$question_id = $qrow["question_id"];

header("Location: view_question.php?id=$question_id");
exit;
?>