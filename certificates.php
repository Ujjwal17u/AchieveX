<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT id, title, issuer, description, certificate_date
    FROM certificates
    WHERE user_id = ?
    ORDER BY certificate_date DESC, id DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Certificates - AchieveX</title>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    color: #111827;
}

.navbar {
    height: 65px;
    background: #1f2937;
    color: white;
    padding: 0 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.container {
    display: flex;
    min-height: calc(100vh - 65px);
}

.sidebar {
    width: 220px;
    background: #111827;
    padding: 25px 20px;
    flex-shrink: 0;
}

.sidebar a {
    display: block;
    color: white;
    text-decoration: none;
    padding: 12px 10px;
    margin-bottom: 5px;
    border-radius: 7px;
}

.sidebar a:hover {
    background: #1f2937;
}

.content {
    flex: 1;
    padding: 35px;
    max-width: 1100px;
}

.content h1 {
    font-size: 30px;
    margin-bottom: 8px;
}

.subtitle {
    color: #6b7280;
    margin-bottom: 25px;
}

.top-action {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 20px;
}

.add-button {
    background: #4f46e5;
    color: white;
    text-decoration: none;
    padding: 11px 18px;
    border-radius: 8px;
    font-weight: bold;
}

.add-button:hover {
    background: #4338ca;
}

.certificate-card {
    background: white;
    border-radius: 12px;
    padding: 22px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

.certificate-header {
    display: flex;
    justify-content: space-between;
    gap: 15px;
}

.certificate-title {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 8px;
}

.issuer {
    color: #4f46e5;
    font-weight: bold;
}

.description {
    color: #4b5563;
    line-height: 1.6;
    margin-top: 12px;
}

.date {
    color: #6b7280;
    font-size: 13px;
    margin-top: 12px;
}

.actions {
    display: flex;
    gap: 8px;
}

.actions a {
    text-decoration: none;
    padding: 7px 11px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: bold;
}

.edit {
    background: #e0e7ff;
    color: #3730a3;
}

.delete {
    background: #fee2e2;
    color: #991b1b;
}

.empty {
    background: white;
    border-radius: 12px;
    padding: 45px 20px;
    text-align: center;
    color: #6b7280;
}

@media (max-width: 700px) {

    .sidebar {
        width: 170px;
    }

    .content {
        padding: 20px 15px;
    }

    .certificate-header {
        flex-direction: column;
    }

}

@media (max-width: 500px) {

    .container {
        display: block;
    }

    .sidebar {
        width: 100%;
        min-height: auto;
        display: flex;
        overflow-x: auto;
        gap: 5px;
        padding: 10px;
    }

    .sidebar a {
        white-space: nowrap;
        margin: 0;
    }

    .top-action {
        justify-content: stretch;
    }

    .add-button {
        width: 100%;
        text-align: center;
    }

}

</style>

</head>

<body>

<div class="navbar">

    <h2>AchieveX</h2>

    <span>
        <?php
        echo htmlspecialchars($_SESSION["user_name"]);
        ?>
    </span>

</div>

<div class="container">

    <div class="sidebar">

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="achievements.php">
            🏆 Achievements
        </a>

        <a href="certificates.php">
            📜 Certificates
        </a>

        <a href="#">
            💻 Projects
        </a>

        <a href="#">
            🛠 Skills
        </a>

        <a href="profile.php">
            My Profile
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>


    <div class="content">

        <h1>📜 My Certificates</h1>

        <p class="subtitle">
            Manage and showcase your certificates.
        </p>


        <div class="top-action">

            <a
                href="add_certificate.php"
                class="add-button"
            >
                + Add Certificate
            </a>

        </div>


        <?php if ($result->num_rows > 0): ?>

            <?php while ($certificate = $result->fetch_assoc()): ?>

                <div class="certificate-card">

                    <div class="certificate-header">

                        <div>

                            <div class="certificate-title">

                                📜
                                <?php
                                echo htmlspecialchars(
                                    $certificate["title"]
                                );
                                ?>

                            </div>


                            <?php if (!empty($certificate["issuer"])): ?>

                                <div class="issuer">

                                    Issued by:
                                    <?php
                                    echo htmlspecialchars(
                                        $certificate["issuer"]
                                    );
                                    ?>

                                </div>

                            <?php endif; ?>

                        </div>


                        <div class="actions">

                            <a
                                href="edit_certificate.php?id=<?php
                                    echo (int)$certificate["id"];
                                ?>"
                                class="edit"
                            >
                                Edit
                            </a>

                            <a
                                href="delete_certificate.php?id=<?php
                                    echo (int)$certificate["id"];
                                ?>"
                                class="delete"
                                onclick="return confirm(
                                    'Are you sure you want to delete this certificate?'
                                );"
                            >
                                Delete
                            </a>

                        </div>

                    </div>


                    <?php if (!empty($certificate["description"])): ?>

                        <div class="description">

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $certificate["description"]
                                )
                            );
                            ?>

                        </div>

                    <?php endif; ?>


                    <?php if (!empty($certificate["certificate_date"])): ?>

                        <div class="date">

                            📅 Certificate Date:
                            <?php
                            echo htmlspecialchars(
                                $certificate["certificate_date"]
                            );
                            ?>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="empty">

                <h2>No certificates yet</h2>

                <p style="margin-top:10px;">
                    Add your first certificate to AchieveX.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>