<?php
// Database Connection for Railway
$host = getenv('MYSQLHOST') ?: 'localhost';
$dbusername = getenv('MYSQLUSER') ?: 'root';
$dbpassword = getenv('MYSQLPASSWORD') ?: '';
$databasename = getenv('MYSQLDATABASE') ?: 'railway';
$port = getenv('MYSQLPORT') ?: 3306;

$conn = mysqli_connect($host, $dbusername, $dbpassword, $databasename, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
