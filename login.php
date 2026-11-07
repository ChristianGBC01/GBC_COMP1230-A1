<?php
require 'header.php';
$pdo = require 'db.php';

$error = '';
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username']?? '';
    $password = $_POST['password']?? '';

    $stmt = $pdo->prepare("SELECT id, display_name, password_hash FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['display_name'] = $user['display_name'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Username or password are incorrect.";
    }
}
?>

    <h1>Log in</h1>

    <div class="test-accounts-box">
        <p><strong>Test Accounts</strong></p>
        <p>Password for both: Study-1230</p>
        <ul>
            <li>tester1</li>
            <li>tester2</li>
        </ul>
    </div>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

    <form action="login.php" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Log in</button>
    </form>

<?php
require 'footer.php';
?>