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

// Database connection

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

$account1 = isset($_GET['account1']) ? $_GET['account1'] : '0';
$amount1 = isset($_GET['amount1']) ? $_GET['amount1'] : '0';
$whatFor1 = isset($_GET['reason1']) ? $_GET['reason1'] : '0';
$narration1 = 'Excel Tech';

$account2 = isset($_GET['account2']) ? $_GET['account2'] : '0';
$amount2 = isset($_GET['amount2']) ? $_GET['amount2'] : '0';
//$whatFor2 = isset($_GET['reason2']) ? $_GET['reason2'] : '0';
$narration2 = 'Excel Tech';

$account3 = isset($_GET['account3']) ? $_GET['account3'] : '0';
$accountref3 = isset($_GET['accountref3']) ? $_GET['accountref3'] : '0';
$amount3 = isset($_GET['amount3']) ? $_GET['amount3'] : '0';
//$whatFor3 = isset($_GET['reason3']) ? $_GET['reason3'] : '0';
$narration3 = 'Excel Tech';

$bankcode4 = isset($_GET['bankcode4']) ? $_GET['bankcode4'] : '0';
$account4 = isset($_GET['account4']) ? $_GET['account4'] : '0';
$amount4 = isset($_GET['amount4']) ? $_GET['amount4'] : '0';
//$whatFor4 =  isset($_GET['reason4']) ? $_GET['reason4'] : '0';
$narration4 = 'Excel Tech';


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

//$file = 'received_data.txt';
//file_put_contents($file, $response);

// Check status
$response = $transfer->status($response->tracking_id);
//print_r($response);

$trackingID = $response->tracking_id;


if (isset($_SESSION['userphone'])){
    $notifyPhone = $_SESSION['userphone'];
} else {
    $notifyPhone = '254725887269';
}


$availableMpesaBalance = $response->wallet->available_balance;

//Update Mpesa Payments table
$updateMpesaPayment = "INSERT INTO `mpesa_payments`(`name`, `phone`, `amount`, `accBal`, `date`, `notifyPhone`, `trackingID`) VALUES ('$name','$account','$amount','$availableMpesaBalance', '$date', '$notifyPhone', '$trackingID')";

//Update wallet balance
$updateWalletBal = "UPDATE wallet SET mpesa='$availableMpesaBalance'";

//Record commission paid as an expense
$paymentDetails = $name . " ". $account . " Payment for " . $whatFor;
$recordExpense = "INSERT INTO `expenses`(`name`, `price`, `quantity`, `date`, `paidFrom`) VALUES ('$paymentDetails','$amount','1','$date','Mpesa Online')";

if ($conn->query($updateMpesaPayment) && $conn->query($updateWalletBal) &&  $conn->query($recordExpense)) {

    //header ('Location: /admins');
    if(!empty($whatFor1) && $whatFor1 === "Commission by "){
        $sqlUpdatePaidStatus = "UPDATE payments SET commission_paid = 'Paid' WHERE staff_phone= '$account1'";
        $conn->query($sqlUpdatePaidStatus);
        
        $updateCommissionPayment = "INSERT INTO `commission_payments`(`name`, `phone`, `amount`, `accBal`, `date`) VALUES ('$name','$account','$amount','$availableMpesaBalance', '$date')";
        $conn->query($updateCommissionPayment);
        
        exit;
    }
    
    sleep(10); 
    
    $notifyURL = 'https://www.essentialtech.site/demo/api/requests/get_payment_status/?trackingID='. $trackingID . '&phone=' . $notifyPhone;
    file_get_contents($notifyURL);
    
    //echo "
        //<script>
           // window.location.href = 'withdraw';
        //</script>";
    exit;    
} else {
    echo "Error making payment: " . $conn->error;
    exit;
}

$conn->close();

?>

