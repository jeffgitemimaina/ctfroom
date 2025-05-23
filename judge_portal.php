<?php
require 'config.php';
$judges = $pdo->query("SELECT * FROM judges")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
$selected_judge = isset($_GET['judge_id']) ? (int)$_GET['judge_id'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Judge Portal</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Judge Portal - Assign Scores</h2>
    <form action="judge_portal.php" method="GET">
        <select name="judge_id" onchange="this.form.submit()">
            <option value="">Select Judge</option>
            <?php foreach ($judges as $judge): ?>
            <option value="<?php echo $judge['id']; ?>" <?php echo $selected_judge == $judge['id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($judge['display_name']); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </form>
    <?php if ($selected_judge): ?>
    <h3>Score Users</h3>
    <table>
        <tr><th>User</th><th>Action</th></tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td>
                <form action="add_score.php" method="POST">
                    <input type="hidden" name="judge_id" value="<?php echo $selected_judge; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <input type="number" name="points" min="1" max="100" required>
                    <button type="submit">Submit Score</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</body>
</html>