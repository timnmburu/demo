<?php
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/standardize_phone.php';
    require_once 'templates/sendsms.php';

    
    use Dotenv\Dotenv;
    
    function processPayment($name, $phoneNumber, $services, $amount, $staff_name, $staff_phone, $paymentMode, $date, $notify){
        
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
            $points = 0;
            $pointsBal = 0;
            $sqlNewCustomer = $conn->prepare("INSERT INTO customers (custName, custPhone, points, pointsBal) VALUES (?,?,?,?)");
            $sqlNewCustomer->bind_param("ssii",$name, $phone, $points, $pointsBal);
            
            $sqlPoints = "UPDATE customers SET points = CAST(points AS FLOAT) + (0.01 * '$amount'), pointsBal = CAST(pointsBal AS FLOAT) + (0.01 * '$amount') WHERE custPhone = '$phone'";
            
            if ($sqlNewCustomer->execute() !== TRUE) {
                echo "Error creating new customer: " . $conn->error;
                exit();
            }
        }
        
        $sqlPay = $conn->prepare("INSERT INTO payments (name, phone, services, amount, staff_name, staff_phone, date, payment_mode ) 
        VALUES (?,?,?,?,?,?,?,? )");
        $sqlPay->bind_param("ssssssss", $name, $phone, $services, $amount, $staff_name, $staff_phone, $date, $paymentMode);
        
        
        //Query wallet balance
        $sqlBalMpesa = "SELECT mpesa FROM wallet";
        $sqlBalKcb = "SELECT kcb FROM wallet";
        
        $resultMpesa = $conn->query($sqlBalMpesa);
        $resultKcb = $conn->query($sqlBalKcb);

        // Loop through the table data and generate HTML code for each row
        if ($resultMpesa->num_rows > 0) {
            while ($row = $resultMpesa->fetch_assoc()) {
                $accBalMpesa= $row["mpesa"];
            }
        }
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
        } else {
            $sqlUpdateWallet = "UPDATE wallet SET kcb='$accBalKcb', mpesa='$accBalMpesa'";
            $resultMode = $conn->query($sqlUpdateWallet);
        }
        
        
        if ($sqlPay->execute() === TRUE && $conn->query($sqlPoints) === TRUE && $resultMode === TRUE) {
            // Payment and points successfully inserted/updated
            //header ('Location: pay');
            // Get the necessary data from the database
            $sqlPoints = "SELECT pointsBal FROM customers WHERE SUBSTRING(`custPhone`, -9) = SUBSTRING('$phone', -9)";
            $pointsBalanceResult = $conn->query($sqlPoints);
            $pointsBalance = $pointsBalanceResult->fetch_assoc()['pointsBal'];
            
            //set correct phone format
            $recipient = '+254' . substr($phone, -9);
            
            if($staff_phone === '0781099212'){
            //Construct SMS to customer
            $message = 'Dear ' . $name . ', payment for your booking is received: Kshs.' . $amount . '. Loyalty Points now at ' . $pointsBalance . '. Thank you for choosing us. www.essentialtech.site';
            } else {
            //Construct SMS to customer
            $message = 'Dear ' . $name . ', thank you for visiting EXCEL TECH ESSENTIALS. Payment received: Kshs.' . $amount . '. Loyalty Points now at ' . $pointsBalance . '. See you again. www.essentialtech.site';
            }
            
            $_SESSION['redirect_url'] = 'pay'; //Save the session to return back after processing
            
            //send SMS
            if($notify > 0){
                sendSMS($recipient, $message);
            } else {
                header('Location: pay');
            }

            exit();
        } else {
            echo "Error inserting payment or updating points: " . $conn->error;
        }
        
        $conn->close();

    } 
    
    
    
    //Where 1 customer is served by 2 staff, consolidate the amount paid for all services offered and capture staff
    function processPayment2($name, $phoneNumber, $services, $amount, $staff_name, $staff_phone, $services2, $amount2, $staff_name2, $staff_phone2, $paymentMode, $date, $notify){
            
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
            
            $totalAmount = $amount + $amount2;
            
            // Check if customer already exists in customers table
            $sqlCheckCustomer = "SELECT * FROM customers WHERE SUBSTRING(`custPhone`, -9) = SUBSTRING('$phone', -9)";
            $result = $conn->query($sqlCheckCustomer);
            
            if ($result->num_rows > 0) {
                // Customer exists, update their points
                $sqlPoints = "UPDATE customers SET points = CAST(points AS FLOAT) + (0.01 * '$totalAmount'), pointsBal = CAST(pointsBal AS FLOAT) + (0.01 * '$totalAmount') WHERE SUBSTRING(`custPhone`, -9) = SUBSTRING('$phone', -9)";
            } else {
                // Customer doesn't exist, create new customer and set their points
                $points = 0;
                $pointsBal = 0;
                $sqlNewCustomer = $conn->prepare("INSERT INTO customers (custName, custPhone, points, pointsBal) VALUES (?,?,?,?)");
                $sqlNewCustomer->bind_param("ssii",$name, $phone, $points, $pointsBal);
                
                $sqlPoints = "UPDATE customers SET points = CAST(points AS FLOAT) + (0.01 * '$totalAmount'), pointsBal = CAST(pointsBal AS FLOAT) + (0.01 * '$totalAmount') WHERE custPhone = '$phone'";
                
                if ($sqlNewCustomer->execute() !== TRUE) {
                    echo "Error creating new customer: " . $conn->error;
                    exit();
                }
            }
            
            $sqlPay = $conn->prepare("INSERT INTO payments (name, phone, services, amount, staff_name, staff_phone, date, payment_mode ) VALUES (?,?,?,?,?,?,?,? )");
            $sqlPay->bind_param("ssssssss", $name, $phone, $services, $amount, $staff_name, $staff_phone, $date, $paymentMode);
            
            $sqlPay2 = $conn->prepare("INSERT INTO payments (name, phone, services, amount, staff_name, staff_phone, date, payment_mode ) VALUES (?,?,?,?,?,?,?,? )");
            $sqlPay2->bind_param("ssssssss", $name, $phone, $services2, $amount2, $staff_name2, $staff_phone2, $date, $paymentMode);
            
            //Query wallet balance
            $sqlBalMpesa = "SELECT mpesa FROM wallet";
            $sqlBalKcb = "SELECT kcb FROM wallet";
            
            $resultMpesa = $conn->query($sqlBalMpesa);
            $resultKcb = $conn->query($sqlBalKcb);
    
            // Loop through the table data and generate HTML code for each row
            if ($resultMpesa->num_rows > 0) {
                while ($row = $resultMpesa->fetch_assoc()) {
                    $accBalMpesa= $row["mpesa"];
                }
            }
            if ($resultKcb->num_rows > 0) {
                while ($row = $resultKcb->fetch_assoc()) {
                    $accBalKcb= $row["kcb"];
                }
            }
            
            $updatingMpesaAmount = $totalAmount - ($totalAmount * 0.01);
            $newMpesaWalletBal = $accBalMpesa + $updatingMpesaAmount;
            $newKcbWalletBal = $accBalKcb + $totalAmount;
            
            if ($paymentMode === 'Mpesa Online') {
                $sqlUpdateWallet = "UPDATE wallet SET mpesa='$newMpesaWalletBal', kcb='$accBalKcb'";
                $resultMode = $conn->query($sqlUpdateWallet);
            } elseif ($paymentMode === 'KCB Paybill') {
                $sqlUpdateWallet = "UPDATE wallet SET kcb='$newKcbWalletBal', mpesa='$accBalMpesa'";
                $resultMode = $conn->query($sqlUpdateWallet);
            }
            
            
            if ($sqlPay->execute() === TRUE && $sqlPay2->execute() === TRUE && $conn->query($sqlPoints) === TRUE && $resultMode === TRUE) {
                // Payment and points successfully inserted/updated
                //header ('Location: pay');
                // Get the necessary data from the database
                $sqlPoints = "SELECT pointsBal FROM customers WHERE SUBSTRING(`custPhone`, -9) = SUBSTRING('$phone', -9)";
                $pointsBalanceResult = $conn->query($sqlPoints);
                $pointsBalance = $pointsBalanceResult->fetch_assoc()['pointsBal'];
                
                //set correct phone format
                $recipient = '+254' . substr($phone, -9);
                
                //Construct SMS to customer
                $message = 'Dear ' . $name . ', thank you for visiting EXCEL TECH ESSENTIALS. Payment received: Kshs.' . $totalAmount . '. Loyalty Points now at ' . $pointsBalance . '. See you again. www.essentialtech.site';
                
                $_SESSION['redirect_url'] = 'pay'; //Save the session to return back after processing
                
                //send SMS
                if($notify > 0){
                    sendSMS($recipient, $message);
                } else {
                    header('Location: pay');
                }

                exit();
            } else {
                echo "Error inserting payment or updating points: " . $conn->error;
            }
        $conn->close();
    }
    
?>
