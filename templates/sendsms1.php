<?php    
    require_once (__DIR__ . '/../vendor/autoload.php'); 

    use Dotenv\Dotenv;
    
    session_start();
     
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    
    
function sendSMS($recipient, $message) {    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $date = date('Y-m-d H:i:s', strtotime('+3 hours'));
    
    /* Get credentials for MoveSMS */
    $username = $_ENV['MOVESMS_API_USERNAME'];
    $apiKey = $_ENV['MOVESMS_API_API_KEY'];
    $sender = $_ENV['MOVESMS_API_SENDER'];
    
    /*Get credentials for AdvantaSMS */
    $partnerIDB = $_ENV['ADVANTASMS_PARTNERID'];
    $apiKeyB = $_ENV['ADVANTASMS_API_KEY'];
    $senderB = $_ENV['ADVANTASMS_API_SENDER'];

    // Construct the URL with the necessary parameters
    $urlSms = 'https://sms.movesms.co.ke/api/compose';
    $urlSms .= '?username=' . urlencode($username);
    $urlSms .= '&api_key=' . urlencode($apiKey);
    $urlSms .= '&sender=' . urlencode($sender);
    $urlSms .= '&to=' . urlencode($recipient);
    $urlSms .= '&message=' . urlencode($message);
    $urlSms .= '&msgtype=5';
    $urlSms .= '&dlr=0';
    
    // Construct the URL with the necessary parameters for AdvantaSMS
    $urlSms2 = 'https://quicksms.advantasms.com/api/services/sendsms/?';
    $urlSms2 .= '&apikey=' . urlencode($apiKeyB);
    $urlSms2 .= '&partnerID=' . urlencode($partnerIDB);
    $urlSms2 .= '&message=' . urlencode($message);
    $urlSms2 .= '&shortcode=' . urlencode($senderB);
    $urlSms2 .= '&mobile=' . urlencode($recipient);
    
    
    $stmt=$conn->prepare('INSERT INTO smsQ (recipient, message, sender1, sender2, dateInitiated) VALUES (?, ?, ?, ?, ?) ');
    $stmt->bind_param("sssss", $recipient, $message, $urlSms, $urlSms2, $date);
    
    if($stmt->execute() !== TRUE){
        echo "Error sending message! Error message: " . $stmt->error;
    } else {
        //echo "Message sent succesfully";

        // CSS styles to center the Processing GIF
        echo '<style>
            .processingGIF {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                z-index: 99999;
            }
        </style>';
    
        // HTML structure to center the Processing GIF
        echo '<div class="processingGIF">
            <img src="fileStore/processing.gif" alt="Processing" />
        </div>';
            
        echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
        
        // Sub-function for AJAX handling and redirection
        echo "<script>
            function handleSMSResponse(response) {
                var redirectUrl = '" . $_SESSION['redirect_url'] . "';
                
                if (response.success === true) {
                    window.location.href = redirectUrl; // Redirect if the AJAX request is successful
                } else {
                    // Handle the case where OTP sending failed
                    window.location.href = redirectUrl;
                }
            }
            
            function handleAJAXError2() {
                // Handle the case where AJAX request failed
                var redirectUrl = '" . $_SESSION['redirect_url'] . "';
                window.location.href = redirectUrl;
            }
            
            function handleAJAXError() {
                $.ajax({
                    url: '$urlSms2',
                    method: 'GET',
                    success: handleSMSResponse,
                    error: handleAJAXError2
                });
            }
    
            $.ajax({
                url: '$urlSms',
                method: 'GET',
                success: handleSMSResponse,
                error: handleAJAXError
            });
        </script>";
        
        
        
        unset($_SESSION['redirect_url']); 
        
        $saveSMS = "INSERT INTO sentSMS (recipient, message, date) VALUES ('$recipient', '$message', '$date')";
        
        $conn->query($saveSMS); 
    }
    
    $conn->close();
}


