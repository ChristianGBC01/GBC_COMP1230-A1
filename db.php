<?php

$host     = 'localhost';
$database = 'f6598964_project';
$user     = 'f6598964_Chris';
$password = 'kurisu123';

return new PDO(
    "mysql:host=$host;dbname=$database;charset=utf8mb4",
    $user,
    $password,
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
);
