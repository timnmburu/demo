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
    
    $date= date('Y-m-s H-i-s', strtotime('+3 hours'));
    
    $phone =$_POST['phone'];
    $otpHash = $_POST['otpHash'];
    
    $stmt = $conn->prepare('INSERT INTO otpQ (phone, otpHash, dateInitiated) VALUES (?, ?, ?)');
    $stmt->bind_param("sss", $phone, $otpHash, $date);
    $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
?>