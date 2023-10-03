<?php
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    require_once (__DIR__ . '/../../../templates/sendsms.php');

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
    
    $trackingID =$_GET['trackingID'];
    $phone = $_GET['phone'];
    
    while (true){
        
        $sqlCheckTable1 = "SELECT * FROM mpesa_payments WHERE trackingID = '$trackingID'";
        $result = $conn->query($sqlCheckTable1);
        $rows = $result->fetch_assoc();
        $num_rows = $result->num_rows;
        $notifiedCheck = $rows['notified'];
        
        if($notifiedCheck > 0){
            break;
        } elseif ($notifiedCheck === '0') {
            
            $sqlCheckTable2a = "SELECT * FROM mpesa_transfers WHERE tracking_id = '$trackingID'";
            $result2a = $conn->query($sqlCheckTable2a);
            $rows2 = $result2a->fetch_assoc();
            $transaction_status2 = $rows2['transaction_status'];
            
            if($transaction_status2 === 'Pending'){
                
            } else {
                    
                $sqlCheckTable2 = "SELECT * FROM mpesa_transfers WHERE tracking_id = '$trackingID'";
                $result2 = $conn->query($sqlCheckTable2);
                $rows2 = $result2->fetch_assoc();
                $transaction_status = $rows2['transaction_status']; //Pending, Successful, Unsuccessful
                $account = $rows2['account'];
                $amount = $rows2['amount'];
                $provider_reference = $rows2['provider_reference'];
                $now = date('d-m-Y', strtotime('+3 hours'));
                
                $sqlUpdateNotified = "UPDATE mpesa_payments SET notified = '1', status= '$transaction_status' WHERE trackingID = '$trackingID'";
                $conn->query($sqlUpdateNotified);
                
                $message = 'Hi, your payment of Kshs.' . $amount . ' to ' . $account . ' is ' . $transaction_status . ' on '. $now . '. Transaction reference number ' . $provider_reference . '. Thank you.';
                
                sendSmsReminders($phone, $message);
                
                exit;
            }
        }
        sleep(5);
    }

?>