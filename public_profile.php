<?php
session_start();

require_once __DIR__ . "/config/database.php";

/* =========================
   GET USERNAME
========================= */

$username = trim($_GET["username"] ?? "");

if ($username === "") {
    die("Username missing.");
}


/* =========================
   GET USER
========================= */

$stmt = $conn->prepare("
    SELECT
        id,
        name,
        username,
        bio,
        education,
        skills,
        linkedin_url,
        github_url,
        profile_image
    FROM users
    WHERE username = ?
    LIMIT 1
");

$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    die("Profile not found.");
}

$user = $result->fetch_assoc();

$stmt->close();


/* =========================
   USER DATA
========================= */

$userId = (int) $user["id"];

$name = $user["name"] ?? "User";
$usernameValue = $user["username"] ?? "";
$bio = $user["bio"] ?? "";
$education = $user["education"] ?? "";
$skills = $user["skills"] ?? "";
$linkedin = $user["linkedin_url"] ?? "";
$github = $user["github_url"] ?? "";
$profileImage = $user["profile_image"] ?? "";


/* =========================
   GET ACHIEVEMENTS
========================= */

$achievementStmt = $conn->prepare("
    SELECT
        title,
        description,
        category,
        date_achieved
    FROM achievements
    WHERE user_id = ?
    ORDER BY date_achieved DESC
");

$achievementStmt->bind_param("i", $userId);
$achievementStmt->execute();

$achievements = $achievementStmt->get_result();


/* =========================
   GET PROJECTS
========================= */

$projectStmt = $conn->prepare("
    SELECT
        id,
        project_name,
        description,
        technologies,
        project_url,
        github_url
    FROM projects
    WHERE user_id = ?
    ORDER BY id DESC
");

$projectStmt->bind_param("i", $userId);
$projectStmt->execute();

$projects = $projectStmt->get_result();


/* =========================
   SKILLS ARRAY
========================= */

$skillList = [];

if (!empty($skills)) {

    $skillList = explode(",", $skills);

    $skillList = array_filter(
        array_map("trim", $skillList)
    );
}


/* =========================
   PROFILE INITIAL
========================= */

$initial = strtoupper(
    substr(trim($name), 0, 1)
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
<?php echo htmlspecialchars($name); ?> - AchieveX
</title>


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

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background:
        #f4f6f9;

    color:
        #111827;

    line-height:
        1.5;
}


/* =========================
   NAVBAR
========================= */

.navbar {

    height: 65px;

    background:
        #1f2937;

    color:
        white;

    padding:
        0 30px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

    position:
        sticky;

    top:
        0;

    z-index:
        100;
}

.navbar h2 {

    font-size:
        22px;
}

.navbar-user {

    font-size:
        14px;

    color:
        #d1d5db;
}


/* =========================
   MAIN CONTAINER
========================= */

.container {

    display:
        flex;

    min-height:
        calc(100vh - 65px);
}


/* =========================
   SIDEBAR
========================= */

.sidebar {

    width:
        220px;

    background:
        #111827;

    color:
        white;

    padding:
        25px 20px;

    flex-shrink:
        0;
}

.sidebar a {

    display:
        block;

    color:
        white;

    text-decoration:
        none;

    padding:
        12px 10px;

    margin-bottom:
        5px;

    border-radius:
        7px;

    transition:
        .25s;
}

.sidebar a:hover {

    background:
        #1f2937;

    transform:
        translateX(3px);
}


/* =========================
   CONTENT
========================= */

.content {

    flex:
        1;

    padding:
        35px;

    max-width:
        1150px;

    margin:
        0 auto;

    width:
        100%;
}

.page-title {

    font-size:
        30px;

    margin-bottom:
        6px;
}

.subtitle {

    color:
        #6b7280;

    margin-bottom:
        25px;
}


/* =========================
   PROFILE CARD
========================= */

.profile-card {

    background:
        white;

    border-radius:
        16px;

    overflow:
        hidden;

    box-shadow:
        0 4px 15px rgba(0,0,0,.08);

    margin-bottom:
        25px;
}


/* =========================
   PROFILE HEADER
========================= */

.profile-header {

    padding:
        35px;

    display:
        flex;

    align-items:
        center;

    gap:
        25px;

    border-bottom:
        1px solid #e5e7eb;
}


/* =========================
   PROFILE IMAGE
========================= */

.profile-image {

    width:
        120px;

    height:
        120px;

    border-radius:
        50%;

    overflow:
        hidden;

    background:
        #1f2937;

    color:
        white;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    font-size:
        42px;

    font-weight:
        bold;

    flex-shrink:
        0;

    border:
        4px solid #e5e7eb;
}

.profile-image img {

    width:
        100%;

    height:
        100%;

    object-fit:
        cover;
}


/* =========================
   PROFILE INFO
========================= */

.profile-info {

    flex:
        1;
}

.profile-info h2 {

    font-size:
        29px;

    margin-bottom:
        3px;
}

.username {

    color:
        #6b7280;

    margin-bottom:
        10px;
}

.bio {

    color:
        #4b5563;

    max-width:
        750px;

    line-height:
        1.7;
}


/* =========================
   SOCIAL LINKS
========================= */

.social-links {

    display:
        flex;

    gap:
        10px;

    flex-wrap:
        wrap;

    margin-top:
        15px;
}

.social-links a {

    text-decoration:
        none;

    padding:
        8px 14px;

    border-radius:
        7px;

    background:
        #1f2937;

    color:
        white;

    font-size:
        13px;

    font-weight:
        bold;

    transition:
        .25s;
}

.social-links a:hover {

    background:
        #111827;

    transform:
        translateY(-2px);
}


/* =========================
   GRID
========================= */

.grid {

    display:
        grid;

    grid-template-columns:
        1fr 1fr;

    gap:
        20px;

    margin-bottom:
        20px;
}


/* =========================
   SECTION CARD
========================= */

.section-card {

    background:
        white;

    border-radius:
        14px;

    padding:
        25px;

    box-shadow:
        0 3px 12px rgba(0,0,0,.07);
}

.section-card.full {

    grid-column:
        1 / -1;
}

.section-title {

    font-size:
        20px;

    margin-bottom:
        16px;

    display:
        flex;

    align-items:
        center;

    gap:
        8px;
}


/* =========================
   EDUCATION
========================= */

.info-box {

    background:
        #f4f6f9;

    padding:
        15px;

    border-radius:
        9px;

    color:
        #374151;

    line-height:
        1.7;
}


/* =========================
   SKILLS
========================= */

.skills {

    display:
        flex;

    flex-wrap:
        wrap;

    gap:
        9px;
}

.skill {

    background:
        #eef2ff;

    color:
        #3730a3;

    border:
        1px solid #c7d2fe;

    padding:
        7px 13px;

    border-radius:
        20px;

    font-size:
        14px;

    font-weight:
        600;

    transition:
        .25s;
}

.skill:hover {

    transform:
        translateY(-2px);
}


/* =========================
   PROJECTS
========================= */

.project {

    border:
        1px solid #e5e7eb;

    border-radius:
        11px;

    padding:
        18px;

    margin-bottom:
        14px;

    background:
        #fafafa;

    transition:
        .25s;
}

.project:last-child {

    margin-bottom:
        0;
}

.project:hover {

    transform:
        translateY(-2px);

    box-shadow:
        0 5px 15px rgba(0,0,0,.07);
}

.project-header {

    display:
        flex;

    justify-content:
        space-between;

    align-items:
        flex-start;

    gap:
        15px;
}

.project-name {

    font-size:
        18px;

    font-weight:
        bold;

    margin-bottom:
        6px;
}

.project-tech {

    color:
        #4f46e5;

    font-size:
        13px;

    font-weight:
        bold;
}

.project-description {

    color:
        #4b5563;

    margin-top:
        12px;

    line-height:
        1.6;
}

.project-links {

    display:
        flex;

    gap:
        8px;

    flex-wrap:
        wrap;

    margin-top:
        14px;
}

.project-links a {

    text-decoration:
        none;

    padding:
        7px 11px;

    border-radius:
        7px;

    font-size:
        12px;

    font-weight:
        bold;
}

.live {

    background:
        #e0e7ff;

    color:
        #3730a3;
}

.project-github {

    background:
        #e5e7eb;

    color:
        #111827;
}


/* =========================
   ACHIEVEMENTS
========================= */

.achievement {

    border:
        1px solid #e5e7eb;

    border-radius:
        10px;

    padding:
        18px;

    margin-bottom:
        13px;

    background:
        #fafafa;
}

.achievement:last-child {

    margin-bottom:
        0;
}

.achievement-title {

    font-size:
        17px;

    font-weight:
        bold;

    margin-bottom:
        8px;
}

.category {

    display:
        inline-block;

    background:
        #e5e7eb;

    color:
        #374151;

    padding:
        4px 9px;

    border-radius:
        15px;

    font-size:
        11px;

    font-weight:
        bold;
}

.achievement-description {

    color:
        #4b5563;

    margin-top:
        10px;

    line-height:
        1.6;
}

.date {

    color:
        #6b7280;

    font-size:
        12px;

    margin-top:
        9px;
}


/* =========================
   EMPTY
========================= */

.empty {

    text-align:
        center;

    padding:
        25px;

    background:
        #f9fafb;

    border-radius:
        9px;

    color:
        #6b7280;
}


/* =========================
   BACK BUTTON
========================= */

.back {

    display:
        inline-block;

    margin-top:
        5px;

    text-decoration:
        none;

    color:
        #1f2937;

    font-weight:
        bold;

    transition:
        .2s;
}

.back:hover {

    transform:
        translateX(-3px);
}


/* =========================
   MOBILE
========================= */

@media (max-width: 800px) {

    .sidebar {

        width:
            180px;
    }

    .content {

        padding:
            25px 20px;
    }

    .grid {

        grid-template-columns:
            1fr;
    }

    .section-card.full {

        grid-column:
            auto;
    }

}


/* =========================
   SMALL MOBILE
========================= */

@media (max-width: 550px) {

    .navbar {

        padding:
            0 15px;
    }

    .navbar-user {

        display:
            none;
    }

    .container {

        display:
            block;
    }

    .sidebar {

        width:
            100%;

        min-height:
            auto;

        display:
            flex;

        overflow-x:
            auto;

        gap:
            5px;

        padding:
            10px;
    }

    .sidebar a {

        white-space:
            nowrap;

        margin:
            0;
    }

    .content {

        padding:
            20px 15px;
    }

    .page-title {

        font-size:
            25px;
    }

    .profile-header {

        flex-direction:
            column;

        text-align:
            center;

        padding:
            25px 18px;
    }

    .profile-info h2 {

        font-size:
            24px;
    }

    .social-links {

        justify-content:
            center;
    }

    .project-header {

        flex-direction:
            column;
    }

}

</style>

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

<div class="navbar">

    <h2>AchieveX</h2>

    <div class="navbar-user">

        <?php

        if (isset($_SESSION["user_name"])) {

            echo htmlspecialchars(
                $_SESSION["user_name"]
            );

        } else {

            echo "Public Profile";

        }

        ?>

    </div>

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

    <a href="#">
        🛠 Skills
    </a>

    <?php if (isset($_SESSION["user_id"])): ?>

        <a href="profile.php">
            My Profile
        </a>

        <a href="logout.php">
            Logout
        </a>

    <?php endif; ?>

</div>


<!-- =========================
     CONTENT
========================= -->

<div class="content">


    <h1 class="page-title">

        <?php echo htmlspecialchars($name); ?>'s Profile

    </h1>

    <p class="subtitle">

        Professional profile on AchieveX

    </p>


    <!-- =========================
         PROFILE HEADER
    ========================= -->

    <div class="profile-card">

        <div class="profile-header">


            <div class="profile-image">

                <?php if (!empty($profileImage)): ?>

                    <img
                        src="<?php
                            echo htmlspecialchars(
                                $profileImage
                            );
                        ?>"
                        alt="Profile Image"
                    >

                <?php else: ?>

                    <?php echo htmlspecialchars($initial); ?>

                <?php endif; ?>

            </div>


            <div class="profile-info">

                <h2>

                    <?php
                    echo htmlspecialchars($name);
                    ?>

                </h2>


                <?php if (!empty($usernameValue)): ?>

                    <div class="username">

                        @<?php
                        echo htmlspecialchars(
                            $usernameValue
                        );
                        ?>

                    </div>

                <?php endif; ?>


                <?php if (!empty($bio)): ?>

                    <div class="bio">

                        <?php

                        echo nl2br(
                            htmlspecialchars($bio)
                        );

                        ?>

                    </div>

                <?php endif; ?>


                <?php if (
                    !empty($linkedin) ||
                    !empty($github)
                ): ?>

                    <div class="social-links">


                        <?php if (!empty($linkedin)): ?>

                            <a
                                href="<?php
                                    echo htmlspecialchars(
                                        $linkedin
                                    );
                                ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >

                                🔗 LinkedIn

                            </a>

                        <?php endif; ?>


                        <?php if (!empty($github)): ?>

                            <a
                                href="<?php
                                    echo htmlspecialchars(
                                        $github
                                    );
                                ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >

                                💻 GitHub

                            </a>

                        <?php endif; ?>


                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>


    <!-- =========================
         EDUCATION + SKILLS
    ========================= -->

    <div class="grid">


        <?php if (!empty($education)): ?>

            <div class="section-card">

                <h3 class="section-title">

                    🎓 Education

                </h3>

                <div class="info-box">

                    <?php

                    echo nl2br(
                        htmlspecialchars(
                            $education
                        )
                    );

                    ?>

                </div>

            </div>

        <?php endif; ?>


        <?php if (!empty($skillList)): ?>

            <div class="section-card">

                <h3 class="section-title">

                    🛠 Skills

                </h3>

                <div class="skills">

                    <?php foreach ($skillList as $skill): ?>

                        <span class="skill">

                            <?php
                            echo htmlspecialchars($skill);
                            ?>

                        </span>

                    <?php endforeach; ?>

                </div>

            </div>

        <?php endif; ?>


        <!-- =========================
             PROJECTS
        ========================= -->

        <div class="section-card full">

            <h3 class="section-title">

                💻 Projects

            </h3>


            <?php if ($projects->num_rows > 0): ?>


                <?php while (
                    $project =
                    $projects->fetch_assoc()
                ): ?>


                    <div class="project">


                        <div class="project-header">


                            <div>

                                <div class="project-name">

                                    💻

                                    <?php

                                    echo htmlspecialchars(
                                        $project["project_name"]
                                    );

                                    ?>

                                </div>


                                <?php if (
                                    !empty(
                                        $project["technologies"]
                                    )
                                ): ?>

                                    <div class="project-tech">

                                        🛠

                                        <?php

                                        echo htmlspecialchars(
                                            $project["technologies"]
                                        );

                                        ?>

                                    </div>

                                <?php endif; ?>

                            </div>


                        </div>


                        <?php if (
                            !empty(
                                $project["description"]
                            )
                        ): ?>

                            <div class="project-description">

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $project["description"]
                                    )
                                );

                                ?>

                            </div>

                        <?php endif; ?>


                        <?php if (
                            !empty(
                                $project["project_url"]
                            ) ||
                            !empty(
                                $project["github_url"]
                            )
                        ): ?>

                            <div class="project-links">


                                <?php if (
                                    !empty(
                                        $project["project_url"]
                                    )
                                ): ?>

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                $project[
                                                    "project_url"
                                                ]
                                            );
                                        ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="live"
                                    >

                                        🔗 Live Project

                                    </a>

                                <?php endif; ?>


                                <?php if (
                                    !empty(
                                        $project["github_url"]
                                    )
                                ): ?>

                                    <a
                                        href="<?php
                                            echo htmlspecialchars(
                                                $project[
                                                    "github_url"
                                                ]
                                            );
                                        ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="project-github"
                                    >

                                        💻 GitHub

                                    </a>

                                <?php endif; ?>


                            </div>

                        <?php endif; ?>


                    </div>


                <?php endwhile; ?>


            <?php else: ?>


                <div class="empty">

                    No projects added yet.

                </div>


            <?php endif; ?>


        </div>


        <!-- =========================
             ACHIEVEMENTS
        ========================= -->

        <div class="section-card full">

            <h3 class="section-title">

                🏆 Achievements

            </h3>


            <?php if (
                $achievements->num_rows > 0
            ): ?>


                <?php while (
                    $achievement =
                    $achievements->fetch_assoc()
                ): ?>


                    <div class="achievement">


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


                        <?php if (
                            !empty(
                                $achievement["description"]
                            )
                        ): ?>

                            <div class="achievement-description">

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $achievement[
                                            "description"
                                        ]
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

                    No achievements added yet.

                </div>


            <?php endif; ?>


        </div>


    </div>


    <a
        href="dashboard.php"
        class="back"
    >

        ← Back to Dashboard

    </a>


</div>

</div>


</body>

</html>


<?php

$achievementStmt->close();

$projectStmt->close();

$conn->close();

?>