<?php
session_start();
include("includes/db_connect.php");
$page_title = "Edit Question";
 include("includes/header.php");

if (!isset($_GET["id"])) {
    die("No question selected.");
}

$question_id = intval($_GET["id"]);
$message = "";

// Get question
$sql = "SELECT * FROM questions WHERE id = $question_id";
$res = mysqli_query($conn, $sql);
$question = mysqli_fetch_assoc($res);

if (!$question) {
    die("Question not found.");
}

// Check ownership
if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] != $question["user_id"]) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_title = mysqli_real_escape_string($conn, $_POST["title"]);
    $new_content = mysqli_real_escape_string($conn, $_POST["content"]);

    $update = "UPDATE questions SET title = '$new_title', content = '$new_content' WHERE id = $question_id";
    if (mysqli_query($conn, $update)) {
        header("Location: view_question.php?id=$question_id");
        exit;
    } else {
        $message = "Failed to update question.";
    }
}
?>

<html>
<head><title>Edit Question</title>
<link rel="stylesheet" type="text/css" href="css/style.css">

</head>
<body>
    <h2>Edit Your Question</h2>
    <?php if ($message) echo "<p><strong>$message</strong></p>"; ?>
    <form method="post">
        <label>Title:</label><br />
        <input type="text" name="title" value="<?php echo htmlspecialchars($question["title"]); ?>" required /><br /><br />

        <label>Content:</label><br />
        <textarea name="content" rows="8" cols="60" required><?php echo htmlspecialchars($question["content"]); ?></textarea><br /><br />

        <input type="submit" value="Update Question" />
    </form>
    <p><a href="view_question.php?id=<?php echo $question_id; ?>">Cancel</a></p>

    <br><hr>
    <p><a href="index.php"><button>← Back to Home</button></a></p>
</body>
</html>
