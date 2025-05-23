<?php
require 'config.php';
require 'csrf.php';
require 'rate_limit.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        logAction("CSRF validation failed for add_judge");
        die("Invalid CSRF token.");
    }

    // Rate limiting (10 requests per minute per IP)
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!checkRateLimit('add_judge', $ip, 10, 60)) {
        logAction("Rate limit exceeded for add_judge by IP: $ip");
        die("Too many requests. Please try again later.");
    }

    $username = $_POST['username'];
    $display_name = $_POST['display_name'];

    if (strlen($username) < 3 || strlen($display_name) < 1) {
        logAction("Invalid input for add_judge: username=$username");
        die("Invalid input.");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO judges (username, display_name) VALUES (?, ?)");
        $stmt->execute([$username, $display_name]);
        logAction("Judge added: username=$username, display_name=$display_name");
        header("Location: admin.php");
        exit;
    } catch (PDOException $e) {
        logAction("Error adding judge: " . $e->getMessage());
        die("Error adding judge: " . $e->getMessage());
    }
}
?>