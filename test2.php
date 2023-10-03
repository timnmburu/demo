<?php 
    require_once 'templates/cryptOtp.php';
    
    $phone = '254725887269';
    
    $decryptedOtp = decryptOtp($phone);
    
    $return = json_decode($decryptedOtp, true);
    
    if(isset($return['error'])) {
        $errorParts = explode(' ', $return['error']);
        //$timeoutValue = (int)$errorParts[1];
        $message = $return['error'];
    } elseif (isset($return['success'])) {
        $errorParts = explode(' ', $return['success']);
        //$timeoutValue = (int)$errorParts[1];
        $message = $return['success'];
    } else {
        $message = 'n/a';
    }
    
    echo $message;
 
?>