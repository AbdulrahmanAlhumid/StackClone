<?php
// Modern-compatible version using mysqli
$host = "localhost";
$user = "root";
$password = "";
$dbname = "qna_site";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
