<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    $errors = [];

    if (empty($username)) $errors[] = "Username required";
    if (empty($email)) $errors[] = "Email required";
    if (empty($password)) $errors[] = "Password required";
    if ($password !== $confirm) $errors[] = "Passwords do not match";

    if (empty($errors)) {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Username or email already exists";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                header('Location: dashboard.php');
                exit;
            } else {
                $errors[] = "Registration failed";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Wedding Invitation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <!-- Heading-->
            <div class="col-heading">
                <h2>Sign Up</h2>
            </div>
            <!-- Image-->
            <div class="col-image">
                <img src="photo/regimg.jpeg" alt="Wedding" class="side-image">
            </div>
            <!-- Form  -->
            <div class="col-form">
                <?php if (!empty($errors)): ?>
                    <div class="error"><?= implode('<br>', $errors) ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <button type="submit">Sign Up</button>
                </form>
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>