<?php
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($username)) $errors[] = "Username required";
    if (empty($email)) $errors[] = "Email required";
    if (empty($password)) $errors[] = "Password required";
    if ($password !== $confirm) $errors[] = "Passwords do not match";

    if (empty($errors)) {
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
<title>Wedding Sign Up</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Playfair+Display&display=swap" rel="stylesheet">

<style>

*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins', sans-serif;}

body{
  background:linear-gradient(to bottom right,#fff,#fdeeee);
}

/* Layout */
.wrapper{
  display:flex;
  min-height:100vh;
}

/* LEFT IMAGE */
.left{
  flex:1;
  background:url("photo/bg.jpeg") no-repeat;
  background-size:cover;
  display:flex;
  justify-content:center;
  align-items:center;
}

/* Overlay */
.overlay{
  background:rgba(0,0,0,0.1);
  width:100%;
  height:100%;
  display:flex;
  flex-direction:column;
  justify-content:center;
  align-items:center;
  color:#fff;
  text-align:center;
}

.overlay h1{
  font-family:'Playfair Display', serif;
  font-size:40px;
  color: #f28b82;
}

.overlay p{
  margin-top:10px;
  font-size:18px;
  color: #000000;
}

/* RIGHT */
.right{
  flex:1;
  display:flex;
  justify-content:center;
  align-items:center;
  padding:30px;
}

/* CARD */
.container{
  width:90%;
  max-width:420px;
  background:url("photo/bg.jpeg") no-repeat;
  padding:30px;
  border-radius:20px;
  box-shadow:0 10px 30px rgba(0,0,0,0.1);
  text-align:center;
}

/* TEXT */
.title{
  font-family:'Playfair Display', serif;
  font-size:28px;
  font-weight:600;
  color: #000000cf;
}

.heading{
  font-family:'Playfair Display', serif;
  font-size:24px;
  margin-top:10px;
  font-weight:600;
  color: #1e1919b6;
}

.sub{
  font-size:14px;
  color:#777;
  margin-top:5px;
}

/* INPUT */
.input-group{
  margin-top:15px;
  text-align:left;
}

.input-group input{
  width:100%;
  padding:12px;
  border-radius:10px;
  border:1px solid #ddd;
  margin-top:5px;
}

.input-group input:focus{
  border-color:#f28b82;
}

/* ERROR */
.error{
  background:#f8d7da;
  color:#721c24;
  padding:10px;
  border-radius:10px;
  margin-top:10px;
  font-size:14px;
  text-align:left;
}

/* TERMS */
.terms{
  margin-top:15px;
  font-size:13px;
  display:flex;
  align-items:center;
  gap:8px;
}

.terms a{
  color: #f28b82;
  text-decoration:none;
}

/* BUTTON */
.btn{
  width:100%;
  margin-top:20px;
  padding:12px;
  border:none;
  border-radius:25px;
  background:linear-gradient(to right,#f28b82,#f6a5a5);
  color:#fff;
  font-size:16px;
  cursor:pointer;
}

/* DIVIDER */
.divider{
  margin:20px 0;
  display:flex;
  align-items:center;
  gap:10px;
}

.divider::before,
.divider::after{
  content:'';
  flex:1;
  height:1px;
  background:#ddd;
}

/* SOCIAL */
.social{
  display:flex;
  gap:10px;
}

.social button{
  flex:1;
  padding:10px;
  border:none;
  border-radius:10px;
  background:#f5f5f5;
}

/* LOGIN LINK */
.login{
  margin-top:20px;
  font-size:14px;
}

.login a{
  color:#f28b82;
  text-decoration:none;
  font-weight:bold;
}

/* MOBILE */
@media(max-width:768px){
  .wrapper{flex-direction:column;}
  .left{display:none;}
  .right{width:100%;min-height:100vh;}
}

/* DESKTOP */
@media(min-width:768px){
  .container{max-width:450px;padding:40px;}
  .title{font-size:32px;}
}

</style>
</head>

<body>

<div class="wrapper">

  <!-- LEFT -->
  <div class="left">
    <div class="overlay">
      <h1>Join Our Wedding</h1>
      <p>Create your account</p>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="right">

    <div class="container">

      <div class="title">Wedding Invitation</div>
      <div class="heading">Create Your Account</div>
      <div class="sub">Join us to plan your perfect wedding!</div>

      <?php if (!empty($errors)): ?>
        <div class="error"><?= implode('<br>', $errors) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="input-group">
          <input type="text" name="username" placeholder="Full Name" required>
        </div>

        <div class="input-group">
          <input type="email" name="email" placeholder="Email Address" required>
        </div>

        <div class="input-group">
          <input type="password" name="password" placeholder="Password" required>
        </div>

        <div class="input-group">
          <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </div>

        <div class="terms">
          <input type="checkbox" required>
          <span>I agree to the <a href="#">Terms & Conditions</a></span>
        </div>

        <button type="submit" class="btn">Sign Up</button>
      </form>

      <div class="divider">Or sign up with</div>

      <div class="social">
        <button>Facebook</button>
        <button>Google</button>
      </div>

      <div class="login">
        Already have an account? <a href="login.php">Login</a>
      </div>

    </div>

  </div>

</div>

</body>
</html>