<?php
    session_start();
    
    // unset all session variables
    $_SESSION = array();
    
    // delete the session cookie
    if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    
    // destroy the session
    session_unset();
    session_destroy();
    
    // redirect the user to the login page
    echo "
        <script>
            localStorage.clear();
            setTimeout(function() {
                window.location.href = 'login';
            }, 100);
        </script>
    ";
    
    exit;
?>
