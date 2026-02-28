<?php
session_start();
require 'db_connect.php';

// If the user is logged in, clear the remember token in the database
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

// Clear the remember_me cookie
if (isset($_COOKIE['remember_me'])) {
    setcookie("remember_me", "", time() - 3600, "/"); 
}

session_unset();

session_destroy();

header(header: "location: login.php");
exit();