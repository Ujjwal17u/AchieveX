<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$user_id = $_SESSION["user_id"];
$project_id = (int)($_GET["id"] ?? 0);

if ($project_id <= 0) {
    header("Location: projects.php");
    exit;
}

/*
   Delete only the project belonging
   to the currently logged-in user.
*/

$stmt = $conn->prepare("
    DELETE FROM projects
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $project_id,
    $user_id
);

$stmt->execute();

$stmt->close();
$conn->close();

header("Location: projects.php");
exit;
?>