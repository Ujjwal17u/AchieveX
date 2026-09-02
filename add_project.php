<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$user_id = $_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $project_name = trim($_POST["project_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $technologies = trim($_POST["technologies"] ?? "");
    $project_url = trim($_POST["project_url"] ?? "");
    $github_url = trim($_POST["github_url"] ?? "");

    if ($project_name === "") {

        $message = "Please enter project name.";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO projects
            (user_id, project_name, description, technologies, project_url, github_url)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssss",
            $user_id,
            $project_name,
            $description,
            $technologies,
            $project_url,
            $github_url
        );

        if ($stmt->execute()) {

            $stmt->close();

            header("Location: projects.php");
            exit;

        } else {

            $message = "Something went wrong. Please try again.";

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Project - AchieveX</title>

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

.form-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    max-width: 700px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

input,
textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 15px;
    outline: none;
}

input:focus,
textarea:focus {
    border-color: #4f46e5;
}

textarea {
    min-height: 120px;
    resize: vertical;
}

.message {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}

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

@media (max-width: 600px) {

    .container {
        display: block;
    }

    .sidebar {
        width: 100%;
        display: flex;
        overflow-x: auto;
        padding: 10px;
        gap: 5px;
    }

    .sidebar a {
        white-space: nowrap;
        margin: 0;
    }

    .content {
        padding: 20px 15px;
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

<div class="navbar">

    <h2>AchieveX</h2>

    <span>
        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
    </span>

</div>


<div class="container">

    <div class="sidebar">

        <a href="dashboard.php">Dashboard</a>

        <a href="achievements.php">🏆 Achievements</a>

        <a href="certificates.php">📜 Certificates</a>

        <a href="projects.php">💻 Projects</a>

        <a href="#">🛠 Skills</a>

        <a href="profile.php">My Profile</a>

        <a href="logout.php">Logout</a>

    </div>


    <div class="content">

        <h1>💻 Add Project</h1>

        <p class="subtitle">
            Add your project to your AchieveX profile.
        </p>


        <?php if (!empty($message)): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <div class="form-card">

            <form method="POST">

                <div class="form-group">

                    <label for="project_name">
                        Project Name
                    </label>

                    <input
                        type="text"
                        id="project_name"
                        name="project_name"
                        placeholder="Example: AchieveX"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="technologies">
                        Technologies
                    </label>

                    <input
                        type="text"
                        id="technologies"
                        name="technologies"
                        placeholder="Example: PHP, MySQL, HTML, CSS"
                    >

                </div>


                <div class="form-group">

                    <label for="project_url">
                        Project URL
                    </label>

                    <input
                        type="url"
                        id="project_url"
                        name="project_url"
                        placeholder="https://example.com"
                    >

                </div>


                <div class="form-group">

                    <label for="github_url">
                        GitHub URL
                    </label>

                    <input
                        type="url"
                        id="github_url"
                        name="github_url"
                        placeholder="https://github.com/username/project"
                    >

                </div>


                <div class="form-group">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        placeholder="Describe your project..."
                    ></textarea>

                </div>


                <div class="buttons">

                    <button type="submit">
                        Save Project 🚀
                    </button>

                    <a
                        href="projects.php"
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