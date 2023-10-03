<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['redirect_url'] = 'performance'; // Store the target page URL
    header('Location: login'); // Redirect to the login page
    exit;
} else {
    // Check if the session username is neither "Tim" nor "Millie"
    if ($_SESSION['username'] != 'Tim' && $_SESSION['username'] != 'Millie') {
        session_unset(); // Clear all session variables
        session_destroy(); // Destroy the session
        header('Location: admins'); // Redirect to the admins page
        exit; 
    }
}

$conn = conn();

// Query to get the total sum of payments
$sqlTotalPayments = "SELECT (
    SELECT SUM(amount) FROM payments
) AS total_payments";
$resultPayment = mysqli_query($conn, $sqlTotalPayments);

// Check if the query was successful
if ($resultPayment) {
    // Fetch the result row
    $row = mysqli_fetch_assoc($resultPayment);

    // Get the total sum of payments from the result
    $totalPayments = $row['total_payments'];
}    

// Query to get the total sum of expenses
$sqlTotalExpenses = "SELECT (
    SELECT SUM(price) FROM expenses
) AS total_expenses";
$resultExpenses = mysqli_query($conn, $sqlTotalExpenses);

// Check if the query was successful
if ($resultExpenses) {
    // Fetch the result row
    $row = mysqli_fetch_assoc($resultExpenses);

    // Get the total sum of payments from the result
    $totalExpenses = $row['total_expenses'];
}   

$income = $totalPayments - $totalExpenses;

$percentage = $income / $totalPayments * 100;
$percent = $percentage . "%";


//Insert the values in the performance table
//$sqlPerformance = "INSERT INTO `performance`(`cashIn`, `cashOut`, `income`, `percent`) VALUES ('$totalPayments', '$totalExpenses', '$income', '$percent')";
$sqlPerformance = "UPDATE `performance` SET `cashIn`='$totalPayments',`cashOut`='$totalExpenses',`income`='$income',`percent`='$percent' WHERE `cashIn`>0";
$resultUpdate = $conn->query($sqlPerformance);

if ($resultUpdate) {
        //echo "Total payments inserted successfully into the performance table.";
        //header ("Location: /performance");
        //echo "<script> <html> <a href = 'www.lfhcompany.site/performance'></a> </html> </script>";
    } else {
        echo "Error inserting total payments into the performance table: " . mysqli_error($conn);
    }

?>
