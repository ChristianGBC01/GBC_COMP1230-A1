<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = require 'db.php';

$course_code = "";
$title = "";
$description = "";
$meets_at = "";
$location = "";
$capacity = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_code = trim($_POST["course_code"] ?? '');
    $title       = trim($_POST["title"] ?? '');
    $description = trim($_POST["description"] ?? '');
    $meets_at    = trim($_POST["meets_at"] ?? '');
    $location    = trim($_POST["location"] ?? '');
    $capacity    = trim($_POST["capacity"] ?? '');

    if (!preg_match('/^[A-Za-z]{4}\d{4}$/', $course_code)) {
        $error = "Course code must be 4 letters and 4 digits, like COMP1230.";
    } else {
        $course_code = strtoupper($course_code);
    }

    if (!$error && (mb_strlen($title) < 3 || mb_strlen($title) > 80)) {
        $error = "Title must be 3-80 characters.";
    }

    if (!$error && ($description === '' || mb_strlen($description) > 1000)) {
        $error = "Description is required and cannot exceed 1000 characters.";
    }

    if (!$error) {
        $validDate = false;
        if (strlen($meets_at) === 16 && $meets_at[4] === '-' && $meets_at[7] === '-'
                && $meets_at[10] === 'T' && $meets_at[13] === ':') {
            $year   = substr($meets_at, 0, 4);
            $month  = substr($meets_at, 5, 2);
            $day    = substr($meets_at, 8, 2);
            $hour   = substr($meets_at, 11, 2);
            $minute = substr($meets_at, 14, 2);

            if (ctype_digit($year) && ctype_digit($month) && ctype_digit($day)
                    && ctype_digit($hour) && ctype_digit($minute)
                    && checkdate((int)$month, (int)$day, (int)$year)
                    && (int)$hour <= 23 && (int)$minute <= 59) {
                $validDate = true;
            }
        }
        if (!$validDate) {
            $error = "Please enter a valid date and time.";
        }
    }

    if (!$error && ($location === '' || mb_strlen($location) > 80)) {
        $error = "Location is required and cannot exceed 80 characters.";
    }

    if (!$error && (!ctype_digit($capacity) || $capacity < 2 || $capacity > 10)) {
        $error = "Capacity must be between 2 and 10.";
    }

    if (!$error) {
        $forDatabase = str_replace('T', ' ', $meets_at) . ':00';

        $sql = "INSERT INTO study_groups (owner_id, course_code, title, description, meets_at, location, capacity) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
                $_SESSION['user_id'],
                $course_code,
                $title,
                $description,
                $forDatabase,
                $location,
                $capacity
        ]);
        header("Location: index.php");
        exit;
    }
}

require 'header.php';
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