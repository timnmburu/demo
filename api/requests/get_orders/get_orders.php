<?php
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    require_once (__DIR__ . '/../../../templates/emailing.php');

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
		$email = $_POST['email'];
		$phone = $_POST['phone'];
		$product = $_POST['product'];
		$quantity = $_POST['quantity'];
		$country = $_POST['country'];
		$postal_address = $_POST['postal_address'];
		$postal_code = $_POST['postal_code'];
		$location = $_POST['location'];
		$date = $_POST['date'];

		$sqlOrder = "INSERT INTO orders (orderTime, custName, email, phone, product, quantity, country, postal_address, postal_code, location) 
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		
		$stmt = $conn->prepare($sqlOrder);
		$stmt->bind_param("sssssisiis", $date, $name, $email, $phone, $product, $quantity, $country, $postal_address, $postal_code, $location);

		if ($stmt->execute()) {
			//Email order to LFH Email
			$subject = 'Order From ' . $name . '[' . $email . ']' . ' at ' . $date;
			$body = '<b> Order Details </b> <br>  Product: ' . $product . '<br> Quantity: ' . $quantity . ' <br> Phone: ' . $phone . '<br> Country: ' . $country . ' <br> Postal: ' . $postal_address . '<br> Location: '. $location . '<br> <br> In case of any correspondence, reply to this email.';
			
			$replyTo = $email;
			$email = $_ENV['THE_EMAIL'];
			
			
			sendEmail($email, $subject, $body, $replyTo);
			
			echo ("Email sent");
			exit();
		} else {
			echo "Error: " . $stmt->error;
		}
		
		$stmt->close();
	}
    
?>