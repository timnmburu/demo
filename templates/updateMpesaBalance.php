<?php
require_once 'vendor/autoload.php';


use IntaSend\IntaSendPHP\Transfer;
use IntaSend\IntaSendPHP\Wallet;
use Dotenv\Dotenv;

// Load the environment variables from .env
$dotenv = Dotenv::createImmutable(__DIR__. '/..');
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

$wallet = new Wallet();
$wallet->init($credentials);
  
$response = $wallet->retrieve();

$results = $response->results;

$availableBalance = 0;

foreach ($results as $result) {
  if ($result->currency === "KES") {
    $availableBalance = $result->available_balance;
  }
}

//Update wallet balance
$updateWalletBal = "UPDATE wallet SET mpesa='$availableBalance'";


if ($conn->query($updateWalletBal)) {
    //echo "Commission Paid Successfully";
    
} else {
    echo "Error updating balance: " . $conn->error;
}


?>

