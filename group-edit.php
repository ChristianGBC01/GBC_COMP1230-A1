<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = require 'db.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM study_groups WHERE id = ?");
$stmt->execute([$id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    http_response_code(404);
    require 'header.php';
    echo '<h1>Not found</h1>';
    echo '<p class="error">That study group was not found.</p>';
    require 'footer.php';
    exit;
}

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

        $sql = "UPDATE study_groups
                SET course_code = ?, title = ?, description = ?, meets_at = ?, location = ?, capacity = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $course_code,
            $title,
            $description,
            $forDatabase,
            $location,
            $capacity,
            $id
        ]);
        header("Location: index.php");
        exit;
    }
} else {
    $course_code = $group['course_code'];
    $title       = $group['title'];
    $description = $group['description'];
    $meets_at    = str_replace(' ', 'T', substr($group['meets_at'], 0, 16));
    $location    = $group['location'];
    $capacity    = (string)$group['capacity'];
}

require 'header.php';
?>

    <h1>Edit Study Group</h1>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

    <form action="group-edit.php?id=<?= $id ?>" method="POST">
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

        <button type="submit">Save Changes</button>
    </form>

<?php
require 'footer.php';
?>