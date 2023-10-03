<?php
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    require_once(__DIR__ . '/../../../templates/notifications.php');

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
    
    // Read the raw POST data from the callback
    $jsonData = file_get_contents('php://input');
    
    // Decode the JSON data
    $data = json_decode($jsonData, true);
    
    if ($data === null) {
        // JSON decoding failed; handle the error
        $response = json_encode(["status" => "error with json response"]);
    } else {
        
        $passKey = $data['challenge'];
        $invoice_id = $data['invoice_id'];
        $state = $data['state'];
        $provider = $data['provider'];
        $charges =  $data['charges'];
        $net_amount =  $data['net_amount'];
        $value = $data['value'];
        $account = $data['account'];
        $api_ref =  $data['api_ref'];
        
        $clearing_status = null;
        if(isset($data['clearing_status'])){
            $clearing_status = $data['clearing_status'];
        }else {
            $clearing_status = null;
        }
        
        $mpesa_reference = $data['mpesa_reference'];
        $failed_reason = $data['failed_reason'];
        $failed_code =  $data['failed_code'];
        $updated_at = $data['updated_at'];
        
        if($passKey === $_ENV['INTASEND_CALLBACK_KEY']){
            // Write the received data to a file
            $file = 'received_data.json';
            file_put_contents($file, $jsonData);
            
            //Check whether transaction is already created in table
            $stmt = $conn->prepare("SELECT * FROM mpesa_collections WHERE invoice_id = ?");
            $stmt->bind_param("s", $invoice_id);
            $stmt -> execute();
            $foundInvoice = $stmt->get_result();
            
            if ($foundInvoice->num_rows > 0){
                $stmt = $conn->prepare("UPDATE mpesa_collections SET state=?, clearing_status=?, mpesa_reference=?, failed_reason=?, failed_code=? WHERE invoice_id=?");
                $stmt->bind_param("ssssss", $state, $clearing_status, $mpesa_reference, $failed_reason, $failed_code, $invoice_id);
                $stmt->execute();
                
                $message = "Hi, Payment of Kshs." . $net_amount . " by " . $account . " via " . $provider . " is " . $state . " at " . $updated_at . ". Reference: " . $mpesa_reference . " . Thank you!" ;
                
                notify($message);
            } else {
                $stmt = $conn->prepare("INSERT INTO mpesa_collections (invoice_id, state, provider, charges, net_amount, value, account, api_ref, clearing_status, mpesa_reference, failed_reason, failed_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssssss", $invoice_id, $state, $provider, $charges, $net_amount, $value, $account, $api_ref, $clearing_status, $mpesa_reference, $failed_reason, $failed_code);
                $stmt->execute();
            }
            
            // Respond with a JSON acknowledgment
            $response = json_encode(["status" => "success"]);
            
        } else {
            $response = json_encode(["status" => "error authenticating"]);
        }
    }
    
    // Set the response content type to JSON
    header('Content-Type: application/json');
    
    // Send the JSON response
    echo $response;
    
    
?>