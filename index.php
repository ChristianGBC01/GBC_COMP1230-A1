<?php
session_start();
$pdo = require 'db.php';

$sql = "SELECT study_groups.*, users.display_name
        FROM study_groups
        JOIN users ON study_groups.owner_id = users.id
        ORDER BY study_groups.meets_at ASC";

$stmt = $pdo->query($sql);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

require 'header.php';
?>

    <h1>Study Groups</h1>

<?php if (isset($_SESSION['user_id'])): ?>
    <p><a href="group-create.php">+ Create a study group</a></p>
<?php endif; ?>

    <table>
        <thead>
        <tr>
            <th>Course</th>
            <th>Title</th>
            <th>When</th>
            <th>Where</th>
            <th>Capacity</th>
            <th>Created by</th>
            <?php if (isset($_SESSION['user_id'])): ?>
                <th>Actions</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($groups as $group) : ?>
            <tr>
                <td><?= htmlspecialchars($group['course_code']) ?></td>
                <td><?= htmlspecialchars($group['title']) ?></td>
                <td><?= htmlspecialchars($group['meets_at']) ?></td>
                <td><?= htmlspecialchars($group['location']) ?></td>
                <td><?= htmlspecialchars($group['capacity']) ?></td>
                <td><?= htmlspecialchars($group['display_name']) ?></td>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <td>
                        <a href="group-edit.php?id=<?= (int)$group['id'] ?>">Edit</a>
                        <form action="group-delete.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= (int)$group['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php
require "footer.php";
?>