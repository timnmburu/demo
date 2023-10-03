<?php    
    require_once(__DIR__ . '/../../../vendor/autoload.php');

    use Dotenv\Dotenv;
     
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
    $dotenv->load();
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $headers = getallheaders();
    
    if (!array_key_exists('authorization', $headers)) {
    
        echo json_encode(["error" => "Authorization header is missing"]);
        exit;
    }
    
    if (substr($headers['authorization'], 0, 7) !== 'Bearer ') {

        echo json_encode(["error" => "Bearer keyword is missing"]);
        exit;
    }
    
    $receivedToken = trim(substr($headers['authorization'], 7));
    
    $username = $_POST['username'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $results = $stmt->get_result();
    $rows = $results->fetch_assoc();
    
    if ($rows) {
        $expectedToken = $rows['api_key'];
    } else {
        echo "Credentials not found.";
        exit;
    }
    
    $expectedToken = hash('sha512', $username . ':' . $expectedToken);
    
    header('Content-Type: application/json');
    
    if (base64_decode($receivedToken) !== base64_decode($expectedToken)) {
        //header('HTTP/1.1 401 Unauthorized');
        //echo $expectedToken;
        $response = array(
            "response_code" => 401,
            "response_message" => "Unauthorized! If you think this is an error, contact us on info@essentialtech.site"
        );
        echo json_encode($response);
        exit;
    } else {
    
        $recipient = $_POST['recipient'];
        $message = $_POST['message'];
        
        $date = date('Y-m-d H:i:s', strtotime('+3 hours'));
        
        // Get credentials for MoveSMS 
        $username = $_ENV['MOVESMS_API_USERNAME'];
        $apiKey = $_ENV['MOVESMS_API_API_KEY'];
        $sender = $_ENV['MOVESMS_API_SENDER'];
        
        //Get credentials for AdvantaSMS 
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
        
        if($stmt->execute() === TRUE){
    
            $ch = curl_init();
            $url = $urlSms;
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                echo 'Error: ' . curl_error($ch);
            } else {
                if($response === 'Message Sent:1701'){
                    $responseM = array(
                        "response_code" => 200,
                        "response_message" => "Message sent"
                    );
                    $saveSMS = "INSERT INTO sentSMS (recipient, message, date) VALUES ('$recipient', '$message', '$date')";
                    $conn->query($saveSMS);
                } elseif ($response === 'Not Sent; Repetition Detected') {
                    $responseM = array(
                        "response_code" => 402,
                        "response_message" => "Message not sent due to repetition"
                    );
                } else {
                    $responseM = array(
                        "response_code" => 403,
                        "response_message" => "Message not sent due to other reasons."
                    );
                }
                //header('Content-Type: application/json');
                echo json_encode($responseM);
                $conn->close();
            }
            curl_close($ch);
        } else {
            echo "Error sending message! Error message: " . $stmt->error;
        }
    }
    
?>


