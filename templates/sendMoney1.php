<?php
require_once __DIR__ .'/../vendor/autoload.php';


use IntaSend\IntaSendPHP\Transfer;
use Dotenv\Dotenv;

// Load the environment variables from .env
$dotenv = Dotenv::createImmutable(__DIR__. '/../');
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

$credentials = [
    'token' => $_ENV['INTASEND_TOKEN'],
    'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
];


$transfer = new Transfer();
$transfer->init($credentials);

//if (isset($_POST['getInvoiceStatus'])) {
    
// Retrieve the name and phone from the query parameters
date_default_timezone_set('Etc/GMT-3');
$date = date('Y-m-d H:i:s');
$now = date('d F');

$name = $_GET['name'];

$account1 = $_GET['account1'];
$amount1 = '1';
$whatFor1 = $_GET['reason1'];
$narration1 = 'Excel Tech';

$account2 = $_GET['account2'];
$amount2 = '1';
$whatFor2 = $_GET['reason2'];
$narration2 = 'Excel Tech';

$account3 = $_GET['account3'];
$accountref3 = $_GET['accountref3'];
$amount3 = '1';
$whatFor3 = $_GET['reason3'];
$narration3 = 'Excel Tech';

$bankcode4 = $_GET['bankcode4'];
$account4 = $_GET['account4'];
$amount4 = '1';
$whatFor4 = $_GET['reason4'];
//$narration4 = $whatFor4 . $now;
$narration4 ='Excel Tech';


$transactions1 = [
    ['account'=>$account1,'amount'=>$amount1, 'narrative'=>$narration1]
];

$transactions2 = [
    ['name' => $name,'account'=>$account2,'amount'=>$amount2, 'account_type'=>'TillNumber', 'narrative'=> $narration2]
];

$transactions3 = [
    ['name' => $name,'account'=>$account3,'amount'=>$amount3, 'account_type'=>'PayBill', 'account_reference'=>$accountref3, 'narrative'=> $narration3]
];

$transactions4 = [
    ['name' => $name,'account'=>$account4,'amount'=>$amount4, 'bank_code'=>$bankcode4, 'narrative'=> $narration4]
];


if (!empty($account1)) {
    $response=$transfer->mpesa("KES", $transactions1);
    $account = $account1;
    $whatFor = $narration1;
    $amount = $amount1;
} elseif (!empty($account2)) {
    $response=$transfer->mpesa_b2b("KES", $transactions2);
    $account = $account2;
    $whatFor = $narration2;
    $amount = $amount2;
} elseif (!empty($account3)) {
    $response=$transfer->mpesa_b2b("KES", $transactions3);
    $account = $account3;
    $whatFor = $narration3;
    $amount = $amount3;
} elseif (!empty($account4)) {
    $response=$transfer->bank("KES", $transactions4);
    $account = $account4;
    $whatFor = $narration4;
    $amount = $amount4;
} else {
    echo "Payment Mode not captured";
    $response=null;
    $account = null;
    $whatFor = null;
    $amount = null;
}

//call approve method for approving last transaction
$response = $transfer->approve($response);
//print_r($response);

// Check status
$response = $transfer->status($response->tracking_id);
//print_r($response);
$availableMpesaBalance = $response->wallet->available_balance;

//Update Mpesa Payments table
$updateMpesaPayment = "INSERT INTO `mpesa_payments`(`name`, `phone`, `amount`, `accBal`, `date`) VALUES ('$name','$account','$amount','$availableMpesaBalance', '$date')";

//Update wallet balance
$updateWalletBal = "UPDATE wallet SET mpesa='$availableMpesaBalance'";

//Record commission paid as an expense
$paymentDetails = $name . " ". $account . " Payment for " . $whatFor;
$recordExpense = "INSERT INTO `expenses`(`name`, `price`, `quantity`, `date`, `paidFrom`) VALUES ('$paymentDetails','$amount','1','$date','Mpesa Online')";

if ($conn->query($updateMpesaPayment) && $conn->query($updateWalletBal) &&  $conn->query($recordExpense)) {
    //echo "Commission Paid Successfully";
    //header ('Location: /admins');
    if(!empty($whatFor1) && $whatFor1 === "Commission by "){
        $sqlUpdatePaidStatus = "UPDATE payments SET commission_paid = 'Paid' WHERE staff_phone= '$account1'";
        $conn->query($sqlUpdatePaidStatus);
        
        $updateCommissionPayment = "INSERT INTO `commission_payments`(`name`, `phone`, `amount`, `accBal`, `date`) VALUES ('$name','$account','$amount','$availableMpesaBalance', '$date')";
        $conn->query($updateCommissionPayment);
    } else {
        //nothing
    }
} else {
    //echo "Error making payment: " . $conn->error;
}

$conn->close();
?>

