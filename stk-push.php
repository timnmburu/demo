<?php
    require_once 'vendor/autoload.php';
    require_once __DIR__ . '/../demo/templates/standardize_phone.php';
    
    use IntaSend\IntaSendPHP\Collection;
    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
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
        // Database credentials
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $status = "SELECT * FROM mpesa_collections WHERE invoice_id='$invoice_id'";
        $resultStatus = $conn->query($status);
        $resultStatus = $resultStatus->fetch_assoc();
        $responseS = $resultStatus['state'];
        $responseR = $resultStatus['failed_reason'];
        $response = [
                'state'=>$responseS,
                'failed_reason' =>$responseR,
            ];
            
        return $response;
    }
    
    function performPaymentRequest($amount, $formatted_phone_number, $api_ref) {
        $collection = initCollection();
        $response = $collection->mpesa_stk_push($amount, $formatted_phone_number, $api_ref);
        return $response;
    }
    
    if (isset($_POST['getInvoiceStatus'])) {
        $invoice_id = $_POST['invoice_idT']; // Retrieve the invoice ID from the form input
        
        // Get the payment status
        $response = getInvoiceStatus($invoice_id);
        
        // Send the JSON-encoded response back to the client
        echo json_encode($response);
        exit;
    }
    
    if (isset($_POST['stkPushed'])) {
        // Retrieve the form data
        $amount = $_POST['amount'];
        $phone_number = $_POST['phone_number'];
        
        // Extract the last 9 digits from the phone number
        $standardizedInput = standardizePhoneNumber($phone_number);
        
        // Add the prefix "254" to the phone number
        $formatted_phone_number = '254' . $standardizedInput;
        
        $api_ref = "Excel Tech"; // You can generate a unique reference for each transaction
        
        // Perform the payment request
        $response = performPaymentRequest($amount, $formatted_phone_number, $api_ref);
        
        // Get the invoice ID from the response
        $invoice = $response->invoice;
        $invoice_id = $invoice->invoice_id;
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>M-Pesa Payment</title>
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                //Load processing gif
                $('#status').html('<img src="/demo/fileStore/processing.gif" alt="Processing..." style="display: flex; zoom: 70% ;">');
                //process payment status check
              $('#form1').submit(function(event) {
                event.preventDefault(); // Prevent form submission
                
                // Make an AJAX request to the PHP script
                $.ajax({
                  url: '',
                  type: 'POST',
                  data: { getInvoiceStatus: true, invoice_idT: $('#invoice_id').val() },
                  dataType: 'json',
                  success: function(response) {
                    // Update the status on the page
                    
                    if (response.state === "COMPLETE") {
                      $('#status').text(response.state);
                      // Print link or perform any other action upon completion
                      $('#back1').show();
                        
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else if (response.state === "FAILED") {
                      $('#status').text(response.state + ': ' + response.failed_reason);
            
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else if (response.state === "RETRY") {
                      $('#status').text(response.state + ': ' + response.failed_reason);
            
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else {
                      // Display the loading GIF
                      //$('#status').html('<img src="fileStore/processing.gif" alt="Processing..." style="display: flex; zoom: 70% ;">');
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
        /*
            html, body {
                height: 800px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                zoom: 150% ;
            }
            
            #payment-form {
                width: 400px;
                border: 2px solid #ddd;
                padding:4px;
            }
            #logo-container {
                zoom:100%;
            }
            
            */
            #status, #back, #pushedData {
                display: flex;
                align-items: center;
                justify-content: center;
                zoom: 70% ;
            }
            
            /*
        
            @media only screen and (min-width: 1000px) {
                html, body {
                    height: 600px;
                    zoom:100%;
                    display: flex;
                    flex-direction: row;
                }
            }
            
            */
        </style>
    </head>
    <body class="body">
        
        <?php echo include "templates/header-admins.php" ?>;
        <!--
            <div class="logo-container" id="logo-container">
                <a href="/demo" target="">
                    <img src="/demo/logos/Logo.jpg" alt="Logo"/>
                </a>
            </div>
        -->
        <br>
        <div id="payment-form" style="display: flex; flex-direction: column; align-items: center; justify-content: center; zoom: 150% ;">
            
            <h1>M-Pesa Payment</h1>
            
            <form id="form" method="POST" action="">
                <label for="phone_number">Phone Number:</label>
                <input type="number" id="phone_number" name="phone_number" placeholder="07... OR 01..." style="border: 2px solid #ddd; height:30px;" required><br><br>
            
                <label for="amount">Amount (Kes.):</label>
                <input type="number" id="amount" name="amount" style="border: 2px solid #ddd; height:30px;" required><br><br>
            
                <input type="submit" id="stkPushed" name="stkPushed" value="REQUEST PAYMENT" style="background-color: rgba(61,181,84,255); color:white; font-weight:bold; height:35px; margin-left: 120px;">
            </form>
            <br>
            
            <?php
            if (isset($_POST['stkPushed'])) {
                if ($invoice_id === null) {
                    echo "";
                } else {
                    //echo "Payment for Invoice ID " . $invoice_id . " is Successfully Initiated";
                    echo "<div id=pushedData >";
                    echo "Payment of Kshs." . $amount . " to Phone " . $phone_number . " is Successfully Initiated. Invoice ID " . $invoice_id ;
                    echo "</div>";
            ?>
                    
                    <form id="form1" action="" method="POST">
                        <input type="hidden" id="invoice_id" name="invoice_id" value="<?php  if(isset($invoice_id)){ echo $invoice_id ; } ?>">
                        <br>
                        <input type="submit" id="getInvoiceStatus" value="Get Payment Status" hidden>
                    </form>
                    
                    <div id="status"></div>
                    <br>
                    <div id="back1" style="display: none;">
                        <div  id="back" > <a href="/demo/pay?phone_number=<?php echo urlencode($phone_number); ?>&amount=<?php echo urlencode($amount); ?>&mode=<?php echo urlencode('Mpesa Online'); ?>">Record Payment Now?</a></div>
                    </div>
                    
                    <?php    
                }
            }
            ?>
            
        </div>    
    </body>
</html>
