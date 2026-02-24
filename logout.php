<?php
session_start();

// Alle Session-Daten löschen
session_unset();

// Die Session zerstören
session_destroy();

// Weiterleitung zur Login-Seite
header("Location: login.php");
exit;
