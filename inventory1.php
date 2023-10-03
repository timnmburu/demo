<?php
    require_once 'vendor/autoload.php'; // Include the Dotenv library

    use Dotenv\Dotenv;
    
    session_start();
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
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>

<!DOCTYPE html>
<html en-US>
    <head>
        <title>Inventory</title>
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
            .formInventory {
               border:1px solid black; 
               display:flex; 
               width: 40%; 
            }

            @media only screen and (max-width: 768px) {
                .formInventory {
                   border:1px solid black; 
                   display:flex; 
                   width: 100%;
                }
            }
        </style>
    </head> 
    
    <body class="body">
        <?php include "templates/header-admins.php" ?>
        
        <div class="body-content" >
    
            <h1>Inventory Management</h1>
            
            <div class="formInventory">
                <form id="submitInventory" style="width: 80%; padding: 4px;" method="POST" action="">
                    <input placeholder="Item Name" type="text" name="item-name" style="width: 95%; height: 30px;" required>
                    <br> 
                    <br>
                    <input placeholder="Item Price" type="number" name="item-price" style="width: 95%; height: 30px;" required>
                    <br>
                    <br>
                    <input placeholder="Item Quantity" type="number" name="item-quantity"style="width: 95%; height: 30px;" required>
                    <br>
                    <br>
                    <input placeholder="Date of purchase" type="date" name="date" style="width: 95%; height: 30px;" required>
                    <br>
                    <br>
                    Select Payment Mode:
                    <select name="payment-mode" style="width: 120px; height: 30px;" required>
                        <option name="kcb" >KCB Paybill</option> 
                        <option name="mpesa" >Mpesa Online</option>
                    </select> 
                    <br>
                    <br>
                    <input type="submit" value="Submit Item Purchase" name="submitInventory" style="width: 180px; height: 30px;align-items: center;justify-content: center;zoom:120%;" />
                </form>
            </div>
            
            <h2> Recorded Inventory</h2>
            
            <!-- Add a button to export the table to Excel -->
            <?php
                // Initialize $export_allowed to false by default
                $export_allowed = false;
                
                // Check if the session username is "Tim" or "Millie"
                if (isset($_SESSION['username']) && ($_SESSION['username'] == $_ENV['ADMIN1'] || $_SESSION['username'] == $_ENV['ADMIN2'])) {
                    // User is authorized to export tables
                    $export_allowed = true;
                }
            ?>
            <button <?php if (!$export_allowed) { echo 'disabled'; } ?> onclick="exportTableToExcel('inventory-table', 'inventory')" style="border:2px solid grey; width:130px; height:30px;" >Export to Excel</button>
            
            <!-- Add a search bar -->
            <input type="text" id="inventory-search" placeholder="Search by item name" style="width:30%; height:25px; border: 2px solid #000;" >
            
            
            <table id="inventory-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Item Name</th>
                        <th>Item Price</th>
                        <th>Item Quantity</th>
                        <th>Date Purchased</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        
                        $sqlInventory = "SELECT id, name, price, quantity, date FROM inventory ORDER BY date DESC";
                        $result = $conn->query($sqlInventory);
                        
                        // Loop through the table data and generate HTML code for each row
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td><td>" . $row["price"] . "</td><td>" . $row["quantity"] . "</td><td>" . $row["date"] . "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No results found.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
            <br>
            <br>
            
            <?php     
                // Process inventory purchase information
                if (isset($_POST['submitInventory'])) {
                    $name = $_POST['item-name'];
                    $price = $_POST['item-price'];
                    $quantity = $_POST['item-quantity'];
                    $date = $_POST['date'];
                    $paymentMode = $_POST['payment-mode'];
                    
                    $sqlItem = "INSERT INTO inventory (name, price, quantity, date, paidFrom) VALUES ('$name', '$price', '$quantity', '$date', '$paymentMode')";
                    $sqlExpenses = "INSERT INTO expenses (name, price, quantity, date, paidFrom) VALUES ('$name', '$price', '$quantity', '$date', '$paymentMode')";
                    
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
                    
                    $newMpesaWalletBal = $accBalMpesa - $price;
                    $newKcbWalletBal = $accBalKcb - $price;
                    
                    if ($paymentMode === 'Mpesa Online') {
                        $sqlUpdateWallet = "UPDATE wallet SET mpesa='$newMpesaWalletBal', kcb='$accBalKcb'";
                        $resultMode = $conn->query($sqlUpdateWallet);
                    } elseif ($paymentMode === 'KCB Paybill') {
                        $sqlUpdateWallet = "UPDATE wallet SET kcb='$newKcbWalletBal', mpesa='$accBalMpesa'";
                        $resultMode = $conn->query($sqlUpdateWallet);
                    }
                    
                    if ($conn->query($sqlItem) === TRUE && $conn->query($sqlExpenses) === TRUE && $resultMode === TRUE) {
                            // Items successfully added to table
                            echo "Successfully added";
                            exit();
                        } else {
                            echo "Error inserting inventory: " . $conn->error;
                        }
                }
                
                $conn->close();
            ?>
            
            <!-- Add a script to export the table to Excel -->
            <script>
                function exportTableToExcel(tableId, filename = 'inventory'){
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
                        }else{
                            // Create a link to the file
                            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
                            
                            // Setting the file name
                            downloadLink.download = filename;
                            
                            //triggering the function
                            downloadLink.click();
                        }
                }
            </script>
            
            <!-- Add a script to search the table -->
            <script>
                function searchTable() {
                    // Get the input field and the table
                    var input = document.getElementById("inventory-search");
                    var table = document.getElementById("inventory-table");
                    
                    // Get the search query and convert it to lowercase
                    var query = input.value.toLowerCase();
                    
                    // Iterate through the rows of the table
                    for (var i = 1; i < table.rows.length; i++) {
                        var row = table.rows[i];
                        var name = row.cells[1].textContent.toLowerCase();
                        
                        // Check if the name or phone number contains the search query
                        if (name.includes(query)) {
                            row.style.display = "";
                        } else {
                            row.style.display = "none";
                        }
                    }
                }
                
                var input = document.getElementById("inventory-search");
                input.addEventListener("input", searchTable);
            </script>
            <?php include 'templates/sessionTimeoutL.php'; ?>
            <br>
        </div> 
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>