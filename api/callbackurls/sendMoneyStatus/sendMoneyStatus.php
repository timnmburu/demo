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
    

    // Read the raw POST data from the callback
    $jsonData = file_get_contents('php://input');

    // Write the received data to a file
    $file = 'received_data.json';
    file_put_contents($file, $jsonData);

    
?>


