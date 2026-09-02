<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT
        id,
        title,
        description,
        category,
        date_achieved
    FROM achievements
    WHERE user_id = ?
    ORDER BY date_achieved DESC, id DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Achievements - AchieveX</title>

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


/* NAVBAR */

.navbar {

    height: 65px;

    background: #1f2937;

    color: white;

    padding: 0 30px;

    display: flex;

    align-items: center;

    justify-content: space-between;
}

.navbar h2 {
    margin: 0;
}


/* LAYOUT */

.container {

    display: flex;

    min-height: calc(100vh - 65px);
}


/* SIDEBAR */

.sidebar {

    width: 220px;

    background: #111827;

    color: white;

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

    transition: .25s;
}

.sidebar a:hover {

    background: #1f2937;
}


/* CONTENT */

.content {

    flex: 1;

    padding: 35px;

    max-width: 1100px;
}

.content h1 {

    margin-bottom: 8px;

    font-size: 30px;
}

.subtitle {

    color: #6b7280;

    margin-bottom: 25px;
}


/* TOP ACTION */

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

    transition: .25s;
}

.add-button:hover {

    background: #4338ca;

    transform: translateY(-1px);
}


/* ACHIEVEMENT CARD */

.achievement-card {

    background: white;

    border-radius: 12px;

    padding: 22px;

    margin-bottom: 15px;

    box-shadow:
        0 2px 8px rgba(0,0,0,.08);
}

.achievement-header {

    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    gap: 15px;
}

.achievement-title {

    font-size: 20px;

    font-weight: bold;

    margin-bottom: 8px;
}

.category {

    display: inline-block;

    background: #e5e7eb;

    color: #374151;

    padding: 5px 10px;

    border-radius: 15px;

    font-size: 12px;
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


/* ACTIONS */

.actions {

    display: flex;

    gap: 8px;

    flex-shrink: 0;
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


/* EMPTY */

.empty {

    background: white;

    border-radius: 12px;

    padding: 45px 20px;

    text-align: center;

    color: #6b7280;

    box-shadow:
        0 2px 8px rgba(0,0,0,.08);
}


/* MOBILE */

@media (max-width: 700px) {

    .sidebar {

        width: 170px;

        padding: 20px 12px;
    }

    .content {

        padding: 20px 15px;
    }

    .achievement-header {

        flex-direction: column;
    }

    .actions {

        width: 100%;
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


<!-- NAVBAR -->

<div class="navbar">

    <h2>AchieveX</h2>

    <span>
        <?php
        echo htmlspecialchars(
            $_SESSION["user_name"]
        );
        ?>
    </span>

</div>


<div class="container">


    <!-- SIDEBAR -->

    <div class="sidebar">

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="achievements.php">
            🏆 Achievements
        </a>

        <a href="#">
            Certificates
        </a>

        <a href="#">
            Projects
        </a>

        <a href="#">
            Skills
        </a>

        <a href="profile.php">
            My Profile
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>


    <!-- CONTENT -->

    <div class="content">

        <h1>
            🏆 My Achievements
        </h1>

        <p class="subtitle">
            Manage and showcase your achievements.
        </p>


        <div class="top-action">

            <a
                href="add_achievement.php"
                class="add-button"
            >
                + Add Achievement
            </a>

        </div>


        <?php if ($result->num_rows > 0): ?>


            <?php while (
                $achievement =
                $result->fetch_assoc()
            ): ?>


                <div class="achievement-card">


                    <div class="achievement-header">


                        <div>

                            <div class="achievement-title">

                                🏆

                                <?php

                                echo htmlspecialchars(
                                    $achievement["title"]
                                );

                                ?>

                            </div>


                            <?php if (
                                !empty(
                                    $achievement["category"]
                                )
                            ): ?>

                                <span class="category">

                                    <?php

                                    echo htmlspecialchars(
                                        $achievement["category"]
                                    );

                                    ?>

                                </span>

                            <?php endif; ?>

                        </div>


                        <div class="actions">

                            <a
                                href="edit_achievement.php?id=<?php
                                    echo (int)$achievement["id"];
                                ?>"
                                class="edit"
                            >
                                Edit
                            </a>


                            <a
                                href="delete_achievement.php?id=<?php
                                    echo (int)$achievement["id"];
                                ?>"
                                class="delete"
                                onclick="return confirm(
                                    'Are you sure you want to delete this achievement?'
                                );"
                            >
                                Delete
                            </a>

                        </div>


                    </div>


                    <?php if (
                        !empty(
                            $achievement["description"]
                        )
                    ): ?>

                        <div class="description">

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $achievement["description"]
                                )
                            );

                            ?>

                        </div>

                    <?php endif; ?>


                    <?php if (
                        !empty(
                            $achievement["date_achieved"]
                        )
                    ): ?>

                        <div class="date">

                            📅

                            <?php

                            echo htmlspecialchars(
                                $achievement[
                                    "date_achieved"
                                ]
                            );

                            ?>

                        </div>

                    <?php endif; ?>


                </div>


            <?php endwhile; ?>


        <?php else: ?>


            <div class="empty">

                <h2>
                    No achievements yet
                </h2>

                <p style="margin-top:10px;">
                    Start building your AchieveX portfolio.
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