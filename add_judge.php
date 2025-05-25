<?php
session_start();
require 'config.php';
require 'csrf.php';
require 'rate_limit.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        logAction("CSRF validation failed for add_judge");
        $errors[] = "Invalid CSRF token.";
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!checkRateLimit('add_judge', $ip, 10, 60)) {
            logAction("Rate limit exceeded for add_judge by IP: $ip");
            $errors[] = "Too many requests. Try again later.";
        } else {
            if (isset($_POST['action']) && $_POST['action'] === 'delete') {
                // Delete judge
                $judge_id = filter_input(INPUT_POST, 'judge_id', FILTER_VALIDATE_INT);
                if ($judge_id) {
                    try {
                        $stmt = $pdo->prepare("DELETE FROM judges WHERE id = ?");
                        $stmt->execute([$judge_id]);
                        logAction("Judge deleted: id=$judge_id");
                        $success = "Judge deleted successfully.";
                    } catch (PDOException $e) {
                        logAction("Error deleting judge: " . $e->getMessage());
                        $errors[] = "Error deleting judge: " . $e->getMessage();
                    }
                } else {
                    $errors[] = "Invalid judge ID.";
                }
            } else {
                // Add judge
                $username = trim($_POST['username'] ?? '');
                $display_name = trim($_POST['display_name'] ?? '');

                if (strlen($username) < 3 || strlen($display_name) < 1) {
                    $errors[] = "Username must be at least 3 characters, and display name cannot be empty.";
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO judges (username, display_name) VALUES (?, ?)");
                        $stmt->execute([$username, $display_name]);
                        logAction("Judge added: username=$username, display_name=$display_name");
                        $success = "Judge added successfully.";
                    } catch (PDOException $e) {
                        logAction("Error adding judge: " . $e->getMessage());
                        $errors[] = "Error adding judge: " . $e->getMessage();
                    }
                }
            }
        }
    }
}

// Fetch judges for display
try {
    $stmt = $pdo->query("SELECT id, username, display_name FROM judges");
    $judges = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    logAction("Error fetching judges: " . $e->getMessage());
    $errors[] = "Error fetching judges.";
    $judges = [];
}

$csrf_token = generateCsrfToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Judge</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .error { color: red; }
        .success { color: green; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th { background-color: #f2f2f2; }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .delete-btn:hover { background-color: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Judge</h2>
        <?php if ($errors): ?>
            <?php foreach ($errors as $error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="display_name">Display Name:</label>
                <input type="text" id="display_name" name="display_name" required>
            </div>
            <button type="submit">Add Judge</button>
        </form>

        <h3>Existing Judges</h3>
        <?php if ($judges): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Display Name</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($judges as $judge): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($judge['id']); ?></td>
                        <td><?php echo htmlspecialchars($judge['username']); ?></td>
                        <td><?php echo htmlspecialchars($judge['display_name']); ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this judge?');">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="judge_id" value="<?php echo $judge['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No judges found.</p>
        <?php endif; ?>
        <p><a href="index.php">Back to Home</a></p>
    </div>
</body>
</html>