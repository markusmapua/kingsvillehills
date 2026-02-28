<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db_connect.php';

// Check if user is not logged in but has a remember_me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['first_name'] . " " . $user['last_name'];
    } else {
        setcookie("remember_me", "", time() - 3600, "/"); // Clear invalid cookie
    }
    $stmt->close();
}

// If the user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header(header: "location: login.php");
    exit();
}