<?php
require 'config.php';
$scoreboard = $pdo->query("
    SELECT u.name, SUM(s.points) as total_points
    FROM users u
    LEFT JOIN scores s ON u.id = s.user_id
    GROUP BY u.id
    ORDER BY total_points DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Public Scoreboard</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        setTimeout(() => location.reload(), 30000); // Auto-refresh every 30 seconds
    </script>
</head>
<body>
    <h2>Public Scoreboard</h2>
    <table>
        <tr><th>Rank</th><th>User</th><th>Total Points</th></tr>
        <?php $rank = 1; foreach ($scoreboard as $entry): ?>
        <tr>
            <td><?php echo $rank++; ?></td>
            <td><?php echo htmlspecialchars($entry['name']); ?></td>
            <td><?php echo (int)$entry['total_points'] ?: 0; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>