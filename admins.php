<?php
    require_once 'vendor/autoload.php'; // Include the Dotenv library

    use Dotenv\Dotenv;
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
    
    if (!isset($_SESSION['username'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: login'); // Redirect to the login page
        exit;
    }
    
    // Check if the session username is "Tim" or "Millie"
    if ($_SESSION['admin'] === true) {
        $admin = 1;
    }else {
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
        <title>Home</title>
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
        </style>
    </head>
    <body class="body">
        <?php include "templates/header-admins.php" ?>
        
        <!-- <?php //include "templates/sidenav-admins.php" ?> -->
        
        <div class="body-content">
            <h1>Home</h1> 
            
            <!--NB: Check out our <a href="/api_doc">API doc</a> incase you need to integrate your system/website with ours<br>-->
            
            <p><b>FEEDBACK FROM WEBSITE</b></p>
            
            <!-- Add a button to export the table to Excel -->
            <button <?php if ($admin === 0) { echo 'hidden'; } ?> onclick="exportTableToExcel('feedback-table', 'feedback')" style="border:2px solid grey; width:130px; height:30px;">Export to Excel</button>
            
            <table id="feedback-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Comment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                                $sql = "SELECT name, email, comment, date FROM feedback ORDER BY date DESC LIMIT 3";
                                $result = $conn->query($sql);
                                
                                    // Loop through the table data and generate HTML code for each row
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                    echo "<tr><td>" . $row["name"] . "</td><td>" . $row["email"] . "</td><td>" . $row["comment"] . "</td><td>" . $row["date"] . "</td></tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3'>No results found.</td></tr>";
                                }
                        
                    ?>
                </tbody>
            </table>
            
            <p><b>ORDERS RECIEVED FROM WEBSITE</b></p>
            <!-- Add a button to export the table to Excel -->
            <button <?php if ($admin === 0) { echo 'hidden'; } ?> onclick="exportTableToExcel('order-table', 'orders')" style="border:2px solid grey; width:130px; height:30px;">Export to Excel</button>
            
            <table id="order-table">
                <thead>
                    <tr>
                        <th> Order Number</th>
                        <th>Order Time</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php
                        
                        $sql1 = "SELECT order_number, orderTime, custName, email, phone, product, quantity, delivered FROM orders ORDER BY order_number DESC";
                        $result = $conn->query($sql1);
                        
                        // Loop through the table data and generate HTML code for each row
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr><td>" . $row["order_number"] . "</td><td>" . $row["orderTime"] . "</td><td>" . $row["custName"] . "</td><td>" . $row["email"] . "</td><td>" . $row["phone"] . "</td><td>" . $row["product"] . "</td><td>" . $row["quantity"] . "</td>";
                                
                                // Add action button to update delivered status
                                echo "<td>";
                                if ($row["delivered"] == "No") {
                                    echo "<form method='post'>
                                        <input type='hidden' name='order_number' value='" . $row["order_number"] . "'>
                                        <button type='submit' name='deliver' style='width: 120px; height: 30px; padding: 4px;'>Deliver</button>
                                        </form>";
                                } else {
                                    echo "Delivered";
                                }
                                echo "</td>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No results found.</td></tr>";
                        }
                        //Update status of order if delivered
                        
                        if (isset($_POST['deliver'])) {
                            $order_number = $_POST['order_number'];
                            $sql3 = "UPDATE orders SET delivered='Yes' WHERE order_number=$order_number";
                            if ($conn->query($sql3) === TRUE) {
                                echo "<script>
                                    alert('Order number $order_number has been marked as delivered.');
                                    window.location.href = ('admins');
                                </script>";
                            } else {
                                echo "Error updating record: " . $conn->error;
                            }
                        }
                        
                    ?>
                </tbody>
            </table>
            <br>
            <br>

            <!-- Add a button to Upload more images -->
            <?php
                if ($admin === 1) {
            ?>
            <p><b>WEBSITE SLIDESHOW <i>(Services Page)</i></b></p>
            
            <button onclick="openImageUploadPopup()" style="border:2px solid grey; width:130px; height:40px;">Upload New Images</button>
            
            <table id="images">
                <thead>
                    <tr>
                        <th>Image Name</th>
                        <th>Uploaded Time</th>
                        <th>Download Link</th>
                        <th>Delete Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php    
                        $sqlImageTable = "SELECT image_path, time FROM images ORDER BY time DESC";
                        $result = $conn->query($sqlImageTable);
                        
                        // Loop through the table data and generate HTML code for each row
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr><td>" . $row["image_path"] . "</td><td>" . $row["time"] . "</td><td>";
                                echo "<a href='" . $row["image_path"] . "' download>Download</a></td><td>"; // download link
                                //echo "<button style='width: 120px; height: 30px; padding: 4px;' onclick='deleteImage(\"" . $row["image_path"] . "\")'>Delete</button>"; // delete button
                                echo "<form method='post'>
                                        <input  type='hidden' name='image_path' value='" . $row["image_path"] . "'>
                                        <button type='submit' name='deleteImage' style='width: 120px; height: 30px; padding: 4px;'>Delete</button>
                                    </form>";
                                echo "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No results found.</td></tr>";
                        }

                        //Deleting image from database
                        if (isset($_POST['deleteImage'])) {
                            $imagePath = $_POST['image_path'];
                            
                            $sqlDeleteImage = "DELETE FROM images WHERE image_path = '$imagePath'";
                            if ($conn->query($sqlDeleteImage) === TRUE) {
                                echo "<script> 
                                        alert('Image deleted successfully.');
                                        window.location.href = 'admins';
                                    </script>";
                                exit();
                            } else {
                                echo "Error deleting image: " . $conn->error;
                            }
                        } else {
                            echo "No image path provided.";
                        }
                    ?>
                </tbody>
            </table>
            <br>
            <br>
            
            <p><b>OFFERS</b></p>
            
            <button  onclick=openNewOfferPopup() style="border:2px solid grey; width:130px; height:30px;"> New Offer</button>
            
            <button  onclick=openStopOfferPopup() style="border:2px solid grey; width:130px; height:30px;"> Stop Offer</button>
            
            <table id="offer">
                <thead>
                    <tr>
                        <th>Offer No.</th>
                        <th>Offer Name</th>
                        <th>Offer Poster</th>
                        <th>Offer Start Date</th>
                        <th>Offer End Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php    
                        $sqlOffersTable = "SELECT s_no, offer_name, offer_image_poster, start_date, end_date, status FROM offers ORDER BY start_date DESC";
                        $resultOffer = $conn->query($sqlOffersTable);
                
                        // Loop through the table data and generate HTML code for each row
                        if ($resultOffer->num_rows > 0) {
                            while ($row = $resultOffer->fetch_assoc()) {
                                echo "<tr><td>" . $row["s_no"] . "</td><td>" . $row["offer_name"] . "</td><td>" . "<a href='" . $row["offer_image_poster"] . "' download>Download</a></td><td>". $row["start_date"] . "</td><td>". $row["end_date"] . "</td><td>". $row["status"] . "</td>"; // download link
                                                
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No results found.</td></tr>";
                        }
                    ?>
                                
            </tbody>
            </table>
            
            <?php
                }
                 $conn->close();
            ?> 
            
            
            <br>
            <br>

            <script>
                // Check delivered status on page load
                $(document).ready(function() {
                    $(".deliver").each(function() {
                        var orderID = $(this).data("orderid");
                        var delivered = $(this).data("delivered");
                        if (delivered === "Yes") {
                            $(".deliver[data-orderid='" + orderID + "']").text("Delivered");
                            $(".deliver[data-orderid='" + orderID + "']").attr("disabled", true);
                        }
                    });
                });
            </script>
            
            <!-- Add a script to export the table to Excel -->
            <script>
                function exportTableToExcel(tableId, filename = ''){
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
            
            <!-- Add a script to open Upload Images popup -->
            <script>
                function openImageUploadPopup() {
                    window.open("upload_pics.php", "Upload Images", "width=400, height=400");
                }
            </script>
                
      <!-- Add a script for Offers -->
            <script>
                //New offers
                function openNewOfferPopup() {
                    window.open("upload_offer.php", "New Offer", "width=400, height=400");
                }
                
                //To stop an offer
                function openStopOfferPopup() {
                    window.open("stop_offer.php", "Stop Offer", "width=400, height=400");
                }
                
                    // Get all elements with class 'help-toggle-description'
                    const toggleButtons = document.querySelectorAll('.help-toggle-description');
                    
                    // Add a click event listener to each toggle button
                    toggleButtons.forEach(button => {
                      button.addEventListener('click', () => {
                        const description = button.previousElementSibling;
                        description.style.display = (description.style.display === 'none') ? 'block' : 'none';
                      });
                    });
            </script>
            
            <?php //include 'templates/sessionTimeoutL.php'; ?>
            <br>
            <br>
        </div>
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>
