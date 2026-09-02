<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$user_id = $_SESSION["user_id"];
$skill_id = (int)($_GET["id"] ?? 0);

if ($skill_id <= 0) {
    header("Location: skills.php");
    exit;
}

/*
    Delete only the skill belonging
    to the currently logged-in user.
*/

$stmt = $conn->prepare("
    DELETE FROM skills
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $skill_id,
    $user_id
);

$stmt->execute();

$stmt->close();
$conn->close();

header("Location: skills.php");
exit;
?>