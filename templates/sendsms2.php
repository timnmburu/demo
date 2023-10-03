<?php    
    require_once __DIR__ . '/../vendor/autoload.php'; 
    
    use Dotenv\Dotenv;
    
    session_start();
     
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    
    
function sendSMS($recipient, $message) {  

    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    // Database connection
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    try {
        /*Get credentials for AdvantaSMS */
        $partnerIDB = $_ENV['ADVANTASMS_PARTNERID'];
        $apiKeyB = $_ENV['ADVANTASMS_API_KEY'];
        $senderB = $_ENV['ADVANTASMS_API_SENDER'];
        
        $urlSms2 = 'https://quicksms.advantasms.com/api/services/sendsms/?';
        $urlSms2 .= '&apikey=' . urlencode($apiKeyB);
        $urlSms2 .= '&partnerID=' . urlencode($partnerIDB);
        $urlSms2 .= '&message=' . urlencode($message);
        $urlSms2 .= '&messageID=' . urlencode($message);
        $urlSms2 .= '&shortcode=' . urlencode($senderB);
        $urlSms2 .= '&mobile=' . urlencode($recipient);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlSms2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $response = 'cURL Error: ' . curl_error($ch);
        }
        
        curl_close($ch);
            
        //$redirectUrl = $_SESSION['redirect_url'];
        
        //header('Location: $redirectUrl');
            
        $date = date('Y-m-d H:i:s', strtotime('+3 hours'));
        
        $saveSMS = "INSERT INTO sentSMS (recipient, message, date) VALUES ('$recipient', '$message', '$date')";
        
        $conn->query($saveSMS); 
        
        
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
    
    return $response;
}

function sendSMSBooking($recipient, $message) {  

    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    // Database connection
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $date = date('Y-m-d H:i:s', strtotime('+3 hours'));
    
    try {
        /*Get credentials for AdvantaSMS */
        $partnerIDB = $_ENV['ADVANTASMS_PARTNERID'];
        $apiKeyB = $_ENV['ADVANTASMS_API_KEY'];
        $senderB = $_ENV['ADVANTASMS_API_SENDER'];
        
        $urlSms = 'https://quicksms.advantasms.com/api/services/sendsms/?';
        $urlSms .= '&apikey=' . urlencode($apiKeyB);
        $urlSms .= '&partnerID=' . urlencode($partnerIDB);
        $urlSms .= '&message=' . urlencode($message);
        $urlSms .= '&messageID=' . urlencode($message);
        $urlSms .= '&shortcode=' . urlencode($senderB);
        $urlSms .= '&mobile=' . urlencode($recipient);
        
        /* Get credentials for MoveSMS */
        $username = $_ENV['MOVESMS_API_USERNAME'];
        $apiKey = $_ENV['MOVESMS_API_API_KEY'];
        $sender = $_ENV['MOVESMS_API_SENDER'];
    
        // Construct the URL with the necessary parameters
        $urlSms2 = 'https://sms.movesms.co.ke/api/compose';
        $urlSms2 .= '?username=' . urlencode($username);
        $urlSms2 .= '&api_key=' . urlencode($apiKey);
        $urlSms2 .= '&sender=' . urlencode($sender);
        $urlSms2 .= '&to=' . urlencode($recipient);
        $urlSms2 .= '&message=' . urlencode($message);
        $urlSms2 .= '&msgtype=5';
        $urlSms2 .= '&dlr=0';
        
        $stmt=$conn->prepare('INSERT INTO smsQ (recipient, message, sender1, sender2, dateInitiated) VALUES (?, ?, ?, ?, ?) ');
        $stmt->bind_param("sssss", $recipient, $message, $urlSms, $urlSms2, $date);
    
        if($stmt->execute() !== true){
            $return = $stmt->error;
        } else {
        
            $saveSMS = "INSERT INTO sentSMS (recipient, message, date) VALUES ('$recipient', '$message', '$date')";
            
            $conn->query($saveSMS); 
            
            $return = 'sent';
        }
        
    } catch (Exception $e) {
        $return = $e->getMessage();
    }
    
    return $return;
}


