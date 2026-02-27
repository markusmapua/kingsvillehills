<?php
session_start();
require '../db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header(header: "Location: error.php?code=403");
    exit();
}

// Form verification
if (isset($_POST['submit_announcement'])) {
    
    // Security stuff
    $title = mysqli_real_escape_string(mysql: $conn, string: $_POST['ann_title']);
    $message = mysqli_real_escape_string(mysql: $conn, string: $_POST['ann_message']);
    
    $date_posted = date(format: 'Y-m-d H:i:s');

    $query = "INSERT INTO announcements (title, message, date_posted) 
              VALUES ('$title', '$message', '$date_posted')";

    if (mysqli_query(mysql: $conn, query: $query)) {
        header(header: "Location: ../index.php?status=success");
        exit();
    } else {
        echo "Error: " . mysqli_error(mysql: $conn);
    }
}
?>