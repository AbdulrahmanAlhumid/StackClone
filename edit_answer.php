<?php
session_start();
include("includes/db_connect.php");
$page_title = "Edit Answer";
include("includes/header.php");


if (!isset($_GET["id"])) {
    die("No answer selected.");
}

$answer_id = intval($_GET["id"]);
$message = "";

// Fetch answer
$sql = "SELECT * FROM answers WHERE id = $answer_id";
$res = mysqli_query($conn, $sql);
$answer = mysqli_fetch_assoc($res);

if (!$answer) {
    die("Answer not found.");
}

// Check ownership
if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] != $answer["user_id"]) {
    die("Unauthorized.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_content = mysqli_real_escape_string($conn, $_POST["content"]);
    $update = "UPDATE answers SET content = '$new_content' WHERE id = $answer_id";
    if (mysqli_query($conn, $update)) {
        header("Location: view_question.php?id=" . $answer["question_id"]);
        exit;
    } else {
        $message = "Failed to update answer.";
    }
}
?>

<html>
<head><title>Edit Answer</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h2>Edit Your Answer</h2>
    <?php if ($message) echo "<p><strong>$message</strong></p>"; ?>
    <form method="post">
        <textarea name="content" rows="8" cols="60" required><?php echo htmlspecialchars($answer["content"]); ?></textarea><br /><br />
        <input type="submit" value="Update Answer" />
    </form>
    <p><a href="view_question.php?id=<?php echo $answer["question_id"]; ?>">Cancel</a></p>

    
</body>
</html>
