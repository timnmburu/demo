<?php
    use Dotenv\Dotenv;
    
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    session_start();
    if (!isset($_SESSION['username'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        session_unset();
        header('Location: login'); // Redirect to the login page
        exit;
    }  elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false || $_SESSION['admin'] === false){
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
    
    //include "templates/updateMpesaBalance.php";
    
    // Initialize $export_allowed to false by default
    $button_allowed = false;
    
    // Check if the session username is an admin
    if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
        // User is authorized to export tables
        $button_allowed = true;
        $admin = true;
    }
?>
<!DOCTYPE html>
<html en-US>
    <head>
        <title>Commissions</title>
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
        
            <h1>Commissions</h1>
            
            <p><h2>Commission Payment</h2></p>

            <div>
                Select Staff:  
                <?php
                    // SQL query to fetch staff from the staff table
                    $sqlStaffList = "SELECT staff_name, staff_phone FROM staff WHERE status = 'Active'";
                    
                    // Execute the query
                    $result = $conn->query($sqlStaffList);
                    
                    // Check if there are any rows returned
                    if ($result->num_rows > 0) {
                        // Start building the dropdown list
                        echo '<select name="staff" id="staff" style="height:30px;">';
                        echo '<option value="">Select Staff</option>';
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
                
                
                <?php

                ?>
                <!--Pull commissions report -->
                <button <?php if (!$button_allowed) { echo 'disabled'; } ?> style="border:2px solid grey; width:100px; height:30px;" onclick="pullCommissionReport()"><b>Pull Report</b></button>
                
                <!--Get Sum Total-->
                <?php
                    // Get the selected staff from the dropdown
                    if (isset($_GET['staff'])) {
                        $selectedStaff = $_GET['staff'];
                        // Split the selected staff value into name and phone
                        $selectedStaffParts = explode(" - ", $selectedStaff);
                        $selectedStaffName = $selectedStaffParts[0];
                        $selectedStaffPhone = $selectedStaffParts[1];
                    } else {
                        // Default values if no staff is selected
                        $selectedStaffName = "";
                        $selectedStaffPhone = "";
                    }
            
                    $sqlUnpaid = "SELECT s_no, name, phone, services, amount, staff_name, staff_phone, date, commission_paid FROM payments WHERE commission_paid = 'Not Paid' AND staff_name = '$selectedStaffName' AND staff_phone = '$selectedStaffPhone' ORDER BY date DESC";
                    
                    //Table Total Amount
                    $sumTotal = 0;
                    
                    $result = $conn->query($sqlUnpaid);
            
                    // Loop through the table data and generate HTML code for each row
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $sumTotal += $row["amount"];
                        }
                    } else {
                        echo "<tr><td colspan='9'></td></tr>";
                    }
                    
                ?>
                Sum Total: 
            
                <input disabled value="<?php echo number_format($sumTotal, 2); ?>" name="sumTotal" style="border:2px solid grey; width:100px; height:30px;" >
                
                Commission due:
                    <?php
                        // Calculate the commission due
                        $commissionRate = 0.35; // Commission rate (35%)
                        $commissionDue = $sumTotal * $commissionRate;
                    ?>
                <input disabled value="<?php echo number_format($commissionDue, 2); ?>" name="commissionDue" id="commissionDue" style="border:2px solid grey; width:100px; height:35px;">
                
                <!--Account Balance check-->
                <?php 
                    $sqlBal = "SELECT * FROM wallet";
                    
                    $result1 = $conn->query($sqlBal);
            
                    // Loop through the table data and generate HTML code for each row
                    if ($result1->num_rows > 0) {
                        while ($row = $result1->fetch_assoc()) {
                            $accBalMpesa= "100";
                            $accBalKcb= $row["kcb"];
                        }
                    }
                ?>
                
                <!--Display wallet Balances-->
                <input <?php if (!$button_allowed) { echo 'hidden'; } echo 'disabled';?> value= " Mpesa Bal = <?php echo number_format($accBalMpesa-50, 2); ?>" name="accBal" style=" border:2px solid grey; width:150px; height:35px;">
                <input <?php if (!$button_allowed) { echo 'hidden'; } echo 'disabled';?> value= " KCB Bal = <?php echo number_format($accBalKcb, 2); ?>" name="accBal" style=" border:2px solid grey; width:150px; height:35px;">
                
                <!--Pay commissions button -->
                <button <?php if (!$button_allowed) { echo 'hidden'; } elseif ($button_allowed && $commissionDue == 0) { echo 'disabled'; } elseif ($commissionDue > $accBalMpesa){echo 'disabled'; } ?> style="border:2px solid grey; width:130px; height:40px;" onclick="openSendMoney()"><b>Pay Commission</b></button>
            </div>
     
    
            <h2>Commissions Not Yet Paid</h2>    
    
            <!--Commissions Not Paid for staff selected-->
            <table id="commission-table">
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
                        <th>Commission Paid</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Get the selected staff from the dropdown
                    if (isset($_GET['staff'])) {
                        $selectedStaff = $_GET['staff'];
                        // Split the selected staff value into name and phone
                        $selectedStaffParts = explode(" - ", $selectedStaff);
                        $selectedStaffName = $selectedStaffParts[0];
                        $selectedStaffPhone = $selectedStaffParts[1];
                    } else {
                        // Default values if no staff is selected
                        $selectedStaffName = "";
                        $selectedStaffPhone = "";
                    }
            
                    $sqlUnpaid = "SELECT s_no, name, phone, services, amount, staff_name, staff_phone, date, commission_paid FROM payments WHERE commission_paid = 'Not Paid' AND staff_name = '$selectedStaffName' AND staff_phone = '$selectedStaffPhone' ORDER BY date DESC";
                    
                    //Table Total Amount
                    $sumTotal = 0;
            
                    $result = $conn->query($sqlUnpaid);
            
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
                                    <td>" . $row["commission_paid"] . "</td>
                                </tr>";
                                
                            //$sumTotal += $row["amount"];
                        }
                    } else {
                        echo "<tr><td colspan='9'>No results found.</td></tr>";
                    }
            
                    //$conn->close();
                    ?>
                </tbody>
            </table>
            
            
            <h2>Commissions Already Paid</h2>
            
            <!--Commissions Paid for all staff-->
            <table id="paid-commission-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Staff Name</th>
                        <th>Phone Number</th>
                        <th>Amount Paid</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
            
                    $sqlPaid = "SELECT * FROM commission_payments ORDER BY date DESC";
            
                    $result = $conn->query($sqlPaid);
            
                    // Loop through the table data and generate HTML code for each row
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row["s_no"] . "</td>
                                    <td>" . $row["name"] . "</td>
                                    <td>" . $row["phone"] . "</td>
                                    <td>" . $row["amount"] . "</td>
                                    <td>" . $row["date"] . "</td>
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
            /*Reload with staff name selected to get table*/
            function pullCommissionReport() {
                var selectedStaff = document.getElementById("staff").value;
                var url = window.location.href.split('?')[0]; // Get the current URL without query parameters
                url += '?staff=' + encodeURIComponent(selectedStaff); // Append the selected staff as a query parameter
                window.location.href = url; // Redirect to the updated URL
            }
            
            //New method for payment through OTP page
            function openSendMoney() {
                var urlAll = 'templates/sendMoney.php';
                
                var params = new URLSearchParams(window.location.search);
                var staffParam = params.get("staff");
                var name1 = "";
                var account1 = "";
                var decodedStaffParam = decodeURIComponent(staffParam);
                var parts = decodedStaffParam.split("-");
                
                if (parts.length >= 2) {
                    name1 = parts[0].trim();
                    account1 = parts[1].trim();
                }
            //Details for individual payment

                var amount1 = parseInt(document.getElementById("commissionDue").value);
                //var floatValue = parseFloat(amount11.replace(/,/g, ""));
                //var amount1 = Math.floor(floatValue);
                var reason1 = 'Commission by ';
                var comment1 = reason1;
   
            //Process payment as per payment mode selected
                var payUrl = urlAll;
                payUrl += '?name=' + encodeURIComponent(name1);
                payUrl += '&account1=' + encodeURIComponent(account1);
                payUrl += '&amount1=' + encodeURIComponent('1');
                payUrl += '&reason1=' + encodeURIComponent(comment1);
                    
            // AJAX request to handle the payment
            // Save payment URL for use later on
                let sourceUrl = window.location.href;
                localStorage.setItem('targetUrl', payUrl);
                localStorage.setItem('sourceUrl', sourceUrl);
                //console.log(payUrl);
                
                window.location.href ="templates/setOtp.php";
            }
        </script>
        
        <?php include 'templates/scrollUp.php'; ?>
        <?php include 'templates/sessionTimeoutH.php'; ?>
    </body>
</html>