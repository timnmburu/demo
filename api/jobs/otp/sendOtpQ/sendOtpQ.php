<?php
    require_once(__DIR__ . '/../../../../vendor/autoload.php');
    require_once(__DIR__ . '/../../../../templates/sendsms.php');
    require_once(__DIR__ . '/../../../../templates/crytpOtp.php');

    use Dotenv\Dotenv;
     
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../');
    $dotenv->load();
    
    $phone = $_POST['phone'];
    //$phone = $_ENV['ADMIN1_PHONE'];
    
    $otp = decryptOtp($phone);
    
    $message = 'OTP: '. $otp;
    
    $return = sendSMSOtp($phone, $message);
    
    echo $return;
    

    
    
?>