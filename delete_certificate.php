<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$user_id = $_SESSION["user_id"];
$certificate_id = (int)($_GET["id"] ?? 0);

if ($certificate_id <= 0) {
    header("Location: certificates.php");
    exit;
}

/*
   Delete only the certificate
   belonging to the logged-in user.
*/

$stmt = $conn->prepare("
    DELETE FROM certificates
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $certificate_id,
    $user_id
);

$stmt->execute();

$stmt->close();
$conn->close();

header("Location: certificates.php");
exit;
?>