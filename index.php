<?php
session_start();
include("includes/db_connect.php");
$page_title = "Home";
include("includes/header.php");

$search = "";
$search_sql = "";

if (isset($_GET["search"])) {
    $search = mysqli_real_escape_string($conn, $_GET["search"]);
    $search_sql = "WHERE q.title LIKE '%$search%' OR q.content LIKE '%$search%'";
}

$sql = "SELECT q.id, q.title, u.username, q.created_at
        FROM questions q
        LEFT JOIN users u ON q.user_id = u.id
        $search_sql
        ORDER BY q.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<html>
<head>
    <title>Q&A Home</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h2>Recent Questions</h2>

    <form method="get" action="index.php">
        <input type="text" name="search" style="width: 50%;" placeholder="Search for a question..." value="<?php echo htmlspecialchars($search); ?>" />
        <input type="submit" value="Search" />
    </form>

    <ul>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <li>
            <a href="view_question.php?id=<?php echo $row["id"]; ?>">
                <?php echo htmlspecialchars($row["title"]); ?>
            </a><br />
            <small>By <?php echo htmlspecialchars($row["username"]); ?> on <?php echo $row["created_at"]; ?></small>
        </li>
    <?php endwhile; ?>
    </ul>
</body>
</html>
