<?php
require_once 'config.php';

$token = $_GET['token'] ?? '';
$invitation = null;

if ($token) {
    $stmt = $pdo->prepare("SELECT guest_name, message FROM invitations WHERE unique_token = ?");
    $stmt->execute([$token]);
    $invitation = $stmt->fetch();
}

if (!$invitation) {
    die("Invalid invitation link.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Wedding Invitation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', 'Georgia', 'Times New Roman', serif;
        }
        
        body {
            background: #2e241f;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Main container - responsive */
        .invitation-wrapper {
            position: relative;
            width: 100%;
            max-width: 428px;
            height: 750px;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 30px 50px rgba(0,0,0,0.4);
            transition: all 0.3s ease;
        }

        /* Full invitation (preview) */
        .full-invitation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1519741497674-611481863552?w=400');
            background-size: cover;
            background-position: center;
            z-index: 1;
            overflow-y: auto;
            padding: 20px 16px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Custom scrollbar */
        .full-invitation::-webkit-scrollbar {
            width: 4px;
        }
        
        .full-invitation::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
        }
        
        .full-invitation::-webkit-scrollbar-thumb {
            background: #d4af37;
            border-radius: 10px;
        }

        /* Semi-transparent content containers */
        .content-card {
            border-radius: 30px;
            padding: 20px 16px;
            margin-bottom: 16px;
            width: 100%;
            text-align: center;
            color: #fff6e8;
        }

        .preview-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
            pointer-events: none;
        }

        .half {
            position: absolute;
            top: 0;
            width: 50%;
            height: 100%;
            background: linear-gradient(135deg, #f9e6cf, #f5d5b3);
            transition: transform 0.8s cubic-bezier(0.77, 0, 0.18, 1);
            pointer-events: none;
        }

        .half.left {
            left: 0;
            transform: translateX(0);
        }

        .half.right {
            right: 0;
            transform: translateX(0);
        }

        /* Preview content (names + button) */
        .preview-content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 15;
            pointer-events: auto;
            text-align: center;
            color: white;
            transition: opacity 0.4s ease;
            background: rgba(0,0,0,0.4);
        }

        .preview-content h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
            font-family: 'Georgia', serif;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.6);
            color: #ffdd88;
        }

        .open-btn {
            background: #b87a3a;
            color: white;
            border: none;
            padding: 18px 40px;
            font-size: 24px;
            font-weight: 600;
            border-radius: 60px;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            letter-spacing: 1px;
        }
        .open-btn:hover{
            background-color: #9a6234;
            transform: scale(1.02);
        }

        .open-btn:active {
            transform: scale(0.95);
        }

        /* Animation */
        .preview-container.split .half.left {
            transform: translateX(-100%);
        }

        .preview-container.split .half.right {
            transform: translateX(100%);
        }

        .preview-container.split .preview-content {
            opacity: 0;
            pointer-events: none;
        }

        .preview-container.hidden {
            display: none;
        }

        .wedding-of {
            font-size: 18px;
            letter-spacing: 2px;
            color: #ffdd99;
            text-transform: uppercase;
        }

        .couple-names {
            font-size: 28px;
            font-weight: 800;
            color: #ffdd99;
            line-height: 1.2;
            margin: 10px 0;
            font-family: 'Georgia', serif;
        }

        .round-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 3px solid #ffdd99;
            margin: 15px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .round-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .save-date {
            font-size: 22px;
            font-weight: 600;
            color: #ffdd99;
            margin: 10px 0 5px;
        }

        .invite-text {
            font-size: 16px;
            margin: 15px 0;
            line-height: 1.6;
            color: #fff6e8;
        }

        .guest-name {
            font-size: 18px;
            font-weight: 600;
            color: #ffdd99;
            margin: 10px 0;
        }

        hr {
            border: none;
            border-top: 2px solid #ffdd99;
            width: 80%;
            margin: 20px auto;
        }

        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: #ffdd99;
            margin-bottom: 15px;
        }

        .sub-title {
            font-size: 18px;
            font-weight: 600;
            color: #fff6e8;
            margin: 10px 0;
        }

        .map-placeholder {
            width: 100%;
            border-radius: 20px;
            margin: 10px 0;
            overflow: hidden;
        }
        
        iframe {
            width: 100%;
            height: 200px;
            border-radius: 20px;
            border-style: none;
        }

        /* Our moments carousel */
        .moments-carousel {
            position: relative;
            width: 100%;
            margin: 1rem 0;
            border-radius: 20px;
            overflow: hidden;
            aspect-ratio: 4 / 3;
            background: #2e241f;
            box-shadow: 0 12px 20px rgba(0,0,0,0.2);
        }

        .gallery-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 0.7s ease-in-out;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
        }

        .gallery-image.active {
            opacity: 1;
            position: relative;
        }

        /* Buttons */
        .gold-btn {
            background: #b87a3a;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            width: 100%;
            margin: 8px 0;
            cursor: pointer;
            transition: all 0.3s;
        }

        .gold-btn:hover{
            background-color: #9a6234;
            transform: translateY(-2px);
        }
        .gold-btn:active {
            transform: scale(0.97);
        }

        .rsvp-box {
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(5px);
            border-radius: 30px;
            padding: 20px 16px;
            margin-bottom: 16px;
            width: 100%;
            text-align: center;
            color: #fff6e8;
            border: 1px solid rgba(212,175,55,0.3);
        }

        .rsvp-title {
            font-size: 22px;
            font-weight: 700;
            color: #ffdd99;
            margin-bottom: 15px;
        }

        .rsvp-date {
            font-size: 16px;
            color: #fff6e8;
            margin: 10px 0;
        }

        .contact-text {
            font-size: 14px;
            font-weight: 600;
            color: #fff6e8;
            margin: 0;
            text-align: center;
        }
        
        .date-columns {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin: 1rem 0;
            text-align: center;
            flex-wrap: wrap;
        }
        
        .date-item {
            flex: 1;
            padding: 0.75rem;
            font-weight: bold;
            color: #f9e6cf;
            font-size: 28px;
        }

        /* RESPONSIVE DESIGN */
        @media (min-width: 769px) {
            body {
                padding: 40px;
            }
            .invitation-wrapper {
                max-width: 480px;
                height: 800px;
                border-radius: 32px;
            }
            .full-invitation {
                padding: 25px 20px 35px;
            }
            .content-card, .rsvp-box {
                padding: 25px 20px;
                margin-bottom: 20px;
            }
            .wedding-of {
                font-size: 20px;
            }
            .couple-names {
                font-size: 32px;
            }
            .round-photo {
                width: 220px;
                height: 220px;
            }
            .save-date {
                font-size: 24px;
            }
            .invite-text {
                font-size: 17px;
            }
            .guest-name {
                font-size: 20px;
            }
            .section-title {
                font-size: 24px;
            }
            .sub-title {
                font-size: 20px;
            }
            .date-item {
                font-size: 15px;
                padding: 0.8rem;
            }
            .gold-btn {
                padding: 14px 28px;
                font-size: 17px;
            }
            .preview-content h1 {
                font-size: 56px;
            }
            .open-btn {
                padding: 20px 48px;
                font-size: 28px;
            }
            iframe {
                height: 220px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 12px;
            }
            .invitation-wrapper {
                max-width: 100%;
                height: 700px;
                border-radius: 24px;
            }
            .full-invitation {
                padding: 16px 12px 25px;
            }
            .content-card, .rsvp-box {
                padding: 16px 12px;
                margin-bottom: 12px;
            }
            .wedding-of {
                font-size: 16px;
            }
            .couple-names {
                font-size: 24px;
            }
            .round-photo {
                width: 150px;
                height: 150px;
            }
            .save-date {
                font-size: 20px;
            }
            .invite-text {
                font-size: 14px;
            }
            .guest-name {
                font-size: 16px;
            }
            .section-title {
                font-size: 20px;
            }
            .sub-title {
                font-size: 16px;
            }
            .date-columns {
                gap: 0.5rem;
            }
            .date-item {
                font-size: 12px;
                padding: 0.5rem;
            }
            .gold-btn {
                padding: 10px 20px;
                font-size: 14px;
            }
            .preview-content h1 {
                font-size: 40px;
            }
            .open-btn {
                padding: 14px 32px;
                font-size: 20px;
            }
            iframe {
                height: 180px;
            }
            .rsvp-title {
                font-size: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .invitation-wrapper {
                height: 650px;
            }
            .couple-names {
                font-size: 20px;
            }
            .round-photo {
                width: 120px;
                height: 120px;
            }
            .invite-text {
                font-size: 13px;
            }
            .preview-content h1 {
                font-size: 32px;
            }
            .open-btn {
                padding: 12px 24px;
                font-size: 18px;
            }
            .date-item {
                font-size: 11px;
            }
        }
        
        @media (max-width: 768px) and (orientation: landscape) {
            .invitation-wrapper {
                height: 90vh;
            }
            .full-invitation {
                padding: 12px 12px 20px;
            }
            .round-photo {
                width: 100px;
                height: 100px;
            }
            .content-card {
                padding: 12px;
                margin-bottom: 8px;
            }
        }
        
        @media (min-width: 1440px) {
            .invitation-wrapper {
                max-width: 520px;
                height: 850px;
            }
        }
        
        @media (hover: none) and (pointer: coarse) {
            .open-btn:active {
                transform: scale(0.95);
            }
            .gold-btn:active {
                transform: scale(0.97);
            }
        }
    </style>
</head>
<body>
    <div class="invitation-wrapper">
        <!-- Full Invitation -->
        <div class="full-invitation">
            <!-- The Wedding of -->
            <div class="content-card">
                <div class="wedding-of">The Wedding of</div>
                <div class="couple-names">
                    Nuwan & Kumari
                </div>
                
                <!-- Couple round photo -->
                <div class="round-photo">
                    <img src="photo/FB_IMG_1773831380680.jpg" alt="couple">
                </div>
                
                <!-- Invitation paragraph -->
                <div class="invite-text">
                    We cordially invite you to attend the wedding ceremony of Mr. and Mrs. Gamage's beloved son, <b>Nuwan Madushanka</b>, and Mr. and Mrs. Nimal's beloved daughter, <b>Kumari</b>.
                </div>
                
                <br>
                
                <hr>

                <h1 style="color: #ffdd99; font-size: 24px;">Wedding Invitation</h1>
                <br>

                <!-- Save the date -->
                <div class="save-date">Save the date</div>

                <!-- Save the date columns -->
                <div class="date-columns">
                    <div class="date-item">Hotel Kamath</div>
                    <div class="date-item">2026 . 05 . 05</div>
                    <div class="date-item">Poruwa ceremony at 10:34</div>
                </div>

                <br>
                <div class="guest-name">Dear <?= htmlspecialchars($invitation['guest_name']) ?>,</div>
                <hr>
                <br>
                <p style="font-size: 14px; line-height: 1.5;">We are delighted to invite you to share in our joy and celebrate this special day with us. Your presence would make our wedding day even more memorable.</p>
                <br>
                <p style="font-size: 14px; line-height: 1.5;">We look forward to celebrating with you and creating beautiful memories together.</p>
            </div>
            
            <hr>
            
            <!-- Wedding Location -->
            <div class="content-card">
                <div class="section-title">Wedding Location</div>
                <div class="sub-title">Wedding Mass</div>
                <div class="map-placeholder">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.981411127114!2d81.02554053068157!3d6.133199642709395!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae6bae3a64e801d%3A0x39e32713520462ea!2sKamatha%20Hotel%20%26%20Restaurant!5e0!3m2!1sen!2slk!4v1773998830699!5m2!1sen!2slk" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="sub-title" style="margin-top: 15px;">Wedding Reception</div>
                <div class="map-placeholder">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.981411127114!2d81.02554053068157!3d6.133199642709395!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae6bae3a64e801d%3A0x39e32713520462ea!2sKamatha%20Hotel%20%26%20Restaurant!5e0!3m2!1sen!2slk!4v1773998830699!5m2!1sen!2slk" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
            
            <hr>
            
            <!-- Our moments -->
            <div class="content-card">
                <div class="section-title">Our Moments</div>
                <div class="moments-carousel" id="momentsCarousel">
                    <img class="gallery-image active" src="photo/FB_IMG_1773831380680.jpg" alt="moment1">
                    <img class="gallery-image" src="photo/FB_IMG_1773831380791.jpg" alt="moment2">
                    <img class="gallery-image" src="photo/FB_IMG_1773831389205.jpg" alt="moment3">
                    <img class="gallery-image" src="photo/FB_IMG_1773831389206.jpg" alt="moment4">
                    <img class="gallery-image" src="photo/FB_IMG_1773831406989.jpg" alt="moment5">
                </div>
            </div>
            
            <hr>
            
            <!-- Add to Google Calendar -->
            <div class="section-title">Add to Google Clender</div>
            <br>
            <button class="gold-btn" onclick="addToGoogleCalendar()">
                Add to Google Calendar
            </button>
            
            <hr>

            <!-- RSVP Box -->
            <div class="section-title">RSVP From</div>
                <br>
                <div class="rsvp-box">
                <div class="rsvp-title">Please RSVP</div>
                <div class="rsvp-date">Before 27<sup>th</sup> April 2026</div>
                <button class="gold-btn" style="margin-top: 5px;" onclick="openRSVP()">
                    RSVP Now
                </button>
            </div>
            
            <hr>
            
            <!-- For your invitation contact -->
            <div class="contact-text">
                <p>For your invitation contact :</p>
                <br>
                <p>Nuwan: +94 77 123 4567 | Kumari: +94 77 765 4321</p>
                <p>nuwan.kumari.wedding@gmail.com</p>
            </div>
        </div>
        
        <!-- Preview Container -->
        <div class="preview-container" id="previewContainer">
            <div class="half left"></div>
            <div class="half right"></div>
            <div class="preview-content">
                <h1>N & K</h1>
                <button class="open-btn" id="openBtn">Tap to open</button>
            </div>
        </div>
    </div>

    <script>
        const preview = document.getElementById('previewContainer');
        const openBtn = document.getElementById('openBtn');
        
        openBtn.addEventListener('click', function() {
            preview.classList.add('split');
            
            setTimeout(() => {
                preview.style.display = 'none';
            }, 800);
        });

        // Google Calendar function
        function addToGoogleCalendar() {
            const event = {
                title: 'Nuwan & Kumari Wedding',
                details: 'Poruwa ceremony at 10:34 AM, Reception to follow. Join us to celebrate our special day!',
                location: 'Hotel Kamath, Sri Lanka',
                dates: '20260505T103400Z/20260505T170000Z'
            };
            const url = `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(event.title)}&details=${encodeURIComponent(event.details)}&location=${encodeURIComponent(event.location)}&dates=${event.dates}`;
            window.open(url, '_blank');
        }

        function openRSVP() {
            window.open('https://docs.google.com/forms/d/e/1FAIpQLSeL1HreU0JG9jyrhOoXocQYrqCDsJsVzxpcEjTIpgdmazrODA/viewform?usp=header', '_blank');
        }

        // Auto-scroll carousel - our moments
        let galleryInterval = null;
        const galleryImages = document.querySelectorAll('.gallery-image');
        let currentIdx = 0;
        
        function startGallerySlideshow() {
            if (galleryInterval) clearInterval(galleryInterval);
            if (!galleryImages.length) return;
            
            galleryImages.forEach((img, idx) => {
                if (idx === 0) img.classList.add('active');
                else img.classList.remove('active');
            });
            currentIdx = 0;
            
            galleryInterval = setInterval(() => {
                const activeImg = document.querySelector('.gallery-image.active');
                if (activeImg) activeImg.classList.remove('active');
                currentIdx = (currentIdx + 1) % galleryImages.length;
                galleryImages[currentIdx].classList.add('active');
            }, 3000);
        }
        
        window.addEventListener('DOMContentLoaded', startGallerySlideshow);
    </script>
</body>
</html>