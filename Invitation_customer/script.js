$(document).ready(function() {
    // Copy URL 
    $('#copy-btn').click(function() {
        const urlInput = $('#invite-url');
        urlInput.select();
        
        try {
            navigator.clipboard.writeText(urlInput.val()).then(function() {
                alert('Invitation link copied to clipboard!');
            }).catch(function() {
                // Fallback 
                document.execCommand('copy');
                alert('Invitation link copied to clipboard!');
            });
        } catch(err) {
            document.execCommand('copy');
            alert('Invitation link copied to clipboard!');
        }
    });

    // WhatsApp sharing
    $('#whatsapp-btn').click(function() {
        const btn = $(this);
        const guestName = btn.data('guest-name');
        const message = btn.data('message');
        const url = btn.data('url');

        if (!guestName) {
            alert('No guest name found. Please create an invitation first.');
            return;
        }

        if (!url) {
            alert('No invitation URL found. Please create an invitation first.');
            return;
        }

        // WhatsApp Create message
        let whatsappText = '';
        if (guestName && message && url) {
            whatsappText = `Hi ${guestName},\n\n ${message} \n\n Wedding Invitation Link:\n ${url}`;
        } else if (guestName && url) {
            whatsappText = `Hi ${guestName},\n\n You are cordially invited to our wedding! \n\n Invitation Link:\n ${url}`;
        } else {
            whatsappText = `Wedding Invitation Link: \n ${url}`;
        }
        
        // URL
        const encoded = encodeURIComponent(whatsappText);
        
        // Open WhatsApp
        window.open(`https://wa.me/?text=${encoded}`, '_blank');
    });
});