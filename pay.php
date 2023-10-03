<?php
    use Dotenv\Dotenv;
    
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/pay-process.php';
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    //session_start();
    
    if (!isset($_SESSION['username'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: login'); // Redirect to the login page
        exit;
    } 
    
    if($_SESSION['admin'] === true) {
        $admin = 1;
    } else {
        $admin = 0;
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
    
    if (isset($_POST['submitPay'])) {
        $name = $_POST['cust-name'];
        $phoneNumber = $_POST['tel-number'];
        $services = $_POST['services'];
        $amount = $_POST['amount'];
        $staffData = explode('-', $_POST['staff']);
        $staff_name = trim($staffData[0]);
        $staff_phone = trim($staffData[1]);
        
        $services2 = $_POST['services2'];
        $amount2 = $_POST['amount2'];
        $staffData2 = explode('-', $_POST['staff2']);
        $staff_name2 = trim($staffData2[0]);
        $staff_phone2 = trim($staffData2[1]);
        
        $paymentMode = $_POST['payment-mode'];
        if(isset($_POST['notify']) && $_POST['notify'] === 'yes'){
            $notify = 1;
        } else {
            $notify = 0;
        }
        date_default_timezone_set('Etc/GMT-3');
        $date = date('Y-m-d H:i:s');
        
        if(empty($amount2) || $amount2 < 1){
            processPayment($name, $phoneNumber, $services, $amount, $staff_name, $staff_phone, $paymentMode, $date, $notify);
        } else {
            processPayment2($name, $phoneNumber, $services, $amount, $staff_name, $staff_phone, $services2, $amount2, $staff_name2, $staff_phone2, $paymentMode, $date, $notify);
        }
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
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
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
            
            .edit-field {
                border:none;
                border-radius: 50px;
            }
            
            .editing-row {
                background-color: lightblue; 
            }
            
            .saved-row {
                background-color: #f2f2f2; 
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
            
            <!--Withdraw button 
            <button <?php if ($admin === 2) { echo 'hidden'; } ?> id = "withdraw" type="button" name="withdraw" onclick="redirectToWithdraw()" style="background-color: rgba(61,181,84,255); color:white; font-weight:bold;">Withdraw Cash</button> -->
        
            <p><h2>Record payments</h2></p>
            
            <div class="formPay">
                <form id="submitPay" style="width: 80%;" method="POST" action="">
                    <div id="customerDetails" style="display:; border: solid lightgrey; padding:5px;">
                        Customer details:
                        <input placeholder="Customer name" type="text" name="cust-name" style="width: 95%; height: 30px;" required>
                        <br> 
                        <br>
                        <input placeholder="Customer Phone Number" type="number" id="tel-number" name="tel-number" style="width: 95%; height: 30px;" required>
                    </div>
                    <br>
                    <div id="servedBy1" class="servedBy1" style="display:; border: solid lightgrey; padding:5px;">
                        Service details:
                        <textarea name="services" id="services" class="services" placeholder = "Which services have been given?" style="width: 95%;" required></textarea>
                        <br>

                        <input placeholder="Total Amount" type="number" id="amount" name="amount" style="width: 95%; height: 30px;" required>
                        <br>

                        Select Staff:
                        
                        <?php
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
                        
            			<button id="addService" style="border:2px solid grey;  width:40px; height:20px; background-color:lightblue;" name="addService">Add</button>
                    </div>
                    <br>
                    <div hidden="hidden" id="servedBy2" class="servedBy2" style="display:; border: solid lightgrey; padding:5px;">
                        Other Service details:
                        <textarea name="services2" id="services2" class="services2" placeholder = "Which services have been given?" style="width: 95%;" ></textarea>
                        <br>

                        <input placeholder="Total Amount" type="number" id="amount2" class="amount2" name="amount2" style="width: 95%; height: 30px;" >
                        <br>

                        Select Staff:
                        
                        <?php
                            // SQL query to fetch staff from the staff table
                            $sqlStaffList = "SELECT staff_name, staff_phone FROM staff WHERE status='active'";
                            
                            // Execute the query
                            $result = $conn->query($sqlStaffList);
                            
                            // Check if there are any rows returned
                            if ($result->num_rows > 0) {
                                // Start building the dropdown list
                                echo '<select name="staff2" id="staff2" style="height:30px;">';
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
                    </div>
                    <br>
                    Select Payment Mode:
                    <select id="payment-mode" name="payment-mode" style="width: 120px; height: 30px;" required>
                        <option id="kcb" name="kcb" >KCB Paybill</option> 
                        <option id="mpesa" name="mpesa" >Mpesa Online</option>
                    </select> 
                    <br>
                    <br>
                    <input type="checkbox" id="notify" name="notify" checked value="yes"> Notify customer via SMS?
                    <br><br>
                    <input id="submitPay" type="submit" value="Record Payment" name="submitPay" />
                </form>
            </div>
            <br>

            <h2> Recorded payments</h2>
            
            <!-- Add a button to export the table to Excel -->
            <button <?php if ($admin === 0) { echo 'hidden'; } ?> onclick="exportTableToExcel('payments-table', 'payments')" style="border:2px solid grey; width:130px; height:30px;">Export to Excel</button>
            
            <!-- Add a search bar -->
            <input type="text" id="payments-search" placeholder="Search by name or phone number" style="width:30%; height:25px; border: 2px solid #000;" >
            
            <br>
            <table id="payments-table">
                <thead>
                    <tr>
                        <?php if($admin === 1){ ?>
                            <th>Action</th>
                        <?php } ?>
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
                    
                    $weekNumber = date("W");
                    $limit = (isset($_SESSION['admin']) && $_SESSION['admin'] == true) ? "" : "WHERE WEEK(date) = $weekNumber AND WEEK(date) = $weekNumber-1";
            
                    $sqlPaid = "SELECT s_no, name, phone, services, amount, staff_name, staff_phone, date FROM payments $limit ORDER BY date DESC";
            
                    $result = $conn->query($sqlPaid);
            
                    // Loop through the table data and generate HTML code for each row
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='saved-row'>";
                            if($admin === 1){ 
                                echo "<td><button class='edit-btn'  >Edit</button> <button class='save-btn' style='display:none;'>Save</button></td>";
                            }
                            echo "<td>" . $row["s_no"] . "</td>";
                            echo "<td><input type='text' class='edit-field' disabled value='" . $row["name"] . "'></td>";
                            echo "<td><input type='text' class='edit-field' disabled value='" . $row["phone"] . "'></td>";
                            echo "<td><input type='text' class='edit-field' disabled value='" . $row["services"] . "'></td>";
                            echo "<td><input type='text' class='edit-field' disabled  value='" . $row["amount"] . "'></td>";
                            echo "<td><input type='text' class='edit-field' disabled  value='" . $row["staff_name"] . "'></td>";
                            echo "<td><input type='text' class='edit-field' disabled  value='" . $row["staff_phone"] . "'></td>";
                            echo "<td><input type='text' class='edit-field' disabled  value='" . $row["date"] . "'></td>";
                            echo "<td>
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
            
                    //$conn->close();
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
                
                //Button for adding extra service by different staff
    			const btnAddService = document.getElementById('addService');
    
    			btnAddService.addEventListener('click', function() {
    				// Show booking mode
    				document.querySelector('.servedBy2').removeAttribute('hidden');
    				document.querySelector('.services2').setAttribute('required', 'required');
    				document.querySelector('.amount2').setAttribute('required', 'required');
    
    			});
            </script>
            
            <!--Script to edit values on the table -->
            <script>
                document.querySelectorAll('.edit-btn').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const row = button.closest('tr');
                        const editFields = row.querySelectorAll('.edit-field');
                        editFields.forEach(function(field) {
                            field.removeAttribute('disabled');
                        });
                        row.classList.remove('saved-row');
                        row.classList.add('editing-row');
                        button.style.display = 'none';
                        row.querySelector('.save-btn').style.display = 'inline';
                    });
                });
        
                document.querySelectorAll('.save-btn').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const row = button.closest('tr');
                        const editFields = row.querySelectorAll('.edit-field');
                        const newData = {
                            id: row.querySelector('td:nth-child(2)').innerText,
                            name: editFields[0].value,
                            phone: editFields[1].value,
                            services: editFields[2].value,
                            amount: editFields[3].value,
                            staff_name: editFields[4].value,
                            staff_phone: editFields[5].value,
                            date: editFields[6].value,
                            table:"payments"
                        };
        
                        // Send an AJAX request to a PHP script to update the data
                        fetch('templates/editTables.php', {
                            method: 'POST',
                            body: JSON.stringify(newData),
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the UI or provide feedback to the user
                                editFields.forEach(function(field) {
                                    field.setAttribute('disabled', 'disabled');
                                });
                                row.classList.remove('editing-row');
                                row.classList.add('saved-row');
                                button.style.display = 'none';
                                row.querySelector('.edit-btn').style.display = 'inline';
                            } else {
                                // Handle errors or display an error message to the user
                            }
                        });
                    });
                });
            </script>
            
            <?php include 'templates/sessionTimeoutL.php'; ?>

        </div>  
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>