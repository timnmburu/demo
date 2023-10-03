<?php    
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/pay-process.php';


    use Dotenv\Dotenv;
    
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
    
    //Enter staff details in database
    if(isset($_POST['submitExtraPayment'])){
        $selectedBooking = $_POST['booking'];
        $selectedBookingParts = explode(" - ", $selectedBooking);
        $selectedCustomerName = $selectedBookingParts[0];
        $selectedCustomerPhone = $selectedBookingParts[1];
        $bookingID = $selectedBookingParts[2];
        
        $extraAmount = $_POST['extra-paid'];
        $date = $_POST['date'];
        
        $sqlBalanceDue = "SELECT * FROM bookings WHERE bookingID='$bookingID'";
        $resultBalanceDue = mysqli_query($conn, $sqlBalanceDue);
        
        if ($resultBalanceDue && mysqli_num_rows($resultBalanceDue) > 0) {
            $row = mysqli_fetch_assoc($resultBalanceDue);
            $balanceDue = $row['balanceDue'];
            $totalPaid = $row['totalPaid'];
            $services = $row['services'];
            $depositPaid = $row['depositPaid'];
        } else {
            echo "Booking ID not set";
        }
        
        $newBalance = $balanceDue - $extraAmount;
        
        $newTotalPaid = $totalPaid + $extraAmount;
        
        if ($depositPaid < 1){
            $sqlUpdateBookings = "UPDATE bookings SET lastPaymentDate='$date', totalPaid='$newTotalPaid', depositPaid='$extraAmount', depositCode='Other', balanceDue='$newBalance', status='Partial Payment' WHERE bookingID='$bookingID'";
        }elseif($newBalance < 1 && $depositPaid < 1 ){
            $sqlUpdateBookings = "UPDATE bookings SET lastPaymentDate='$date', totalPaid='$newTotalPaid', depositPaid='$extraAmount', depositCode='Other', balanceDue='$newBalance', status='Payment Completed' WHERE bookingID='$bookingID'"; 
        }elseif($newBalance < 1 && $depositPaid > 1 ){
            $sqlUpdateBookings = "UPDATE bookings SET lastPaymentDate='$date', totalPaid='$newTotalPaid', balanceDue='$newBalance', status='Payment Completed' WHERE bookingID='$bookingID'";
        }else {
            $sqlUpdateBookings = "UPDATE bookings SET lastPaymentDate='$date', totalPaid='$newTotalPaid', balanceDue='$newBalance', status='Partial Payment' WHERE bookingID='$bookingID'";
        }
        
        if ($conn->query($sqlUpdateBookings)) {

            $name = $selectedCustomerName;
            $phoneNumber = $selectedCustomerPhone;
            $services = 'Booking for ' . $services;
            $amount = $extraAmount;
            //$staffData = explode('-', $_POST['staff']);
            $staff_name = 'Booking';
            $staff_phone = '0797099***';
            $paymentMode = 'Mpesa Online';
            date_default_timezone_set('Etc/GMT-3');
            $date = date('Y-m-d H:i:s');
            $notify = 1;
            
            processPayment($name, $phoneNumber, $services, $amount, $staff_name, $staff_phone, $paymentMode, $date, $notify);
            
            echo "Successfully added extra payment of Kshs. " . "<b>" . $extraAmount . "</b>" . ". New balance is Kshs. " .  "<b>" . $newBalance . "</b>";
            exit();
        } else {
            echo "Error: " . $sqlUpdateBookings . "<br>" . $conn->error;
        }
    }
    

?>

<!DOCTYPE html>    
<html>
    <h1> Add Extra Booking Payment</h1>
    
            <div id="bookings-extra-payment">
                <form id="extraPayment" method="POST" action="" style="border: solid lightgrey; width:80%; padding:4px;">
                    Select Booking:  
                    <?php
                        // SQL query to fetch booking from the bookings table
                        $sqlBookingList = "SELECT bookingID, name, phone FROM bookings WHERE confirmation = 'Confirmed'";
                        $result = $conn->query($sqlBookingList);
                        if ($result->num_rows > 0) {
                            // Start building the dropdown list
                            echo '<select name="booking" id="booking" style="height:30px;">';
                            echo '<option value="">Select Booking</option>';
                            // Loop through the rows and add options to the dropdown list
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['name'] . ' - ' . $row['phone'] .  ' - ' . $row['bookingID'] .  '">' . $row['name'] . ' - ' . $row['phone'] . ' - ' . $row['bookingID'] .'</option>';
                            }
                            // Close the dropdown list
                            echo '</select>';
                        } else {
                            echo 'No confirmed booking found.';
                        }
                        
                    ?>

                    <br>
                    <br> Extra Amount Paid: <br>
                    <input type="number" id="extra-paid" name="extra-paid" style="height:30px; width:80%;" placeholder="Extra Payment Made.."/>
                    <br>
                    <br>Date Paid: <br>
                    <input type="date" id="date" name="date" style="height:30px; width:80%;" />
                    <br>
                    <br>
                    <input type="submit" id="submitExtraPayment" name="submitExtraPayment" style="height:30px; width:200px;" value="Add Payment"/>
                </form>
            </div>
    <?php include 'templates/sessionTimeoutL.php'; ?>
    
</html>


<?php
    $conn->close();
?>