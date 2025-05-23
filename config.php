<?php
session_start(); // Required for CSRF
$host = getenv('MYSQL_HOST') ?: 'localhost';
$db = getenv('MYSQL_DATABASE') ?: 'judge_system';
$user = getenv('MYSQL_USER') ?: 'judge_user';
$pass = getenv('MYSQL_PASSWORD') ?: 'secure_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    logAction("Database connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
}

function logAction($message) {
    $logFile = __DIR__ . '/logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    file_put_contents($logFile, "[$timestamp] [$ip] $message\n", FILE_APPEND);
}

// Ensure logs directory exists
$logDir = __DIR__ . '/logs';
$rateLimitDir = __DIR__ . '/logs/rate_limit';
if (!is_dir($logDir)) mkdir($logDir, 0755, true);
if (!is_dir($rateLimitDir)) mkdir($rateLimitDir, 0755, true);
?>