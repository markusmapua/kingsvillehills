<?php
session_start();

require '../db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../error.php?code=403");
    exit();
}

if (isset($_GET['id'])) {
    $announcement_id = $_GET['id'];

    // If JavaScript accidentally sends 'undefined', stop the code so it doesn't crash the database!
    if ($announcement_id === 'undefined') {
        die("Wait! The JavaScript is sending 'undefined' instead of a real number. Check your dashboard button!");
    }

    $stmt = $conn->prepare("DELETE FROM announcements WHERE announcement_id = ?");
    $stmt->bind_param("i", $announcement_id);

    if ($stmt->execute()) {
        header("Location: ../index.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting announcement: " . $stmt->error;
    }

    $stmt->close();
} else {
    header("Location: ../index.php");
    exit();
}
?>