<?php
session_start();

$host = getenv('MYSQL_HOST') ?: 'judge-mysql-judge-mysql.l.aivencloud.com';
$db = getenv('MYSQL_DATABASE') ?: 'judge_system'; // Use judge_system
$user = getenv('MYSQL_USER') ?: 'avnadmin';
$pass = getenv('MYSQL_PASSWORD') ?: 'AVNS_Y4Hy2MeZg6KSrviWwhI';
$port = getenv('MYSQL_PORT') ?: '21509';

$conn = "mysql:host=$host;port=$port;dbname=$db;sslmode=verify-ca;sslrootcert=/var/www/html/ssl/ca.pem";

$maxRetries = 5;
$retryDelay = 2; // seconds
for ($i = 0; $i < $maxRetries; $i++) {
    try {
        $pdo = new PDO($conn, $user, $pass, [
            PDO::MYSQL_ATTR_SSL_CA => '/var/www/html/ssl/ca.pem',
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
            PDO::ATTR_TIMEOUT => 10 // 10-second timeout
        ]);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        break; // Connection successful
    } catch (PDOException $e) {
        if ($i === $maxRetries - 1) {
            logAction("Database connection failed after $maxRetries attempts: " . $e->getMessage());
            die("Connection failed: " . $e->getMessage());
        }
        sleep($retryDelay);
    }
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