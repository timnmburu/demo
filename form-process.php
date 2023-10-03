<?php

require_once 'vendor/autoload.php';
require_once 'templates/emailing.php';
require_once 'templates/sendsms.php';


use Dotenv\Dotenv;

// Load the environment variables from .env
$dotenv = Dotenv::createImmutable(__DIR__);
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

// Insert form data into feedback database
if(isset($_POST['submitFeedback'])){
    $name = $_POST['name'];
    $emailFrom = $_POST['email'];
    $comment = $_POST['feedback'];
    $date = date('Y-m-d H:i:s', strtotime('+3 hours'));
    
    $sqlFeedback = "INSERT INTO feedback (name, email, comment, date ) 
    VALUES ('$name', '$emailFrom', '$comment', '$date')";
    
    $pattern1 = "/no\s*reply/i";
    $pattern2 = "/no[\s.]*reply/i";
    $pattern3 ="/no[\s.-]*reply/i";
    $pattern4 = "/Robert[\s.-]*Omirm/i";
  
    $doubleCheck = "SELECT email FROM feedback WHERE email = '$emailFrom'";
    $doubleEntry = mysqli_query($conn, $doubleCheck);
  
    if (preg_match($pattern1, $emailFrom) || preg_match($pattern2, $emailFrom) || preg_match($pattern3, $emailFrom) || preg_match($pattern4, $name)) {
        header ("Location: contacts");
        exit();
    } else if (mysqli_num_rows($doubleEntry) > 0 ){
        header ("Location: contacts");
        exit();
    } else {
        if (mysqli_query($conn, $sqlFeedback)) {
            header("Location: contacts");
            //Email feedback to LFH Email
            $email = 'info@lfhcompany.site';
            $subject = 'Feedback From ' . $name . '[' . $emailFrom . ']';
            $body = $comment;
            $replyTo = $emailFrom;
            
            sendEmail($email, $subject, $body, $replyTo);
            
            exit();
        } else {
            echo "Error: " . $sqlFeedback . "<br>" . mysqli_error($conn);
        }
        
        
        exit();
        
    }
}


$sql = "SELECT * FROM feedback";
$result = $conn->query($sql);


// Insert form data into customers database
if(isset($_POST['submitCust'])){
  $nameOO = $_POST['customerName'];
  $phoneOO = $_POST['customerPhone'];

  $sqlCust = "INSERT INTO customers (custName, custPhone) 
  VALUES ('$nameOO', '$phoneOO')";

  if ($conn->query($sqlCust) === TRUE) {
        header("Location: new_customer");
    exit();
  } else {
    echo "Error: " . $sqlCust . "<br>" . $conn->error;
  }
}
// Insert form data into customers database (Redirected to sendSms.php)
if(isset($_POST['submitPay'])){
  $name = $_POST['cust-name'];
  $phone = $_POST['tel-number'];
  $services = $_POST['services'];
  $amount = $_POST['amount'];
  $staffData = explode('-', $_POST['staff']);
  $staff_name = trim($staffData[0]);
  $staff_phone = trim($staffData[1]);

  // Check if customer already exists in customers table
  $sqlCheckCustomer = "SELECT * FROM customers WHERE RIGHT(`custPhone`, 9) = RIGHT('$phone', 9)";
  $result = $conn->query($sqlCheckCustomer);

  if ($result->num_rows > 0) {
    // Customer exists, update their points
    $sqlPoints = "UPDATE customers SET points = CAST(points AS FLOAT) + (0.01 * '$amount'), pointsBal = CAST(pointsBal AS FLOAT) + (0.01 * '$amount') WHERE RIGHT(`custPhone`, 9) = RIGHT('$phone', 9)";
  } else {
    // Customer doesn't exist, create new customer and set their points
    $sqlNewCustomer = "INSERT INTO customers (custName, custPhone, points, pointsBal) VALUES ('$name', '$phone', '0', '0')";
    $sqlPoints = "UPDATE customers SET points = CAST(points AS FLOAT) + (0.01 * '$amount'), pointsBal = CAST(pointsBal AS FLOAT) + (0.01 * '$amount') WHERE custPhone = '$phone'";
    
    if ($conn->query($sqlNewCustomer) !== TRUE) {
      echo "Error creating new customer: " . $conn->error;
      exit();
    }
  }

  $sqlPay = "INSERT INTO payments (name, phone, services, amount, staff_name, staff_phone ) 
  VALUES ('$name', '$phone', '$services', '$amount', '$staff_name', '$staff_phone' )";
  
  if ($conn->query($sqlPay) === TRUE && $conn->query($sqlPoints) === TRUE) {
    // Payment and points successfully inserted/updated
    header("Location: pay");
    exit();
  } else {
    echo "Error inserting payment or updating points: " . $conn->error;
  }
}


