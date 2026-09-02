<?php
session_start();

require_once __DIR__ . "/config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Find user by email
    $stmt = $conn->prepare("
        SELECT
            id,
            name,
            email,
            password,
            username,
            bio,
            education,
            skills,
            linkedin_url,
            github_url,
            profile_image
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    if (!$stmt) {
        die("Database query error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        // Check password
        if (password_verify($password, $user["password"])) {

            // Create session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];

            // Optional profile information
            $_SESSION["username"] = $user["username"] ?? "";
            $_SESSION["profile_image"] = $user["profile_image"] ?? "";

            // Login successful
            header("Location: dashboard.php");
            exit;

        } else {

            $message = "❌ Incorrect password!";

        }

    } else {

        $message = "❌ Email not found!";

    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AchieveX - Login</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.10);
        }

        .logo {
            text-align: center;
            color: #4f46e5;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 30px;
        }

        h2 {
            margin-bottom: 20px;
            color: #111827;
        }

        .message {
            background: #fee2e2;
            color: #991b1b;
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
            color: #374151;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
        }

        input:focus {
            border-color: #4f46e5;
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

        .register {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
        }

        .register a {
            color: #4f46e5;
            font-weight: bold;
            text-decoration: none;
        }

        .register a:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

<div class="login-container">

    <div class="login-card">

        <div class="logo">
            AchieveX
        </div>

        <p class="subtitle">
            Login to your achievement portfolio
        </p>

        <h2>Login</h2>

        <?php if (!empty($message)): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>

            <button type="submit">
                Login
            </button>

        </form>

        <p class="register">

            Don't have an account?

            <a href="register.php">
                Register
            </a>

        </p>

    </div>

</div>

</body>

</html>