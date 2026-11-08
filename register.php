<?php
session_start();
$pdo = require 'db.php';

$username = '';
$display_name = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $display_name = trim($_POST['display_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!preg_match('/^[a-z0-9_]{3,20}$/', $username)) {
        $error = "Username must be 3-20 characters, only lowercase letters, digits, and underscores.";
    }
    if (!$error && $display_name === '') {
        $error = "Display name is required.";
    }
    if (!$error && mb_strlen($display_name) > 50) {
        $error = "Display name cannot exceed 50 characters.";
    }
    if (!$error && mb_strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    }
    if (!$error && $password !== $password_confirm) {
        $error = "Passwords do not match.";
    }
    if (!$error) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "That username is already taken.";
        }
    }

    if (!$error) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, display_name, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$username, $display_name, $hash]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['display_name'] = $display_name;
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $error = "That username is already taken.";
        }
    }
}

require 'header.php';
?>

    <h1>Register</h1>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

    <form action="register.php" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required><br><br>

        <label>Display Name:</label><br>
        <input type="text" name="display_name" value="<?= htmlspecialchars($display_name) ?>" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="password_confirm" required><br><br>

        <button type="submit">Register</button>
    </form>

<?php
require 'footer.php';
?>