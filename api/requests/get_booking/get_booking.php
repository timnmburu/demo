<?php
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    require_once (__DIR__ . '/../../../templates/emailing.php');
    require_once (__DIR__ . '/../../../templates/sendsms.php');

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
    
    $headers = getallheaders();
    
    if (!array_key_exists('authorization', $headers)) {
    
        echo json_encode(["error" => "Authorization header is missing"]);
        exit;
    }
    
    if (substr($headers['authorization'], 0, 7) !== 'Bearer ') {

        echo json_encode(["error" => "Bearer keyword is missing"]);
        exit;
    }
    
    $receivedToken = trim(substr($headers['authorization'], 7));
    
    $username = $_POST['username'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $results = $stmt->get_result();
    $rows = $results->fetch_assoc();
    
    if ($rows) {
        $expectedToken = $rows['api_key'];
    } else {
        echo "Credentials not found.";
        exit;
    }
    
    $expectedToken = hash('sha512', $username . ':' . $expectedToken);
    
    header('Content-Type: application/json');
    
    if (base64_decode($receivedToken) !== base64_decode($expectedToken)) {
        //header('HTTP/1.1 401 Unauthorized');
        //echo $expectedToken;
        $response = array(
            "response_code" => 401,
            "response_message" => "Unauthorized! If you think this is an error, contact us on info@essentialtech.site"
        );
        echo json_encode($response);
        exit;
    } else {
    
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $emailFrom = $_POST['email'];
        $services = $_POST['services'];
        $dateBooked = $_POST['date'];
        $dateRequested = date('Y-m-d H:i:s', strtotime('+3 hours'));
        $quote = "";
        $bookingID = date('YmdHis', strtotime('+3 hours'));
        
        if(!empty($quote)){
            $quote = "";
        } else {
            $quote = $_POST['quote'];
        }
        
        $stmt = $conn->prepare("INSERT INTO bookings (bookingID, name, phone, email, services, dateBooked, quote, dateRequested ) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $bookingID, $name, $phone, $emailFrom, $services, $dateBooked, $quote, $dateRequested);
        
        if ($stmt->execute()) {
            //header("Location: bookings");
            //Email feedback to LFH Email
            $email = $_ENV['THE_EMAIL'];
            $subject = 'New Booking By ' . $name . '[' . $phone . ']';
            $body = 'Requesting for ' . $services . ' on ' . $dateBooked . ' for Quote No:' . $quote;
            $replyTo = $emailFrom;
            
            sendEmail($email, $subject, $body, $replyTo);
            
            //Communicate to customer
            $recipient = '+254' . substr($phone, -9);
                    
            // Construct the SMS message
            $message = 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site';
            
            $_SESSION['redirect_url'] = 'book'; 
            
            sendSMS($recipient, $message);
            
            exit();
        } else {
            //echo "Error: " . $sqlBooking . "<br>" . mysqli_error($conn);
        }
        exit();
    }





?>