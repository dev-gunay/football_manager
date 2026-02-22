<?php
// admin.php
session_start();

// --- 1️⃣ DB Verbindung ---
$host = 'localhost';
$db   = 'football_match_manager';
$user = 'root';
$pass = ''; // hier dein Passwort
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("DB Verbindung fehlgeschlagen: " . $e->getMessage());
}

// --- 2️⃣ Neues Formular abgeschickt ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['create_user'])) {
        // Neue User erstellen
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['passwort'], PASSWORD_DEFAULT); // Passwort hashen

        $stmt = $pdo->prepare("INSERT INTO `user` (name, email, passwort) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        $msg = "Neuer User '$name' erstellt!";
    }

    if (isset($_POST['create_event'])) {
        // Neues Event erstellen
        $title = $_POST['title'];
        $location = $_POST['location'];
        $event_date = $_POST['event_date'];

        $stmt = $pdo->prepare("INSERT INTO events (title, location, event_date) VALUES (?, ?, ?)");
        $stmt->execute([$title, $location, $event_date]);
        $msg = "Event '$title' erstellt!";
    }
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
</head>

<body>
    <h1>Admin Panel</h1>

    <?php if (isset($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

    <h2>Neuen User erstellen</h2>
    <form method="post">
        <input type="hidden" name="create_user">
        Name: <input type="text" name="name" required><br>
        Email: <input type="email" name="email" required><br>
        Passwort: <input type="password" name="password" required><br>
        <button type="submit">User erstellen</button>
    </form>

    <h2>Neues Event erstellen</h2>
    <form method="post">
        <input type="hidden" name="create_event">
        Titel: <input type="text" name="title" required><br>
        Location: <input type="text" name="location" required><br>
        Datum: <input type="date" name="event_date" required><br>
        <button type="submit">Event erstellen</button>
    </form>
</body>

</html>