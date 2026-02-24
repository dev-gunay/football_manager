<?php
session_start();

// --- 1️⃣ DB Verbindung ---
$host = 'localhost';
$db   = 'football_match_manager';
$user = 'root';
$pass = '';
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
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['passwort'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO `user` (name, email, passwort) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        $msg = "Neuer User '$name' erstellt!";
    }

    if (isset($_POST['create_event'])) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>

<body>

    <div class="admin-container">
        <h1>Admin Panel</h1>

        <?php if (isset($msg)) echo "<div class='success'>$msg</div>"; ?>

        <div class="card">
            <h2>Neuen User erstellen</h2>
            <form method="post">
                <input type="hidden" name="create_user">

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Passwort</label>
                    <input type="password" name="passwort" required>
                </div>

                <button type="submit">User erstellen</button>
            </form>
        </div>

        <div class="card">
            <h2>Neues Event erstellen</h2>
            <form method="post">
                <input type="hidden" name="create_event">

                <div class="form-group">
                    <label>Titel</label>
                    <input type="text" name="title" required>
                </div>

                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" required>
                </div>

                <div class="form-group">
                    <label>Datum</label>
                    <input type="date" name="event_date" required>
                </div>

                <button type="submit">Event erstellen</button>
            </form>
        </div>
    </div>

</body>

</html>