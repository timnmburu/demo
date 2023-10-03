<?php
    use Dotenv\Dotenv;
    
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    
    session_start();
    if (!isset($_SESSION['username'])) {
          header('Location: login');
          exit;
    } elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false){
        header('Location: admins');
    }
    
        // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
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
    
    if(isset($_POST['submitFrequentPayment'])){
        $name = $_POST['name'];
        $wallet = $_POST['wallet'];
        $account = $_POST['account'];
        $reference = $_POST['reference'];
        
        $sqlAddFrequent = $conn->prepare("INSERT INTO frequentPayments (name, wallet, account, reference) VALUES (?,?,?,?) ");
        $sqlAddFrequent->bind_param("ssss",$name, $wallet, $account, $reference);
        
        
        if($sqlAddFrequent->execute()){
            echo "Frequent Payment Added Successfully";
            $sqlAddFrequent->close();
        }
   
    }

    $conn->close();

?>

<!DOCTYPE html>    
<html>
    <h1> Add Frequent Payment</h1>
    <div class="newFrequent" style="border:1px solid black; display:flex; padding:4px;" >
        <form style="width: 80%;" method="POST" action="">
            <title> Name: </title>
            <input placeholder="Name" type="text" name="name" style="width: 95%; height: 30px;" required>
            <br> 
            <br>
            <title> Wallet:</title>
            <input placeholder="Either Mpesa, Buygoods, Paybill, or Bank" type="text" name="wallet" style="width: 95%; height: 30px;" required>
            <br>
            <br>

            <title> Reference Number:</title>
            <input placeholder="Bank Name or Mpesa Paybill" type="text" name="reference" style="width: 95%; height: 30px;" required>
            <br>
            <br>
            <title> Account Number:</title>
            <input placeholder="Bank or Mpesa account" type="number" name="account" style="width: 95%; height: 30px;" required>
            <br>
            <br>
            <input type="submit" value="Add New Details" name="submitFrequentPayment" style="width: 110px; height: 30px; padding: 4px;" >
        </form>
    

        <?php include 'templates/sessionTimeoutL.php'; ?>
    </div>
    
</html>
