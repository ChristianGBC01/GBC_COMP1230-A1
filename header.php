<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Study Group Finder</title>
    <style>
        body {
            font-family: system-ui;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        nav {
            margin-bottom: 20px;
            font-weight: bold;
        }
        nav a {
            text-decoration: none;
            margin-right: 10px;
        }
        nav a:hover {
            text-decoration: underline;
            background-color: lightgray;
        }
        table {
            width: 100%;
            max-width: 800px;
        }
        th, td {
            border: 1px solid black;
            padding: 12px;
            text-align: left;
            border-radius: 3px;
        }
        tr td a {
            text-decoration: none;
        }
        tr td a:hover {
            background-color: lightgray;
        }

        form {
            max-width: 400px;
        }
        label{
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }
        input[type="text"], input[type="password"]{
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid black;
            box-sizing: border-box;
        }
        .test-accounts-box{
            max-width: 400px;
            border: 1px solid black;
            border-radius: 3px;
            padding: 15px;
            margin-bottom: 25px;
        }
        .test-accounts-box p {
            margin-top: 0;
            margin-bottom: 8px;
            padding: 8px;
        }
        .test-accounts-box ul {
            margin-top: 0;
            margin-bottom: 0;
            padding: 8px;
        }
    </style>
</head>
<body>
<nav>
    <a href="index.php">Home</a>
    <a href="team.php">Team</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        Logged in as <?= htmlspecialchars($_SESSION['display_name']) ?>
        <form action="logout.php" method="POST" style="display:inline;">
            <button type="submit">log out</button>
        </form>
    <?php
    else:
        ?>
        <a href="login.php">Log In</a>
        <a href="register.php">Register</a>
    <?php
    endif;
    ?>
</nav>