function sendSmsReminders($recipient, $message) {    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    // Database connection
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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
    
    echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
    
    // Sub-function for AJAX handling and redirection
    echo "<script>
        function handleSMSResponse(response) {
            if (response) {
                alert('Reminders sent successfully');
            } else {
                alert('Reminders not sent!');
            }
        }
        
        function handleAJAXError2() {
            alert('Reminders not sent!');
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
    
    $date = date('Y-m-d H:i:s', strtotime('+3 hours'));
    
    $saveSMS = "INSERT INTO sentSMS (recipient, message, date) VALUES ('$recipient', '$message', '$date')";
    
    $conn->query($saveSMS);    
}



function sendSMSOtp($recipient, $message) {  

    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    // Database connection
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $date = date('Y-m-d H:i:s', strtotime('+3 hours'));
    
    try {
        /*Get credentials for AdvantaSMS */
        $partnerIDB = $_ENV['ADVANTASMS_PARTNERID'];
        $apiKeyB = $_ENV['ADVANTASMS_API_KEY'];
        $senderB = $_ENV['ADVANTASMS_API_SENDER'];
        
        $urlSms = 'https://quicksms.advantasms.com/api/services/sendsms/?';
        $urlSms .= '&apikey=' . urlencode($apiKeyB);
        $urlSms .= '&partnerID=' . urlencode($partnerIDB);
        $urlSms .= '&message=' . urlencode($message);
        $urlSms .= '&messageID=' . urlencode($message);
        $urlSms .= '&shortcode=' . urlencode($senderB);
        $urlSms .= '&mobile=' . urlencode($recipient);
        
        /* Get credentials for MoveSMS */
        $username = $_ENV['MOVESMS_API_USERNAME'];
        $apiKey = $_ENV['MOVESMS_API_API_KEY'];
        $sender = $_ENV['MOVESMS_API_SENDER'];
    
        // Construct the URL with the necessary parameters
        $urlSms2 = 'https://sms.movesms.co.ke/api/compose';
        $urlSms2 .= '?username=' . urlencode($username);
        $urlSms2 .= '&api_key=' . urlencode($apiKey);
        $urlSms2 .= '&sender=' . urlencode($sender);
        $urlSms2 .= '&to=' . urlencode($recipient);
        $urlSms2 .= '&message=' . urlencode($message);
        $urlSms2 .= '&msgtype=5';
        $urlSms2 .= '&dlr=0';
        
        $stmt=$conn->prepare('INSERT INTO smsQ (recipient, message, sender1, sender2, dateInitiated) VALUES (?, ?, ?, ?, ?) ');
        $stmt->bind_param("sssss", $recipient, $message, $urlSms, $urlSms2, $date);
    
        if($stmt->execute() !== true){
            $return = $stmt->error;
        } else {
            
            try {
            
        ?>
                <script>
                        var urlOtpSms2a = 'https://quicksms.advantasms.com/api/services/sendsms/?';
                        
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", urlOtpSms2a, true);
                        var formData = new FormData();
                        formData.append('apikey', '<?php echo $apiKeyB; ?>');
                        formData.append('partnerID',  '<?php echo $partnerIDB; ?>');
                        formData.append('message',  '<?php echo $message; ?>');
                        formData.append('shortcode',  '<?php echo $senderB; ?>');
                        formData.append('mobile',  '<?php echo $recipient; ?>');
                        
                        xhr.onload = function () {
                            if (xhr.status === 200) {
                                var response = xhr.responseText;
                                if(response){
                                    //handleOTPSuccess();
                                }
                            }
                        };
                        
                        xhr.onerror = function () {
                            //handleOTPSuccess();
                        };
                        
                        xhr.send(formData);
                </script>
        <?php
        
            }  catch (Exception $e) {
                //echo 'Caught exception: ', $e->getMessage();
            }
        
            $saveSMS = "INSERT INTO sentSMS (recipient, message, date) VALUES ('$recipient', '$message', '$date')";
            
            $conn->query($saveSMS); 
            
            $updateOtpQ = "UPDATE otpQ SET status='Sent', dateDelivered='$date' WHERE phone=$recipient ORDER BY s_no DESC";
            $conn->query($updateOtpQ);
            
            $conn->close();
            
            $return = 'sent';
        }
        
    } catch (Exception $e) {
        $return = $e->getMessage();
    }
    
    return $return;
}
?>
