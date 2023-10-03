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
    if (isset($_POST['stopOffer'])) {
        $selectedOffer = $_POST['offer-details']; // Format: "staff_name - staff_phone"
        $offerData = explode(' - ', $selectedOffer);
        $end_date = $offerData[1]; // Extracted staff phone number
        $offer_name = $offerData[0]; // Extracted staff name
        $date = date('Y-m-d', strtotime('+3 hours'));
        
        $comment = 'Stopped ' . $date;
    
        $sqlStopOffer = "UPDATE offers SET status='$comment' WHERE offer_name = '$offer_name'";
    
        if ($conn->query($sqlStopOffer)) {
            echo "Successfully stopped " . "<b>" . $offer_name;
            exit();
        } else {
            echo "Error: " . $sqlStopOffer . "<br>" . $conn->error;
        }
    }
    

?>

<!DOCTYPE html>    
<html>
    <h1> Stop Offer</h1>
    
    <form style="width: 80%;" method="POST" action="">
        <br>
         Select Offer to Stop:
         <br>
            <?php
                
                // SQL query to fetch active offers from the offers table
                $sqlOfferList = "SELECT offer_name, end_date FROM offers WHERE status='Active'";
                
                // Execute the query
                $result = $conn->query($sqlOfferList);
                
                // Check if there are any rows returned
                if ($result->num_rows > 0) {
                    // Start building the dropdown list
                    echo '<select name="offer-details" id="offer-details" style="height:30px;">';
                    // Loop through the rows and add options to the dropdown list
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['offer_name'] . ' - ' . $row['end_date'] . '">' . $row['offer_name'] . ' - ' . $row['end_date'] . '</option>';
                    }
                    // Close the dropdown list
                    echo '</select>';
                } else {
                    echo 'No offer found.';
                }
            ?>

        <br>
        <br>
        <input type="submit" value="Stop Offer" name="stopOffer" style="width: 120px; height: 30px; padding: 4px;">
    </form>
    <?php include 'templates/sessionTimeoutH.php'; ?>
    
</html>


<?php
    $conn->close();
?>