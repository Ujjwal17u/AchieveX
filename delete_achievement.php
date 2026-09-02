<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$user_id = $_SESSION["user_id"];
$achievement_id = (int)($_GET["id"] ?? 0);

if ($achievement_id <= 0) {
    header("Location: achievements.php");
    exit;
}


/*
   Delete only the achievement
   belonging to the logged-in user.
*/

$stmt = $conn->prepare("
    DELETE FROM achievements
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $achievement_id,
    $user_id
);

$stmt->execute();

$stmt->close();
$conn->close();


/*
   Go back to achievements list
*/

header("Location: achievements.php");
exit;
?>