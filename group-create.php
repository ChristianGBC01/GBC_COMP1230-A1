<?php

require 'header.php';
$pdo = require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
    $course_code="";
    $title="";
    $description="";
    $meets_at="";
    $location="";
    $capacity="";
    $error="";

    if (S_SERVER("REQUEST_METHOD") == "POST") {
        $course_code = $_POST["course_code"] ?? '';
        $title = $_POST["title"] ?? '';
        $description = $_POST["description"] ?? '';
        $meets_at = $_POST["meets_at"] ?? '';
        $location = $_POST["location"] ?? '';
        $capacity = $_POST["capacity"] ?? '';
        $error = $_POST["error"] ?? '';

        if (!preg_match('/^[A-Za-z]{4}\d{4}$/', $course_code)) {
            $error = "Course code must have 4 letter and 4 numbers.";
        } else {
            $course_code = strtoupper($course_code);
        }
        if (!$error && mb_strlen($title) < 3 || mb_strlen($title) > 80) {
            $error = "Title must have 3 - 80 characters.";
        }
        if (!$error && mb_strlen($description) > 1000) {
            $error = "Description exceeds 1000 characters.";
        }
        if (!$error && mb_strlen($location) > 80) {
            $error = "Location exceeds 80 characters.";
        }
        if (!$error && (!filter_var($capacity, FILTER_VALIDATE_INT) || $capacity < 2 || $capacity > 10)) {
            $error = "Capacity must have a number between 2 amd 10.";
        }

        if (!$error) {
            $sql = "INSERT INTO study_groups (owner_id, course_code, title, description, meets_at, location, capacity) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_SESSION['user_id'],
                $course_code,
                $title,
                $description,
                $location,
                $capacity
            ]);
            header("Location: index.php");
            exit;
        }
    }
            ?>

<h1>Create Study Group</h1>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="group-create.php" method="POST">
    <label>Course Code:</label><br>
    <input type="text" name="course_code" value="<?= htmlspecialchars($course_code) ?>" required><br><br>

    <label>Title:</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" required><?= htmlspecialchars($description) ?></textarea><br><br>

    <label>When:</label><br>
    <input type="datetime-local" name="meets_at" value="<?= htmlspecialchars($meets_at) ?>" required><br><br>

    <label>Where:</label><br>
    <input type="text" name="location" value="<?= htmlspecialchars($location) ?>" required><br><br>

    <label>Capacity:</label><br>
    <input type="number" name="capacity" value="<?= htmlspecialchars($capacity) ?>" required><br><br>

    <button type="submit">Create Group</button>
</form>

<?php
require 'footer.php';
?>