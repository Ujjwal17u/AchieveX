<?php

include "config/database.php";

$message = "";

if (isset($_POST['register'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Password ko secure hash mein convert karna
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // User ko database mein insert karna
    $sql = "INSERT INTO users (name, email, password)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("sss", $name, $email, $hashedPassword);

    if ($stmt->execute()) {
        $message = "Registration successful!";
    } else {
        $message = "Registration failed: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - AchieveX</title>
</head>

<body>

    <h1>Create Your AchieveX Account</h1>

    <?php if ($message != ""): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Name:</label>
        <input type="text" name="name" required>

        <br><br>

        <label>Email:</label>
        <input type="email" name="email" required>

        <br><br>

        <label>Password:</label>
        <input type="password" name="password" required>

        <br><br>

        <button type="submit" name="register">
            Register
        </button>

    </form>

</body>
</html>