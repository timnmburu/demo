<?php
    require_once 'vendor/autoload.php'; // Include the Dotenv library

    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    session_start();
    if (!isset($_SESSION['username'])) {
        
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: login'); // Redirect to the login page
        exit;
    } elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false){
        header('Location: admins');
    } 
    
    // Initialize $export_allowed to false by default
    $admin = 0;
    // Check if the session username is an admin
    if ($_SESSION['admin'] === true) {
        $admin = 1;
    } else {
        $admin = 0;
    }
    
    // Database connection
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
            tr {
                /*border-radius: 50px;*/
            }
            
            th, td {
                text-align: center;
                padding: 8px;
                border: 1px solid #ddd;
                /*border-radius: 50px;*/
            }
            
            th {
                background-color: #f2f2f2;
            }
            
            .forminventory {
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
                .forminventory {
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
                
            <p><h1>Inventory Management</h1></p>
                <div class="forminventory">
                    <form id="submitinventory" style="width: 80%; padding: 4px;" method="POST" action="inventory.php">
                        <input placeholder="Item Description" type="text" name="item-name" style="width: 95%; height: 30px;" required>
                        <br> 
                        <br>
                        <input placeholder="Paid Amount" type="number" name="item-price" style="width: 95%; height: 30px;" required>
                        <br>
                        <br>
                        <input placeholder="Item Quantity" type="number" name="item-quantity" style="width: 95%; height: 30px;" required>
                        <br>
                        <br>
                        <input placeholder="Date of Payment" type="date" name="date" style="width: 95%; height: 30px;" required>
                        <br>
                        <br>
                        Select Payment Mode:
                        <select name="payment-mode" style="width: 120px; height: 30px;" required>
                            <option name="kcb" >KCB Paybill</option> 
                            <option name="mpesa" >Mpesa Online</option>
                        </select> 
                        <br>
                        <br>
                        <input type="submit" value="Submit Payment" name="submitinventory" style="width: 180px; height: 30px;align-items: center;justify-content: center;zoom:120%;" />
                    </form>
                </div>
                <br><br>
                <div>
                    
                </div>
            
            <h2> Recorded Inventory</h2>
            
            <!-- Add a button to export the table to Excel -->
            <button <?php if ($admin === 0) { echo 'hidden'; } ?> onclick="exportTableToExcel('inventory-table', 'inventory')" style="border:2px solid grey; width:130px; height:30px;">Export to Excel</button>
            
            <!-- Add a search bar -->
            <input type="text" id="inventory-search" placeholder="Search by item name" style="width:30%; height:25px; border: 2px solid #000;" >
            
            <button <?php if ($admin === 0) { echo 'hidden'; } ?> name="addRecurrentExp" onclick=openRecurrentExp() style="border:2px solid grey; width:170px; height:30px;" > Add Recurrent Expense</button>
            
            <button <?php if ($admin === 0) { echo 'hidden'; } ?> name="removeRecurrentExp" onclick=openRecurrentExpRemove() style="border:2px solid grey; width:210px; height:30px;" > Remove Recurrent Expense</button>
            
            
            <table id="inventory-table">
                <thead>
                    <tr>
                        <?php if($admin === 1){ ?>
                            <th>Action</th>
                        <?php } ?>
                        <th>No.</th>
                        <th>Item Description</th>
                        <th>Paid Amount</th>
                        <th>Item Quantity</th>
                        <th>Date of Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                        // Fetch data from the database
                        $sqlinventory = "SELECT * FROM inventory ORDER BY date DESC";
                        $result = $conn->query($sqlinventory);
        
                        // Display data in the table
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr class='saved-row'>";
                                if($admin === 1){ 
                                    echo "<td><button class='edit-btn'  >Edit</button> <button class='save-btn' style='display:none;'>Save</button></td>";
                                }
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td><input type='text' class='edit-field' disabled value='" . $row["name"] . "'></td>";
                                echo "<td><input type='text' class='edit-field' disabled value='" . $row["price"] . "'></td>";
                                echo "<td><input type='text' class='edit-field' disabled value='" . $row["quantity"] . "'></td>";
                                echo "<td><input type='text' class='edit-field' disabled  value='" . $row["date"] . "'></td>";

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No results found.</td></tr>";
                        }
        
                        // Close the database connection
                        $conn->close();
                    ?>
                </tbody>
            </table>
            <br>
            <br>
            
            <?php     
                // Process inventory information
                if (isset($_POST['submitinventory'])) {
                    $name = $_POST['item-name'];
                    $price = $_POST['item-price'];
                    $quantity = $_POST['item-quantity'];
                    $date = $_POST['date'];
                    $paymentMode = $_POST['payment-mode'];
                    
                    $sqlItem = "INSERT INTO inventory (name, price, quantity, date, paidFrom) VALUES ('$name', '$price', '$quantity', '$date', '$paymentMode')";
                    
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
                    
                    if ($conn->query($sqlItem) === TRUE && $resultMode === TRUE) {
                        // Items successfully added to table
                        echo "Successfully added";
                        exit();
                    } else {
                        echo "Error inserting Expense: " . $conn->error;
                    }
                }
                
                
                
                //$conn->close();
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
                        var name = row.cells[2].textContent.toLowerCase();
                        
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
            
            <script>
            //To add recurrent inventory
                function openRecurrentExp() {
                    window.open("/recurrentExp.php", "Recurrent Expenditure", "width=400, height=400");
                }
                
            //To remove recurrent inventory
                function openRecurrentExpRemove() {
                    window.open("/recurrentExpRemove.php", "Recurrent Expenditure", "width=400, height=400");
                }
                
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
                            price: editFields[1].value,
                            quantity: editFields[2].value,
                            date: editFields[3].value,
                            table:"inventory"
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
            
            <br>
            <br>
            <br>
            <?php //include 'templates/navbar.php'; ?>
        </div>
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>