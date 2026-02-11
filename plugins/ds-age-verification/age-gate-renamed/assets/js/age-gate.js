(function() {
    'use strict';
    
    // Check if already verified
    if (document.cookie.includes('age_verified=true')) return;

    const overlay = document.getElementById('age-gate-overlay');
    const enterBtn = document.getElementById('age-gate-enter');
    const declineBtn = document.getElementById('age-gate-decline');
    const confirmBtn = document.getElementById('age-gate-confirm');
    const cookiePrefs = document.getElementById('cookie-preferences');
    const ageButtons = document.querySelector('.age-buttons');
    const declinedMsg = document.getElementById('age-gate-declined');

    // Show overlay with fade-in
    overlay.style.display = 'block';
    setTimeout(() => overlay.classList.add('active'), 10);

    // Trap focus within popup
    const popup = document.getElementById('age-gate-popup');
    const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    
    function trapFocus(e) {
        const focusable = Array.from(popup.querySelectorAll(focusableElements));
        const firstFocusable = focusable[0];
        const lastFocusable = focusable[focusable.length - 1];

        if (e.key === 'Tab') {
            if (e.shiftKey && document.activeElement === firstFocusable) {
                lastFocusable.focus();
                e.preventDefault();
            } else if (!e.shiftKey && document.activeElement === lastFocusable) {
                firstFocusable.focus();
                e.preventDefault();
            }
        }

        // Allow Escape to decline
        if (e.key === 'Escape') {
            handleDecline();
        }
    }

    document.addEventListener('keydown', trapFocus);

    // Step 1: User confirms they're 21+
    enterBtn.addEventListener('click', function() {
        // Hide age buttons, show cookie preferences
        ageButtons.style.display = 'none';
        cookiePrefs.style.display = 'block';
        
        // Focus the first radio button for accessibility
        document.querySelector('input[name="cookie-pref"]').focus();
    });

    // Step 2: User selects cookie preference and confirms
    confirmBtn.addEventListener('click', function() {
        const pref = document.querySelector('input[name="cookie-pref"]:checked').value;
        setAgeVerificationCookie(pref);
        closeGate();
    });

    // Handle decline
    declineBtn.addEventListener('click', handleDecline);

    function handleDecline() {
        // Hide everything except declined message
        ageButtons.style.display = 'none';
        cookiePrefs.style.display = 'none';
        declinedMsg.style.display = 'block';

        // Redirect after 3 seconds
        setTimeout(function() {
            window.location.href = AgeGateData.declineUrl;
        }, 3000);
    }

    function setAgeVerificationCookie(preference) {
        const cookieParts = [
            'age_verified=true',
            'path=/',
            'SameSite=Strict'
        ];

        // Add Secure flag if on HTTPS
        if (window.location.protocol === 'https:') {
            cookieParts.push('Secure');
        }

        // Set expiration for persistent cookie
        if (preference === 'persistent') {
            const days = parseInt(AgeGateData.cookieDays) || 30;
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            cookieParts.push('expires=' + expires.toUTCString());
        }
        // Session cookie (no expires) is default

        document.cookie = cookieParts.join('; ');
    }

    function closeGate() {
        overlay.classList.remove('active');
        setTimeout(function() {
            overlay.style.display = 'none';
            document.removeEventListener('keydown', trapFocus);
        }, 300);
    }

    // Prevent body scroll while gate is active
    document.body.style.overflow = 'hidden';
    
    // Restore body scroll when gate closes
    const originalClose = closeGate;
    closeGate = function() {
        document.body.style.overflow = '';
        originalClose();
    };

})();
