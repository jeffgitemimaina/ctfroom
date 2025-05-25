<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge System - Home</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        h1 {
            color: #333;
        }
        .nav-menu {
            margin-top: 20px;
        }
        .nav-menu a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .nav-menu a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Judge System</h1>
        <p>Navigate to the desired section below:</p>
        <div class="nav-menu">
            <a href="admin.php">Admin Dashboard</a>
            <a href="add_judge.php">Add Judge</a>
            <a href="add_score.php">Add Score</a>
            <a href="judge_portal.php">Judge Portal</a>
            <a href="scoreboard.php">Scoreboard</a>
            <a href="manage_users.php">Manage Users</a>
        </div>
    </div>
</body>
</html>