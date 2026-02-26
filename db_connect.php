<?php
// XAMPP Default Credentials
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "kingsville_db";

// Bridge
$conn = mysqli_connect(hostname: $servername, username: $username, password: $password, database: $dbname);

// Connection test
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}