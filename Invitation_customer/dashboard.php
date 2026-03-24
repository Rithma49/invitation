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
        // Generate token
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
    <title>Dashboard - Wedding Invitation</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js" defer></script>
</head>
<body>
    <div class="dashboard">
        <header>
            <div class="logo">Wedding Invitation</div>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                <div class="profile-placeholder"></div>
                <a href="logout.php" class="logout">Logout</a>
            </div>
        </header>

        <main>
            <h2>Create Invitation</h2>
            <?php if ($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="POST" id="invite-form">
                <div class="form-group">
                    <label for="guest_name">Guest Name:</label>
                    <input type="text" name="guest_name" id="guest_name" required>
                </div>
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea name="message" id="message" rows="4">You are cordially invited to our wedding! Please join us to celebrate this special day.</textarea>
                </div>
                <button type="submit" name="create">Create</button>
            </form>

            <?php if ($invitation_url): ?>
                <div class="invitation-box">
                    <p>Your invitation link:</p>
                    <div class="url-box">
                        <input type="text" id="invite-url" value="<?= htmlspecialchars($invitation_url) ?>" readonly>
                        <button id="copy-btn" data-url="<?= htmlspecialchars($invitation_url) ?>">Copy</button>
                    </div>
                    <button id="whatsapp-btn" 
                            class="whatsapp" 
                            data-guest-name="<?= htmlspecialchars($guest_name) ?>"
                            data-message="<?= htmlspecialchars($custom_message) ?>"
                            data-url="<?= htmlspecialchars($invitation_url) ?>">
                        Send via WhatsApp
                    </button>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>