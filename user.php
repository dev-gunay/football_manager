<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

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

$pdo = new PDO($dsn, $user, $pass, $options);

$user_id = $_SESSION['user'];

/* -----------------------------
   STATUS SPEICHERN
------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'], $_POST['status'])) {

    $event_id = $_POST['event_id'];
    $status = $_POST['status'];

    $check = $pdo->prepare("SELECT * FROM participation WHERE user_id = ? AND event_id = ?");
    $check->execute([$user_id, $event_id]);

    if ($check->rowCount() > 0) {
        $update = $pdo->prepare("UPDATE participation SET status = ? WHERE user_id = ? AND event_id = ?");
        $update->execute([$status, $user_id, $event_id]);
    } else {
        $insert = $pdo->prepare("INSERT INTO participation (user_id, event_id, status) VALUES (?, ?, ?)");
        $insert->execute([$user_id, $event_id, $status]);
    }
}

/* -----------------------------
   EVENTS LADEN
------------------------------ */
$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user.css">
</head>

<body>
    <div class="container">

        <h1>Willkommen, <?php echo htmlspecialchars($_SESSION['name']); ?> 👋</h1>

        <?php foreach ($events as $event): ?>

            <div class="card">

                <div class="event-title">
                    <?php echo htmlspecialchars($event['title']); ?>
                </div>

                <div class="event-info">
                    📍 <?php echo htmlspecialchars($event['location']); ?>
                </div>

                <div class="event-info">
                    📅 <?php echo date("d.m.Y", strtotime($event['event_date'])); ?>
                </div>

                <!-- STATUS BUTTONS -->
                <form method="POST">
                    <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                    <div class="status-buttons">
                        <button class="yes" name="status" value="Yes">Yes</button>
                        <button class="maybe" name="status" value="Maybe">Maybe</button>
                        <button class="no" name="status" value="No">No</button>
                    </div>
                </form>

                <!-- TEILNEHMERLISTE -->
                <div class="participants">

                    <?php
                    $stmt = $pdo->prepare("
            SELECT u.name, p.status
            FROM participation p
            JOIN user u ON p.user_id = u.user_id
            WHERE p.event_id = ?
        ");
                    $stmt->execute([$event['event_id']]);
                    $participants = $stmt->fetchAll();

                    $yes = [];
                    $maybe = [];
                    $no = [];

                    foreach ($participants as $p) {
                        if ($p['status'] === 'Yes') $yes[] = $p['name'];
                        if ($p['status'] === 'Maybe') $maybe[] = $p['name'];
                        if ($p['status'] === 'No') $no[] = $p['name'];
                    }
                    ?>

                    <div>
                        <h4>✅ Yes (<?php echo count($yes); ?>)</h4>
                        <?php foreach ($yes as $name) echo htmlspecialchars($name) . "<br>"; ?>
                    </div>

                    <div>
                        <h4>🤔 Maybe (<?php echo count($maybe); ?>)</h4>
                        <?php foreach ($maybe as $name) echo htmlspecialchars($name) . "<br>"; ?>
                    </div>

                    <div>
                        <h4>❌ No (<?php echo count($no); ?>)</h4>
                        <?php foreach ($no as $name) echo htmlspecialchars($name) . "<br>"; ?>
                    </div>

                </div>

            </div>

        <?php endforeach; ?>

        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>

    </div>
</body>

</html>