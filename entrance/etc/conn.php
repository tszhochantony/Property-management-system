<?php
$server_name = 'localhost';
$username = 'root';
$password = '';
$db = 'fyp';

$conn = mysqli_connect($server_name, $username, $password, $db);
if (mysqli_connect_errno()) {
	$error_msg .= 'Failed to connect to the database.';
	exit();
}
 ?>
