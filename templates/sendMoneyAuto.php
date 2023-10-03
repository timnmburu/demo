<?php
require_once __DIR__ .'/../vendor/autoload.php';


use IntaSend\IntaSendPHP\Transfer;
use Dotenv\Dotenv;

// Load the environment variables from .env
$dotenv = Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();

//function sendTithe(){
    // Retrieve the date if 10th of the month run script
    date_default_timezone_set('Etc/GMT-3');
    $date = date('Y-m-d H:i:s');
    $today = date('Y-m-d');
    $now = date('d F');
    $nowDay=date('d');
    $nowHour=date('H');
    $appointedDate = $_ENV['TITHE_DATE'];
    $appointedHour = $_ENV['TITHE_HOUR'];
    
    
    if($nowDay === $appointedDate && $nowHour === $appointedHour ){
        
        $currentMonth = date('Y-m');
        
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        //get the sum total of payments received on the 10th day of the current month
        $sqlGet10thTotal = "SELECT (SELECT SUM(amount) FROM payments WHERE DATE_FORMAT(date, '%Y-%m') = '$currentMonth' AND DAY(date) = '$appointedDate') AS total_payments ";
        $resultPayments = mysqli_query($conn, $sqlGet10thTotal);
        
        // Check if the query was successful
        if ($resultPayments->num_rows > 0) {
            // Fetch the result row
            $row = mysqli_fetch_assoc($resultPayments);
        
            // Get the total sum of payments from the result
            $totalPayments = $row['total_payments'];
        } else {
            $totalPayments = "0";
        }
        
        $name = $_ENV['PST_NAME'];
        $account1 = $_ENV['PST_NUMBER'];
        $amount1 = $totalPayments;
        $whatFor1 = 'Tithe';
        $narration1 = $whatFor1;
        
        if($amount1 > "0"){
            //Get status of the day whether paid already or not paid
            $sqlGetPaymentStatus = "SELECT status FROM givings WHERE DATE_FORMAT(date, '%Y-%m') = '$currentMonth' AND DAY(date) = '$appointedDate' ";
            $resultPaymentsStatus = mysqli_query($conn, $sqlGetPaymentStatus);
            
            // Check if the query was successful
            if ($resultPaymentsStatus->num_rows > 0) {
                // Fetch the result row
                $row = mysqli_fetch_assoc($resultPaymentsStatus);
            
                // Get the total status from the result
                $statusPaid = $row['status'];
            } else {
                $statusPaid = 'Not Paid';
            }
            
            //Check if already paid
            if($statusPaid === 'Not Paid'){
                
                //Initiate payment
                $credentials = [
                    'token' => $_ENV['INTASEND_TOKEN'],
                    'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
                ];
                
                $transfer = new Transfer();
                $transfer->init($credentials);
        
                $transactions1 = [
                    ['account'=>$account1,'amount'=>$amount1, 'narrative'=>$narration1]
                ];
                
                $response=$transfer->mpesa("KES", $transactions1);
                $account = $account1;
                $whatFor = $narration1;
                $amount = $amount1;
            
                //call approve method for approving last transaction
                $response = $transfer->approve($response);
                //print_r($response);
                
                // Check status
                $response = $transfer->status($response->tracking_id);
                //print_r($response);
                $availableMpesaBalance = $response->wallet->available_balance;
                
                if($availableMpesaBalance){
                    //Update givings table
                    $updateTithePayment = "INSERT INTO `givings`(`name`, `phone`, `amount`, `narration`, `date`, `status`) VALUES ('$name','$account','$amount','$narration1', '$date', 'Paid')";
                    
                    //Update Mpesa Payments table
                    $updateMpesaPayment = "INSERT INTO `mpesa_payments`(`name`, `phone`, `amount`, `accBal`, `date`) VALUES ('$name','$account','$amount','$availableMpesaBalance', '$date')";
                    
                    //Update wallet balance
                    $updateWalletBal = "UPDATE wallet SET mpesa='$availableMpesaBalance'";
                    
                    //Record commission paid as an expense
                    $paymentDetails = $name . " ". $account . " Payment for " . $whatFor;
                    $recordExpense = "INSERT INTO `expenses`(`name`, `price`, `quantity`, `date`, `paidFrom`) VALUES ('$paymentDetails','$amount','1','$date','Mpesa Online')";
                    
                    if ($conn->query($updateTithePayment) && $conn->query($updateMpesaPayment)  &&  $conn->query($updateWalletBal) &&  $conn->query($recordExpense)) {
                        echo " Payment sent and Records updated Successfully";
      
                    } else {
                        echo "Records update ERROR";
                    }
                } else {
                    echo "Payment not successful";
                }
            } else {
                echo "Already paid";
            }
        } else {
            echo "No amount to send";
        }
    } else {
        echo "Today is either not date " .$appointedDate . " and/or time is not " . $appointedHour . "00 Hours";
    }
    
$conn->close();
//}


?>

