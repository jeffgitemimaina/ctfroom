<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $display_name = $_POST['display_name'];

    if (strlen($username) < 3 || strlen($display_name) < 1) {
        die("Invalid input.");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO judges (username, display_name) VALUES (?, ?)");
        $stmt->execute([$username, $display_name]);
        header("Location: admin.php");
        exit;
    } catch (PDOException $e) {
        die("Error adding judge: " . $e->getMessage());
    }
}
?>