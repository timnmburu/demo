<?php
    require_once 'vendor/autoload.php'; 
    require_once 'api/requests/get_account_details/get_account_details.php';

    use Dotenv\Dotenv;
    
    session_start();
    if (!isset($_SESSION['username'])) {
        
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: login'); // Redirect to the login page
        exit;
    }
    if ($_SESSION['admin'] === true) {
        $admin = 1;
    } else {
        $admin = 0;
    }
    if($_SESSION['access'] === true){
        $access = 1;
    } else {
        $access = 0;
    }
        
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Database credentials
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
    try{
        $usernameValue = $_SESSION['username'];
        
        // Prepare and execute the SQL query to get Email
        $stmt = $conn->prepare("SELECT email, lastResetDate FROM users WHERE username = ?");
        $stmt->bind_param("s", $usernameValue);
        $stmt->execute();
        
        // Get the result set
        $resultSet = $stmt->get_result();
        $lastResetDate = "";
        
        // Fetch the result
        if ($resultSet->num_rows > 0) {
            $result = $resultSet->fetch_assoc();
            $emailU = $result['email'];
            $lastResetDate = $result['lastResetDate'];
            // Use the $emailU and $lastResetDate variables as needed
        } else {
            // Email not found or invalid username
            echo "Invalid username.";
        }
        
        // Close the statement and result set
        $stmt->close();
        $resultSet->close();
        
        // Prepare and execute the SQL query to get phone number and full name
        $stmt2 = $conn->prepare("SELECT staff_phone, staff_name FROM staff WHERE staff_email = ?");
        $stmt2->bind_param("s", $emailU);
        $stmt2->execute();
        
        // Get the result set
        $resultSet2 = $stmt2->get_result();
        
        // Fetch the result
        if ($resultSet2->num_rows > 0) {
            $result2 = $resultSet2->fetch_assoc();
            $phoneU = $result2['staff_phone'];
            $nameU = $result2['staff_name'];
            // Use the $emailU and $lastResetDate variables as needed
        } else {
            // Email not found or invalid username
            echo "Invalid email.";
        }
        
        $stmt2->close();
        $resultSet2->close();
    
        $fullnameValue = $nameU;
        $emailValue = $emailU;
        $phoneValue = $phoneU;
        $lastPasswordResetValue = $lastResetDate;
        
            // Handle form submission for updating profile details
        if (isset($_POST['saveProfile'])) {
            // Retrieve the modified values from the form
            $newFullName = $_POST['fullName'];
            $newPhone = $_POST['phone_no'];
            $newEmail = $_POST['emailAdd'];
            $newUsername = $_POST['userName'];
            
            // Prepare and execute the SQL query to update the staff table
            $updateStmt = $conn->prepare("UPDATE staff SET staff_name = ?, staff_phone = ? , staff_email = ? WHERE staff_email = '$emailValue'");
            $updateStmt->bind_param("sss", $newFullName, $newPhone, $newEmail);
            $updateStmt->execute();
            
            // Check if the update was successful
            if ($updateStmt->affected_rows > 0) {
                // Update successful
                echo "Profile updated successfully.";
                $updateUsersTable = "UPDATE users SET email = '$newEmail' WHERE username='$usernameValue'";
                $conn->query($updateUsersTable);
            } else {
                // Update failed
                echo "Profile update failed.";
            }
            // Close the update statement
            $updateStmt->close();
        }
    } catch (Exception $e){
        //session_start();
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: error");
        exit();
    }
    
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Edit Profile</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css"/>
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
        <style>
            .profile_element {
                border:2px solid #ddd;
                align-items: center;
            }
            .profile_fields {
                border:2px solid grey; 
                width:300px; 
                height:30px;
                margin-right:10px;
                font-size:24px;
            }
        </style>
    </head>
    <body class="body">
        <?php include "templates/header-admins.php" ?>
        
        <div class="body-content">
            <div style="border: black solid; border-width: 2px; padding: 2px; ">
                <h1> Profile  </h1>
                <form action="" method="POST" class="profile-element">    
                    <?php
        
                        // Initialize $edit_allowed to false by default
                        $edit_allowed = false;
                        
                        // Check if the Edit button is clicked
                        if (isset($_GET['edit'])) {
                            // User is authorized to export tables
                            $edit_allowed = true;
                        }
                        
                    ?>    
                    
                    Username:
                    
                    <input disabled name="userName" value="<?php echo $usernameValue; ?>" class="profile_fields" >
                    
                    <br>
                    <br>
                
                    Full Name:
                    
                    <input <?php if (!$edit_allowed) { echo 'disabled'; } ?> name="fullName" value="<?php echo $nameU; ?>" class="profile_fields" >
                    
                    <br>
                    <br>
                    
                    Phone No:
                    
                    <input <?php if (!$edit_allowed) { echo 'disabled'; } ?> name="phone_no" value="<?php echo $phoneValue; ?>" class="profile_fields">
                    
                    <br>
                    <br>
                    
                    Email Add:
                    
                    <input <?php if (!$edit_allowed) { echo 'disabled'; } ?> name="emailAdd" value="<?php echo $emailValue; ?>" class="profile_fields">
                    <br>
                    <br>
                    
                    Password:
                    
                    <input disabled value="<?php echo "xxxxxxx"; ?>" class="profile_fields" >
                    
                    <a href="passwordReset"> Change? </a> 
                    
                    Last Password Reset:
                    <?php echo $lastPasswordResetValue; ?>
                    <br>
                    <br>
                    <button <?php if ($edit_allowed) { echo 'disabled'; } ?> style="border:2px solid grey; width:150px; height:30px; background-color:lightblue;" onclick="addEditQueryParam()"><b>EDIT PROFILE</b></button>
                    
                    <button <?php if (!$edit_allowed) { echo 'disabled'; } ?> style="border:2px solid grey;  width:150px; height:30px; background-color:green;" name="saveProfile"><b>SAVE PROFILE</b></button>
                    
                    <button <?php if ($edit_allowed) { echo 'disabled'; } ?> style="border:2px solid grey;  width:150px; height:30px; background-color:green;" name="refresh"><b>Refresh</b></button>
                </form>
            </div>
        
            <br><br>
            <div <?php if($admin === 0) { echo 'hidden'; } ?> >
                <?php
                    $sql11 = "SELECT s_no, custID, subDate, subAmount, lastPayAmount, lastPayDate, nextPayDate, status FROM account ORDER BY s_no DESC";
                    $result = $conn->query($sql11);
                    
                    if ($result->num_rows > 0) {
                        $custIDrow = $result->fetch_assoc();
                        $customerID = $custIDrow["custID"];
                        $amountDue = $_SESSION['amount_due'];
                    }
                ?>
                <h1>Account Details (Account Access: <?php if($access === 1){ echo " Full)"; } else { echo " Limited <a href='/mpesa?customer=$customerID&amountDue=$amountDue'>Pay Now?</a> )";} ?> </h1>
                <table id="account">
                    <thead>
                        <tr>
                            <th>Customer Number</th>
                            <th>Subscription Date</th>
                            <th>Subscription Amount</th>
                            <th>Last Payment Amount</th>
                            <th>Last Payment Date</th>
                            <th>Next Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    <?php
                    // Loop through the table data and generate HTML code for each row
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr><td>" . $row["custID"] . "</td><td>" . $row["subDate"] . "</td><td>" . $row["subAmount"] . "</td><td>" . $row["lastPayAmount"] . "</td><td>" . $row["lastPayDate"] . "</td><td>" . $row["nextPayDate"] . "</td>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No results found.</td></tr>";
                    }
                        
                    $conn->close();
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            function addEditQueryParam() {
                // Prevent form submission
                event.preventDefault();
                // Get the current URL
                var url = window.location.href;
                
                // Check if the query string already exists
                if (url.indexOf('?') === -1) {
                    // Add the query string with 'edit=true'
                    url += '?edit=true';
                } else {
                    // Add the query string with '&edit=true'
                    url += '&edit=true';
                }
                
                // Reload the page with the new URL
                window.location.href = url;
            }
        </script>
   
    <?php //include 'templates/sessionTimeoutL.php'; ?>    
    </body>
</html>