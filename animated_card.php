<?php

require_once __DIR__ . "/config/database.php";

/*
|--------------------------------------------------------------------------
| Dynamic Profile ID
|--------------------------------------------------------------------------
| URL example:
| animated_card.php?id=1
| animated_card.php?id=2
|--------------------------------------------------------------------------
*/

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid profile ID.");
}

$user_id = (int) $_GET["id"];


/*
|--------------------------------------------------------------------------
| Fetch User
|--------------------------------------------------------------------------
*/

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
    WHERE id = ?
");

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| Profile Not Found
|--------------------------------------------------------------------------
*/

if ($result->num_rows !== 1) {
    die("Profile not found.");
}


$user = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Profile Data
|--------------------------------------------------------------------------
*/

$name = $user["name"] ?? "User";

$username = $user["username"] ?? "";

$bio = $user["bio"] ?? "No bio available.";

$education = $user["education"] ?? "Not provided.";

$skills = $user["skills"] ?? "";

$linkedin = $user["linkedin_url"] ?? "#";

$github = $user["github_url"] ?? "#";

$profileImage = $user["profile_image"] ?? "";

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>AchieveX - Profile</title>

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

    min-height: 100vh;

    display: flex;

    justify-content: center;

    align-items: center;

    padding: 30px;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    color: white;

    background:
        radial-gradient(
            circle at top left,
            #312e81,
            transparent 35%
        ),

        radial-gradient(
            circle at bottom right,
            #0f766e,
            transparent 35%
        ),

        #050816;
}


/* =========================
   CARD WRAPPER
========================= */

.card-wrapper {

    width: 100%;

    max-width: 430px;

    perspective: 1200px;
}


/* =========================
   PROFILE CARD
========================= */

.profile-card {

    position: relative;

    width: 100%;

    min-height: 500px;

    padding: 30px;

    border-radius: 28px;

    overflow: hidden;

    background:
        linear-gradient(
            145deg,
            rgba(255,255,255,0.12),
            rgba(255,255,255,0.04)
        );

    border:
        1px solid
        rgba(255,255,255,0.15);

    backdrop-filter: blur(20px);

    box-shadow:
        0 30px 80px
        rgba(0,0,0,0.45);

    transition:
        transform 0.6s
        cubic-bezier(.2,.8,.2,1),

        box-shadow 0.6s ease;
}


/* =========================
   GLOW
========================= */

.profile-card::before {

    content: "";

    position: absolute;

    width: 250px;

    height: 250px;

    top: -120px;

    right: -100px;

    background:
        rgba(99,102,241,0.35);

    filter: blur(60px);

    border-radius: 50%;

    pointer-events: none;
}


/* =========================
   PROFILE TOP
========================= */

.profile-top {

    position: relative;

    z-index: 2;

    display: flex;

    flex-direction: column;

    align-items: center;

    text-align: center;
}


/* =========================
   PROFILE IMAGE
========================= */

.profile-image {

    width: 130px;

    height: 130px;

    padding: 4px;

    margin-bottom: 22px;

    border-radius: 50%;

    background:
        linear-gradient(
            135deg,
            #818cf8,
            #22d3ee,
            #a78bfa
        );

    box-shadow:
        0 0 40px
        rgba(99,102,241,0.4);

    animation:
        floating 4s
        ease-in-out
        infinite;
}


.profile-image-inner {

    width: 100%;

    height: 100%;

    border-radius: 50%;

    overflow: hidden;

    display: flex;

    justify-content: center;

    align-items: center;

    background:
        #111827;

    font-size: 42px;

    font-weight: bold;
}


.profile-image-inner img {

    width: 100%;

    height: 100%;

    object-fit: cover;
}


@keyframes floating {

    0%,
    100% {

        transform:
            translateY(0);
    }

    50% {

        transform:
            translateY(-8px);
    }
}


/* =========================
   NAME
========================= */

.name {

    font-size: 30px;

    font-weight: 700;

    margin-bottom: 6px;
}


.username {

    color:
        #a5b4fc;

    font-size: 15px;

    margin-bottom: 18px;
}


.bio {

    max-width: 330px;

    color:
        #cbd5e1;

    line-height: 1.6;
}


/* =========================
   MORE DETAILS BUTTON
========================= */

.details-button {

    position: relative;

    z-index: 5;

    margin-top: 28px;

    padding:
        13px 25px;

    border: none;

    border-radius: 999px;

    background:
        white;

    color:
        #111827;

    font-size: 15px;

    font-weight: 700;

    cursor: pointer;

    transition:
        transform 0.3s ease,

        box-shadow 0.3s ease;
}


.details-button:hover {

    transform:
        translateY(-3px);

    box-shadow:
        0 12px 30px
        rgba(255,255,255,0.2);
}


/* =========================
   DETAILS
========================= */

.details {

    position: relative;

    z-index: 2;

    max-height: 0;

    overflow: hidden;

    opacity: 0;

    transform:
        translateY(25px);

    transition:
        max-height 0.7s
        cubic-bezier(.2,.8,.2,1),

        opacity 0.5s ease,

        transform 0.6s ease;
}


/* =========================
   OPEN DETAILS
========================= */

