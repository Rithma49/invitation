<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $errors = [];
    if (empty($email)) $errors[] = "Email required";
    if (empty($password)) $errors[] = "Password required";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = "Invalid email or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Wedding Invitation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <!-- Heading -->
            <div class="col-heading">
                <h2>Login</h2>
            </div>
            <!-- Image -->
            <div class="col-image">
                <img src="photo/regimg.jpeg" alt="Wedding" class="side-image">
            </div>
            <!-- Form -->
            <div class="col-form">
                <?php if (!empty($errors)): ?>
                    <div class="error"><?= implode('<br>', $errors) ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                </form>
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            </div>
        </div>
    </div>
</body>
</html>
