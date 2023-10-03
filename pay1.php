<?php
    use Dotenv\Dotenv;
    
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/pay-process.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['username'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: login'); // Redirect to the login page
        exit;
    }
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    
    // Initialize $export_allowed to false by default
    $button_allowed = false;
    
    // Check if the session username is "Tim" or "Millie"
    if (isset($_SESSION['username']) && ($_SESSION['username'] == $_ENV['ADMIN1'] || $_SESSION['username'] == $_ENV['ADMIN2'])) {
        // User is authorized to export tables
        $button_allowed = true;
    }
    
    
    if (isset($_POST['submitPay'])) {
        $name = $_POST['cust-name'];
        $phoneNumber = $_POST['tel-number'];
        $services = $_POST['services'];
        $amount = $_POST['amount'];
        $staffData = explode('-', $_POST['staff']);
        $staff_name = trim($staffData[0]);
        $staff_phone = trim($staffData[1]);
        $paymentMode = $_POST['payment-mode'];
        date_default_timezone_set('Etc/GMT-3');
        $date = date('Y-m-d H:i:s');
        
        processPayment($name, $phoneNumber, $services, $amount, $staff_name, $staff_phone, $paymentMode, $date);
    }
        

?>
<!DOCTYPE html>
<html en-US>
    <head>
        <title>Payments</title>
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
                border: 2px solid #ddd;

            }
            
            th {
                background-color: #f2f2f2;
            }
            
            #submitPay {
                display: ;
                align-items: center;
                justify-content: center;
                zoom:120%;
            }
            
            #stkPush {
                padding: 4px;
                zoom:120%;
            }
            
            #withdraw {
                padding: 4px;
                zoom:120%;
            }
            
            #submitPay {
                padding: 4px;
                zoom:120%;
            }
            .formPay {
               border:1px solid black; 
               display:flex; 
               width: 40%; 
            }
            @media only screen and (max-width: 768px) {
                .formPay {
                   border:1px solid black; 
                   display:flex; 
                   width: 100%;
                }
            }
        </style>
    </head> 
    
    <body class="body">
        <?php include "templates/header-admins.php" ?>
        
        <h1>Payments</h1>
        
        <div class="body-content">  
            <button id = "stkPush" type="button" name="stkPush" onclick="redirectToStkPush()" style="background-color: rgba(61,181,84,255); color:white; font-weight:bold;">Initiate M-Pesa Payment</button>
            
            <!--Withdraw button -->
            <button <?php if (!$button_allowed) { echo 'hidden'; } ?> id = "withdraw" type="button" name="withdraw" onclick="redirectToWithdraw()" style="background-color: rgba(61,181,84,255); color:white; font-weight:bold;">Withdraw Cash</button>
        
            <p><h2>Record Payments</h2></p>
            
            <div class="formPay">
                <form id="submitPay" style="width: 80%;" method="POST" action="">
                    <input placeholder="Customer name" type="text" name="cust-name" style="width: 95%; height: 30px;" required>
                    <br> 
                    <br>
                    <input placeholder="Customer Phone Number" type="number" id="tel-number" name="tel-number" style="width: 95%; height: 30px;" required>
                    <br>
                    <br>
                    <textarea name="services" placeholder = "Which services have been given?" style="width: 95%;" required></textarea>
                    <br>
                    <br>
                    <input placeholder="Total Amount" type="text" id="amount" name="amount" style="width: 95%; height: 30px;" required>
                    <br>
                    <br>
                    Select Staff:
                    
                    <?php
                        // Database connection
                        
                        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
                        
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        
                        // SQL query to fetch staff from the staff table
                        $sqlStaffList = "SELECT staff_name, staff_phone FROM staff WHERE status='active'";
                        
                        // Execute the query
                        $result = $conn->query($sqlStaffList);
                        
                        // Check if there are any rows returned
                        if ($result->num_rows > 0) {
                            // Start building the dropdown list
                            echo '<select name="staff" id="staff" style="height:30px;">';
                            // Loop through the rows and add options to the dropdown list
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['staff_name'] . ' - ' . $row['staff_phone'] . '">' . $row['staff_name'] . ' - ' . $row['staff_phone'] . '</option>';
                            }
                            // Close the dropdown list
                            echo '</select>';
                        } else {
                            echo 'No staff found.';
                        }
                    ?>
                    <br>
                    <br>
                    Select Payment Mode:
                    <select id="payment-mode" name="payment-mode" style="width: 120px; height: 30px;" required>
                        <option id="kcb" name="kcb" >KCB Paybill</option> 
                        <option id="mpesa" name="mpesa" >Mpesa Online</option>
                    </select> 
                    <br>
                    <br>
                    <input id="submitPay" type="submit" value="Record Payment" name="submitPay" />
                </form>
            </div>
            <br>

            <h2> Recorded Payments</h2>
            
            <!-- Add a button to export the table to Excel -->
            <button <?php if (!$button_allowed) { echo 'hidden'; } ?> onclick="exportTableToExcel('payments-table', 'payments')" style="border:2px solid grey; width:130px; height:30px;">Export to Excel</button>
            
            <!-- Add a search bar -->
            <input type="text" id="payments-search" placeholder="Search by name or phone number" style="width:30%; height:25px; border: 2px solid #000;" >
            
            <br>
            <table id="payments-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Customer Name</th>
                        <th>Phone Number</th>
                        <th>Services</th>
                        <th>Amount Paid</th>
                        <th>Staff Name</th>
                        <th>Staff Phone</th>
                        <th>Date</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Connect to the database and retrieve data from the table
                    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
            
                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
            
                    $sqlPaid = "SELECT s_no, name, phone, services, amount, staff_name, staff_phone, date FROM payments ORDER BY date DESC";
            
                    // Check if the username is "Tim" or "Millie"
                    $username = $_SESSION['username']; // Replace with the actual username value
                    $limit = ($username === "Tim" || $username === "Millie") ? "" : "LIMIT 10";
            
                    $sqlPaid .= " $limit"; // Append the limit clause to the query
            
                    $result = $conn->query($sqlPaid);
            
                    // Loop through the table data and generate HTML code for each row
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row["s_no"] . "</td>
                                    <td>" . $row["name"] . "</td>
                                    <td>" . $row["phone"] . "</td>
                                    <td>" . $row["services"] . "</td>
                                    <td>" . $row["amount"] . "</td>
                                    <td>" . $row["staff_name"] . "</td>
                                    <td>" . $row["staff_phone"] . "</td>
                                    <td>" . $row["date"] . "</td>
                                    <td>
                                        <form method='POST' action='templates/generateDocs.php' target='_blank'>
                                            <input type='hidden' name='payment_id' value='" . $row["s_no"] . "'>
                                            <input type='submit' value='Generate Receipt' style='width: 120px; height: 30px; padding: 4px;'>
                                        </form>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No results found.</td></tr>";
                    }
            
                    $conn->close();
                    ?>
                </tbody>
            </table>

            
            <!-- Add a script to export the table to Excel -->
            <script>
                function exportTableToExcel(tableId, filename = 'payments'){
                    var downloadLink;
                    var dataType = 'application/vnd.ms-excel';
                    var tableSelect = document.getElementById(tableId);
                    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
                    
                    // Specify the filename
                    filename = filename?filename+'.xls':'excel_data.xls';
                    
                    // Create download link element
                    downloadLink = document.createElement("a");
                    
                    document.body.appendChild(downloadLink);
                    
                    if(navigator.msSaveOrOpenBlob){
                        var blob = new Blob(['\ufeff', tableHTML], {
                            type: dataType
                        });
                        navigator.msSaveOrOpenBlob( blob, filename);
                    } else {
                        // Create a link to the file
                        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
                    
                        // Setting the file name
                        downloadLink.download = filename;
                    
                        //triggering the function
                        downloadLink.click();
                    }
                }

            
            //Add a script to search the table -->
                function searchTable() {
                    // Get the input field and the table
                    var input = document.getElementById("payments-search");
                    var table = document.getElementById("payments-table");
                    
                    // Get the search query and convert it to lowercase
                    var query = input.value.toLowerCase();
                    
                    // Iterate through the rows of the table
                    for (var i = 1; i < table.rows.length; i++) {
                        var row = table.rows[i];
                        var name = row.cells[1].textContent.toLowerCase();
                        var phone = row.cells[2].textContent.toLowerCase();
                        
                        // Check if the name or phone number contains the search query
                        if (name.includes(query) || phone.includes(query)) {
                            row.style.display = "";
                        } else {
                            row.style.display = "none";
                        }
                    }
                }
                
                var input = document.getElementById("payments-search");
                input.addEventListener("input", searchTable);
            
            //Redirect to STK Push from Mpesa Pay button-->

                function redirectToStkPush() {
                    // Open stk-push.php in a new tab
                    window.open('mpesa', '_blank');
                }
            
            //Redirect to Pay Commission-->
                function openPayCommission() {
                    // Open payCommission.php in a new tab
                    window.open('payCommission.php', '_blank');
                }
                
            //Redirect to Pay Commission-->
                function redirectToWithdraw() {
                    // Open payCommission.php in a new tab
                    window.location.href = 'withdraw';
                    //window.open('withdraw','_blank');
                }
                
            </script>
            
            <script>
            //Look for whther a phone number was passed from the Mpesa Payment Page
                $(document).ready(function() {
                    // Get the phone number from the URL query parameter
                    var urlParams = new URLSearchParams(window.location.search);
                    var phoneNumber = urlParams.get('phone_number');
                    var paidAmount = urlParams.get('amount');
                    var paidVia = urlParams.get('mode');
                    
                    // Check if a phone number is present
                    if (phoneNumber) {
                        // Pre-fill the Phone Input box with the retrieved phone number
                        $('#tel-number').val(phoneNumber);
                        $('#amount').val(paidAmount);
                        $('#payment-mode').val(paidVia);
                    } else {
                        // No phone number passed, load normally
                        // Add any other logic or actions needed for the normal page load
                  }
                });
            </script>
            
            <?php include 'templates/sessionTimeoutL.php'; ?>
        </div>  
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>