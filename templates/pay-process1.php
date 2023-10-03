<?php
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/standardize_phone.php';
    require_once 'templates/sendsms.php';
    
    use Dotenv\Dotenv;
    
    function processPayment($name, $phoneNumber, $services, $amount, $staff_name, $staff_phone, $paymentMode, $date){
    
        // Load the environment variables from .env
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
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
            
            $standardizedInput = standardizePhoneNumber($phoneNumber);
            
            $phone = '0'. $standardizedInput;
            
            // Check if customer already exists in customers table
            $sqlCheckCustomer = "SELECT * FROM customers WHERE SUBSTRING(`custPhone`, -9) = SUBSTRING('$phone', -9)";
            $result = $conn->query($sqlCheckCustomer);
            
            if ($result->num_rows > 0) {
                // Customer exists, update their points
                $sqlPoints = "UPDATE customers SET points = CAST(points AS FLOAT) + (0.01 * '$amount'), pointsBal = CAST(pointsBal AS FLOAT) + (0.01 * '$amount') WHERE SUBSTRING(`custPhone`, -9) = SUBSTRING('$phone', -9)";
            } else {
                // Customer doesn't exist, create new customer and set their points
                $sqlNewCustomer = "INSERT INTO customers (custName, custPhone, points, pointsBal) VALUES ('$name', '$phone', '0', '0')";
                $sqlPoints = "UPDATE customers SET points = CAST(points AS FLOAT) + (0.01 * '$amount'), pointsBal = CAST(pointsBal AS FLOAT) + (0.01 * '$amount') WHERE custPhone = '$phone'";
                
                if ($conn->query($sqlNewCustomer) !== TRUE) {
                    echo "Error creating new customer: " . $conn->error;
                    exit();
                }
            }
            
            $sqlPay = "INSERT INTO payments (name, phone, services, amount, staff_name, staff_phone, date, payment_mode ) 
            VALUES ('$name', '$phone', '$services', '$amount', '$staff_name', '$staff_phone', '$date', '$paymentMode' )";
            
            //Query wallet balance
            $sqlBalMpesa = "SELECT mpesa FROM wallet";
            $sqlBalKcb = "SELECT kcb FROM wallet";
            
            $resultMpesa = $conn->query($sqlBalMpesa);
            $resultKcb = $conn->query($sqlBalKcb);
    
            // Loop through the table data and generate HTML code for each row
            $accBalMpesa = 0;
            if ($resultMpesa->num_rows > 0) {
                while ($row = $resultMpesa->fetch_assoc()) {
                    $accBalMpesa= $row["mpesa"];
                }
            }
            $accBalKcb = 0;
            if ($resultKcb->num_rows > 0) {
                while ($row = $resultKcb->fetch_assoc()) {
                    $accBalKcb= $row["kcb"];
                }
            }
            
            $updatingMpesaAmount = $amount - ($amount * 0.01);
            $newMpesaWalletBal = $accBalMpesa + $updatingMpesaAmount;
            $newKcbWalletBal = $accBalKcb + $amount;
            
            if ($paymentMode === 'Mpesa Online') {
                $sqlUpdateWallet = "UPDATE wallet SET mpesa='$newMpesaWalletBal', kcb='$accBalKcb'";
                $resultMode = $conn->query($sqlUpdateWallet);
            } elseif ($paymentMode === 'KCB Paybill') {
                $sqlUpdateWallet = "UPDATE wallet SET kcb='$newKcbWalletBal', mpesa='$accBalMpesa'";
                $resultMode = $conn->query($sqlUpdateWallet);
            }
            
            
            if ($conn->query($sqlPay) === TRUE && $conn->query($sqlPoints) === TRUE && $resultMode === TRUE) {
                // Payment and points successfully inserted/updated
                header ('Location: pay');
                // Get the necessary data from the database
                $sqlPoints = "SELECT pointsBal FROM customers WHERE SUBSTRING(`custPhone`, -9) = SUBSTRING('$phone', -9)";
                $pointsBalanceResult = $conn->query($sqlPoints);
                $pointsBalance = $pointsBalanceResult->fetch_assoc()['pointsBal'];
                
                //set correct phone format
                $recipient = '+254' . substr($phone, -9);
                
                //Construct SMS to customer
                $message = 'Dear Customer, thank you for working with us. Payment received: Kshs.' . $amount . ' See you again. www.essentialtech.site';
                
                $_SESSION['redirect_url'] = 'demo/pay'; //Save the session to return back after processing
                
                //send SMS
                //sendSMS($recipient, $message);

                exit();
            } else {
                echo "Error inserting payment or updating points: " . $conn->error;
            }
    
        
    $conn->close();
    }    
    
?>
