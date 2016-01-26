<?php
header("Content-Type: text/html; charset=UTF-8");
error_reporting(E_ALL);
ini_set("display_errors", 1);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'xogus2977');
define('DB_NAME', 'bamboo');

$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
if(!$connect) {
	die("Database connection failed: ".mysqli_error());
}

mysqli_select_db($connect, DB_NAME);
mysqli_query($connect, 'set names utf8');
?>