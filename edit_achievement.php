<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$user_id = $_SESSION["user_id"];
$achievement_id = (int)($_GET["id"] ?? 0);

if ($achievement_id <= 0) {
    die("Invalid achievement.");
}


/* =========================
   GET ACHIEVEMENT
========================= */

$stmt = $conn->prepare("
    SELECT
        id,
        title,
        description,
        category,
        date_achieved
    FROM achievements
    WHERE id = ? AND user_id = ?
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $achievement_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Achievement not found.");
}

$achievement = $result->fetch_assoc();

$stmt->close();


/* =========================
   UPDATE ACHIEVEMENT
========================= */

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $date_achieved = $_POST["date_achieved"] ?? "";

    if ($title === "") {

        $message = "Please enter achievement title.";

    } else {

        $update = $conn->prepare("
            UPDATE achievements
            SET
                title = ?,
                description = ?,
                category = ?,
                date_achieved = ?
            WHERE id = ? AND user_id = ?
        ");

        $update->bind_param(
            "ssssii",
            $title,
            $description,
            $category,
            $date_achieved,
            $achievement_id,
            $user_id
        );

        if ($update->execute()) {

            $update->close();

            header(
                "Location: achievements.php"
            );

            exit;

        } else {

            $message =
                "Something went wrong. Please try again.";

            $update->close();
        }
    }
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

<title>Edit Achievement - AchieveX</title>


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


/* =========================
   NAVBAR
========================= */

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


/* =========================
   LAYOUT
========================= */

.container {

    display: flex;

    min-height:
        calc(100vh - 65px);
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

    transition: .25s;
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

    max-width: 1000px;
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
   FORM CARD
========================= */

.form-card {

    background: white;

    padding: 30px;

    border-radius: 12px;

    box-shadow:
        0 2px 8px rgba(0,0,0,.08);
}


/* =========================
   MESSAGE
========================= */

.message {

    background: #fee2e2;

    color: #991b1b;

    padding: 12px;

    border-radius: 8px;

    margin-bottom: 20px;
}


/* =========================
   FORM
========================= */

.form-group {

    margin-bottom: 20px;
}

label {

    display: block;

    margin-bottom: 8px;

    font-weight: bold;
}

input,
textarea,
select {

    width: 100%;

    padding: 12px;

    border:
        1px solid #d1d5db;

    border-radius: 8px;

    font-size: 15px;

    outline: none;

    background: white;
}

input:focus,
textarea:focus,
select:focus {

    border-color: #4f46e5;
}

textarea {

    min-height: 130px;

    resize: vertical;
}


/* =========================
   BUTTONS
========================= */

.buttons {

    display: flex;

    gap: 10px;

    margin-top: 25px;
}

button {

    border: none;

    padding: 12px 20px;

    border-radius: 8px;

    background: #4f46e5;

    color: white;

    font-size: 15px;

    font-weight: bold;

    cursor: pointer;
}

button:hover {

    background: #4338ca;
}

.cancel {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding: 12px 20px;

    border-radius: 8px;

    background: #e5e7eb;

    color: #374151;

    text-decoration: none;

    font-weight: bold;
}

.cancel:hover {

    background: #d1d5db;
}


/* =========================
   MOBILE
========================= */

@media (max-width: 700px) {

    .sidebar {

        width: 170px;

        padding: 20px 12px;
    }

    .content {

        padding: 20px 15px;
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

    .buttons {

        flex-direction: column;
    }

    button,
    .cancel {

        width: 100%;
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


<!-- =========================
     CONTENT
========================= -->

<div class="content">

    <h1>
        ✏️ Edit Achievement
    </h1>

    <p class="subtitle">
        Update your achievement details.
    </p>


    <div class="form-card">


        <?php if (!empty($message)): ?>

            <div class="message">

                <?php

                echo htmlspecialchars(
                    $message
                );

                ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <!-- TITLE -->

            <div class="form-group">

                <label for="title">
                    Achievement Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?php
                        echo htmlspecialchars(
                            $achievement["title"]
                        );
                    ?>"
                    required
                >

            </div>


            <!-- CATEGORY -->

            <div class="form-group">

                <label for="category">
                    Category
                </label>

                <select
                    id="category"
                    name="category"
                >

                    <option value="">
                        Select Category
                    </option>

                    <option
                        value="Academic"
                        <?php
                        if (
                            $achievement["category"]
                            === "Academic"
                        ) echo "selected";
                        ?>
                    >
                        Academic
                    </option>

                    <option
                        value="Hackathon"
                        <?php
                        if (
                            $achievement["category"]
                            === "Hackathon"
                        ) echo "selected";
                        ?>
                    >
                        Hackathon
                    </option>

                    <option
                        value="Technical"
                        <?php
                        if (
                            $achievement["category"]
                            === "Technical"
                        ) echo "selected";
                        ?>
                    >
                        Technical
                    </option>

                    <option
                        value="Sports"
                        <?php
                        if (
                            $achievement["category"]
                            === "Sports"
                        ) echo "selected";
                        ?>
                    >
                        Sports
                    </option>

                    <option
                        value="Certification"
                        <?php
                        if (
                            $achievement["category"]
                            === "Certification"
                        ) echo "selected";
                        ?>
                    >
                        Certification
                    </option>

                    <option
                        value="Other"
                        <?php
                        if (
                            $achievement["category"]
                            === "Other"
                        ) echo "selected";
                        ?>
                    >
                        Other
                    </option>

                </select>

            </div>


            <!-- DATE -->

            <div class="form-group">

                <label for="date_achieved">
                    Date Achieved
                </label>

                <input
                    type="date"
                    id="date_achieved"
                    name="date_achieved"
                    value="<?php
                        echo htmlspecialchars(
                            $achievement[
                                "date_achieved"
                            ] ?? ""
                        );
                    ?>"
                >

            </div>


            <!-- DESCRIPTION -->

            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                ><?php

                echo htmlspecialchars(
                    $achievement[
                        "description"
                    ] ?? ""
                );

                ?></textarea>

            </div>


            <!-- BUTTONS -->

            <div class="buttons">

                <button type="submit">
                    Save Changes
                </button>

                <a
                    href="achievements.php"
                    class="cancel"
                >
                    Cancel
                </a>

            </div>


        </form>


    </div>


</div>


</div>


</body>

</html>

<?php

$conn->close();

?>