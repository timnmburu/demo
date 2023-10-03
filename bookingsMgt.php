<?php
    use Dotenv\Dotenv;
    
    
    require_once 'vendor/autoload.php'; // Include the Dotenv library    
    require_once 'templates/sendsms.php';
    require_once 'templates/generateDocs.php';
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    if (!isset($_SESSION['username'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        session_unset();
        header('Location: login'); // Redirect to the login page
        exit;
    } elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false){
        header('Location: admins');
    }
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    if (isset($_POST['submitConfirmation'])) {
        // Get the selected booking from the dropdown
        if (isset($_POST['booking'])) {
            $selectedBooking = $_POST['booking'];
            // Split the selected staff value into name and phone
            $selectedBookingParts = explode(" - ", $selectedBooking);
            $selectedCustomerName = $selectedBookingParts[0];
            $selectedCustomerPhone = $selectedBookingParts[1];
            $selectedBookingCode = $selectedBookingParts[2];
        } else {
            // Default values if no staff is selected
            $selectedCustomerName = '';
            $selectedCustomerPhone = '';
            $selectedBookingCode = '';
        }
        
        $description = $_POST['describe'];
        $amountDue = $_POST['amount-due'];
        
        $sqlUpdateBooking = "UPDATE bookings SET description='$description', amountDue= '$amountDue', balanceDue='$amountDue', confirmation='Confirmed' WHERE bookingID='$selectedBookingCode'";
        
        $resultUpdate = $conn->query($sqlUpdateBooking);
        
        if($resultUpdate){
            //echo "<script> alert('Successfully Updated Booking ID: ' . $selectedBookingCode); </script>" ;
            
            //Generate booking invoice and contract
            generateInvoice($selectedBookingCode);
            generateContract($selectedBookingCode);
            
            //set correct phone format
            $recipient = '+254' . substr($selectedCustomerPhone, -9);
            
            //Construct SMS to customer
            $message = 'Dear ' . $selectedCustomerName . ', your booking is successfully processed. To confirm your booking, please go to www.essentialtech.site/book and select Booking Confirmation. Use Booking Code: ' . $selectedBookingCode . ' to confirm. Thank you again for choosing us.';
            
            $_SESSION['redirect_url'] = 'bookingsmgt'; //Save the session to return back after processing
            
            //send SMS
            sendSMS($recipient, $message);
        }
          
    }
    
                    
?>
<!DOCTYPE html>
<html en-US>
    <head>
        <title>Bookings Mgt</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css"/>
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
        
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            
            th, td {
                text-align: left;
                padding: 8px;
                border: 2px solid #ddd;
            }
            
            th {
                background-color: #f2f2f2;
            }
            
        </style>
    </head> 
    
    <body class="body">
        <?php include "templates/header-admins.php" ?>
        
        <div class="body-content">   
        
        <h1>Bookings</h1>
            
            <button onclick="openExtraPayment()" style="height:30px; padding: 5px; ">Add Extra Booking Payment</button>
            <button onclick="openNewBooking()" style="height:30px; padding: 5px; ">Manually Create Booking</button>
            <br><br>
            <div id="confirming-bookings">
                <form id="confirmingForm" method="POST" action="" style="border: solid lightgrey; width:80%; padding:4px;">
                    Select Unconfirmed Booking:  
                    <?php
                        // SQL query to fetch booking from the bookings table
                        $sqlBookingList = "SELECT bookingID, name, phone, dateBooked FROM bookings WHERE confirmation = 'Unconfirmed'";
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
                            echo 'No unconfirmed booking found.';
                        }
                        
                    ?>
                    <br>
                    <br>
                    <input type="textarea" id="describe" name="describe" style="height:50px; width:80%" placeholder="Enter full description..."/>
                    <br>
                    <br>
                    <input type="number" id="amount-due" name="amount-due" style="height:30px; width:120px" placeholder="Total amount due..."/>
                    <br>
                    <br>
                    <input type="submit" id="submitConfirmation" name="submitConfirmation" style="height:30px;" value="Confirm Booking"/>
                </form>
            </div>
     
    
            <h2>Unconfirmed Bookings</h2>    
    
            <!--Bookings not confirmed-->
            <table id="unconfirmed-bookings-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Booking ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Services</th>
                        <th>Quote</th>
                        <th>Description</th>
                        <th>Date Booked</th>
                        <th>Date Requested</th>
                        <th>Confirmation</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
            
                    $sqlUnconfirmed = "SELECT s_no, bookingID, name, phone, email, services, description, dateBooked, quote, dateRequested, amountDue, depositCode, depositPaid, balanceDue, confirmation, status, invoiceLink, contractLink FROM bookings WHERE confirmation = 'Unconfirmed' ORDER BY dateRequested DESC";
                    
                    // Check if the username is "Tim" or "Millie"
                    $username = $_SESSION['username']; // Replace with the actual username value
                    $limit = ($username === $_ENV['ADMIN1'] || $username === $_ENV['ADMIN2']) ? "" : "LIMIT 10";
            
                    $sqlUnconfirmed .= " $limit"; // Append the limit clause to the query
            
                    $result = $conn->query($sqlUnconfirmed);
            
                    // Loop through the table data and generate HTML code for each row
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row["s_no"] . "</td>
                                    <td>" . $row["bookingID"] . "</td>
                                    <td>" . $row["name"] . "</td>
                                    <td>" . $row["phone"] . "</td>
                                    <td>" . $row["email"] . "</td>
                                    <td>" . $row["services"] . "</td>
                                    <td>" . $row["quote"] . "</td>
                                    <td>" . $row["description"] . "</td>
                                    <td>" . $row["dateBooked"] . "</td>
                                    <td>" . $row["dateRequested"] . "</td>
                                    <td>" . $row["confirmation"] . "</td>
                                    <td>" . $row["status"] . "</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No results found.</td></tr>";
                    }
            
                    ?>
                </tbody>
            </table>
            
            
            <h2>Confirmed Bookings</h2>
            
            <!--Confirmed bookings-->
            <table id="confirmed-bookings-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Booking ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Services</th>
                        <th>Quote</th>
                        <th>Description</th>
                        <th>Date Booked</th>
                        <th>Date Requested</th>
                        <th>Amount Due</th>
                        <th>Deposit Code</th>
                        <th>Deposit Paid</th>
                        <th>Total Paid</th>
                        <th>Balance Due</th>
                        <th>Confirmation</th>
                        <th>Status</th>
                        <th>Invoice Link</th>
                        <th>Contract Link</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
            
                    $sqlUnconfirmed = "SELECT s_no, bookingID, name, phone, email, services, description, dateBooked, quote, dateRequested, amountDue, depositCode, depositPaid, totalPaid, balanceDue, confirmation, status, invoiceLink, contractLink FROM bookings WHERE confirmation = 'Confirmed' ORDER BY dateRequested DESC";
                    
                    // Check if the username is "Tim" or "Millie"
                    $username = $_SESSION['username']; // Replace with the actual username value
                    $limit = ($username === "Tim" || $username === "Millie") ? "" : "LIMIT 10";
            
                    $sqlUnconfirmed .= " $limit"; // Append the limit clause to the query
            
                    $result = $conn->query($sqlUnconfirmed);
            
                    // Loop through the table data and generate HTML code for each row
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row["s_no"] . "</td>
                                    <td>" . $row["bookingID"] . "</td>
                                    <td>" . $row["name"] . "</td>
                                    <td>" . $row["phone"] . "</td>
                                    <td>" . $row["email"] . "</td>
                                    <td>" . $row["services"] . "</td>
                                    <td>" . $row["quote"] . "</td>
                                    <td>" . $row["description"] . "</td>
                                    <td>" . $row["dateBooked"] . "</td>
                                    <td>" . $row["dateRequested"] . "</td>
                                    <td>" . $row["amountDue"] . "</td>
                                    <td>" . $row["depositCode"] . "</td>
                                    <td>" . $row["depositPaid"] . "</td>
                                    <td>" . $row["totalPaid"] . "</td>
                                    <td>" . $row["balanceDue"] . "</td>
                                    <td>" . $row["confirmation"] . "</td>
                                    <td>" . $row["status"] . "</td>
                                    <td>" . "<a href='" . $row["invoiceLink"] . "' download>Download</a></td>
                                    <td>" . "<a href='" . $row["contractLink"] . "' download>Download</a></td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No results found.</td></tr>";
                    }
            
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
        <br>
        <br>
        <script>
            function openExtraPayment() {
                window.open("bookingExtraPayment.php", "Redeem Points", "width=600, height=600");
            }
            
            function openNewBooking() {
                window.open("book", "New Booking");
            }
        </script>
        
        <?php include 'templates/scrollUp.php'; ?>
        <?php include 'templates/sessionTimeoutL.php'; ?>

    </body>
</html>