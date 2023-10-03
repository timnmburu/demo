<?php
    require_once __DIR__ .'/../vendor/autoload.php'; // Include the Dotenv library

    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__. '/../');
    $dotenv->load();

    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    // Replace this with your own logic to retrieve payment details from the database
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Check if the request method is POST to edit the expenses table
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Get the JSON data from the request body
        $requestData = file_get_contents("php://input");
        $expenseData = json_decode($requestData);
        
        $table = $expenseData->table;
        
        // Update table with decoded json data
        if ($expenseData !== null && $table === 'expenses') {
            // Extract the data from the JSON object
            $id = $expenseData->id;
            $name = $expenseData->name;
            $price = $expenseData->price;
            $quantity = $expenseData->quantity;
            $date = $expenseData->date;
            
    
            // Use prepared statements to prevent SQL injection
            $sqlUpdate = "UPDATE $table SET name=?, price=?, quantity=?, date=? WHERE id=?";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bind_param("ssisi", $name, $price, $quantity, $date, $id);
    
            if ($stmt->execute() === true) {
                // Database update was successful
                $response = array("success" => true);
            } else {
                // Database update failed
                $response = array("success" => false, "error" => $stmt->error);
            }
    
            // Close the database connection
            $stmt->close();
            $conn->close();
    
            // Send a JSON response to the client
            header("Content-Type: application/json");
            echo json_encode($response);
        } elseif ($expenseData !== null && $table === 'payments') {
            // Extract the data from the JSON object
            $id = $expenseData->id;
            $name = $expenseData->name;
            $phone = $expenseData->phone;
            $services = $expenseData->services;
            $amount = $expenseData->amount;
            $staff_name = $expenseData->staff_name;
            $staff_phone = $expenseData->staff_phone;
            $date = $expenseData->date;
            
    
            // Use prepared statements to prevent SQL injection
            $sqlUpdate = "UPDATE $table SET name=?, phone=?, services=?, amount=?, staff_name=?, staff_phone=?, date=? WHERE s_no=?";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bind_param("ssssssss", $name, $phone, $services, $amount, $staff_name, $staff_phone, $date, $id);
    
            if ($stmt->execute()) {
                // Database update was successful
                $response = array("success" => true);
            } else {
                // Database update failed
                $response = array("success" => false, "error" => $stmt->error);
            }
    
            // Close the database connection
            $stmt->close();
            $conn->close();
    
            // Send a JSON response to the client
            header("Content-Type: application/json");
            echo json_encode($response);
        }elseif ($expenseData !== null && $table === 'inventory') {
            // Extract the data from the JSON object
            $id = $expenseData->id;
            $name = $expenseData->name;
            $price = $expenseData->price;
            $quantity = $expenseData->quantity;
            $date = $expenseData->date;
            
    
            // Use prepared statements to prevent SQL injection
            $sqlUpdate = "UPDATE $table SET name=?, price=?, quantity=?, date=? WHERE id=?";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bind_param("ssisi", $name, $price, $quantity, $date, $id);
    
            if ($stmt->execute() === true) {
                // Database update was successful
                $response = array("success" => true);
            } else {
                // Database update failed
                $response = array("success" => false, "error" => $stmt->error);
            }
    
            // Close the database connection
            $stmt->close();
            $conn->close();
    
            // Send a JSON response to the client
            header("Content-Type: application/json");
            echo json_encode($response);
        } else {
            // JSON data could not be decoded
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("error" => "Invalid JSON data"));
        }
    }
?>