<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "library_db";
$port = 3306;

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

?>