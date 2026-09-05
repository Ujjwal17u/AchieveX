<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$user_id = (int) $_SESSION["user_id"];


/* =========================
   USER PROFILE DATA
========================= */

$stmt = $conn->prepare("
    SELECT
        name,
        username,
        bio,
        education,
        skills,
        linkedin_url,
        github_url,
        profile_image
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

$stmt->close();


/* =========================
   BASIC USER DATA
========================= */

$user_name = $user["name"] ?? $_SESSION["user_name"] ?? "User";
$user_username = $user["username"] ?? "";
$user_bio = trim($user["bio"] ?? "");
$user_education = trim($user["education"] ?? "");
$user_skills = trim($user["skills"] ?? "");
$user_linkedin = trim($user["linkedin_url"] ?? "");
$user_github = trim($user["github_url"] ?? "");
$user_profile_image = trim($user["profile_image"] ?? "");


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

$total_achievements = (int) $row["total"];

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

$total_certificates = (int) $row["total"];

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

$total_projects = (int) $row["total"];

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

$total_skills = (int) $row["total"];

$stmt->close();


/* =========================
   PROFILE COMPLETION
========================= */

$completion_points = 0;

if ($user_bio !== "") {
    $completion_points++;
}

if ($user_education !== "") {
    $completion_points++;
}

if ($user_profile_image !== "") {
    $completion_points++;
}

if ($user_linkedin !== "") {
    $completion_points++;
}

if ($user_github !== "") {
    $completion_points++;
}

if ($total_achievements > 0) {
    $completion_points++;
}

if ($total_projects > 0) {
    $completion_points++;
}

if ($total_skills > 0 || $total_certificates > 0) {
    $completion_points++;
}

$profile_completion = round(
    ($completion_points / 8) * 100
);


/* =========================
   COMPLETION MESSAGE
========================= */

if ($profile_completion >= 100) {

    $completion_message =
        "Your AchieveX profile is complete! 🎉";

} elseif ($profile_completion >= 75) {

    $completion_message =
        "Almost there! Complete a few more details.";

} elseif ($profile_completion >= 50) {

    $completion_message =
        "Good progress! Keep building your portfolio.";

} elseif ($profile_completion >= 25) {

    $completion_message =
        "Start adding more information to strengthen your profile.";

} else {

    $completion_message =
        "Complete your profile to build a stronger professional presence.";

}


/* =========================
   PROFILE URL
========================= */

if ($user_username !== "") {

    $public_profile_url =
        "public_profile.php?username=" .
        urlencode($user_username);

} else {

    $public_profile_url =
        "profile.php";

}

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

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

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
   CONTAINER
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
    transform: translateX(3px);
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
    grid-template-columns: repeat(4, 1fr);
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
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition:
        transform .25s ease,
        box-shadow .25s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
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
   STAT LINK
========================= */

.stat-link {
    margin-top: 12px;
    font-size: 13px;
    font-weight: bold;
    color: #4f46e5;
    transition: 0.25s;
}

.stat-card:hover .stat-link {
    transform: translateX(4px);
}


/* =========================
   PROFILE COMPLETION
========================= */

.completion-section {
    margin-top: 30px;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.completion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.completion-header h2 {
    font-size: 21px;
}

.completion-percentage {
    font-size: 24px;
    font-weight: bold;
    color: #4f46e5;
}


/* =========================
   PROGRESS BAR
========================= */

.progress-container {
    width: 100%;
    height: 12px;
    background: #e5e7eb;
    border-radius: 20px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    width: <?php echo $profile_completion; ?>%;
    background: #4f46e5;
    border-radius: 20px;
    transition: width .5s ease;
}

.completion-message {
    margin-top: 12px;
    color: #6b7280;
    line-height: 1.6;
}

.improve-button {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 17px;
    background: #4f46e5;
    color: white;
    text-decoration: none;
    border-radius: 7px;
    font-weight: bold;
    transition: .25s;
}

.improve-button:hover {
    background: #4338ca;
    transform: translateY(-2px);
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
    transform: translateY(-2px);
}


/* =========================
   PUBLIC PROFILE
========================= */

.profile-section {
    margin-top: 35px;
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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
   RESPONSIVE
========================= */

@media (max-width: 900px) {

    .statistics {
        grid-template-columns: repeat(2, 1fr);
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

    .completion-header {
        align-items: flex-start;
    }

}

</style>

</head>

<body>


<!-- NAVBAR -->

<div class="navbar">

    <h2>
        AchieveX
    </h2>

    <span>

        <?php
        echo htmlspecialchars($user_name);
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


<!-- CONTENT -->

<div class="content">


<h1>

    Welcome back,
    <?php
    echo htmlspecialchars($user_name);
    ?>
    👋

</h1>


<p class="subtitle">

    Manage your achievements
    and build your professional
    portfolio.

</p>


<!-- STATISTICS -->

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

        <div class="stat-link">
            View →
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

        <div class="stat-link">
            View →
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

        <div class="stat-link">
            View →
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

        <div class="stat-link">
            View →
        </div>

    </a>


</div>


<!-- PROFILE COMPLETION -->

<div class="completion-section">

    <div class="completion-header">

        <h2>
            📈 Profile Completion
        </h2>

        <div class="completion-percentage">

            <?php
            echo $profile_completion;
            ?>%

        </div>

    </div>


    <div class="progress-container">

        <div class="progress-bar"></div>

    </div>


    <p class="completion-message">

        <?php
        echo htmlspecialchars(
            $completion_message
        );
        ?>

    </p>


    <?php if ($profile_completion < 100): ?>

        <a
            href="profile.php"
            class="improve-button"
        >
            Complete Profile →
        </a>

    <?php else: ?>

        <a
            href="<?php
                echo htmlspecialchars(
                    $public_profile_url
                );
            ?>"
            class="improve-button"
        >
            View Public Profile →
        </a>

    <?php endif; ?>


</div>


<!-- QUICK ACTIONS -->

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


<!-- PUBLIC PROFILE -->

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


    <a
        href="<?php
            echo htmlspecialchars(
                $public_profile_url
            );
        ?>"
        class="profile-button"
    >

        View Public Profile →

    </a>

</div>


</div>

</div>


<?php
$conn->close();
?>

</body>

</html>