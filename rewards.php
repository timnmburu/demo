<?php
    use Dotenv\Dotenv;
    
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    
    session_start();
    if (!isset($_SESSION['username'])) {
        
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: login'); // Redirect to the login page
        exit;
    } elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false || $_SESSION['admin'] === false){
        header('Location: admins');
    } 
    
    if ($_SESSION['admin'] === true){
        $admin = 1;
    } else {
        $admin = 0;
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
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Rewards Portal</title>
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
        </style>
    </head>        
    <body class="body">
        <?php include "templates/header-admins.php" ?>
        
        <div class="body-content">
                
            <h1> Customer Rewards Page</h1>
    
            <p> 
                Find customer details below;
            </p>
            <!-- Add a button to export the table to Excel -->
            <!-- Add a button to export the table to Excel -->
            <button <?php if ($admin === 0) { echo 'hidden'; } ?> onclick="exportTableToExcel('rewards', 'rewards')" style="border:2px solid grey; width:130px; height:30px;">Export to Excel</button>
            
            <!-- Add a button to redeem points -->
            <button onclick="openRedeemPopup()" style="border:2px solid grey; width:130px; height:30px;">Redeem Points</button>
            
            <table id="rewards">
                <thead>
                    <tr>
                        <th>Customer Number</th>
                        <th>Customer Name</th>
                        <th>Phone Number</th>
                        <th>Points Received</th>
                        <th>Points Redeemed</th>
                        <th>Last Redeem Date</th>
                        <th>Points Balance</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php

                $sql11 = "SELECT custID, custName, custPhone, points, redeemed, lastRedeemed, pointsBal FROM customers";
                $result = $conn->query($sql11);
            
                // Loop through the table data and generate HTML code for each row
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["custID"] . "</td><td>" . $row["custName"] . "</td><td>" . $row["custPhone"] . "</td><td>" . $row["points"] . "</td><td>" . $row["redeemed"] . "</td><td>" . $row["lastRedeemed"] . "</td><td>" . $row["pointsBal"] . "</td>";
                        
                    }
                } else {
                    echo "<tr><td colspan='7'>No results found.</td></tr>";
                }
                    
                $conn->close();
                ?>
                </tbody>
            </table>
    
            <!-- Add a script to export the table to Excel -->
            <script>
                function exportTableToExcel(tableId, filename = 'rewards'){
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
            </script>
            <!-- Add a script to open redeeming points popup -->
            <script>
                function openRedeemPopup() {
                    window.open("redeem", "Redeem Points", "width=400, height=400");
                }
            </script>
            <?php include 'templates/sessionTimeoutL.php'; ?>

        </div>  
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>