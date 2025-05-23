<?php
require 'config.php';
require 'csrf.php';
$judges = $pdo->query("SELECT * FROM judges")->fetchAll(PDO::FETCH_ASSOC);
$csrf_token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Judge Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Admin Panel - Manage Judges</h2>
    <form action="add_judge.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="text" name="username" placeholder="Username" required pattern="[A-Za-z0-9]{3,50}">
        <input type="text" name="display_name" placeholder="Display Name" required>
        <button type="submit">Add Judge</button>
    </form>
    <h3>Judges List</h3>
    <table>
        <tr><th>ID</th><th>Username</th><th>Display Name</th></tr>
        <?php foreach ($judges as $judge): ?>
        <tr>
            <td><?php echo htmlspecialchars($judge['id']); ?></td>
            <td><?php echo htmlspecialchars($judge['username']); ?></td>
            <td><?php echo htmlspecialchars($judge['display_name']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>