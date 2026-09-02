<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$user_id = $_SESSION["user_id"];


/* =========================
   ACHIEVEMENTS COUNT
========================= */

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM achievements
    WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_achievements = (int)$row["total"];

$stmt->close();


/* =========================
   CERTIFICATES COUNT
========================= */

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM certificates
    WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_certificates = (int)$row["total"];

$stmt->close();


/* =========================
   PROJECTS COUNT
========================= */

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM projects
    WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_projects = (int)$row["total"];

$stmt->close();


/* =========================
   SKILLS COUNT
========================= */

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM skills
    WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$total_skills = (int)$row["total"];

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>AchieveX Dashboard</title>


<style>

/* =========================
   RESET
========================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


/* =========================
   BODY
========================= */

body {

    font-family: Arial, sans-serif;

    background: #f4f6f9;

    color: #111827;

}


/* =========================
   NAVBAR
========================= */

.navbar {

    height: 65px;

    background: #1f2937;

    color: white;

    padding: 0 30px;

    display: flex;

    justify-content: space-between;

    align-items: center;

}

.navbar h2 {

    font-size: 22px;

}

.navbar span {

    font-size: 15px;

}


/* =========================
   MAIN CONTAINER
========================= */

.container {

    display: flex;

    min-height: calc(100vh - 65px);

}


/* =========================
   SIDEBAR
========================= */

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

    transition: 0.25s;

}

.sidebar a:hover {

    background: #1f2937;

}


/* =========================
   CONTENT
========================= */

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


/* =========================
   STATISTICS
========================= */

.statistics {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 18px;

    margin-top: 25px;

}


/* =========================
   STAT CARD
========================= */

.stat-card {

    display: block;

    background: white;

    padding: 24px;

    border-radius: 12px;

    text-decoration: none;

    color: #111827;

    box-shadow:
        0 2px 8px rgba(0,0,0,0.08);

    transition:
        transform .25s ease,
        box-shadow .25s ease;

}

.stat-card:hover {

    transform:
        translateY(-4px);

    box-shadow:
        0 8px 20px rgba(0,0,0,0.12);

}


/* =========================
   STAT ICON
========================= */

.stat-icon {

    font-size: 28px;

    margin-bottom: 12px;

}


/* =========================
   STAT TITLE
========================= */

.stat-title {

    color: #6b7280;

    font-size: 14px;

    margin-bottom: 7px;

}


/* =========================
   STAT NUMBER
========================= */

.stat-number {

    font-size: 30px;

    font-weight: bold;

}


/* =========================
   QUICK ACTIONS
========================= */

.quick-section {

    margin-top: 35px;

}

.quick-section h2 {

    font-size: 21px;

    margin-bottom: 15px;

}

.quick-actions {

    display: flex;

    gap: 12px;

    flex-wrap: wrap;

}

.quick-actions a {

    display: inline-block;

    background: #1f2937;

    color: white;

    text-decoration: none;

    padding: 11px 17px;

    border-radius: 8px;

    font-weight: bold;

    transition: .25s;

}

.quick-actions a:hover {

    background: #111827;

    transform:
        translateY(-2px);

}


/* =========================
   PROFILE ACTION
========================= */

.profile-section {

    margin-top: 35px;

    background: white;

    border-radius: 12px;

    padding: 25px;

    box-shadow:
        0 2px 8px rgba(0,0,0,0.08);

}

.profile-section h2 {

    margin-bottom: 8px;

}

.profile-section p {

    color: #6b7280;

    line-height: 1.6;

}

.profile-button {

    display: inline-block;

    margin-top: 15px;

    padding: 10px 17px;

    background: #4f46e5;

    color: white;

    text-decoration: none;

    border-radius: 7px;

    font-weight: bold;

}

.profile-button:hover {

    background: #4338ca;

}


/* =========================
   MOBILE
========================= */

@media (max-width: 900px) {

    .statistics {

        grid-template-columns:
            repeat(2, 1fr);

    }

}


