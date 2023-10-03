<?php

?>
<script>
    function startSessionTimeout() {
        var sessionTimeoutInMinutes = 15;
        var sessionTimeout = sessionTimeoutInMinutes * 60 * 1000; // Convert minutes to milliseconds
        
        var timeoutTimer;
        
        function resetTimeout() {
            clearTimeout(timeoutTimer);
            timeoutTimer = setTimeout(logout, sessionTimeout);
        }
        
        function logout() {
            // Perform session cleanup tasks here
            // For example, clear session data or redirect to a logout page
            window.location.href = 'logout';
        }
        
        document.addEventListener('mousemove', resetTimeout);
        document.addEventListener('keypress', resetTimeout);
        // Add other event listeners as needed to track user activity
        
        // Start the session timeout initially
        resetTimeout();
    }
    
    // Call the function to start the session timeout when needed
    startSessionTimeout();

</script>