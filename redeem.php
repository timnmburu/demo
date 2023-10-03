<?php

    use Dotenv\Dotenv;
    
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    
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
?>

<!DOCTYPE html>
<html>
    <header>
        <h1> Redeem Points</h1>
    </header>
    <body>
        <div class="redeeming" style="border:1px solid black; display:flex; width: 350px; padding:4px;" >
            <form action="form-process.php" method="POST">
                <label for="search">Search Customer:</label>
                <?php
                    // Check if customer has been selected
                    if (isset($_POST['customer'])) {
                        $selectedCustomer = $_POST['customer'];
                    
                        // SQL query to fetch customer from the customers table
                        $sqlCustomer = "SELECT custName, custPhone, points FROM customers WHERE custPhone = '" . $selectedCustomer . "'";
                    
                        // Execute the query
                        $result = $conn->query($sqlCustomer);
                    
                        // Check if there are any rows returned
                        if ($result->num_rows > 0) {
                            // Get the customer data
                            $row = $result->fetch_assoc();
                            $custName = $row['custName'];
                            $custPhone = $row['custPhone'];
                            $custPoints = $row['points'];
                        }
                    } else {
                        $selectedCustomer = '';
                    }
                    
                    // SQL query to fetch customers from the customers table
                    $sqlCustomers = "SELECT custName, custPhone, pointsBal FROM customers";
                    
                    // Execute the query
                    $result = $conn->query($sqlCustomers);
                    
                    // Check if there are any rows returned
                    if ($result->num_rows > 0) {
                        // Start building the dropdown list
                        echo '<select name="customer">';
                        // Loop through the rows and add options to the dropdown list
                        while ($row = $result->fetch_assoc()) {
                            $selected = ($row['custPhone'] == $selectedCustomer) ? 'selected' : '';
                            echo '<option value="' . $row['custPhone'] . '" ' . $selected . '>' . $row['custName'] . ' - ' . $row['custPhone'] . ' - ' . $row['pointsBal'] . ' points</option>';
                        }
                        // Close the dropdown list
                        echo '</select>';
                    } else {
                        echo 'No customers found.';
                    }
                ?>
                <br>
                <?php
                    if (isset($_POST['customer'])) {
                        echo 'Total Points: ' . $custPoints;
                    }
                ?>
                <br>
                <label for="points">Points to Redeem:</label>
                <input style="width:30%; height:25px; border: 2px solid #000;" type="number" id="points" name="points" min="1" required
                       onchange="document.getElementById('valueToRedeem').value = this.value * 5">
                <br>
                <br>
                Value to Redeem: <input style="width:30%; height:25px; border: 2px solid #000;" type="text" id="valueToRedeem" name="valueToRedeem" readonly>
                <br>
                <br>
                <button type="submit" name="submitRedeem" style="width:100px; height:40px; padding:4px;">REDEEM</button>
            </form>
        </div>
        
        <?php
            $conn->close();
        ?> 
    
    
    </body>
</html>
