<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';
require 'csrf.php';
require 'rate_limit.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        logAction("CSRF validation failed for manage_users");
        $errors[] = "Invalid CSRF token.";
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!checkRateLimit('manage_users', $ip, 10, 60)) {
            logAction("Rate limit exceeded for manage_users by IP: $ip");
            $errors[] = "Too many requests. Try again later.";
        } else {
            if (isset($_POST['action']) && $_POST['action'] === 'delete') {
                // Delete user
                $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
                if ($user_id) {
                    try {
                        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                        $stmt->execute([$user_id]);
                        logAction("User deleted: id=$user_id");
                        $success = "User deleted successfully.";
                    } catch (PDOException $e) {
                        logAction("Error deleting user: " . $e->getMessage());
                        $errors[] = "Error deleting user: " . $e->getMessage();
                    }
                } else {
                    $errors[] = "Invalid user ID.";
                }
            } else {
                // Add user
                $name = trim($_POST['name'] ?? '');
                if (strlen($name) < 1) {
                    $errors[] = "Name cannot be empty.";
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO users (name) VALUES (?)");
                        $stmt->execute([$name]);
                        logAction("User added: name=$name");
                        $success = "User added successfully.";
                    } catch (PDOException $e) {
                        logAction("Error adding user: " . $e->getMessage());
                        $errors[] = "Error adding user: " . $e->getMessage();
                    }
                }
            }
        }
    }
}

// Fetch users for display
try {
    $stmt = $pdo->query("SELECT id, name FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    logAction("Error fetching users: " . $e->getMessage());
    $errors[] = "Error fetching users.";
    $users = [];
}

$csrf_token = generateCsrfToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
            text-align: center;
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
        input[type="text"], button {
            margin: 10px 0;
            padding: 5px;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Users</h2>
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
                <label for="name">User Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <button type="submit">Add User</button>
        </form>

        <h3>Existing Users</h3>
        <?php if ($users): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
        <p><a href="index.php">Back to Home</a></p>
    </div>
</body>
</html>