//Redeeming Points
// Check if the form has been submitted
if (isset($_POST['submitRedeem'])) {
    $selectedCustomer = $_POST['customer'];
    $pointsToRedeem = $_POST['points'];
    
    // SQL query to fetch customer from the customers table
    $sqlCustomer = "SELECT points, redeemed, lastRedeemed, pointsBal FROM customers WHERE custPhone = '" . $selectedCustomer . "'";
    
    // Execute the query
    $result = $conn->query($sqlCustomer);
    
    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        // Get the customer data
        $row = $result->fetch_assoc();
        $custPoints = $row['points'];
        $redeemedPoints = $row['redeemed'];
        $lastRedeemed = $row['lastRedeemed'];
        $pointsBal = $row['pointsBal'];
        
        // Check if the customer has enough points to redeem
        if ($custPoints >= $pointsToRedeem) {
            // Calculate the new points balance
            $newPoints = $pointsBal - $pointsToRedeem;
            $totalRedeemed = $redeemedPoints + $pointsToRedeem;
            $currentDateTime = date('Y-m-d H:i:s', strtotime('+3 hours'));
            
            
            // SQL query to update customer points
            $sqlUpdate = "UPDATE customers SET pointsBal = " . $newPoints . ", redeemed = " . $totalRedeemed . ", lastRedeemed = '$currentDateTime' WHERE custPhone = '" . $selectedCustomer . "'";
            
            // Execute the query
            if ($conn->query($sqlUpdate) === TRUE) {
                //Get phone number of customer
                    $sqlPhone = "SELECT custPhone FROM customers WHERE lastRedeemed = '$currentDateTime'";
                    $phoneResult = $conn->query($sqlPhone);
                    $phone = $phoneResult->fetch_assoc()['custPhone'];
                    
                    $sqlName = "SELECT custName FROM customers WHERE SUBSTRING(`custPhone`, -9) = SUBSTRING('$phone', -9)";
                    $nameResult = $conn->query($sqlName);
                    $name = $nameResult->fetch_assoc()['custName'];
                
                //Send SMS or Redeeming Points to customer
                $sqlName = "SELECT custName FROM customers WHERE SUBSTRING(`custPhone`, -9) = SUBSTRING('$phone', -9)";
                $nameResult = $conn->query($sqlName);
                $name = $nameResult->fetch_assoc()['custName'];
                
                $recipient = '+254' . substr($phone, -9);
                
                // Construct the SMS message
                $message = 'Dear Customer, thank you for visiting LOURICE BEAUTY PARLOUR. Redeemed points:' . $pointsToRedeem . '. Loyalty Points now at ' . $newPoints . '. See you again. www.lfhcompany.site';
                
                $_SESSION['redirect_url'] = 'redeem'; //Save the session to return back after processing
                
                //sendSMS($recipient, $message);
                
                echo "Points redeemed successfully";
                exit;
            } else {
                echo "Error updating points: " . $conn->error;
            }
        } else {
            echo "Customer does not have enough points to redeem";
        }
    } else {
        echo "Customer not found";
    } 
}



// Check the customers points
if (isset($_POST['checkPointsBal'])) {
    // Retrieve form data
    $phone = $_POST['customer-phone'];

    // Check if the customer exists in the payments table
    $sqlCheckPointsBal  = "SELECT pointsBal, lastRedeemed FROM customers WHERE RIGHT(`custPhone`, 9) = RIGHT('$phone', 9)";
    $result = $conn->query($sqlCheckPointsBal);

    if ($result->num_rows > 0) {
        // Customer found, retrieve their points and last redeemed date
        $row = $result->fetch_assoc();
        $points = $row['pointsBal'];
        $lastRedeemed = $row['lastRedeemed'];
        echo "<br>" . "Your points: " . $points . "<br>" . " Redeem Value: Kshs. " . $points * 5 . "<br>";
        echo "Last Redeemed: " . $lastRedeemed;
    } else {
        // Customer not found, display error message
        echo "Customer not found.";
    }

}

// Insert form data into feedback database
if(isset($_POST['submitBooking'])){
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
    
    $sqlBooking = "INSERT INTO bookings (bookingID, name, phone, email, services, dateBooked, quote, dateRequested ) 
    VALUES ('$bookingID', '$name', '$phone', '$emailFrom', '$services', '$dateBooked', '$quote', '$dateRequested')";
    
    if (mysqli_query($conn, $sqlBooking)) {
        //header("Location: bookings");
        //Email feedback to LFH Email
        $email = 'info@essentialtech.site';
        $subject = 'New Booking By ' . $name . '[' . $phone . ']';
        $body = 'Requesting for ' . $services . ' on ' . $dateBooked . ' for Quote No:' . $quote;
        $replyTo = $emailFrom;
        
        sendEmail($email, $subject, $body, $replyTo);
        
        //Communicate to customer
        $recipient = '+254' . substr($phone, -9);
                
        // Construct the SMS message
        $message = 'Dear Customer, thank you for choosing us. Your booking has been captured successfully and is being processed. Please wait for us to contact you on the next steps. www.essentialtech.site';
        
        //$return = sendSMSBooking($recipient, $message);
        $return = sendSMS($recipient, $message);
        
        if($return){
            header("Location: book");
        }
        
        exit();
    } else {
        //echo "Error: " . $sqlBooking . "<br>" . mysqli_error($conn);
    }
    exit();
}

$conn->close();