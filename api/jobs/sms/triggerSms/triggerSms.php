<?php
    require_once(__DIR__ . '/../../../../vendor/autoload.php');

    use Dotenv\Dotenv;
     
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../');
    $dotenv->load();
    
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
    
    
    //Check if there is any message not sent
    $sqlCheckpoint = "SELECT * FROM smsQ WHERE status IS NULL ORDER BY recipient ASC";
    $foundUnsent = $conn->query($sqlCheckpoint);
    $rows = $foundUnsent->fetch_assoc();
    
    if($foundUnsent->num_rows > 0){
        $recipient = $rows['recipient'];
        
        $sqlMessage = "SELECT * FROM smsQ WHERE recipient=$recipient";
        $foundMessage = $conn->query($sqlMessage);
        $rowsMessage = $foundMessage->fetch_assoc();
        $message = $rowsMessage['message'];
        $sender1Url = $rowsMessage['sender1'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sender1Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch);
        }
        
        curl_close($ch);
        
        if($response){
            $date = date("Y-m-d H-i-s", strtotime('+3 hours'));
            
            $status = 'Sent';
            $stmt = $conn->prepare("UPDATE smsQ SET status=?, dateDelivered=? WHERE recipient=?");
            $stmt->bind_param("sss", $status, $date, $recipient);
            $stmt->execute();
            $stmt->close();
            $conn->close();
        }
    } else {
        echo "Nothing to send!";
    }
    


?>