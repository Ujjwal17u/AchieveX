<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT id, skill_name, skill_level, description
    FROM skills
    WHERE user_id = ?
    ORDER BY id DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Skills - AchieveX</title>

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

.skills-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.skill-card {
    background: white;
    padding: 22px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

.skill-header {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.skill-name {
    font-size: 20px;
    font-weight: bold;
}

.skill-level {
    display: inline-block;
    margin-top: 10px;
    padding: 5px 10px;
    border-radius: 20px;
    background: #e0e7ff;
    color: #3730a3;
    font-size: 13px;
    font-weight: bold;
}

.description {
    color: #4b5563;
    line-height: 1.6;
    margin-top: 13px;
}

.actions {
    display: flex;
    gap: 7px;
}

.actions a {
    text-decoration: none;
    padding: 7px 10px;
    border-radius: 6px;
    font-size: 12px;
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

@media (max-width: 800px) {

    .skills-grid {
        grid-template-columns: 1fr;
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

    <a href="projects.php">
        💻 Projects
    </a>

    <a href="skills.php">
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

    <h1>🛠 My Skills</h1>

    <p class="subtitle">
        Manage and showcase your technical skills.
    </p>


    <div class="top-action">

        <a
            href="add_skill.php"
            class="add-button"
        >
            + Add Skill
        </a>

    </div>


    <?php if ($result->num_rows > 0): ?>

        <div class="skills-grid">

            <?php while ($skill = $result->fetch_assoc()): ?>

                <div class="skill-card">

                    <div class="skill-header">

                        <div>

                            <div class="skill-name">

                                🛠
                                <?php
                                echo htmlspecialchars(
                                    $skill["skill_name"]
                                );
                                ?>

                            </div>


                            <?php if (!empty($skill["skill_level"])): ?>

                                <span class="skill-level">

                                    <?php
                                    echo htmlspecialchars(
                                        $skill["skill_level"]
                                    );
                                    ?>

                                </span>

                            <?php endif; ?>

                        </div>


                        <div class="actions">

                            <a
                                href="edit_skill.php?id=<?php
                                    echo (int)$skill["id"];
                                ?>"
                                class="edit"
                            >
                                Edit
                            </a>

                            <a
                                href="delete_skill.php?id=<?php
                                    echo (int)$skill["id"];
                                ?>"
                                class="delete"
                                onclick="return confirm(
                                    'Are you sure you want to delete this skill?'
                                );"
                            >
                                Delete
                            </a>

                        </div>

                    </div>


                    <?php if (!empty($skill["description"])): ?>

                        <div class="description">

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $skill["description"]
                                )
                            );
                            ?>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="empty">

            <h2>No skills yet</h2>

            <p style="margin-top:10px;">
                Add your first skill to AchieveX.
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