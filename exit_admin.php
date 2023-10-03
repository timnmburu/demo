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
    if(isset($_POST['submitAdmin'])){
        $userN = $_POST['adminName'];

        
        $sqlRemoveAdmin = "DELETE FROM users WHERE username = '$userN'";
        
        if ($conn->query($sqlRemoveAdmin)) {
            echo "Successfully removed " . "<b>" . $userN . "</b>" . " as an " . "<b>" . "Admin" . "</b>";
            exit();
        } else {
            echo "Error: " . $sqlRemoveAdmin . "<br>" . $conn->error;
        }
    }
    
    // Retrieve staff email addresses from the staff table
    $sqlStaff = "SELECT username FROM users";
    $resultStaff = $conn->query($sqlStaff);

?>

<!DOCTYPE html>    
<html>
    <h1> Remove Admin</h1>
    
    <form style="width: 80%;" method="POST" action="">
        <br>
        <select name="adminName" style="width: 50%;" required>
            <?php
                // Generate dropdown options from staff email addresses
                while ($rowStaff = $resultStaff->fetch_assoc()) {
                    $username = $rowStaff['username'];
                    echo "<option value=\"$username\">$username</option>";
                }
            ?>
        </select>
        <br>
        <br>
        <input type="submit" value="Remove Admin" name="submitAdmin" style="width: 120px; height: 30px; padding: 4px;">
    </form>
    <?php include 'templates/sessionTimeoutH.php'; ?>
    
</html>


<?php
    $conn->close();
?>