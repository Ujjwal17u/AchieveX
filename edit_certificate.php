<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$user_id = $_SESSION["user_id"];
$certificate_id = (int)($_GET["id"] ?? 0);
$message = "";

/* Get certificate */

$stmt = $conn->prepare("
    SELECT id, title, issuer, description, certificate_date
    FROM certificates
    WHERE id = ? AND user_id = ?
");

$stmt->bind_param("ii", $certificate_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();
$certificate = $result->fetch_assoc();

$stmt->close();

if (!$certificate) {
    die("Certificate not found.");
}


/* Update certificate */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $issuer = trim($_POST["issuer"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $certificate_date = $_POST["certificate_date"] ?? "";

    if ($title === "") {

        $message = "Please enter certificate title.";

    } else {

        $stmt = $conn->prepare("
            UPDATE certificates
            SET title = ?,
                issuer = ?,
                description = ?,
                certificate_date = ?
            WHERE id = ? AND user_id = ?
        ");

        $stmt->bind_param(
            "ssssii",
            $title,
            $issuer,
            $description,
            $certificate_date,
            $certificate_id,
            $user_id
        );

        if ($stmt->execute()) {

            $stmt->close();

            header("Location: certificates.php");
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

<title>Edit Certificate - AchieveX</title>

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
    min-height: 130px;
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

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="achievements.php">
            🏆 Achievements
        </a>

        <a href="certificates.php">
            📜 Certificates
        </a>

        <a href="#">
            💻 Projects
        </a>

        <a href="#">
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

        <h1>✏️ Edit Certificate</h1>

        <p class="subtitle">
            Update your certificate information.
        </p>


        <?php if (!empty($message)): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <div class="form-card">

            <form method="POST">

                <div class="form-group">

                    <label for="title">
                        Certificate Title
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?php echo htmlspecialchars($certificate["title"]); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="issuer">
                        Issuing Organization
                    </label>

                    <input
                        type="text"
                        id="issuer"
                        name="issuer"
                        value="<?php echo htmlspecialchars($certificate["issuer"]); ?>"
                    >

                </div>


                <div class="form-group">

                    <label for="certificate_date">
                        Certificate Date
                    </label>

                    <input
                        type="date"
                        id="certificate_date"
                        name="certificate_date"
                        value="<?php echo htmlspecialchars($certificate["certificate_date"]); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                    ><?php echo htmlspecialchars($certificate["description"]); ?></textarea>

                </div>


                <div class="buttons">

                    <button type="submit">
                        Update Certificate 📜
                    </button>

                    <a
                        href="certificates.php"
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