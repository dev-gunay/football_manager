<?php
session_start();
require 'db.php';

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['name'];
    $password = $_POST['passwort'];

    if (empty($user) || empty($password)) {
        $error = "Please fill all fields";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE name =?");
        $stmt->execute([$user]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['passwort'])) {
            $error = "Username or Password is not correct";
        } else {
            $_SESSION['user'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];

            if ($user['role'] == 'admin') {
                header("Location: admin.php");
                exit;
            } else {
                header("Location: user.php");
                exit;
            }
        }
    }
}

?>



<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/login.css">
</head>

<body>
    <!-- <header class="medi">
        <div>
            <svg class="logo" viewBox="0 0 200 85" xmlns="http://www.w3.org/2000/svg" stroke-linecap="round"
                stroke-linejoin="round" class="ml-logo">
                <path
                    d="M27.7 24.2c-1-1.1 2.3-2.8 2.5-2.7 1 .9-.9 4.4-2.5 2.7zm7.8 39.3c0-2.1.7-3.7 4.2-5.2l-.2-6.9c-4.6-.9-9.7-2.5-9.7-8.8 0-7.2 5-9.4 9.2-10.1l-.3-11.2c-2 .7-4 2-6.2 4.3-5.5 5.5-12.2-2-1.8-6.7 3.5-1.6 6-2.4 7.9-2.8l-.2-7.5C21.1 10.5 7.9 25 7.9 42.6c0 18.3 14.4 33.1 32.4 34l-.2-8.2c-2.1-.7-4.6-2.2-4.6-4.9zm3.5.2c0 .7.3 1.1.9 1.5l-.1-3.2c-.5.5-.8 1.1-.8 1.7zm6.6-55-.2 7c8.1.7 11.4 4.3 11.4 11.2 0 2.7-2.7 8.1-8.7 9.9-1 .3-2.3.5-3.5.6l-.3 11c2.5.6 5.8 1.8 5.8 6 0 4.4-4.1 5.6-5.4 6-.2 0-.5.1-.8.1l-.2 6.2c2.5.8 2.5 1.2 2.5 1.6 0 1-1.6 1-2.6.9l-.2 7.5c18-.9 32.3-15.8 32.3-34 .2-17.7-13-32.2-30.1-34zm.8 45.8c0-1-.9-1.6-2.1-2l-.1 4.3c1.4-.5 2.2-1 2.2-2.3zm5.7-27.9c0-5.2-3.8-6-6.8-6L45 32.3c5.1-.4 7.1-3.5 7.1-5.7zm-18 15.6c0 2.7 2.5 3.8 5.2 4.7l-.3-9.4c-2.7.4-4.9 1.3-4.9 4.7zM41.9.6c23.2 0 41.9 18.8 41.9 41.9S65.1 84.4 41.9 84.4 0 65.7 0 42.5 18.8.6 41.9.6zM41.7 80c20.4 0 37.2-16.6 37.2-37-.1-20.6-16.8-37.2-37.2-37.2S4.5 22.4 4.5 43c0 20.4 16.7 37 37.2 37zm-.9-32.5.1 4.3.2 6.1c.1 0 .3-.1.5-.1l1-.3.1-5.2.1-4 .3-10.6c-.9 0-1.8 0-2.7.1.1 0 .4 9.7.4 9.7zm.6 18.4.1 2.8.2 7.9h.3l.2-7.7.1-2.6.1-5.4c-.5.1-.9.2-1.2.5l.2 4.5zm2.3-45.3.1-5 .2-7.1c-.8 0-1.5-.1-2.3-.1-.8 0-1.5 0-2.3.1l.2 7.2.1 5.1.3 11.4c1.1-.1 2.3-.1 3.1-.1m67.7 37.5v-4.5H99.2v-21h-5v25.6h16.6v-.1zm20.3 0v-4.5h-11.9v-6.2h10.2v-4.5h-10.2v-6h11.9v-4.5h-16.8v25.6h16.8v.1zm23.9 0L145.6 44h-3.8l-9.3 25.6h5.2l1.6-4.5h9.2l1.5 4.5h5zm-8-8.7h-6.3l3.3-9.3 3 9.3zm29.9 8.7-5.8-11.1c2.5-.9 4.6-3.2 4.6-6.8 0-4.3-3.1-7.8-8.4-7.8h-10.1v25.6h5V59.4h3.6l5 10.2h6.1zm-6.1-17.7c0 2-1.5 3.4-3.6 3.4h-4.7v-6.8h4.7c2.3 0 3.6 1.5 3.6 3.4zM200 69.6V44.1h-5.1v15.7l-10.2-15.7h-4.5v25.6h5V53.9l10.2 15.7h4.6zm-82.5-33.7V10.4h-5l-6.7 13.9-6.7-13.9h-5V36h5V21l5 9.7h3.4l4.9-9.7v14.9h5.1zm22 0v-4.5h-11.9v-6.2h10.2v-4.5h-10.2v-5.9h11.9v-4.5h-16.8v25.6h16.8v.1zm19.4 33.7V.1h-5v12.3h5v56.6z"
                    fill="currentColor" />
            </svg>
        </div>
    </header> -->

    <main class="login-form">
        <form method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="passwort">Password:</label>
                <input type="password" id="passwort" name="passwort" required>
            </div>

            <!-- Fehlermeldung anzeigen -->
            <?php if (!empty($error)) { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>

            <div class="form-group">
                <button type="submit">Sign in</button>
            </div>
        </form>

        <hr>

        <h3>Forgot your Password ?</h3>
        <form method="POST" action="pw.reset.php">
            <div class="form-group">
                <label for="email">E-Mail-Adres:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <button type="submit">Passwort reset</button>
            </div>
        </form>
    </main>

    <footer>
        <p>© dev-gunay</p>
    </footer>
</body>

</html>