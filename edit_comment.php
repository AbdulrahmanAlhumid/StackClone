<?php
session_start();
include("includes/db_connect.php");

if (!isset($_GET["id"])) {
    die("No comment ID.");
}

$comment_id = intval($_GET["id"]);
$message = "";

// Fetch comment
$sql = "SELECT * FROM comments WHERE id = $comment_id";
$res = mysqli_query($conn, $sql);
$comment = mysqli_fetch_assoc($res);

if (!$comment) {
    die("Comment not found.");
}

// Ensure the logged-in user is the author
if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] != $comment["user_id"]) {
    die("Unauthorized.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_content = mysqli_real_escape_string($conn, $_POST["content"]);
    $update = "UPDATE comments SET content = '$new_content' WHERE id = $comment_id";
    if (mysqli_query($conn, $update)) {
        // Redirect back to question
        $qid = $comment["question_id"];
        if (!$qid && $comment["answer_id"]) {
            $qa_sql = "SELECT question_id FROM answers WHERE id = " . $comment["answer_id"];
            $qa_res = mysqli_query($conn, $qa_sql);
            $qa_row = mysqli_fetch_assoc($qa_res);
            $qid = $qa_row["question_id"];
        }
        header("Location: view_question.php?id=$qid");
        exit;
    } else {
        $message = "Failed to update comment.";
    }
}
?>

<html>
<head><title>Edit Comment</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h2>Edit Comment</h2>
    <?php if ($message) echo "<p><strong>$message</strong></p>"; ?>
    <form method="post">
        <textarea name="content" rows="4" cols="60"><?php echo htmlspecialchars($comment["content"]); ?></textarea><br /><br />
        <input type="submit" value="Save Changes" />
    </form>
    <p><a href="javascript:history.back()">Cancel</a></p>
</body>
</html>
