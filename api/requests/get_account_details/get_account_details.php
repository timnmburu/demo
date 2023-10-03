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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $urlAccount = "https://www.essentialtech.site/api/requests/get_account_details/?custID=";
    
    $sqlGetCustID = $conn->query("SELECT custID FROM users ORDER BY id DESC LIMIT 1");
    $getCustID = $sqlGetCustID->fetch_assoc();
    
    if(!$getCustID){
        
    } else {
        $custID = $getCustID['custID'];
        
        $url = $urlAccount . $custID;
        
        $response = file_get_contents($url);
    
        $data = json_decode($response, true);
        
        $sqlDelete = $conn->query("TRUNCATE account");
        
        if ($data !== null) {
            foreach ($data as $row) {
                $s_no = $row['s_no'];
                $custID = $row['custID'];
                $custName = $row['custName'];
                $custPhone = $row['custPhone'];
                $subDate = $row['subDate'];
                $subAmount = $row['subAmount'];
                $lastPayAmount = $row['lastPayAmount'];
                $lastPayDate = $row['lastPayDate'];
                $nextPayDate = $row['nextPayDate'];
                $status = $row['status'];
                
                //if(strtotime($lastPayDate) < strtotime(date('d-m-Y')) && strtotime($nextPayDate) < strtotime(date('d-m-Y'//))){
                  // $status = 'full access';
                //} elseif(strtotime($nextPayDate) < strtotime(date('d-m-Y'))){
                //   $status = 'limited';
               // }
    
                $sqlUpdate = "INSERT INTO account (custID, custName, custPhone, subDate, subAmount, lastPayAmount, lastPayDate, nextPayDate, status) VALUES ('$custID', '$custName', '$custPhone', '$subDate', '$subAmount', '$lastPayAmount', '$lastPayDate', '$nextPayDate', '$status')";
                $conn->query($sqlUpdate);
           }
        }
    }
    
?>