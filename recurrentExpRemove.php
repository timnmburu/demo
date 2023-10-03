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
    if(isset($_POST['submitExpense'])){
        $expName = $_POST['expenseName'];
        
        $sqlMonthlyTarget = "SELECT monthlyTarget FROM target";
        $resultMonthlyTarget = mysqli_query($conn, $sqlMonthlyTarget);
        
        if ($resultMonthlyTarget && mysqli_num_rows($resultMonthlyTarget) > 0) {
            $row = mysqli_fetch_assoc($resultMonthlyTarget);
            $monthlyTarget = $row['monthlyTarget'];
        }
        
        $sqlExpAmount = "SELECT amount FROM recurrentExp WHERE name='$expName'";
        $resultExpAmount = mysqli_query($conn, $sqlExpAmount);
        $rowExpAmount = mysqli_fetch_assoc($resultExpAmount);
        $expAmount = $rowExpAmount['amount'];
        
        $newTarget = $monthlyTarget - $expAmount;
        
        $sqlRemoveExpense = "DELETE FROM recurrentExp WHERE name = '$expName'";
        
        $sqlReduceTarget = "UPDATE target SET monthlyTarget='$newTarget'";
        
        if ($conn->query($sqlRemoveExpense) && $conn->query($sqlReduceTarget)) {
            echo "Successfully removed " . "<b>" . $expName . "</b>" . " as a Recurrent Expenditure ";
            exit();
        } else {
            echo "Error: " . $sqlRemoveExpense . "<br>" . $conn->error;
        }
    }
    
    // Retrieve staff email addresses from the staff table
    $sqlExpenses = "SELECT name FROM recurrentExp";
    $resultExpenses = $conn->query($sqlExpenses);

?>

<!DOCTYPE html>    
<html>
    <h1> Remove Recurrent Expense</h1>
    
    <form style="width: 80%;" method="POST" action="">
        <br>
        <select name="expenseName" style="width: 50%;" required>
            <?php
                // Generate dropdown options from recurrentExp
                while ($rowExpenses = $resultExpenses->fetch_assoc()) {
                    $name = $rowExpenses['name'];
                    echo "<option value=\"$name\">$name</option>";
                }
            ?>
        </select>
        <br>
        <br>
        <input type="submit" value="Remove Expense" name="submitExpense" style="width: 120px; height: 30px; padding: 4px;">
    </form>
    <?php include 'templates/sessionTimeoutH.php'; ?>
    
</html>


<?php
    $conn->close();
?>