@media (max-width: 600px) {

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

    .content {

        padding: 20px 15px;

    }

    .content h1 {

        font-size: 25px;

    }

    .statistics {

        grid-template-columns: 1fr;

    }

    .navbar {

        padding: 0 15px;

    }

    .navbar span {

        display: none;

    }

}

</style>

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

<div class="navbar">

    <h2>
        AchieveX
    </h2>

    <span>

        <?php

        echo htmlspecialchars(
            $_SESSION["user_name"]
        );

        ?>

    </span>

</div>



<!-- =========================
     MAIN
========================= -->

<div class="container">


    <!-- =========================
         SIDEBAR
    ========================= -->

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

        <a href="projects.php">
            💻 Projects
        </a>

        <a href="skills.php">
            🛠 Skills
        </a>

        <a href="profile.php">
            👤 My Profile
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>



    <!-- =========================
         CONTENT
    ========================= -->

    <div class="content">


        <h1>

            Welcome back,
            <?php

            echo htmlspecialchars(
                $_SESSION["user_name"]
            );

            ?>

            👋

        </h1>


        <p class="subtitle">

            Manage your achievements
            and build your professional
            portfolio.

        </p>



        <!-- =========================
             STATISTICS
        ========================= -->

        <div class="statistics">


            <!-- ACHIEVEMENTS -->

            <a
                href="achievements.php"
                class="stat-card"
            >

                <div class="stat-icon">
                    🏆
                </div>

                <div class="stat-title">
                    Achievements
                </div>

                <div class="stat-number">

                    <?php
                    echo $total_achievements;
                    ?>

                </div>

            </a>



            <!-- CERTIFICATES -->

            <a
                href="certificates.php"
                class="stat-card"
            >

                <div class="stat-icon">
                    📜
                </div>

                <div class="stat-title">
                    Certificates
                </div>

                <div class="stat-number">

                    <?php
                    echo $total_certificates;
                    ?>

                </div>

            </a>



            <!-- PROJECTS -->

            <a
                href="projects.php"
                class="stat-card"
            >

                <div class="stat-icon">
                    💻
                </div>

                <div class="stat-title">
                    Projects
                </div>

                <div class="stat-number">

                    <?php
                    echo $total_projects;
                    ?>

                </div>

            </a>



            <!-- SKILLS -->

            <a
                href="skills.php"
                class="stat-card"
            >

                <div class="stat-icon">
                    🛠
                </div>

                <div class="stat-title">
                    Skills
                </div>

                <div class="stat-number">

                    <?php
                    echo $total_skills;
                    ?>

                </div>

            </a>


        </div>



        <!-- =========================
             QUICK ACTIONS
        ========================= -->

        <div class="quick-section">

            <h2>
                Quick Actions
            </h2>


            <div class="quick-actions">

                <a href="add_achievement.php">
                    + Add Achievement
                </a>

                <a href="add_certificate.php">
                    + Add Certificate
                </a>

                <a href="add_project.php">
                    + Add Project
                </a>

                <a href="add_skill.php">
                    + Add Skill
                </a>

            </div>

        </div>



        <!-- =========================
             PROFILE
        ========================= -->

        <div class="profile-section">

            <h2>
                👤 Your Public Profile
            </h2>

            <p>

                Showcase your achievements,
                certificates, projects and
                skills through your AchieveX
                public profile.

            </p>


            <?php if (!empty($_SESSION["username"])): ?>

                <a
                    href="profile.php?username=<?php
                        echo urlencode(
                            $_SESSION["username"]
                        );
                    ?>"
                    class="profile-button"
                >

                    View Public Profile →

                </a>

            <?php else: ?>

                <a
                    href="profile.php"
                    class="profile-button"
                >

                    View Profile →

                </a>

            <?php endif; ?>

        </div>


    </div>

</div>


</body>

</html>

<?php

$conn->close();

?>