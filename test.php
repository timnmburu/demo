<?php 

    require_once(__DIR__ . '/vendor/autoload.php'); 
    require_once(__DIR__ . '/templates/cryptOtp.php');
    
    
    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Database connection
    $servername = $_ENV['DB_HOST'];
    $usernameD = $_ENV['DB_USERNAME'];
    $passwordD = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = mysqli_connect($servername, $usernameD, $passwordD, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $client_id = $_ENV['SASAPAY_CLIENT_ID'];
    $client_secret=$_ENV['SASAPAY_CLIENT_SECRET'];
    
    
    $url = 'https://sandbox.sasapay.app/api/v1/auth/token/?grant_type=client_credentials';
    
    $requestBody = array(
            'client_id' => $client_id,
           'client_secret' => $client_secret,
        );
        
    $headers = array(
        'Authorization: Basic '. base64_encode($requestBody['client_id'].':'.$requestBody['client_secret']),
    );
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => $headers
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    $response = json_decode($response, true);
    $access_token = $response['access_token'];
    
    if($access_token){
        $MerchantCode = '600980';
        $ConfirmationUrl = 'https://www.essentialtech.site/demo/api/callbackurls/sasapay/';

        $url2 = 'https://sandbox.sasapay.app/api/v1/payments/request-payment/';
        
        $requestBody2 = [
            "MerchantCode" => "600980",
            "NetworkCode"=> "0",
            "PhoneNumber"=> "254725887269",
            "TransactionDesc"=> "Pay for groceries",
            "AccountReference"=> "07258872690",
            "Currency"=> "KES",
            "Amount"=> 1,
            "TransactionFee"=> 0,
            "CallBackURL"=> $ConfirmationUrl
            ];
       $headers2 = [
            'Authorization: Bearer ' . $access_token,
        ];
    
        $curl2 = curl_init();
        curl_setopt_array($curl2, array(
            CURLOPT_URL => $url2,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $requestBody2,
            CURLOPT_HTTPHEADER => $headers2,
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $response2 = curl_exec($curl2);
        curl_close($curl2);
    
        echo $response2;
    
    } else {
        echo "hey";
    }

  
?>