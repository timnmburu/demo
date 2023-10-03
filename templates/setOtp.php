<?php 

    require_once __DIR__ .'/../vendor/autoload.php'; // Include the Dotenv library
    require_once __DIR__ .'/notifications.php';
    require_once __DIR__ .'/getGeoLocation.php';
    require_once __DIR__ .'/cryptOtp.php';
    require_once __DIR__ .'/sendsms.php';
    require_once __DIR__ .'/standardize_phone.php';
    require_once __DIR__ .'/emailing.php';
    
    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    
    function setOtp(){    
        // Database connection
        $servername = $_ENV['DB_HOST'];
        $usernameD = $_ENV['DB_USERNAME'];
        $passwordD = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = mysqli_connect($servername, $usernameD, $passwordD, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $newOtp = rand(100000, 999999);
        
        $hashedOtp = encryptOtp($newOtp);
        
        $date = date('Y-m-d H:i:s', strtotime('+3 hours'));
        
        //Get user phone number and email
        $username = $_SESSION['username'];
        $result = $conn->query("SELECT * FROM users WHERE username='$username'");
        $row = $result->fetch_assoc();
        $phone = $row['phone'];
        $email = $row['email'];
        
        $_SESSION['userphone'] = $phone;
        
        //Add new otp to table
        $conn->query("INSERT INTO otpQ (phone, otpHash, dateInitiated) VALUES ('$phone', '$hashedOtp', '$date')");
        
        //Send SMS OTP
        $recipient = '254'. standardizePhoneNumber($phone);
        $message = 'OTP: '. $newOtp;
        
        sendSMSOtp($recipient, $message);
        
        //Send Email OTP
        $subject = 'OTP';
        $body = 'OTP: '. $newOtp;
        $replyTo='info@essentialtech.site';
        
        sendEmail($email, $subject, $body, $replyTo);
        
        header("Location: ../verify");
    }
    
    setOtp();
?>