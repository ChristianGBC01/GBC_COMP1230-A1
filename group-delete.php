<?php
session_start();
$pdo = require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if ($_SERVER['REQUEST METHOD'] === 'POST' && isset($_POST['id'])) {
    $sql = "DELETE FROM study_groups WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_POST['id']]);
}
header("Location: groups.php");
exit;
