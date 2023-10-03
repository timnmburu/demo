<?php
    use Dotenv\Dotenv;

    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/emailing.php';
    require_once 'templates/cryptOtp.php';
    require_once 'templates/standardize_phone.php';

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
    if(isset($_POST['submitAdmin'])){
        $username = $_POST['username'];
        $email = $_POST['staff_email'];
        $superAdmin = $_POST['super_admin'];
        $password = encryptOtp('admin123');
        $token = '';
        $lastReset = '';
        
        $sqlGetStaffPhone = $conn->query("SELECT staff_phone FROM staff WHERE staff_email='$email'");
        $rows = $sqlGetStaffPhone->fetch_assoc();
        $staff_phone = $rows['staff_phone'];
        
        $phone = '254' . standardizePhoneNumber($staff_phone);
        
        //Get CustID of the user business
        $sqlGetCustID = $conn->query("SELECT custID FROM users ORDER BY id LIMIT 1");
        $rowsCustID = $sqlGetCustID->fetch_assoc();
        $custID = $rowsCustID['custID'];
        
        $sqlNewAdmin = "INSERT INTO users (username, password, email, phone, token, lastResetDate, custID) 
        VALUES ('$username','$password', '$email', '$phone', '$token','$lastReset', '$custID')";
        
        $resetRole = "UPDATE staff SET role='admin' WHERE staff_email='$email'";
        
        if ($conn->query($sqlNewAdmin) === TRUE && $conn->query($resetRole) === TRUE) {
            $passToken = substr(str_shuffle("0123456789aaaaabbbbbcccccddddddeeeee"), 0, 15);
            $current_date_time = date('Y-m-d H:i:s', strtotime('+3 hours'));
            $passTokenEncrypted = encryptOtp($passToken);
            
            $addPassToken = "UPDATE users SET token='$passTokenEncrypted', password='', lastResetDate='$current_date_time' WHERE email='$email'";
            
            $conn->query($addPassToken);
            
            //SetAdmin or Super Admin Role
            if($superAdmin == "Yes"){
                $conn->query("UPDATE users SET role='admin' WHERE email='$email'");
                $role = 'Super Admin';
            } else {
                $role = 'Admin';
            }
            
            echo "Successfully added " . "<b>" . $username . "</b>" . " as an " . "<b>" . $role . "</b>";
            
            //Send email of Password Token for resetting
            $subject = 'New Admin Credentials';
            $body = 'Hi '. $email . ' <br> <br> Your admin credentials have been created successfully' . '<br> Username: <b>'. $username .' </b>. <br> Password: <b>' . $passToken . '</b> <br> <br> Please copy to use that password to set your own password via this link https://www.essentialtech.site/demo/new_password' . '<br> <br> Thank you. <br> <br> If you experience any challenge, please notify Admin immediately!';
            
            $replyTo = "info@essentialtech.site";
        
            sendEmail($email, $subject, $body, $replyTo);
            
            exit();
        } else {
            echo "Error: " . $sqlNewAdmin . "<br>" . $conn->error;
        }
    }
    
    //Update admin roles either Super Admin or Admin
    if(isset($_POST['updateAdmin'])){
        $username = $_POST['adminUsername'];
        $superAdmin = $_POST['super_admin'];
        
        if($superAdmin === 'Yes'){
            $admin = 'admin';
            $role = 'a Super Admin';
        } else {
            $admin = 'NULL';
            $role = 'an Admin';
        }
        
        $conn->query("UPDATE users SET role='$admin' WHERE username='$username'");
        
        echo "Successfully updated " . "<b>" . $username . "</b>" . " as " . "<b>" . $role . "</b>";
    }
    
    // Retrieve staff email addresses from the staff table
    $sqlStaff = "SELECT staff_email FROM staff WHERE role='staff' AND status='active'";
    $resultStaff = $conn->query($sqlStaff);
    
    $staff_email_ = null;
    if(isset($_GET[''])){
        $staff_email_ = $_GET['emailSelected'];
    } else {
        $staff_email_ = null;
    }
    
    // Retrieve admins from the users table
    $currentUser = $_SESSION['username'];
    
    $sqlAdmin = "SELECT * FROM users WHERE username <> '$currentUser'";
    $resultAdmin = $conn->query($sqlAdmin);

?>

<!DOCTYPE html>    
<html>
    <h1> Create Admin</h1>
    <div class="newAdmin" style="border:1px solid black; display:flex; padding:4px;">
        <form style="width: 80%;" method="POST" action="">
            <input placeholder="Staff Username" type="text" name="username" style="width: 95%; height: 30px;" required>
            <br> 
            <br>
            <select name="staff_email" style="width: 80%; height: 30px;" required>
                <?php
                    if($staff_email_ !== null){
                        echo "<option value='$staff_email_'</option>";
                    } else {
                        // Generate dropdown options from staff email addresses
                        while ($rowStaff = $resultStaff->fetch_assoc()) {
                            $email = $rowStaff['staff_email'];
                            echo "<option value=\"$email\">$email</option>";
                        }
                    }
                ?>
            </select> <br><br>
            Super Admin? 
            <br>
            <select name="super_admin">
                <option value="No" >No</option>
                <option value="Yes" >Yes</option>
            </select>
            <br>
            <br>
            <input type="submit" value="Add New Admin" name="submitAdmin" style="width: 120px; height: 30px; padding: 4px;">
        </form>
    </div>
    
    <h1> Update Admin Role</h1>
    <div class="newSuperAdmin" style="border:1px solid black; display:flex; padding:4px;">
        <form style="width: 80%;" method="POST" action="">
            
            <select name="adminUsername" style="width: 80%; height: 30px;" required>
                <?php
                    // Generate dropdown options from staff email addresses
                    while ($rowAdmin = $resultAdmin->fetch_assoc()) {
                        $adminUsername = $rowAdmin['username'];
                        echo "<option value=\"$adminUsername\">$adminUsername</option>";
                    }
                ?>
            </select> <br><br>
            Super Admin? 
            <br>
            <select name="super_admin">
                <option value="No" >No</option>
                <option value="Yes" >Yes</option>
            </select>
            <br>
            <br>
            <input type="submit" value="Update Role" name="updateAdmin" style="width: 120px; height: 30px; padding: 4px;">
        </form>
    </div>
    
    
    <?php include 'templates/sessionTimeoutH.php'; ?>
    
</html>


<?php
    $conn->close();
?>