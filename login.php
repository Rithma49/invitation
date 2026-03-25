<?php
require_once 'config.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

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
    <title>Wedding Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Playfair+Display&display=swap" rel="stylesheet">

    <style>
      *{
        margin:0;
        padding:0;
        box-sizing:border-box;
        font-family:'Poppins', sans-serif;
      }

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
  position:relative;
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

/* RIGHT FORM */
.right{
  flex:1;
  display:flex;
  justify-content:center;
  align-items:center;
  padding:30px;
}

/* Card */
.container{
  width:90%;
  max-width:420px;
  background:url("photo/bg.jpeg") no-repeat;
  padding:30px;
  border-radius:20px;
  box-shadow:0 10px 30px rgba(0, 0, 0, 0.1);
  text-align:center;
}

/* Title */
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


/* Inputs */
.input-group{
  margin-top:20px;
  text-align:left;
}

.input-group label{
  display:block;
  font-size:14px;
  color:#555;
}

.input-group input{
  width:100%;
  padding:12px;
  border-radius:10px;
  border:1px solid #ddd;
  margin-top:5px;
}

/* Error */
.error{
  background:#f8d7da;
  color:#721c24;
  padding:10px;
  border-radius:10px;
  margin-top:10px;
  font-size:14px;
  text-align:left;
}

/* Forgot */
.forgot{
  text-align:right;
  margin-top:10px;
  font-size:13px;
}

.forgot a{
  text-decoration:none;
  color:#f28b82;
}

/* Button */
.btn{
  width:100%;
  padding:12px;
  margin-top:20px;
  border:none;
  border-radius:25px;
  background:linear-gradient(to right,#f28b82,#f6a5a5);
  color:white;
  font-size:16px;
  cursor:pointer;
}

/* Divider */
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

/* Social */
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

/* Signup */
.signup{
  margin-top:20px;
  font-size:14px;
}

.signup a{
  color:#f28b82;
  text-decoration:none;
  font-weight:bold;
}

/* Mobile */
@media(max-width:768px){
  .wrapper{flex-direction:column;}
  .left{display:none;}
  .right{width:100%;min-height:100vh;}
}

/* Desktop */
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
      <h1>Wedding Back!</h1>
      <p>Login to your account</p>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="right">

    <div class="container">

      <div class="title">Wedding Invitation</div>
      <div class="heading">Login to your account</div>
      <div class="sub">Welcome back! Please login to continue.</div>


      <?php if (!empty($errors)): ?>
        <div class="error"><?= implode('<br>', $errors) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="input-group">
          <input type="email" name="email" placeholder="Email Address" required>
        </div>

        <div class="input-group">
          <input type="password" name="password" placeholder="Password" required>
        </div>

        <div class="forgot">
          <a href="#">Forgot Password?</a>
        </div>

        <button type="submit" class="btn">Login</button>
      </form>

      <div class="divider">Or login with</div>

      <div class="social">
        <button>Facebook</button>
        <button>Google</button>
      </div>

      <div class="signup">
        Don't have an account? <a href="signup.php">Sign Up</a>
      </div>

    </div>

  </div>

</div>

</body>
</html>