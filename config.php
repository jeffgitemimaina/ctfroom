<?php
session_start();

$host = getenv('MYSQL_HOST') ?: 'judge-mysql-judge-mysql.l.aivencloud.com';
$db = getenv('MYSQL_DATABASE') ?: 'defaultdb';
$user = getenv('MYSQL_USER') ?: 'avnadmin';
$pass = getenv('MYSQL_PASSWORD') ?: 'AVNS_Y4Hy2MeZg6KSrviWwhI';
$port = getenv('MYSQL_PORT') ?: '21509';

$conn = "mysql:host=$host;port=$port;dbname=$db;sslmode=verify-ca;sslrootcert=/var/www/html/ssl/ca.pem";

try {
    $pdo = new PDO($conn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    logAction("Database connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
}

function logAction($message) {
    $logFile = __DIR__ . '/logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (!file_put_contents($logFile, "[$timestamp] [$ip] $message\n", FILE_APPEND)) {
        error_log("Failed to write to $logFile");
    }
}

$logDir = __DIR__ . '/logs';
$rateLimitDir = __DIR__ . '/logs/rate_limit';
if (!is_dir($logDir)) mkdir($logDir, 0755, true);
if (!is_dir($rateLimitDir)) mkdir($rateLimitDir, 0755, true);
?>