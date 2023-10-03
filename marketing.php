<?php
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/standardize_phone.php';
    require_once 'templates/sendsms.php';

    use Dotenv\Dotenv;
    
    if (!isset($_SESSION['username'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        session_unset();
        header('Location: login'); // Redirect to the login page
        exit;   
    }  elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false){
        header('Location: admins');
    } else {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
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
    
    // Initialize SMS MODE to false by default
    $smsMode = '1';
    
    // Check if the Edit button is clicked
    if (isset($_GET['mode'])) {
        // User is authorized to export tables
        $smsMode = $_GET['mode'];;
    } else {
        $smsMode = '1';
    }
    

    // SQL query to fetch customer from the customers table
    $sqlCustomerList = "SELECT custPhone FROM customers";
    
    // Execute the query
    $result = $conn->query($sqlCustomerList);
    
    // Initialize an empty string to store phone numbers
    $allPhoneNumbers = '';
    $countAllPhoneNos = 1;
    
    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        // Loop through the rows and concatenate formatted phone numbers to the string
        while ($row = $result->fetch_assoc()) {
            // Get the phone number from the current row
            $phoneNumber = $row['custPhone'];
            // Remove leading zero and add '254' prefix
            if (substr($phoneNumber, 0, 1) === '0') {
                $phoneNumber = '254' . substr($phoneNumber, 1);
            }
            // Append the formatted phone number to the string
            $allPhoneNumbers .= $phoneNumber . ',';
            
            $countAllPhoneNos++;
        }
        
        // Remove the trailing comma at the end of the string
        $allPhoneNumbers = rtrim($allPhoneNumbers, ',');
    } else {
        $allPhoneNumbers = 'No phone numbers found.';
    }

    //echo $allPhoneNumbers;

    
    
    //Process the SMS to send
    if (isset($_POST['submitSMS'])) {

        $messageTo = $_POST['message'];
        
        $phone = $_POST['phoneNo'];
        
        $standardizedInput = standardizePhoneNumber($phone) ;
        
        $recipient = '254'. $standardizedInput;
        
        $message = $messageTo . "\nExcel Tech Essentials. www.essentialtech.site";
        
        sendSMSBulk($recipient, $message);
        

        
    } elseif (isset($_POST['submitSMSBulk'])) {

        $messageTo = $_POST['message'];
        
        if (isset($_POST['customerBulk'])) {
            $selectedCustomer = $_POST['customerBulk'];
            $selectedCustomerParts = explode(" - ", $selectedCustomer);
            
            $phone = $selectedCustomerParts[1];
            
            $standardizedInput = standardizePhoneNumber($phone) ;
        
            $recipient = '254'. $standardizedInput;
        
            $message = $messageTo . "\nExcel Tech Essentials. www.essentialtech.site";
        
            sendSMSBulk($recipient, $message);
        }
        
    } elseif (isset($_POST['submitSMSAll'])) {
        $messageTo = $_POST['message'];
        
        $recipient = $allPhoneNumbers . ",254720099212";
        
        $message = $messageTo . "\nLourice Beauty Parlour. www.lfhcompany.site";
        
        sendSMSBulk($recipient, $message);
    }
        

?>

<!DOCTYPE html>
<html en-US>
    <head>
        <title>Marketing</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css"/>
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
        
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            
            th, td {
                text-align: left;
                padding: 8px;
                border: 1px solid #ddd;
            }
            
            th {
                background-color: #f2f2f2;
            }
            .singleMode {
               border:1px solid black; 
               display:flex; 
               width: 50%; 
            }
            
            .bulkMode {
               border:1px solid black; 
               display:flex; 
               width: 50%; 
            }
            
            .allMode {
               border:1px solid black; 
               display:flex; 
               width: 50%; 
            }

            @media only screen and (max-width: 768px) {
                .formExpenses {
                   border:1px solid black; 
                   display:flex; 
                   width: 100%;
                }
            }
        </style>
    </head> 
    
    <body class="body">
         <?php include "templates/header-admins.php" ?> 
        
        <div class="body-content">
                
            <h1>Marketing</h1>
            <div>
                You can send marketing messages to customers. Just enter the details are required below and blast the SMS.
                <br>
                <br>
                Select Phone Number Source:
                <button  style="border:2px solid grey; width:150px; height:30px; background-color:lightblue; <?php if ($smsMode === '1') {echo 'zoom:-60%;  font-weight:bold;';} else{ echo 'zoom:60%;'; } ?>" name="single" onclick="addModeQueryParam1()">Enter Phone</button>
                
                <button  style="border:2px solid grey;  width:150px; height:30px; background-color:lightblue; <?php if ($smsMode === '2') {echo 'zoom:-60%;  font-weight:bold;';} else{ echo 'zoom:60%; '; } ?> " name="bulk" onclick="addModeQueryParam2()">From Customer List</button>
                
                <button  style="border:2px solid grey;  width:150px; height:30px; background-color:lightblue; <?php if ($smsMode === '3') {echo 'zoom:-60%;  font-weight:bold;';} else{ echo 'zoom:60%; '; } ?> " name="bulk" onclick="addModeQueryParam3()">To All Customers</button>
            </div>
            <br>
            <br>
            <div style="<?php if ($smsMode !== '1') {echo 'display:none;';} ?>" class="singleMode" >
                <form id="submitSMS" style="width: 80%; padding: 4px;" method="POST" action="">
                    <input placeholder="Enter phone number 07...or 01..." type="number" name="phoneNo" style="width: 95%; height: 30px;" required>
                    <br> 
                    <br>
                    <textarea name="message" placeholder = "Type your message here..." style="width: 95%; height: 60px;" required oninput="checkCharacterCount(this)" ></textarea>
                    <br>
                    <input id="characterCount" name="" value="" style=" background-color: lavender; border: none; width: 90px; height: 15px;" disabled>
                    <br>
                    <br>
                    <input type="submit" value="Send SMS" name="submitSMS" style="width: 32%; height: 30px;align-items: center;justify-content: center;" />
                </form>
            </div>

            <div style="<?php if ($smsMode !== '2') {echo 'display:none;';} ?>" class="bulkMode" >
                <form id="submitSMS" style="width: 80%; padding: 4px;" method="POST" action="">
                    <?php
                        // SQL query to fetch customer from the customers table
                        $sqlCustomerList = "SELECT custName, custPhone FROM customers";
                        
                        // Execute the query
                        $result = $conn->query($sqlCustomerList);
                        
                        // Check if there are any rows returned
                        if ($result->num_rows > 0) {
                            // Start building the dropdown list
                            echo '<select name="customerBulk" id="customerBulk" style="height:30px; width:99%">';
                            echo '<option value="">Select customer</option>';
                            // Loop through the rows and add options to the dropdown list
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['custName'] . ' - ' . $row['custPhone'] . '">' . $row['custName'] . ' - ' . $row['custPhone'] . '</option>';
                            }
                            // Close the dropdown list
                            echo '</select>';
                        } else {
                            echo 'No customer found.';
                        }
                    ?>
                    <br> 
                    <br>
                    <textarea name="message" placeholder = "Type your message here..." style="width: 95%; height: 60px;" required oninput="checkCharacterCount(this)" ></textarea>
                    <br>
                    <input id="characterCount" name="" value="" style=" background-color: lavender; border: none; width: 90px; height: 15px;" disabled>
                    <br>
                    <br>
                    <input type="submit" value="Send SMS" name="submitSMSBulk" style="width: 32%; height: 30px;align-items: center;justify-content: center;" />
                </form>
            </div>
            
            <div style="<?php if ($smsMode !== '3') {echo 'display:none;';} ?>" class="allMode" >
                <form id="submitSMS" style="width: 80%; padding: 4px;" method="POST" action="">
                    
                    <textarea hidden name="allPhoneNos" value=""><?php echo $allPhoneNumbers. ",254720099212"; ?></textarea>
                    
                    <input id="countPhone" value="Count = <?php echo $countAllPhoneNos ?>" disabled>
                    <br>
                    <br>
                    <textarea name="message" placeholder = "Type your message here..." value=""style="width: 95%; height: 60px;" required oninput="checkCharacterCount(this)" >Dear Customer, did you know about our current discounted prices offer valid to End of July? Hurry, only 1 week left. Gel=250, Tips=500, and more!</textarea>
                    <br>
                    <input id="characterCount" name="" value="" style=" background-color: lavender; border: none; width: 90px; height: 15px;" disabled>
                    <br>
                    <br>
                    <input type="submit" value="Send SMS" name="submitSMSAll" style="width: 32%; height: 30px;align-items: center;justify-content: center;" />
                </form>
            </div>
            

            
            <script>
                function addModeQueryParam1() {
                    // Prevent form submission
                    event.preventDefault();
            
                    // Show singleMode and hide bulkMode
                    document.querySelector('.singleMode').removeAttribute('hidden');
                    document.querySelector('.bulkMode').setAttribute('hidden', 'hidden');
                    document.querySelector('.allMode').setAttribute('hidden', 'hidden');
            
                    // Get the current URL
                    var url = 'marketing';
            
                    // Check if the query string already exists
                    if (url.indexOf('?') === -1) {
                        // Add the query string with 'mode=single'
                        url += '?mode=1';
                    } else {
                        // Add the query string with '&mode=single'
                        //url += '';
                    }
            
                    // Reload the page with the new URL
                    window.location.href = url;
                }
            
                function addModeQueryParam2() {
                    // Prevent form submission
                    event.preventDefault();
            
                    // Show bulkMode and hide singleMode
                    document.querySelector('.bulkMode').removeAttribute('hidden');
                    document.querySelector('.singleMode').setAttribute('hidden', 'hidden');
                    document.querySelector('.allMode').setAttribute('hidden', 'hidden');
            
                    // Get the current URL
                    var url = 'marketing';
            
                    // Check if the query string already exists
                    if (url.indexOf('?') === -1) {
                        // Add the query string with 'mode=bulk'
                        url += '?mode=2';
                    } else {
                        // Add the query string with '&mode=bulk'
                        //url += '&mode=2';
                    }
            
                    // Reload the page with the new URL
                    window.location.href = url;
                }  
                
                function addModeQueryParam3() {
                    // Prevent form submission
                    event.preventDefault();
            
                    // Show bulkMode and hide singleMode
                    document.querySelector('.allMode').removeAttribute('hidden');
                    document.querySelector('.singleMode').setAttribute('hidden', 'hidden');
                    document.querySelector('.bulkMode').setAttribute('hidden', 'hidden');
            
                    // Get the current URL
                    var url = 'marketing';
            
                    // Check if the query string already exists
                    if (url.indexOf('?') === -1) {
                        // Add the query string with 'mode=bulk'
                        url += '?mode=3';
                    } else {
                        // Add the query string with '&mode=bulk'
                        //url += '&mode=2';
                    }
            
                    // Reload the page with the new URL
                    window.location.href = url;
                } 
                
                //Character count in the text area
                function checkCharacterCount(textarea) {
                    const maxLength = 113;
                    let message = textarea.value;
            
                    //if (message.length > maxLength) {
                       // message = message.slice(0, maxLength); // Truncate the message
                        ///textarea.value = message; // Update the textarea with truncated message
                    //}
            
                    const remainingChars = maxLength - message.length;
                    document.getElementById("characterCount").value = remainingChars + ' chars left';
                }
                
            </script>

        </div>
        
        <?php include 'templates/sessionTimeoutL.php'; ?>
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>