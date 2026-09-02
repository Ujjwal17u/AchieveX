<?php
session_start();

require_once __DIR__ . "/config/database.php";

// Login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $date_achieved = $_POST['date_achieved'];

    if (empty($title)) {
        $message = "Please enter achievement title.";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO achievements
            (user_id, title, description, category, date_achieved)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "issss",
            $user_id,
            $title,
            $description,
            $category,
            $date_achieved
        );

        if ($stmt->execute()) {
            $message = "Achievement added successfully! 🏆";
        } else {
            $message = "Something went wrong. Please try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Add Achievement - AchieveX</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5f7fb;
            min-height: 100vh;
        }

        .navbar {
            height: 70px;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .logo {
            font-size: 26px;
            font-weight: bold;
            color: #4f46e5;
        }

        .back {
            text-decoration: none;
            color: #4f46e5;
            font-weight: bold;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .form-card {
            background: white;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        h1 {
            margin-bottom: 10px;
            color: #111827;
        }

        .subtitle {
            color: #6b7280;
            margin-bottom: 30px;
        }

        .message {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #4f46e5;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #4f46e5;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #4338ca;
        }

    </style>

</head>

<body>

<nav class="navbar">

    <div class="logo">
        AchieveX
    </div>

    <a href="dashboard.php" class="back">
        ← Dashboard
    </a>

</nav>


<div class="container">

    <div class="form-card">

        <h1>🏆 Add Achievement</h1>

        <p class="subtitle">
            Add your achievement to your AchieveX profile.
        </p>


        <?php if (!empty($message)): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-group">

                <label for="title">
                    Achievement Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    placeholder="Example: Hackathon Winner"
                    required
                >

            </div>


            <div class="form-group">

                <label for="category">
                    Category
                </label>

                <select id="category" name="category">

                    <option value="">
                        Select Category
                    </option>

                    <option value="Academic">
                        Academic
                    </option>

                    <option value="Hackathon">
                        Hackathon
                    </option>

                    <option value="Technical">
                        Technical
                    </option>

                    <option value="Sports">
                        Sports
                    </option>

                    <option value="Certification">
                        Certification
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label for="date_achieved">
                    Date Achieved
                </label>

                <input
                    type="date"
                    id="date_achieved"
                    name="date_achieved"
                >

            </div>


            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Describe your achievement..."
                ></textarea>

            </div>


            <button type="submit">
                Save Achievement 🏆
            </button>

        </form>

    </div>

</div>

</body>

</html>