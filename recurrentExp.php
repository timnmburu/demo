<?php
    use Dotenv\Dotenv;

    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/emailing.php';

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
        $expenseName = $_POST['name'];
        $amount = $_POST['amount'];
        $date = date('Y-m-d H:i:s', strtotime('+3 hours'));
        
        $sqlNo = "SELECT MAX(s_no) AS highest_value FROM recurrentExp";
        $resultNo = $conn->query($sqlNo);
        $nO = $resultNo->fetch_assoc();
        if ($nO['highest_value'] === null) {
            $lastNo = 0;
        } else {
            $lastNo =$nO['highest_value'];
        }
        
        $nextNo = $lastNo +1; 
        
        
        $sqlTarget = "SELECT SUM(amount) AS total_sum FROM recurrentExp";
        $result = $conn->query($sqlTarget);
        $data = $result->fetch_assoc();
        if ($data['total_sum'] === null) {
            $totalRecurrentExp = $amount;
        } else {
            $totalRecurrentExp =$data['total_sum'] + $amount;
        }
        
        $sqlNewExpense = "INSERT INTO recurrentExp (s_no, name, amount, date, currentTotal) 
        VALUES ('$nextNo', '$expenseName','$amount','$date', '$totalRecurrentExp')";
        
        $sqlNewExpenseHistory = "INSERT INTO expenseHistory (name, amount, date, currentTotal) 
        VALUES ('$expenseName','$amount','$date', '$totalRecurrentExp')";
        
        $baseRevenue = $totalRecurrentExp / 0.65;
        
        $sqlUpdateTarget = "UPDATE target SET monthlyTarget= '$baseRevenue'";

        
        if ($conn->query($sqlNewExpense) && $conn->query($sqlUpdateTarget) && $conn->query($sqlNewExpenseHistory)) {
            echo "Successfully added " . "<b>" . $expenseName . "</b>" . " as a New Recurrent Expense. New Total Recurrent Expenditure is " . $totalRecurrentExp;

        } else {
            echo "Error: " . $sqlNewAdmin . "<br>" . $conn->error;
        }
    }
    
    
    if (isset($_POST['download'])) {
        try {
            // SQL query to fetch data from the 'recurrentExp' table.
            $sqlQuery = "SELECT * FROM recurrentExp";
    
            // Execute the SQL query and get the result.
            $result = $conn->query($sqlQuery);
    
            // Create a buffer to store the CSV data.
            $csvData = '';
    
            // Write column headers to the CSV data.
            $csvData .= "S/No,Name,Amount,Added Date,Cummulative Total\n"; // Replace with your actual column names.
    
            // Write data to the CSV data.
            while ($row = $result->fetch_assoc()) {
                $csvData .= implode(',', $row) . "\n";
            }
    
            // Close the database connection.
            $conn->close();
    
            // Set the appropriate headers to force download.
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="expenses.csv"');
    
            // Output the CSV data.
            echo $csvData;
        } catch (Exception $e) {
            // Handle any errors that occurred during the database connection or query execution.
            echo "Error: " . $e->getMessage();
        }
        exit(); // Exit the script after downloading the data.

    }
    

?>

<!DOCTYPE html>    
<html>
    <h1> Recurrent Expenditure</h1>
    <p>Adding a recurrent expenditure adds onto your monthly target for base revenue</p>
    <div class="newxpense" style="border:1px solid black; display:flex; padding:4px;">
        <form style="width: 80%;" method="POST" action="">
            <input type="text" placeholder="Expense Name" name="name" style="width: 95%; height: 30px;" required>
            <br> 
            <br>
            <input type="text" placeholder="Expense Amount" name="amount" style="width: 95%; height: 30px;" required>
            <br>
            <br>
            <input type="submit" value="Add New Expense" name="submitExpense" style="width: 120px; height: 30px; padding: 4px;">
        </form>
    </div>
    <br> <br>
    <div>
        <form id="download" action="recurrentExp.php" method="POST">
            <input type="submit" value="Download All Recurrent Expenses" name="download" style="width: 210px; height: 30px; padding: 4px;">
        </form>
    </div>
    <?php include 'templates/sessionTimeoutH.php'; ?>
    
</html>


<?php
    $conn->close();
?>