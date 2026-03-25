<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$invitation_url = '';
$guest_name = '';
$custom_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $guest_name = trim($_POST['guest_name']);
    $custom_message = trim($_POST['message']);

    if (empty($custom_message)) {
        $custom_message = "You are cordially invited to our wedding! Please join us to celebrate this special day.";
    }

    if (!empty($guest_name)) {
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("INSERT INTO invitations (user_id, guest_name, message, unique_token) VALUES (?, ?, ?, ?)");

        if ($stmt->execute([$user_id, $guest_name, $custom_message, $token])) {
            $invitation_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/full_invitation.php?token=" . $token;
            $message = "Invitation created successfully!";
        } else {
            $message = "Failed to create invitation.";
        }
    } else {
        $message = "Guest name is required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invitation Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Playfair+Display&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins', sans-serif;}
body{background:#eef1f6;}

/* Header */
.header{
  background:linear-gradient(135deg,#1e5bb8,#4da3ff);
  color:#fff;
  padding:30px 50px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}

.logo{
  font-size:24px;
  font-weight:600;
  font-family:'Playfair Display', serif;
}

.profile{
  display:flex;
  align-items:center;
  gap:30px;
}

.profile img{
  width:40px;
  height:40px;
  border-radius:50%;
}

.logout{
  color:#fff;
  text-decoration:none;
  font-size:12px;
  background:rgba(202, 27, 27, 0.57);
  padding:10px 15px;
  border-radius:6px;
}

/* Container */
.container{
  width:100%;
  max-width:500px;
  margin:30px auto;
}

/* Card */
.card{
  background:url("photo/bg.jpeg") no-repeat;
  padding:20px;
  border-radius:15px;
  box-shadow:0 5px 15px rgba(0,0,0,0.1);
  margin-bottom:20px;
}

label{
  font-weight:500;
  display:block;
  margin-bottom:5px;
}

input, textarea{
  width:100%;
  padding:10px;
  border-radius:8px;
  border:1px solid #ccc;
  margin-bottom:12px;
  outline:none;
}

textarea{
  height:100px;
  resize:none;
}

/* Buttons */
.btn-group{
  display:flex;
  gap:10px;
}

.btn{
  flex:1;
  padding:10px;
  border:none;
  border-radius:8px;
  cursor:pointer;
}

.primary{background:#2f80ed;color:#fff;}
.secondary{background:#e0e0e0;}

/* Link */
.link-box{
  display:flex;
  gap:8px;
  flex-wrap:wrap;
}

.link-box input{
  flex:3;
}

.copy-btn{
  font-size: 24px;
  background: #dddddd00;
}

.whatsapp{
  background:#25D366;
  color:#fff;
  font-size: 16px;
}

/* Alert */
.alert{
  background:#d4edda;
  color:#155724;
  padding:8px;
  border-radius:8px;
  margin-bottom:10px;
}
/* Desktop view */
@media(min-width:768px){
  .container{
    max-width:700px;
  }
}

/* Large Desktop */
@media(min-width:1200px){
  .container{
    max-width:900px;
  }
}

@media(max-width:500px){
  .btn-group{flex-direction:column;}
  .link-box{flex-direction:column;}
}
</style>
</head>

<body>

<div class="header">
  <div class="logo">Wedding Invitation</div>

  <div class="profile">
    <img src="https://i.pravatar.cc/100">
    <span><?= htmlspecialchars($_SESSION['username']) ?></span>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>

<div class="container">

  <div class="card">

    <?php if ($message): ?>
      <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Guest Name</label>
      <input type="text" name="guest_name" value="<?= htmlspecialchars($guest_name) ?>" required>

      <label>Message</label>
      <textarea name="message"><?= htmlspecialchars($custom_message ?: "You are cordially invited to our wedding!") ?></textarea>

      <div class="btn-group">
        <button type="submit" name="create" class="btn primary">Create</button>
        <button type="reset" class="btn secondary">Clean</button>
      </div>
    </form>

  </div>

  <?php if ($invitation_url): ?>
  <div class="card">

    <label>Invitation Link</label>

    <div class="link-box">
      <input type="text" id="link" value="<?= htmlspecialchars($invitation_url) ?>" readonly>

      <button class="btn copy-btn" onclick="copyLink()">📋</button>
      
      <button class="btn whatsapp"
        data-guest-name="<?= htmlspecialchars($guest_name) ?>"
        data-message="<?= htmlspecialchars($custom_message) ?>"
        data-url="<?= htmlspecialchars($invitation_url) ?>">
        WhatsApp
      </button>
    </div>

  </div>
  <?php endif; ?>

</div>

<script>
function copyLink(){
  let link = document.getElementById("link");
  link.select();
  document.execCommand("copy");
  alert("Link copied!");
}

// WhatsApp (FIXED - Pure JS)
document.querySelectorAll('.whatsapp').forEach(btn => {
  btn.addEventListener('click', function() {
    const guestName = this.dataset.guestName;
    const message = this.dataset.message;
    const url = this.dataset.url;

    let text = `Hi ${guestName},\n${message}\nInvitation Link:\n${url}`;
    window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
  });
});
</script>

</body>
</html>