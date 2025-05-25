<?php
session_start();
require 'config.php';
require 'csrf.php';
require 'rate_limit.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        logAction("CSRF validation failed for judge_portal");
        $errors[] = "Invalid CSRF token.";
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!checkRateLimit('judge_portal', $ip, 10, 60)) {
            logAction("Rate limit exceeded for judge_portal by IP: $ip");
            $errors[] = "Too many requests. Try again later.";
        } else {
            // Example: Submit a score (adjust as needed)
            $judge_id = filter_input(INPUT_POST, 'judge_id', FILTER_VALIDATE_INT);
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
            $points = filter_input(INPUT_POST, 'points', FILTER_VALIDATE_INT);

            if (!$judge_id || !$user_id || !$points || $points < 1 || $points > 100) {
                $errors[] = "Invalid input.";
            } else {
                try {
                    $stmt = $pdo->prepare("INSERT INTO scores (judge_id, user_id, points) VALUES (?, ?, ?)");
                    $stmt->execute([$judge_id, $user_id, $points]);
                    logAction("Score added via judge_portal: judge_id=$judge_id, user_id=$user_id, points=$points");
                    $success = "Score submitted successfully.";
                } catch (PDOException $e) {
                    logAction("Error in judge_portal: " . $e->getMessage());
                    $errors[] = "Error submitting score.";
                }
            }
        }
    }
}

try {
    $stmt = $pdo->query("SELECT id, display_name FROM judges");
    $judges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query("SELECT id, name FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Error fetching data.";
    $judges = [];
    $users = [];
}

$csrf_token = generateCsrfToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Portal</title>
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
        select, input[type="number"], button {
            margin: 10px 0;
            padding: 5px;
            width: 100%;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Judge Portal</h2>
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
                <label for="judge_id">Judge:</label>
                <select id="judge_id" name="judge_id" required>
                    <option value="">Select Judge</option>
                    <?php foreach ($judges as $judge): ?>
                        <option value="<?php echo $judge['id']; ?>">
                            <?php echo htmlspecialchars($judge['display_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="user_id">User:</label>
                <select id="user_id" name="user_id" required>
                    <option value="">Select User</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>">
                            <?php echo htmlspecialchars($user['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="points">Points (1-100):</label>
                <input type="number" id="points" name="points" min="1" max="100" required>
            </div>
            <button type="submit">Submit Score</button>
        </form>
        <p><a href="index.php">Back to Home</a></p>
    </div>
</body>
</html>