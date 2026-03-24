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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js" defer></script>
    <style>/* Dashboard styles */
.dashboard {
    background: #fff0f0;
    max-width: 1000px;
    margin: 0 auto;
    padding: 1.5rem;
    border-radius: 2rem;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0 1.5rem;
    border-bottom: 2px solid #f0e2d4;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.logo {
    font-size: 1.8rem;
    font-weight: 600;
    color: #b87a3a;
    font-family: 'Georgia', serif;
    letter-spacing: 1px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 1.2rem;
    background: #fffaf5;
    padding: 0.5rem 1rem 0.5rem 1.2rem;
    border-radius: 3rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.user-info span {
    font-weight: 500;
    color: #5a3e2b;
}

.profile-placeholder {
    width: 40px;
    height: 40px;
    background: #e6d5c3;
    border-radius: 50%;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23b87a3a"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>');
    background-size: 60%;
    background-position: center 55%;
    background-repeat: no-repeat;
}

.logout {
    background: none;
    border: 1px solid #b87a3a;
    padding: 0.4rem 1rem;
    border-radius: 2rem;
    color: #b87a3a;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.logout:hover {
    background: #b87a3a;
    color: white;
}

main {
    background: #ffffff;
    padding: 2rem;
    border-radius: 2rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}
main h2{
    font-family: 'Georgia', serif;
    text-align: center;
    color: #b87a3a;
}
.form-group {
    margin-bottom: 1.5rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #5a3e2b;
}

input, textarea {
    width: 100%;
    padding: 0.85rem 1rem;
    border: 1px solid #e6d5c3;
    border-radius: 2rem;
    font-family: inherit;
    font-size: 1rem;
    background: #fffaf5;
    transition: 0.2s;
}

input:focus, textarea:focus {
    outline: none;
    border-color: #b87a3a;
    box-shadow: 0 0 0 3px rgba(184,122,58,0.1);
}

button {
    background: #b87a3a;
    color: white;
    border: none;
    padding: 0.85rem 1.8rem;
    border-radius: 2rem;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.2s;
}

button:hover {
    background: #9a6234;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.message {
    background: #eef6e6;
    color: #2e6b3e;
    padding: 0.85rem;
    border-radius: 2rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid #2e6b3e;
}

.invitation-box {
    margin-top: 2rem;
    padding: 1.5rem;
    background: #fef7e8;
    border-radius: 2rem;
    border: 1px solid #f0e2d4;
}

.url-box {
    display: flex;
    gap: 0.5rem;
    margin: 1rem 0;
}

.url-box input {
    flex: 1;
    background: white;
}

.whatsapp {
    background: #25D366;
    margin-top: 0.5rem;
}

.whatsapp:hover {
    background: #128C7E;
}

/* Responsive  dashboard */
@media (max-width: 768px) {
    .dashboard {
        padding: 1rem;
    }
    header {
        flex-direction: column;
        text-align: center;
    }
    .user-info {
        justify-content: center;
    }
    .invitation-box .url-box {
        flex-direction: column;
    }
    main {
        padding: 1.5rem;
    }
}
</style>
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
                        Send WhatsApp
                    </button>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
