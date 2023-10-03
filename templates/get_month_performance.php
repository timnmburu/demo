<?php

    use Dotenv\Dotenv;
    
    require_once __DIR__ . '/../vendor/autoload.php'; // Include the Dotenv library
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];

    // Connect to the database and retrieve data from the table
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    // Get the selected month from the AJAX request
    $selectedMonth = $_GET['month'];
    
    //Get Monthly Target
    $sqlMonthlyTarget = "SELECT monthlyTarget FROM target";
    $resultMonthlyTarget = mysqli_query($conn, $sqlMonthlyTarget);
    
    if ($resultMonthlyTarget && mysqli_num_rows($resultMonthlyTarget) > 0) {
        $row = mysqli_fetch_assoc($resultMonthlyTarget);
        $monthlyTarget = $row['monthlyTarget'];
    }
    
    // Validate the selected month to prevent SQL injection (you can use more robust validation as needed)
    if (!preg_match('/^[a-zA-Z]+$/', $selectedMonth)) {
        echo "Invalid month selected.";
        exit;
    }
    
    // Query to get the monthly performance for the selected month
    $sqlSelectedMonthPayments = "SELECT 
                                    DATE_FORMAT(date, '%M') AS month,
                                    SUM(amount) AS total_amount,
                                    COUNT(*) AS payment_count
                                FROM payments 
                                WHERE DATE_FORMAT(date, '%M') = '$selectedMonth'
                                GROUP BY MONTH(date)";
    
    $resultSelectedMonthPayments = mysqli_query($conn, $sqlSelectedMonthPayments);
    
    // Build the HTML table for the selected month's performance
    $tableBody = '';
    if ($resultSelectedMonthPayments) {
        while ($row = mysqli_fetch_assoc($resultSelectedMonthPayments)) {
            $tableBody .= "<tr>";
            $tableBody .= "<td>" . $row["month"] . "</td>";
            $tableBody .= "<td>" . $row["total_amount"] . "</td>";
            $tableBody .= "<td>" . $row["payment_count"] . "</td>";
            $tableBody .= "<td>" . $monthlyTarget . "</td>";
            $tableBody .= "<td>" . ($row["total_amount"] - $monthlyTarget) . "</td>";
            $tableBody .= "<td>" . ($row["total_amount"] / $monthlyTarget * 100) . "%</td>";
            $tableBody .= "</tr>";
        }
    } else {
        $tableBody .= "<tr><td colspan='6'>No results found.</td></tr>";
    }
    
    // Return the table body content only
    echo $tableBody;

?>
