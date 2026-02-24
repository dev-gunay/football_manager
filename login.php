<?php
session_start();
require 'db.php';

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

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/styles.css">

</head>

<body>

    <div class="login-container">
        <h2>Welcome Back</h2>

        <form method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" required>
            </div>

            <div class="form-group">
                <label for="passwort">Password</label>
                <input type="password" name="passwort" id="passwort" required>
            </div>

            <button type="submit">Sign In</button>

            <?php if (!empty($error)) { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>

            <a href="register.php">
                <button type="button" class="register-btn">Register</button>
            </a>
        </form>

        <div class="forgot">
            <p>Forgot your password?</p>
            <form method="POST" action="pw.reset.php">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit" style="margin-top:8px;">Reset Password</button>
            </form>
        </div>
    </div>

    <footer>
        © dev-gunay
    </footer>

</body>

</html>