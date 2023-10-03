<?php
	
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/standardize_phone.php';
    require_once 'templates/emailing.php';
    require_once 'templates/sendsms.php';
    require_once 'templates/generateDocs.php';
	
    use Dotenv\Dotenv;
	use IntaSend\IntaSendPHP\Collection;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Database credentials
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    // Create connection
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	function initCollection() {
        $credentials = [
            'token' => $_ENV['INTASEND_TOKEN'],
            'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
        ];
        
        $collection = new Collection();
        $collection->init($credentials);
        
        return $collection;
    }
	
	function getInvoiceStatus($invoice_id) {
        $collection = initCollection();
        $responseS = $collection->status($invoice_id)->invoice->state;
        $responseR = $collection->status($invoice_id)->invoice->failed_reason;
        $responseA = $collection->status($invoice_id)->invoice->value;
        
        // Create an associative array with the response data
        $response = [
            'state' => $responseS,
            'failed_reason' => $responseR,
            'value' => $responseA,
        ];
        
        return $response;
    }
	
    function performPaymentRequest($amount, $formatted_phone_number, $api_ref) {
        $collection = initCollection();
        $response = $collection->mpesa_stk_push($amount, $formatted_phone_number, $api_ref);
        return $response;
    }
    
    function getPaymentStatus($invoice_id) {
        $collection = initCollection();
        $status_response = $collection->status($invoice_id);
        return $status_response;
    }
    
    
    if (isset($_POST['getInvoiceStatus'])) {
        $invoice_id = $_POST['invoice_id']; // Retrieve the invoice ID from the form input
        
        // Get the payment status
        $response = getInvoiceStatus($invoice_id);
        
        // Send the JSON-encoded response back to the client
        echo json_encode($response);
        
        if($response && $response['state'] === 'COMPLETE'){
            $bookingID1 = $_GET['bookingid'];
            $amount = $response['value'];
            
            $sqlGetTotalPaid = "SELECT * FROM bookings";
            
            $row = mysqli_fetch_assoc($conn->query($sqlGetTotalPaid));
            
            $totalPaidRow = $row['totalPaid'];
            $depositPaidRow = $row['depositPaid'];
            $totalDue = $row['amountDue'];
            $balanceDue = $row['balanceDue'];
            
            if($totalPaidRow == 0){
                $totalPaid = 0;
            } else {
                $totalPaid = $totalPaidRow;
            }
            
            $newTotalPaid = $totalPaid + $amount;
            $newBalanceDue = $balanceDue - $amount;
            
            $date = date('Y-m-d H:i:s', strtotime('+3 hours'));
            
            if($depositPaidRow == 0){
                $sqlUpdateBooking = "UPDATE bookings SET depositCode='$invoice_id', depositPaid='$amount', balanceDue='$newBalanceDue', totalPaid='$amount', lastPaymentDate='$date', status='Deposit Paid' WHERE bookingID='$bookingID1' ";
            } elseif ($depositPaidRow > 0 && $newBalanceDue > 0) {
                $sqlUpdateBooking = "UPDATE bookings SET balanceDue='$newBalanceDue', totalPaid='$newTotalPaid', lastPaymentDate='$date', status='Partial Payment' WHERE bookingID='$bookingID1' ";
            } else {
                $sqlUpdateBooking = "UPDATE bookings SET balanceDue='$newBalanceDue', totalPaid='$newTotalPaid', lastPaymentDate='$date', status='Payment Complete' WHERE bookingID='$bookingID1' ";
            }
            
            $conn->query($sqlUpdateBooking);
            
            $selectedBookingCode = $bookingID1;
            generateInvoice($selectedBookingCode);
            generateContract($selectedBookingCode);
        }
        exit;
    }
    
    // Check if the Booking button is clicked
    if (!isset($_GET['bookingid'])) {
        // User is authorized to export tables
        $bookingID = '1';
    } elseif (isset($_GET['bookingid'])){
        $bookingID = $_GET['bookingid'];
    }
    
	$amountDue= "";
	$depositDue= "";

    if (isset($_POST['confirmBooking'])) {
        // User is authorized to export tables
        $bookingCode = $_POST['bookingID'];

        $sqlBookingId = "SELECT * FROM bookings WHERE bookingID='$bookingCode'";

        $result1 = $conn->query($sqlBookingId);
        $row = mysqli_fetch_assoc($result1);

        
        // Check if the booking is found
        if ($result1->num_rows !== 1) {
            echo "<script>
                    alert('Booking Code not found. Please contact us for assistance.');
                    window.location.href = 'book';
                </script>";
            exit; // Prevent further execution of the code
        } elseif($result1->num_rows === 1) {
        
            $dateBooked = new DateTime($row['dateBooked']);
            $today = new DateTime();
            $interval = $today->diff($dateBooked);
            $daysRemaining = $interval->days;
            
            if($daysRemaining <= 1){
                echo "<script>
                        alert('Booking Code expired. Please contact us for assistance.');
                        window.location.href = 'book';
                    </script>";
                exit; // Prevent further execution of the code
             
            } else {
                $bookingCodeConfirmed = $bookingCode;
                //$amountDue= $row["balanceDue"];
            }
        }
        
        //while ($row = $result1->fetch_assoc()) {
                        //$amountDue= $row["amountDue"];
                       // $dateBooked = $row['dateBooked'];
                  //  }
            $selectedBookingCode = $bookingCodeConfirmed;
            generateInvoice($selectedBookingCode);
            generateContract($selectedBookingCode);

        // Redirect to the same page with the query parameter
        header("Location: book?bookingid=" . urlencode($bookingCodeConfirmed) );

        exit; // Prevent further execution of the code
    }

	if (isset($_POST['stkPushed'])) {
        // Retrieve the form data
        $amount = $_POST['amount'];
        $phone_number = $_POST['phone_number'];
        
        // Extract the last 9 digits from the phone number
        $standardizedInput = standardizePhoneNumber($phone_number);
        
        // Add the prefix "254" to the phone number
        $formatted_phone_number = '254' . $standardizedInput;
        
        $api_ref = "Lourice Booking"; // You can generate a unique reference for each transaction
        
        // Perform the payment request
        $response = performPaymentRequest($amount, $formatted_phone_number, $api_ref);
        
        // Get the invoice ID from the response
        $invoice = $response->invoice;
        $invoice_id = $invoice->invoice_id;
        
        // Retrieve the status of the payment transaction
        $status_response = getPaymentStatus($invoice_id);
    }


	
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Google tag (gtag.js) 
        <script async src="https://www.googletloagmanager.com/gtag/js?id=AW-960693846"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            
            gtag('config', 'AW-960693846');
        </script>
        -->
        <title>Bookings</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css">
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		
		<script>
            $(document).ready(function() {
                //Load processing gif
                $('#status').html('<img src="fileStore/processing.gif" alt="Processing..." style="display: flex; zoom: 70% ;">');
                //process payment status check
              $('#form1').submit(function(event) {
                event.preventDefault(); // Prevent form submission
                
                // Make an AJAX request to the PHP script
                $.ajax({
                  url: '',
                  type: 'POST',
                  data: { getInvoiceStatus: true, invoice_id: $('#invoice_id').val() },
                  dataType: 'json',
                  success: function(response) {
                    // Update the status on the page
                    
                    if (response.state === "COMPLETE") {
                      $('#status').text(response.state);
                      // Print link or perform any other action upon completion
                      alert('Payment received successfully. Thank you.');
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else if (response.state === "FAILED") {
                      $('#status').text(response.state + ': ' + response.failed_reason);
            
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else {
                        
                    }
                  },
                  error: function() {
                    alert('An error occurred while retrieving the invoice status.');
                    $('#status').text('Error while processing');
                    clearInterval(statusInterval);
                  }
                });
              });
            
              // Check the status every 5 seconds
              var statusInterval = setInterval(function() {
                $('#form1').submit();
              }, 5000);
            });

        </script>
        <style>
            /* CSS for the tab navigation */
            .tab-nav {
              list-style: none;
              padding: 0;
              margin: 0;
              display: flex;
            }
            
            .tab-nav li {
              cursor: pointer;
              padding: 10px 20px;
              background-color: #f1f1f1;
              border: 1px solid #ccc;
            }
            
            .tab-nav li.active {
              background-color: #ddd;
            }
            
            /* CSS for the tab content */
            .tab-content {
              display: flex;
              flex-direction: column;
            }
            
            .tab-pane {
              display: none;
              padding: 20px;
              border: 1px solid #ccc;
            }
            
            .tab-pane.active {
              display: block;
            }

        </style>
        <?php //include 'templates/tawkTo.php' ?>
    </head>
    <body class="body">
        <?php include 'templates/header-admins.php'; ?>
        
        <div id="body-content">
            <h1>Book Services</h1>

			<p>Thank you for your interest in our services. Please proceed.</p>
			
            <ul class="tab-nav">
                <li class="<?php if($bookingID === '1'){ echo 'active'; } ?>" onclick="changeTab(0)">New Booking</li>
                <li class="<?php if($bookingID !== '1'){ echo 'active'; } ?>" onclick="changeTab(1)">Booking Confirmation</li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane  <?php if($bookingID === '1'){ echo 'active'; } ?>">
        			<div id="new-booking" class="new-booking" <?php if($bookingID !== '1'){ echo 'active'; } ?>>
        
        				<form id="bookingSubmit" style="width: 80%; padding: 4px; border:solid lightgrey" method="POST" action="form-process.php">
        					To book, kindly fill in the form below:
        					<br>
        					<br>
        					<input placeholder="Your Name" type="text" name="name" style="width: 95%; height: 30px;" required>
        					<br> 
        					<br>
        					<input placeholder="Your Phone No.." type="number" name="phone" style="width: 95%; height: 30px;" required oninput="checkCharacterCount(this)">
        					<br> 
        					<br>
        					<input placeholder="Your Email.." type="email" name="email" style="width: 95%; height: 30px;" required>
        					<br>
        					<br>
        					<input placeholder="Services required" type="text" name="services" style="width: 95%; height: 30px;" required>
        					<br>
        					<br>
        					<input  placeholder="Quotation number (leave empty if you do not have)" type="number" name="quote" id="quote" style="width: 95%; height: 30px;" >
        					<br><br>
        					<input placeholder="Due date" type="date" name="date" id="date" style="width: 95%; height: 30px;" required>
                            <br><br>
        					<input type="checkbox" name="TnCs" required> I confirm that Excel Tech Essentials may process and store my personal data provided in this form to reach out to me from time to time. *
        					<br>
        					<br>
        					<input type="submit" value="Submit Booking" name="submitBooking" style="width: 180px; height: 30px; display: flex; align-items: center; justify-content: center; zoom:120%;" />
        				</form>
        			</div>
                </div>
                
                <div class="tab-pane <?php if($bookingID !== '1'){ echo 'active'; } ?> " >
        			<div id="check-booking" class="check-booking" <?php if($bookingID !== '1'){ echo 'hidden'; } ?> >
        
        				<form id="bookingCheck" method="POST" action="" style="width: 80%; padding: 4px; border:solid lightgrey" >
        					Are you here to confirm your booking?
        					<br>
        					Please enter your booking confirmation code that you received below:
        					<br>
        					<br>
        					<input <?php if($bookingID !== '1'){ echo "disabled" ; } ?>  type="number" name="bookingID" placeholder="<?php if($bookingID === '1'){ echo "Enter booking code.." ; } else {  echo $bookingID; } ?>" style="width: 95%; height: 30px;" required />
        					<br>
        					<br>
        					<input <?php if($bookingID !== '1'){ echo "disabled" ; } ?> type="submit" value="Confirm Booking" name="confirmBooking" style="width: 180px; height: 30px;align-items: center;justify-content: center;zoom:120%;"  />
        				</form>
        			</div>
        			
			<div id="pay-booking" class="pay-booking" <?php if($bookingID === '1'){ echo 'hidden="hidden"'; } ?> style="width: 80%; padding: 4px; border:solid lightgrey">
				<form id="form" method="POST" action="" style="width: 99%; padding: 1px; border:solid lightgrey" >
					<?php
					    if(isset($_GET['bookingid']) && $_GET['bookingid'] > 0){
                            //$balanceDue = $_GET['amountdue'];
                            $bookingCode = $_GET['bookingid'];
                            
                            $sqlGetBookedDate = "SELECT * FROM bookings WHERE bookingID='$bookingCode' ";
                            $resultDate = $conn->query($sqlGetBookedDate);
                            
                            $row = mysqli_fetch_assoc($resultDate);
                            
                            $dateBooked = $row['dateBooked'];
                            $depositPaid = $row['depositPaid'];
                            $totalPaid = $row['totalPaid'];
                            $totalDue = $row['amountDue'];
                            $balanceDue = $row['balanceDue'];
                            $halfPaid = $totalDue * 0.5;
                            $paid80p = $totalDue * 0.8;
                            
                            //get remaining days to Booked date
                            $today = new DateTime(); 
                            $startDate = $today;
                            $endDate = new DateTime($dateBooked);
                            $interval = $startDate->diff($endDate);
                            $daysRemaining = $interval->days;
                            
                            // Get the date 7 days prior to $endDate
                            $sevenDaysPrior = clone $endDate;
                            $sevenDaysPrior->modify('-7 days');
                            
                            // Get the date 7 days prior to $endDate
                            $oneDayPrior = clone $endDate;
                            $oneDayPrior->modify('-1 days');
                            
                            // Get the date 7 days prior to $endDate
                            $twoWeeksPrior = clone $endDate;
                            $twoWeeksPrior->modify('-14 days');
                            
					    } else {
					        $totalDue = 0;
					        $daysRemaining = 0;
					        $depositPaid = 0;
					        $totalPaid = 0;
					        $balanceDue = 0;
					        $sevenDaysPrior=new DateTime("2000-01-01");
					        $oneDayPrior=new DateTime("2000-01-01");
					        $twoWeeksPrior=new DateTime("2000-01-01");
					    }
					    
						if($totalPaid == 0){
						    
    					    if($daysRemaining >= 14){
                    	        $toPay = $totalDue * 0.5 ;
    					    } elseif ($daysRemaining < 14 && $daysRemaining > 7){
    					        $toPay = $totalDue * 0.8 ;
    					    } elseif ($daysRemaining <= 7){
    					        $toPay = $totalDue;
    					    } else{
    					        $toPay = $totalDue;
    					    }
    					    
                            //get remaining days to deposit date
                            $startDate = new DateTime();
                            $endDate = $twoWeeksPrior;
                            $interval = $startDate->diff($endDate);
                            $daysRemaining1 = $interval->days;
    					    
							echo "Your Total Bill will be Kshs." . $balanceDue . ".";
							echo "<br> <br>";
							echo "You are required to pay a deposit of Kshs." . $toPay . " to confirm your booking by ". $twoWeeksPrior->format('Y-m-d') . " (" . $daysRemaining1 . " day(s) remaining)" ;
							
						} elseif ($totalPaid !== 0 && $balanceDue <= $halfPaid && $totalPaid < $paid80p) {
                                if($daysRemaining > 7){
                    	            $toPay = $totalDue * 0.3 ;
                                } else {
                                    $toPay = $balanceDue;
                                }
    					    
                            //get remaining days to next payment date
                            $startDate = new DateTime();
                            $endDate = $sevenDaysPrior;
                            $interval = $startDate->diff($endDate);
                            $daysRemaining1 = $interval->days;
    					    
							echo "Your Total Bill will be Kshs." . $balanceDue . ".";
							echo "<br> <br>";
							echo "You are required to pay Kshs." . $toPay . ' Before: ' . $sevenDaysPrior->format('Y-m-d') . " (" . $daysRemaining1 . " day(s) remaining)" ;
							
						}elseif ($totalPaid !== 0 && $totalPaid >= $paid80p){
					        $toPay = $balanceDue;
    					        
                            //get remaining days to last payment date
                            $startDate = new DateTime();
                            $endDate = $oneDayPrior;
                            $interval = $startDate->diff($endDate);
                            $daysRemaining1 = $interval->days;
    					        
							echo "Your Total Bill will be Kshs." . $toPay . ".";
							echo "<br> <br>";
							echo "You are required to pay Kshs." . $toPay. ' Before: ' . $oneDayPrior->format('Y-m-d') . " (" . $daysRemaining1 . " day(s) remaining)" ; ;
						} else {
						    echo "";
						}
					?>
					<br>
					<br>
                    <?php
                        $bookingCodeConfirmd = '';
                        if(isset($_GET['bookingid'])){
                            $bookingCodeConfirmd = $_GET['bookingid'];
                        }
                        $sqlInvoiceLink = "SELECT invoiceLink, contractLink FROM bookings WHERE bookingID='$bookingCodeConfirmd'";
                        $resultInvoiceLink = $conn->query($sqlInvoiceLink);
                            $contractLink = '';
                        if ($resultInvoiceLink->num_rows > 0) {
                            $row = $resultInvoiceLink->fetch_assoc();
                            $invoiceLink = $row["invoiceLink"] . '?request=' . time();
                            $contractLink = $row["contractLink"] . '?request=' . time();
                        } else {
                            $invoiceLink = "book";
                        }
                    ?>
					
					<p>You can access your invoice by clicking <a href="<?php echo $invoiceLink; ?>" >here</a>. Please confirm booking details before paying.</p>
					<p>You can also access your contract by clicking <a href="<?php echo $contractLink; ?>" >here</a>.</p>

					<input hidden type="number" id="amount" name="amount" value=<?php echo $toPay; ?> >
					<input placeholder="Enter your Phone No to pay now.. 07..01.." type="number" id="phone_number" name="phone_number" style="width: 95%; height: 30px;" required>
					<br>
					<br>
					Clicking below button will prompt for you to key in your Mpesa PIN to complete payment
					<br>
					<br>
					<input type="submit" id="stkPushed" name="stkPushed" value="PROCEED TO PAY" style="background-color: rgba(61,181,84,255); color:white; font-weight:bold; height:35px; display: flex; align-items: center; justify-content: center;">
				</form>
				
				<?php
				if (isset($_POST['stkPushed'])) {
					if ($invoice_id === null) {
						echo "";
					} else {
						//echo "Payment for Invoice ID " . $invoice_id . " is Successfully Initiated";
						echo "<div id=pushedData style='display: flex; align-items: center; justify-content: center;'>";
						echo "Payment of Kshs." . $amount . " to Phone " . $phone_number . " is Successfully Initiated. Invoice ID " . $invoice_id ;
						echo "</div>";
				?>
						
						<form id="form1" action="" method="POST">
							<input type="hidden" id="invoice_id" name="invoice_id" value="<?php  if(isset($invoice_id)){ echo $invoice_id ; } ?>">
							<br>
							<input type="submit" id="getInvoiceStatus" value="Get Payment Status" hidden>
						</form>
						
						<div id="status" style="display: flex; align-items: center; justify-content: center;"></div>
						<br>
		
						<?php    
					}
				}
				?>
					
			</div>
    			</div>
    		</div>
			<br>
			<br> 
			Thank you for giving us an opportunity to serve you.
			<br>

            <br>
            <script>
            // Get the input element to restrict Date to today and future
                const dateInput = document.getElementById("date");
        
                // Get the current date and set it as the minimum date for the input
                const today = new Date().toISOString().split('T')[0];
                dateInput.setAttribute("min", today);
                
    			
                //Character count in the phone input box
                function checkCharacterCount(inputElement) {
                    var prefix = '254';
                    var suffix = inputElement.value.substring(prefix.length); 
                    inputElement.value = prefix + suffix;
                    
                    const inputValue = inputElement.value.trim(); // Remove leading/trailing spaces
                    let maxLength;
                
                    if (inputValue.startsWith("+")) {
                        maxLength = 13;
                    } else if (inputValue.startsWith("2")) {
                        maxLength = 12;
                    } else if (inputValue.startsWith("0")) {
                        maxLength = 10;
                    } else {
                        // Default maximum length
                        maxLength = 13;
                    }
                
                    if (inputValue.length > maxLength) {
                        inputElement.value = inputValue.slice(0, maxLength); // Truncate the input to the maximum length
                    }
                }
    			
                //cange tabs from New booking to Booking confirmation
                function changeTab(tabIndex) {
                    const tabs = document.querySelectorAll('.tab-pane');
                    const tabNavItems = document.querySelectorAll('.tab-nav li');
                    
                    tabs.forEach((tab, index) => {
                        if (index === tabIndex) {
                            tab.classList.add('active');
                            tabNavItems[index].classList.add('active');
                        } else {
                            tab.classList.remove('active');
                            tabNavItems[index].classList.remove('active');
                        }
                    }); 
                } 
  
            </script>
        
            <?php //include 'templates/navbar.php'; ?>
        
        </div>
    </body>
</html>