.profile-card.active
.details {

    max-height: 600px;

    opacity: 1;

    transform:
        translateY(0);
}


/* =========================
   ACTIVE CARD
========================= */

.profile-card.active {

    transform:
        translateY(-5px);

    box-shadow:
        0 40px 100px
        rgba(0,0,0,0.55),

        0 0 50px
        rgba(99,102,241,0.15);
}


/* =========================
   DETAIL SECTION
========================= */

.detail-section {

    margin-top: 25px;

    padding-top: 20px;

    border-top:
        1px solid
        rgba(255,255,255,0.1);
}


.detail-section h3 {

    margin-bottom: 10px;

    color:
        #94a3b8;

    font-size: 13px;

    text-transform:
        uppercase;

    letter-spacing: 1px;
}


.detail-section p {

    color:
        #e2e8f0;

    line-height: 1.6;
}


/* =========================
   SKILLS
========================= */

.skills {

    display: flex;

    flex-wrap: wrap;

    gap: 8px;
}


.skill {

    padding:
        7px 11px;

    border-radius: 999px;

    background:
        rgba(99,102,241,0.18);

    border:
        1px solid
        rgba(129,140,248,0.25);

    color:
        #c7d2fe;

    font-size: 12px;
}


/* =========================
   SOCIAL LINKS
========================= */

.social-links {

    display: flex;

    gap: 10px;

    margin-top: 20px;
}


.social-links a {

    flex: 1;

    padding: 11px;

    border-radius: 10px;

    text-align: center;

    text-decoration: none;

    color: white;

    background:
        rgba(255,255,255,0.08);

    border:
        1px solid
        rgba(255,255,255,0.1);

    transition:
        background 0.3s ease,

        transform 0.3s ease;
}


.social-links a:hover {

    background:
        rgba(255,255,255,0.15);

    transform:
        translateY(-2px);
}


/* =========================
   MOBILE
========================= */

@media (max-width: 500px) {

    body {

        padding: 15px;
    }

    .profile-card {

        padding: 24px;
    }

    .name {

        font-size: 26px;
    }

    .profile-image {

        width: 110px;

        height: 110px;
    }
}

</style>

</head>


<body>


<div class="card-wrapper">

    <div
        class="profile-card"
        id="profileCard"
    >


        <!-- PROFILE -->

        <div class="profile-top">


            <div class="profile-image">

                <div
                    class="profile-image-inner"
                >

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

            </div>


            <h1 class="name">

                <?php

                echo htmlspecialchars(
                    $name
                );

                ?>

            </h1>


            <div class="username">

                @<?php

                echo htmlspecialchars(
                    $username
                );

                ?>

            </div>


            <p class="bio">

                <?php

                echo htmlspecialchars(
                    $bio
                );

                ?>

            </p>


            <button
                type="button"
                class="details-button"
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


        <!-- DETAILS -->

        <div
            class="details"
            id="details"
        >


            <!-- EDUCATION -->

            <div class="detail-section">

                <h3>
                    Education
                </h3>

                <p>

                    <?php

                    echo htmlspecialchars(
                        $education
                    );

                    ?>

                </p>

            </div>


            <!-- ABOUT -->

            <div class="detail-section">

                <h3>
                    About
                </h3>

                <p>

                    <?php

                    echo htmlspecialchars(
                        $bio
                    );

                    ?>

                </p>

            </div>


            <!-- SKILLS -->

            <div class="detail-section">

                <h3>
                    Skills
                </h3>

                <div class="skills">

                    <?php

                    $skillList =
                        explode(
                            ",",
                            $skills
                        );

                    foreach (
                        $skillList
                        as $skill
                    ):

                        $skill =
                            trim($skill);

                    ?>

                        <span class="skill">

                            <?php

                            echo htmlspecialchars(
                                $skill
                            );

                            ?>

                        </span>

                    <?php endforeach; ?>

                </div>

            </div>


            <!-- ACHIEVEMENTS -->

            <div class="detail-section">

                <h3>
                    Achievements
                </h3>

                <p>
                    🏆 View achievements
                    and accomplishments
                </p>

            </div>


            <!-- SOCIAL -->

            <div class="social-links">

                <a
                    href="<?php
                        echo htmlspecialchars(
                            $linkedin
                        );
                    ?>"
                    target="_blank"
                >
                    LinkedIn
                </a>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $github
                        );
                    ?>"
                    target="_blank"
                >
                    GitHub
                </a>

            </div>


        </div>


    </div>

</div>


<script>

/* =========================
   TOGGLE DETAILS
========================= */

function toggleDetails() {

    const card =
        document.getElementById(
            "profileCard"
        );

    const buttonText =
        document.getElementById(
            "buttonText"
        );

    const buttonIcon =
        document.getElementById(
            "buttonIcon"
        );


    card.classList.toggle(
        "active"
    );


    if (
        card.classList.contains(
            "active"
        )
    ) {

        buttonText.innerText =
            "Hide Details";

        buttonIcon.innerText =
            "↑";

    } else {

        buttonText.innerText =
            "More Details";

        buttonIcon.innerText =
            "↓";

    }

}

</script>


</body>

</html>