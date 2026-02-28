<?php
session_start();
require '../db_connect.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../error.php?code=403"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $announcement_id = $_POST['announcement_id'];
    $title = $_POST['ann_title'];
    $message = $_POST['ann_message'];

    $stmt = $conn->prepare("UPDATE announcements SET title = ?, message = ? WHERE announcement_id = ?");

    $stmt->bind_param("ssi", $title, $message, $announcement_id);

    if ($stmt->execute()) {
        header("Location: ../index.php?msg=updated");
        exit();
    } else {
        echo "Error updating announcement: " . $stmt->error;
    }

    $stmt->close();
} else {
    header("Location: ../index.php");
    exit();
}