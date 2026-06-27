<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "formative6";

$connection = mysqli_connect($host, $username, $password);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_query($connection, "CREATE DATABASE IF NOT EXISTS $database");
mysqli_select_db($connection, $database);

$createTable = "CREATE TABLE IF NOT EXISTS dogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    breed VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    color VARCHAR(100) NOT NULL,
    height DECIMAL(6,2) NOT NULL,
    weight DECIMAL(6,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($connection, $createTable);
?>
