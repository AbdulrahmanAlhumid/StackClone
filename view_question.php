
<?php
session_start();
include("includes/db_connect.php");
$page_title = "View Question";
 include("includes/header.php"); 


if (!isset($_GET["id"])) {
    die("Question not found.");
}

$question_id = intval($_GET["id"]);

// Get the question
$qsql = "SELECT q.id, q.title, q.content, q.user_id, u.username, q.created_at
         FROM questions q
         LEFT JOIN users u ON q.user_id = u.id
         WHERE q.id = $question_id";
$qres = mysqli_query($conn, $qsql);
$question = mysqli_fetch_assoc($qres);

// Handle new answer post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["user_id"])) {
    $content = mysqli_real_escape_string($conn, $_POST["content"]);
    $user_id = $_SESSION["user_id"];
    $insert = "INSERT INTO answers (question_id, user_id, content)
               VALUES ('$question_id', '$user_id', '$content')";
    mysqli_query($conn, $insert);
}

// Get answers
$asql = "SELECT a.id, a.content, a.created_at, a.user_id, u.username
         FROM answers a
         LEFT JOIN users u ON a.user_id = u.id
         WHERE a.question_id = $question_id
         ORDER BY a.created_at ASC";
$answers = mysqli_query($conn, $asql);
?>

<html>
<head><title><?php echo htmlspecialchars($question["title"]); ?></title>
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <!-- //display question -->
    <h2><?php echo htmlspecialchars($question["title"]); ?></h2>
    <p><?php echo nl2br(htmlspecialchars($question["content"])); ?></p>
    <small>Asked by <?php echo htmlspecialchars($question["username"]); ?> on <?php echo $question["created_at"]; ?></small>

    <!-- if user is logged in and is the owner of the question, show edit link -->
    <?php if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $question["user_id"]): ?>
        <p><a href="edit_question.php?id=<?php echo $question_id; ?>">Edit Question</a></p>
    <?php endif; ?>

    <hr />
    <!-- //display comments on the question -->
    <h3>Comments on Question</h3>
    <?php
    // Get comments on the question
    $cq_sql = "SELECT c.id, c.content, c.created_at, u.username, c.user_id
               FROM comments c
               LEFT JOIN users u ON c.user_id = u.id
               WHERE c.question_id = $question_id AND c.answer_id IS NULL
               ORDER BY c.created_at ASC";
    $cq_result = mysqli_query($conn, $cq_sql);
    while ($c = mysqli_fetch_assoc($cq_result)) {
        echo "<p>" . htmlspecialchars($c["content"]) .
             "<br /><small>by " . htmlspecialchars($c["username"]) . " at " . $c["created_at"];
        // if user is logged in and is the owner of the comment, show edit link
        if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $c["user_id"]) {
            echo ' | <a href="edit_comment.php?id=' . $c["id"] . '">Edit</a>';
        }
        echo "</small></p>";
    }
    ?>
    <!-- if user is logged in, show add comment form -->
    <?php if (isset($_SESSION["user_id"])): ?>
        <form action="add_comment.php" method="post">
            <input type="hidden" name="question_id" value="<?php echo $question_id; ?>" />
            <textarea name="content" rows="3" cols="50" required placeholder="write a comment.."></textarea><br />
            <input type="submit" value="Add Comment" />
        </form>
    <?php endif; ?>

    <hr />
    <h3>Answers</h3>
    <!-- display answers -->
    <?php while ($ans = mysqli_fetch_assoc($answers)): ?>
        <div style="margin-bottom: 15px; padding: 10px; border-bottom: 1px solid #ccc; border-radius: 5px;">
            <p><?php echo nl2br(htmlspecialchars($ans["content"])); ?></p>
            <small>Answered by <?php echo htmlspecialchars($ans["username"]); ?> on <?php echo $ans["created_at"];
            if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $ans["user_id"]) {
                echo ' | <a href="edit_answer.php?id=' . $ans["id"] . '">Edit</a>';
            }
             ?></small>

            <?php
            $answer_id = $ans["id"];

            // Rating score
            $score_sql = "SELECT SUM(rating) AS total FROM ratings WHERE answer_id = $answer_id";
            $score_res = mysqli_query($conn, $score_sql);
            $score_row = mysqli_fetch_assoc($score_res);
            $total_score = $score_row["total"] ? $score_row["total"] : 0;
            echo "<p><strong>Rating: $total_score</strong></p>";

            // if user is logged in, display rating buttons
            if (isset($_SESSION["user_id"])) {
                echo '<form action="rate_answer.php" method="post" style="display:inline;">
                        <input type="hidden" name="answer_id" value="' . $answer_id . '" />
                        <input type="hidden" name="rating" value="1" />
                        <input type="submit" value="Upvote" />
                      </form>
                      <form action="rate_answer.php" method="post" style="display:inline;">
                        <input type="hidden" name="answer_id" value="' . $answer_id . '" />
                        <input type="hidden" name="rating" value="-1" />
                        <input type="submit" value="Downvote" />
                      </form>';
            }

            // Comments on this answer
            $ca_sql = "SELECT c.id, c.content, c.created_at, u.username, c.user_id
                       FROM comments c
                       LEFT JOIN users u ON c.user_id = u.id
                       WHERE c.answer_id = $answer_id
                       ORDER BY c.created_at ASC";
            $ca_result = mysqli_query($conn, $ca_sql);
            echo "<h4>Comments on Answer</h4>";
            while ($c = mysqli_fetch_assoc($ca_result)) {
                echo "<p>" . htmlspecialchars($c["content"]) .
                     "<br /><small>by " . htmlspecialchars($c["username"]) . " on " . $c["created_at"];
                if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $c["user_id"]) {
                    echo ' | <a href="edit_comment.php?id=' . $c["id"] . '">Edit</a>';
                }
                echo "</small></p>";
            }

            if (isset($_SESSION["user_id"])) {
                echo '<form action="add_comment.php" method="post">
                    <input type="hidden" name="answer_id" value="' . $answer_id . '" />
                    <textarea name="content" rows="3" cols="50" required placeholder="write a comment on this answer..."></textarea><br />
                    <input type="submit" value="Add Comment" />
                </form>';
            }
            ?>
        </div>
    <?php endwhile; ?>
    <!-- if user is logged in, show add answer form -->
    <?php if (isset($_SESSION["user_id"])): ?>
        <h3>Your Answer</h3>
        <form method="post" action="">
            <textarea name="content" rows="3" cols="50" required placeholder="write an answer for the quesiton.."></textarea><br /><br />
            <input type="submit" value="Submit Answer" />
        </form>
    <?php else: ?>
        <p><a href="login.php">Login</a> to submit an answer.</p>
    <?php endif; ?>

    <br><hr>
    <p><a href="index.php"><button>← Back to Home</button></a></p>
</body>
</html>
