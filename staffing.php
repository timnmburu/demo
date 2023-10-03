<?php
    require_once 'vendor/autoload.php'; // Include the Dotenv library

    use Dotenv\Dotenv;
    
    session_start();
    if (!isset($_SESSION['username'])) {
        
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: login'); // Redirect to the login page
        exit;
    } elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false || $_SESSION['admin'] === false){
        header('Location: admins');
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
        <title>Staff Management</title>
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
        </style>
    </head>
    <body class="body">
        <?php include "templates/header-admins.php" ?>

        
        <div class="body-content">
            
            <h1>STAFFING</h1> 
            
            <p><b>Staff Management</b></p>
            
            <!-- Staff Table -->
            <button onclick="openAddStaffPopup()" style="border:2px solid grey; width:130px; height:30px;"> Add New Staff </button>
            <button onclick="openAddAdminPopup()" style="border:2px solid grey; width:130px; height:30px;"> Add New Admin </button>
            <button onclick="openExitAdminPopup()" style="border:2px solid grey; width:130px; height:30px;"> Remove Admin </button>
            <button  onclick=openExitStaffPopup() style="border:2px solid grey; width:130px; height:30px;"> Exit Staff</button>
            <button  onclick=openRecruitStaffPopup() style="border:2px solid grey; width:130px; height:30px;"> New Recruit</button>
            
            <table id="staff">
                <thead>
                    <tr>
                        <th>Staff No.</th>
                        <th>Staff Name</th>
                        <th>Staff Phone</th>
                        <th>Staff Email</th>
                        <th>Joined Date</th>
                        <th>ID Front</th>
                        <th>ID Back</th>
                        <th>Passport Pic</th>
                        <th>Contract</th>
                    </tr>
                </thead>
                <tbody>
                    <?php    
                        $sqlStaffTable = "SELECT staff_no, staff_name, staff_phone, staff_email, joinDate, ID_front, ID_back, passport_pic, contract FROM staff WHERE status='active' ORDER BY joinDate DESC";
                        $result = $conn->query($sqlStaffTable);
                
                        // Loop through the table data and generate HTML code for each row
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr><td>" . $row["staff_no"] . "</td><td>" . $row["staff_name"] . "</td><td>". $row["staff_phone"] . "</td><td>". $row["staff_email"] . "</td><td>". $row["joinDate"] . "</td><td>" . "<a href='" . $row["ID_front"] . "' download>Download</a></td><td>" . "<a href='" . $row["ID_back"] . "' download>Download</a></td><td>" . "<a href='" . $row["passport_pic"] . "' download>Download</a></td><td>" . "<a href='" . $row["contract"] . "' download>Download</a></td><td>"; // download link
                                                
                                echo "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No results found.</td></tr>";
                        }
                    ?>
                                
                </tbody>
            </table>
            
            <?php
                
                 $conn->close();
            ?> 
            
            
            <br>
            <br>
            
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            
                <!-- Add a script to open Add New Staff popup -->
            <script>
                function openAddStaffPopup() {
                    window.open("new_staff.php", "Add New Staff", "width=400, height=400");
                }
            </script>
            
            <!-- Add a script to open Add New Admin popup -->
            <script>
                function openAddAdminPopup() {
                    window.open("new_admin.php", "Add New Admin", "width=400, height=400");
                }
            </script>
            
            <!-- Add a script to open Remove Admin popup -->
            <script>
                function openExitAdminPopup() {
                    window.open("exit_admin.php", "Remove Admin", "width=400, height=400");
                }
            </script>
            
            <!-- Add a script to open Remove staff popup -->
            <script>
                function openExitStaffPopup() {
                    window.open("exit_staff.php", "Remove Staff", "width=400, height=400");
                }
            </script>
            
            <!-- Add a script to open Remove staff popup -->
            <script>
                function openRecruitStaffPopup() {
                    window.open("recruitment.php", "New Recruits", "width=400, height=400");
                }
            </script>
   
            <?php include 'templates/sessionTimeoutL.php'; ?>
            <br>
            <br>
        </div>
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>