<?php

session_start();
require 'db.php';

$message = '';
$e = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['passwort'];

    // Passwort hashen
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // SQL Insert
    $sql = "INSERT INTO user (name, email, passwort) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$name, $email, $hashedPassword]);
        $message = "Succesfully registed";
    } catch (PDOException $e) {
        $message = "Failed to register " . $e->getMessage();
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
</head>

<body>
    <h2>Registrierung</h2>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div>
            <label for="password">Passwort:</label>
            <input type="password" id="passwort" name="passwort" required>
        </div>

        <button type="submit">Registrieren</button>
    </form>
</body>

</html>