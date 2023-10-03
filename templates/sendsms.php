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
        
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $urlSms);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseURL = curl_exec($ch);
    
            if (curl_errno($ch)) {
                $return = 'cURL Error: ' . curl_error($ch);
            }
            curl_close($ch);
            
            $responses = json_decode($responseURL, true);
            $response_code = $responses['responses'][0]['response-code'];
            $responseDescription = $responses['responses'][0]['response-description'];
            $messageId = $responses['responses'][0]['messageid'];
            
        }  catch (Exception $e) {
            //echo 'Caught exception: ', $e->getMessage();
        }
            
        if($response_code !== null){
            $stmt=$conn->prepare('INSERT INTO smsQ (recipient, message, sender1, sender2, dateInitiated, messageID, status) VALUES (?, ?, ?, ?, ?, ? ,?) ');
            $stmt->bind_param("sssssss", $recipient, $message, $urlSms, $urlSms2, $date, $messageId, $responseDescription);
            if($stmt->execute() !== true){
                $return = $stmt->error;
            } 
        }
    } catch (Exception $e) {
        $return = $e->getMessage();
    }
    
    return $messageId;
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


function sendSMSBulk($recipient, $message) {  

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
    $stmt->execute();
    
    $stmtSave = $conn->prepare("INSERT INTO sentSMS (recipient, message, date) VALUES (?, ?, ?)");
    $stmtSave->bind_param("sss", $recipient, $message, $date);
    $stmtSave->execute();
    
    echo "
        <script>
            fetch('$urlSms',
                    {
                        mode: 'no-cors'
                    })
                .then(response => response ? response.text()
                    .then(data => {
                        window.location.href = '/marketing';
                    }) 
                : console.error('Network response was not ok'))
                    .catch(error => 
                            console.error('Error:', error)
                            );
        </script>";
    
    $conn->close();
    
    //return  'sent';
    //return $return;
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
    $stmt->execute();
    
    $stmtSave = $conn->prepare("INSERT INTO sentSMS (recipient, message, date) VALUES (?, ?, ?)");
    $stmtSave->bind_param("sss", $recipient, $message, $date);
    $stmtSave->execute();
    
    echo "
        <script>
            fetch('$urlSms',
                    {
                        mode: 'no-cors'
                    })
                .then(response => response ? response.text()
                    .then(data => {
                        
                    }) 
                : console.error('Network response was not ok'))
                    .catch(error => 
                            console.error('Error:', error)
                            );
        </script>";
    
    $conn->close();
    
    //return  'sent';
    //return $return;
}

?>
