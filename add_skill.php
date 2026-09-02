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

    $skill_name = trim($_POST["skill_name"] ?? "");
    $skill_level = trim($_POST["skill_level"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($skill_name === "") {

        $message = "Please enter skill name.";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO skills
            (user_id, skill_name, skill_level, description)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isss",
            $user_id,
            $skill_name,
            $skill_level,
            $description
        );

        if ($stmt->execute()) {

            $stmt->close();

            header("Location: skills.php");
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

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Add Skill - AchieveX</title>

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
select,
textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 15px;
    outline: none;
}

input:focus,
select:focus,
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

    <a href="skills.php">🛠 Skills</a>

    <a href="profile.php">My Profile</a>

    <a href="logout.php">Logout</a>

</div>


<div class="content">

    <h1>🛠 Add Skill</h1>

    <p class="subtitle">
        Add a skill to your AchieveX profile.
    </p>


    <?php if (!empty($message)): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <div class="form-card">

        <form method="POST">

            <div class="form-group">

                <label for="skill_name">
                    Skill Name
                </label>

                <input
                    type="text"
                    id="skill_name"
                    name="skill_name"
                    placeholder="Example: JavaScript"
                    required
                >

            </div>


            <div class="form-group">

                <label for="skill_level">
                    Skill Level
                </label>

                <select
                    id="skill_level"
                    name="skill_level"
                >

                    <option value="">
                        Select Level
                    </option>

                    <option value="Beginner">
                        Beginner
                    </option>

                    <option value="Intermediate">
                        Intermediate
                    </option>

                    <option value="Advanced">
                        Advanced
                    </option>

                    <option value="Expert">
                        Expert
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Describe your experience with this skill..."
                ></textarea>

            </div>


            <div class="buttons">

                <button type="submit">
                    Save Skill 🛠
                </button>

                <a
                    href="skills.php"
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
