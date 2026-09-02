<?php
session_start();

require_once __DIR__ . "/config/database.php";

/* =========================================================
   GET USERNAME
========================================================= */

$username = trim($_GET["username"] ?? "");

/*
   Agar username URL me nahi diya gaya aur user logged in hai,
   to current logged-in user ka username use hoga.
*/
if ($username === "" && isset($_SESSION["user_id"])) {

    $userId = (int) $_SESSION["user_id"];

    $userStmt = $conn->prepare("
        SELECT username
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    $userStmt->bind_param("i", $userId);
    $userStmt->execute();

    $userResult = $userStmt->get_result();

    if ($userResult->num_rows === 1) {

        $tempUser = $userResult->fetch_assoc();
        $username = $tempUser["username"];

    }

    $userStmt->close();
}


if ($username === "") {
    die("Username missing.");
}


/* =========================================================
   GET USER
========================================================= */

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


/* =========================================================
   USER DATA
========================================================= */

$userId = (int) $user["id"];

$name = $user["name"] ?? "User";
$usernameValue = $user["username"] ?? "";
$bio = $user["bio"] ?? "";
$education = $user["education"] ?? "";
$skills = $user["skills"] ?? "";
$linkedin = $user["linkedin_url"] ?? "";
$github = $user["github_url"] ?? "";
$profileImage = $user["profile_image"] ?? "";


/* =========================================================
   GET ACHIEVEMENTS
========================================================= */

$achievementStmt = $conn->prepare("
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

$achievementStmt->bind_param("i", $userId);
$achievementStmt->execute();

$achievements = $achievementStmt->get_result();


/* =========================================================
   GET PROJECTS
========================================================= */

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


/* =========================================================
   GET CERTIFICATES
========================================================= */

$certificateStmt = $conn->prepare("
    SELECT
        id,
        title,
        issuer,
        description,
        certificate_date
    FROM certificates
    WHERE user_id = ?
    ORDER BY certificate_date DESC, id DESC
");

$certificateStmt->bind_param("i", $userId);
$certificateStmt->execute();

$certificates = $certificateStmt->get_result();


/* =========================================================
   SKILL LIST
========================================================= */

$skillList = [];

if (!empty($skills)) {

    $skillList = explode(",", $skills);

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

<title>
<?php echo htmlspecialchars($name); ?> - AchieveX
</title>


<style>

/* =========================================================
   RESET
========================================================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


/* =========================================================
   BODY
========================================================= */

body {

    font-family: Arial, sans-serif;

    background: #f4f6f9;

    color: #111827;

}


/* =========================================================
   NAVBAR
========================================================= */

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

    font-size: 22px;

}

.navbar span {

    font-size: 15px;

}


/* =========================================================
   MAIN LAYOUT
========================================================= */

.container {

    display: flex;

    min-height: calc(100vh - 65px);

}


/* =========================================================
   SIDEBAR
========================================================= */

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


/* =========================================================
   CONTENT
========================================================= */

.content {

    flex: 1;

    padding: 35px;

    max-width: 1150px;

}

.content h1 {

    font-size: 30px;

    margin-bottom: 8px;

}

.subtitle {

    color: #6b7280;

    margin-bottom: 25px;

}


/* =========================================================
   PROFILE CARD
========================================================= */

.profile-card {

    background: white;

    border-radius: 14px;

    box-shadow: 0 3px 12px rgba(0,0,0,0.08);

    overflow: hidden;

    transition: box-shadow 0.3s ease;

}

.profile-card.active {

    box-shadow: 0 8px 28px rgba(0,0,0,0.12);

}


/* =========================================================
   PROFILE HEADER
========================================================= */

.profile-header {

    display: flex;

    align-items: center;

    gap: 25px;

    padding: 30px;

    border-bottom: 1px solid #e5e7eb;

}


/* =========================================================
   PROFILE IMAGE
========================================================= */

.profile-image {

    width: 115px;

    height: 115px;

    border-radius: 50%;

    overflow: hidden;

    background: #1f2937;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 42px;

    font-weight: bold;

    flex-shrink: 0;

    transition: transform 0.4s ease;

}

.profile-image img {

    width: 100%;

    height: 100%;

    object-fit: cover;

}

.profile-card.active .profile-image {

    transform: scale(1.04);

}


/* =========================================================
   PROFILE INFO
========================================================= */

.profile-info {

    flex: 1;

}

.profile-info h2 {

    font-size: 28px;

    margin-bottom: 5px;

}

.username {

    color: #6b7280;

    margin-bottom: 10px;

}

.bio {

    color: #4b5563;

    line-height: 1.6;

    max-width: 750px;

}


/* =========================================================
   DETAILS BUTTON
========================================================= */

.details-button {

    margin-top: 18px;

    padding: 10px 18px;

    border: none;

    border-radius: 8px;

    background: #1f2937;

    color: white;

    font-size: 14px;

    font-weight: bold;

    cursor: pointer;

    transition: 0.25s;

}

.details-button:hover {

    background: #111827;

    transform: translateY(-1px);

}


/* =========================================================
   DETAILS SLIDE
========================================================= */

.details {

    max-height: 0;

    opacity: 0;

    overflow: hidden;

    transform: translateY(-15px);

    transition:
        max-height 0.8s ease,
        opacity 0.45s ease,
        transform 0.5s ease;

}

.profile-card.active .details {

    max-height: 10000px;

    opacity: 1;

    transform: translateY(0);

}


/* =========================================================
   SECTION
========================================================= */

.section {

    padding: 28px 30px;

    border-bottom: 1px solid #e5e7eb;

}

.section:last-child {

    border-bottom: none;

}

.section h3 {

    font-size: 20px;

    margin-bottom: 17px;

}


/* =========================================================
   INFO BOX
========================================================= */

.info-box {

    background: #f4f6f9;

    padding: 16px;

    border-radius: 9px;

    color: #374151;

    line-height: 1.6;

}


/* =========================================================
   SKILLS
========================================================= */

.skills {

    display: flex;

    flex-wrap: wrap;

    gap: 9px;

}

.skill {

    background: #eef2ff;

    border: 1px solid #c7d2fe;

    color: #3730a3;

    padding: 8px 13px;

    border-radius: 20px;

    font-size: 14px;

    font-weight: 600;

}


/* =========================================================
   LINKS
========================================================= */

.links {

    display: flex;

    flex-wrap: wrap;

    gap: 10px;

}

.links a {

    display: inline-block;

    padding: 10px 16px;

    background: #1f2937;

    color: white;

    text-decoration: none;

    border-radius: 8px;

    font-weight: bold;

    transition: 0.25s;

}

.links a:hover {

    background: #111827;

    transform: translateY(-1px);

}


/* =========================================================
   ACHIEVEMENTS
========================================================= */

.achievement {

    background: #f9fafb;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

    padding: 18px;

    margin-bottom: 13px;

}

.achievement:last-child {

    margin-bottom: 0;

}

.achievement h4 {

    font-size: 17px;

    margin-bottom: 8px;

}

.achievement p {

    color: #4b5563;

    line-height: 1.6;

    margin-top: 10px;

}

.category {

    display: inline-block;

    font-size: 12px;

    background: #e5e7eb;

    color: #374151;

    padding: 5px 9px;

    border-radius: 15px;

}

.date {

    margin-top: 9px;

    color: #6b7280;

    font-size: 13px;

}


/* =========================================================
   PROJECTS
========================================================= */

.project-card {

    background: #f9fafb;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

    padding: 18px;

    margin-bottom: 13px;

}

.project-card:last-child {

    margin-bottom: 0;

}

.project-header {

    display: flex;

    justify-content: space-between;

    gap: 15px;

}

.project-name {

    font-size: 18px;

    font-weight: bold;

    margin-bottom: 8px;

}

.technologies {

    color: #4f46e5;

    font-size: 14px;

    font-weight: bold;

}

.project-description {

    color: #4b5563;

    line-height: 1.6;

    margin-top: 12px;

}

.project-links {

    display: flex;

    flex-wrap: wrap;

    gap: 9px;

    margin-top: 15px;

}

.project-links a {

    text-decoration: none;

    padding: 8px 12px;

    border-radius: 7px;

    font-size: 13px;

    font-weight: bold;

}

.live-project {

    background: #e0e7ff;

    color: #3730a3;

}

.github-project {

    background: #e5e7eb;

    color: #111827;

}


/* =========================================================
   CERTIFICATES
========================================================= */

.certificate-card {

    background: #f9fafb;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

    padding: 18px;

    margin-bottom: 13px;

}

.certificate-card:last-child {

    margin-bottom: 0;

}

.certificate-title {

    font-size: 18px;

    font-weight: bold;

    margin-bottom: 7px;

}

.issuer {

    color: #4f46e5;

    font-weight: bold;

    font-size: 14px;

}

.certificate-description {

    color: #4b5563;

    line-height: 1.6;

    margin-top: 10px;

}

.certificate-date {

    color: #6b7280;

    font-size: 13px;

    margin-top: 9px;

}


/* =========================================================
   EMPTY
========================================================= */

.empty {

    background: #f4f6f9;

    padding: 18px;

    border-radius: 9px;

    color: #6b7280;

}


/* =========================================================
   BACK BUTTON
========================================================= */

.back {

    display: inline-block;

    margin-top: 20px;

    color: #1f2937;

    text-decoration: none;

    font-weight: bold;

}

.back:hover {

    text-decoration: underline;

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 700px) {

    .sidebar {

        width: 170px;

        padding: 20px 12px;

    }

    .content {

        padding: 20px 15px;

    }

    .profile-header {

        flex-direction: column;

        text-align: center;

        padding: 25px 20px;

    }

    .profile-info h2 {

        font-size: 23px;

    }

    .section {

        padding: 22px 20px;

    }

    .project-header {

        flex-direction: column;

    }

}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 500px) {

    .navbar {

        padding: 0 15px;

    }

    .navbar span {

        display: none;

    }

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

        padding: 18px 12px;

    }

    .content h1 {

        font-size: 25px;

    }

    .profile-image {

        width: 100px;

        height: 100px;

    }

    .section {

        padding: 20px 15px;

    }

    .links a {

        flex: 1;

        text-align: center;

    }

}

</style>

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<div class="navbar">

    <h2>AchieveX</h2>

    <span>

        <?php

        if (isset($_SESSION["user_name"])) {

            echo htmlspecialchars(
                $_SESSION["user_name"]
            );

        } else {

            echo "Public Profile";

        }

        ?>

    </span>

</div>


<!-- =====================================================
     MAIN
===================================================== -->

<div class="container">


    <!-- =================================================
         SIDEBAR
    ================================================== -->

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

        <a href="profile.php">
            👤 My Profile
        </a>

        <?php if (isset($_SESSION["user_id"])): ?>

            <a href="logout.php">
                Logout
            </a>

        <?php endif; ?>

    </div>


    <!-- =================================================
         CONTENT
    ================================================== -->

    <div class="content">

        <h1>

            <?php
            echo htmlspecialchars($name);
            ?>'s Profile

        </h1>

        <p class="subtitle">

            Professional profile on AchieveX

        </p>


        <!-- =================================================
             PROFILE CARD
        ================================================== -->

        <div
            class="profile-card"
            id="profileCard"
        >


            <!-- PROFILE HEADER -->

            <div class="profile-header">


                <!-- PROFILE IMAGE -->

                <div class="profile-image">

                    <?php if (!empty($profileImage)): ?>

                        <img
                            src="<?php
                            echo htmlspecialchars(
                                $profileImage
                            );
                            ?>"
                            alt="Profile"
                        >

                    <?php else: ?>

                        <?php

                        echo strtoupper(
                            substr(
                                $name,
                                0,
                                1
                            )
                        );

                        ?>

                    <?php endif; ?>

                </div>


                <!-- PROFILE INFO -->

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


                    <!-- DETAILS BUTTON -->

                    <button
                        type="button"
                        class="details-button"
                        id="detailsButton"
                        onclick="toggleDetails()"
                    >

                        <span id="buttonText">
                            More Details
                        </span>

                        <span id="buttonIcon">
                            ↓
                        </span>

                    </button>

                </div>

            </div>


            <!-- =================================================
                 ALL DETAILS
            ================================================== -->

            <div
                class="details"
                id="details"
            >


                <!-- =================================================
                     EDUCATION
                ================================================== -->

                <?php if (!empty($education)): ?>

                    <div class="section">

                        <h3>
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


                <!-- =================================================
                     SKILLS
                ================================================== -->

                <?php if (!empty($skillList)): ?>

                    <div class="section">

                        <h3>
                            🛠 Skills
                        </h3>

                        <div class="skills">

                            <?php foreach ($skillList as $skill): ?>

                                <?php

                                $skill = trim($skill);

                                ?>

                                <?php if ($skill !== ""): ?>

                                    <span class="skill">

                                        <?php
                                        echo htmlspecialchars(
                                            $skill
                                        );
                                        ?>

                                    </span>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </div>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     LINKS
                ================================================== -->

                <?php if (
                    !empty($linkedin) ||
                    !empty($github)
                ): ?>

                    <div class="section">

                        <h3>
                            🔗 Links
                        </h3>

                        <div class="links">

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
                                    LinkedIn
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
                                    GitHub
                                </a>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     ACHIEVEMENTS
                ================================================== -->

                <div class="section">

                    <h3>
                        🏆 Achievements
                    </h3>


                    <?php if ($achievements->num_rows > 0): ?>

                        <?php while (
                            $achievement =
                            $achievements->fetch_assoc()
                        ): ?>

                            <div class="achievement">

                                <h4>

                                    🏆

                                    <?php
                                    echo htmlspecialchars(
                                        $achievement["title"]
                                    );
                                    ?>

                                </h4>


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

                                    <p>

                                        <?php

                                        echo nl2br(
                                            htmlspecialchars(
                                                $achievement[
                                                    "description"
                                                ]
                                            )
                                        );

                                        ?>

                                    </p>

                                <?php endif; ?>


                                <?php if (
                                    !empty(
                                        $achievement["date_achieved"]
                                    )
                                ): ?>

                                    <div class="date">

                                        📅 Date:

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


                <!-- =================================================
                     PROJECTS
                ================================================== -->

                <div class="section">

                    <h3>
                        💻 Projects
                    </h3>


                    <?php if ($projects->num_rows > 0): ?>

                        <?php while (
                            $project =
                            $projects->fetch_assoc()
                        ): ?>

                            <div class="project-card">


                                <div class="project-header">

                                    <div>

                                        <div class="project-name">

                                            💻

                                            <?php
                                            echo htmlspecialchars(
                                                $project[
                                                    "project_name"
                                                ]
                                            );
                                            ?>

                                        </div>


                                        <?php if (
                                            !empty(
                                                $project[
                                                    "technologies"
                                                ]
                                            )
                                        ): ?>

                                            <div class="technologies">

                                                🛠

                                                <?php
                                                echo htmlspecialchars(
                                                    $project[
                                                        "technologies"
                                                    ]
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
                                                $project[
                                                    "description"
                                                ]
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
                                                $project[
                                                    "project_url"
                                                ]
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
                                                class="live-project"
                                            >
                                                🔗 Live Project
                                            </a>

                                        <?php endif; ?>


                                        <?php if (
                                            !empty(
                                                $project[
                                                    "github_url"
                                                ]
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
                                                class="github-project"
                                            >
                                                GitHub
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


                <!-- =================================================
                     CERTIFICATES
                ================================================== -->

                <div class="section">

                    <h3>
                        📜 Certificates
                    </h3>


                    <?php if ($certificates->num_rows > 0): ?>

                        <?php while (
                            $certificate =
                            $certificates->fetch_assoc()
                        ): ?>

                            <div class="certificate-card">


                                <div class="certificate-title">

                                    📜

                                    <?php
                                    echo htmlspecialchars(
                                        $certificate["title"]
                                    );
                                    ?>

                                </div>


                                <?php if (
                                    !empty(
                                        $certificate["issuer"]
                                    )
                                ): ?>

                                    <div class="issuer">

                                        Issued by:

                                        <?php
                                        echo htmlspecialchars(
                                            $certificate["issuer"]
                                        );
                                        ?>

                                    </div>

                                <?php endif; ?>


                                <?php if (
                                    !empty(
                                        $certificate["description"]
                                    )
                                ): ?>

                                    <div class="certificate-description">

                                        <?php

                                        echo nl2br(
                                            htmlspecialchars(
                                                $certificate[
                                                    "description"
                                                ]
                                            )
                                        );

                                        ?>

                                    </div>

                                <?php endif; ?>


                                <?php if (
                                    !empty(
                                        $certificate[
                                            "certificate_date"
                                        ]
                                    )
                                ): ?>

                                    <div class="certificate-date">

                                        📅 Date:

                                        <?php
                                        echo htmlspecialchars(
                                            $certificate[
                                                "certificate_date"
                                            ]
                                        );
                                        ?>

                                    </div>

                                <?php endif; ?>


                            </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <div class="empty">

                            No certificates added yet.

                        </div>

                    <?php endif; ?>

                </div>


            </div>

        </div>


        <!-- BACK -->

        <a
            href="dashboard.php"
            class="back"
        >
            ← Back to Dashboard
        </a>


    </div>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

function toggleDetails() {

    const card =
        document.getElementById("profileCard");

    const buttonText =
        document.getElementById("buttonText");

    const buttonIcon =
        document.getElementById("buttonIcon");


    card.classList.toggle("active");


    if (card.classList.contains("active")) {

        buttonText.textContent =
            "Hide Details";

        buttonIcon.textContent =
            "↑";

    } else {

        buttonText.textContent =
            "More Details";

        buttonIcon.textContent =
            "↓";

    }

}

</script>


</body>

</html>


<?php

/* =========================================================
   CLOSE DATABASE
========================================================= */

$achievementStmt->close();
$projectStmt->close();
$certificateStmt->close();

$conn->close();

?>