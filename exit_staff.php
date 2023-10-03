<?php    
    require_once 'vendor/autoload.php'; // Include the Dotenv library

    use Dotenv\Dotenv;
    
    session_start();
    if (!isset($_SESSION['username'])) {
          header('Location: login');
          exit;
    } elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false){
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
    
    //Enter staff details in database
    if (isset($_POST['submitStaff'])) {
        $selectedStaff = $_POST['staff-details']; // Format: "staff_name - staff_phone"
        $staffData = explode(' - ', $selectedStaff);
        $staff_no = $staffData[1]; // Extracted staff phone number
        $staff_name = $staffData[0]; // Extracted staff name
        $comment = $_POST['exit-comment'];
        $date = date('Y-m-d H:i:s', strtotime('+3 hours'));
    
        $sqlExitStaff = "UPDATE staff SET status='exited', exit_comment='$comment', exited_date='$date' WHERE staff_phone = '$staff_no'";
    
        if ($conn->query($sqlExitStaff)) {
            echo "Successfully exited " . "<b>" . $staff_name;
            exit();
        } else {
            echo "Error: " . $sqlExitStaff . "<br>" . $conn->error;
        }
    }
    

?>

<!DOCTYPE html>    
<html>
    <h1> Remove Staff</h1>
    
    <form style="width: 80%;" method="POST" action="">
        <br>
         Select Staff:
         <br>
            <?php

                // SQL query to fetch staff from the staff table
                $sqlStaffList = "SELECT staff_name, staff_phone FROM staff WHERE status='active'";
                
                // Execute the query
                $result = $conn->query($sqlStaffList);
                
                // Check if there are any rows returned
                if ($result->num_rows > 0) {
                    // Start building the dropdown list
                    echo '<select name="staff-details" id="staff-details" style="height:30px;">';
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
        <input type="text" placeholder="Exit Comment" name="exit-comment" style="width: 50%; height: 100px;" required>
        <br>
        <br>        
        <input type="submit" value="Remove Staff" name="submitStaff" style="width: 120px; height: 30px; padding: 4px;">
    </form>
    <?php include 'templates/sessionTimeoutH.php'; ?>
    
</html>


<?php
    $conn->close();
?>