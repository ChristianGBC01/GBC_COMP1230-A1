<?php

require 'header.php';
$pdo = require 'db.php';

$sql = "SELECT study_groups.*, users.display_name
        FROM study_groups
        JOIN users ON study_groups.owner_id = users.id
        ORDER BY study_groups.meets_at ASC";

$stmt = $pdo->query($sql);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <h1>Study Groups</h1>
    <table>
        <thead>
        <tr>
            <th>Course</th>
            <th>Title</th>
            <th>When</th>
            <th>Where</th>
            <th>Capacity</th>
            <th>Created by</th>
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
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    require "footer.php";
    ?>
