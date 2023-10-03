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
    }  else {
        $username_session = $_SESSION['username'];
        if($username_session === $_ENV['ADMIN1']) {
            $editAllowed = 1;
        } else {
            $editAllowed = 0;
        }
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
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Staff Management</title>
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

        
        <div class="body-content">
            
            <h1>STAFFING</h1> 
            
            <p><b>Staff Management</b></p>
            
            <!-- Staff Table -->
            <button onclick="openAddStaffPopup()" style="border:2px solid grey; width:130px; height:30px;"> Add New Staff </button>
            <button onclick="openAddAdminPopup()" style="border:2px solid grey; width:130px; height:30px;"> Add New Admin </button>
            <button onclick="openExitAdminPopup()" style="border:2px solid grey; width:130px; height:30px;"> Remove Admin </button>
            <button  onclick=openExitStaffPopup() style="border:2px solid grey; width:130px; height:30px;"> Exit Staff</button>
            <button  onclick=openRecruitStaffPopup() style="border:2px solid grey; width:130px; height:30px;"> New Recruit</button>
            
            <table id="staff-table">
                <thead>
                    <tr>
                        <?php if($editAllowed === 1){ ?>
                            <th>Action</th>
                            <th>Go</th>
                        <?php } ?>
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
                        $sqlStaffTable = "SELECT staff_no, staff_name, staff_phone, staff_email, joinDate, ID_front, ID_back, passport_pic, contract FROM staff WHERE status='active' AND staff_name <> '$username_session' ORDER BY joinDate DESC";
                        $result = $conn->query($sqlStaffTable);
                
                        // Loop through the table data and generate HTML code for each row
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                 if($editAllowed === 1){ 
                                    //echo "<td><button class='edit-btn'  >Edit</button> <button class='save-btn' style='display:none;'>Save</button></td>";
                                    echo "<td>";
                                    echo '<select name="action" id="action" style="height:30px;">';
                                    echo '<option value="">Actions</option>';
                                    echo '<option value="new_admin">Make Admin</option>';
                                    echo '<option value="exitAdmin">Remove Admin</option>';
                                    echo '<option value="exitStaff">Exit Staff</option>';
                                    echo '<option value="editStaff">Edit Staff</option>';
                                    echo '</select>';
                                    echo "</td>";
                                    echo "<td><button class='go'>Go</button></td>";
                                }
                                echo "<td>" . $row["staff_no"] . "</td>";
                                echo "<td>" . $row["staff_name"] . "</td>";
                                echo "<td>". $row["staff_phone"] . "</td>";
                                echo "<td>". $row["staff_email"] . "</td>";
                                echo "<td>". $row["joinDate"] . "</td>";
                                echo "<td>" . "<a href='" . $row["ID_front"] . "' download>Download</a></td>";
                                echo "<td>" . "<a href='" . $row["ID_back"] . "' download>Download</a></td>";
                                echo "<td>" . "<a href='" . $row["passport_pic"] . "' download>Download</a></td>";
                                echo "<td>" . "<a href='" . $row["contract"] . "' download>Download</a></td>";
                                echo "</tr>";
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
            
            <script>
                document.querySelectorAll('.go').forEach(function(button) {
                    button.addEventListener('click', function() {
                        var row = button.closest('tr');
                        var actionSelected = $('#action').val();
                        var emailSelected = row.querySelector('td:nth-child(6)').innerText;
                        
                        console.log(actionSelected);
                        console.log(emailSelected);
                        
                        if (actionSelected) {
                            // Construct the URL with the emailSelected parameter
                            var url = encodeURIComponent(actionSelected). ".php?emailSelected=" + encodeURIComponent(emailSelected);
                        
                            // Open a new window with the constructed URL
                            window.open(url, "Action", "width=400, height=400");
                        }
                        
                        window.location.href = '';
                        
                    });
                });
                
                /*
                //Editing
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
                
                */
            </script>
            
            <?php //include 'templates/sessionTimeoutL.php'; ?>
            <br>
            <br>
        </div>
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>