<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
echo "<h1>Selamat Datang di Dashboard</h1>";
?>
<a href="logout.php">Logout</a>