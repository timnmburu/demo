<?php
    use Dotenv\Dotenv;
    
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    //require_once 'templates/get_month_performance.php';
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    session_start();
    if (!isset($_SESSION['username'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: login'); // Redirect to the login page
        exit;
    } elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false || $_SESSION['admin'] === false){
        header('Location: admins');
    }
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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
    
    if($totalPayments > '0'){
        $percentage = $income / $totalPayments * 100;
        $percent = $percentage . "%";
    } else {
        $percent = '0';
    }
    
    date_default_timezone_set('Etc/GMT-3');
    $date = date('Y-m-d H:i:s');
    
    
    //Insert the values in the performance table
    //$sqlPerformance = "INSERT INTO `performance`(`cashIn`, `cashOut`, `income`, `percent`) VALUES ('$totalPayments', '$totalExpenses', '$income', '$percent')";
    $sqlPerformance = "UPDATE `performance` SET `cashIn`='$totalPayments',`cashOut`='$totalExpenses',`income`='$income',`percent`='$percent', `date`='$date' WHERE `cashIn`>0";
    $resultUpdate = $conn->query($sqlPerformance);
    
    if ($resultUpdate) {
            //echo "Total payments inserted successfully into the performance table.";
            //header ("Location: /performance");
            //echo "<script> <html> <a href = 'www.lfhcompany.site/performance'></a> </html> </script>";
            $sqlPerformanceHist = "INSERT INTO `performanceHistory`(`cashIn`, `cashOut`, `income`, `percent`, `date`) VALUES ('$totalPayments', '$totalExpenses', '$income', '$percent', '$date')";
            $conn->query($sqlPerformanceHist);
    } else {
        echo "Error updating total payments into the performance table: " . mysqli_error($conn);
    }
    
    if(isset($_POST["pushNewTarget"])){
        $newTarget = $_POST["newTarget"];
        
        $sqlUpdateTarget = "UPDATE target SET monthlyTarget = '$newTarget' ";
        $conn->query($sqlUpdateTarget);
        
    }
?>

<!DOCTYPE html>
<html>
    <head>
       <title>Performance</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css"/>
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>


        
        <style>
            table {
              border-collapse: collapse;
              width: 100%;
            }
            
            th, td {
              text-align: left;
              padding: 8px;
              border: 3px solid #ddd;
            }
            
            th {
              background-color: #f2f2f2;
            }
        </style>
    </head>
    <body class="body" onload="populateChart()">
                  
        <?php include "templates/header-admins.php" ?>
        
        <div class="body-content">
  
    
            <h1>Performance</h1> 
            
            <p> 
                Find performance in the table below;
            </p>
            <!-- Add a button to export the table to Excel -->
            <?php
                    // Initialize $export_allowed to false by default
                    $export_allowed = false;
                    
                    // Check if the session username is "Tim" or "Millie"
                    if (isset($_SESSION['username']) && ($_SESSION['username'] == $_ENV['ADMIN1'] || $_SESSION['username'] == $_ENV['ADMIN2'])) {
                        // User is authorized to export tables
                        $export_allowed = true;
                    }
            ?>
            <button <?php if (!$export_allowed) { echo 'disabled'; } ?> onclick="exportTableToExcel('performance-table', 'performance')" style="width: 120px; height: 30px; padding: 4px;">Export to Excel</button>
    
    
            <table id="performance-table">
                <thead>
                    <tr>
                        <th><a href="pay">Cash In-Flows</a></th>
                        <th><a href="expenses">Cash Out-Flows</a></th>
                        <th>Income</th>
                        <th>Percentage</th>
                        <th>As At</th>
                    </tr>
                </thead>
                <tbody>
                    
                <?php
                    $sqlPerforming = "SELECT cashIn, cashOut, income, percent, date FROM performance";
                    $resultTable = $conn->query($sqlPerforming);
            
                    // Loop through the table data and generate HTML code for each row
                    if ($resultTable->num_rows > 0) {
                        while($row = $resultTable->fetch_assoc()) {
                            echo "<tr><td>" . $row["cashIn"] . "</td><td>" . $row["cashOut"] . "</td><td>" . $row["income"] . "</td><td>" . $row["percent"] . "</td><td>" . $row["date"] . "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No results found.</td></tr>";
                    }
            
                    //$conn->close();
                ?>
                </tbody>
            </table>
            <br>
            <br>
            To God be the glory!!!!
            <br>
            <div hidden>
                Update Monthly Target
                <form id="updatingTarget" action="" method="POST">
                    <input type="number" name="newTarget">
                    <input type="submit" name="pushNewTarget" value="Update">
                </form>
            </div>
            <br>
            <b>Current Month Performance Table </b>
            
            <!-- Add a button to export the table to Excel -->
            <button <?php if (!$export_allowed) { echo 'disabled'; } ?> onclick="exportTableToExcel('current-month-performance-table', 'WeeklyPerformance')" style="width: 120px; height: 30px; padding: 4px;">Export to Excel</button>
            
            <?php
            // Query to get the list of months that have payments
            $sqlDistinctMonths = "SELECT DISTINCT DATE_FORMAT(date, '%M') AS month
                                  FROM payments
                                  ORDER BY date DESC";
            $resultDistinctMonths = mysqli_query($conn, $sqlDistinctMonths);
            ?>
            
            <!-- Add a dropdown to select months -->
            <select id="month-filter">
                <option value="">Select Month</option>
                <?php
                while ($row = mysqli_fetch_assoc($resultDistinctMonths)) {
                    echo "<option value='" . $row["month"] . "'>" . $row["month"] . "</option>";
                }
                ?>
            </select>
        
            <table id="current-month-performance-table">
                
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Month Payments Total</th>
                        <th>Month Payments No.</th>
                        <th>Month Target</th>
                        <th>Difference</th>
                        <th>% of Target</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        //Get Monthly Target
                    $sqlMonthlyTarget = "SELECT monthlyTarget FROM target";
                    $resultMonthlyTarget = mysqli_query($conn, $sqlMonthlyTarget);
                    
                    if ($resultMonthlyTarget && mysqli_num_rows($resultMonthlyTarget) > 0) {
                        $row = mysqli_fetch_assoc($resultMonthlyTarget);
                        $monthlyTarget = $row['monthlyTarget'];
                    }
                    
                    $currentMonth = date('m');
                    $currentYear = date('Y');
                    // Query to get the weekly total amounts paid with additional columns
                    $sqlCurrentMonthPayments = "SELECT 
                                                DATE_FORMAT(date, '%M') AS month,
                                                SUM(amount) AS total_amount,
                                                COUNT(*) AS payment_count
                                            FROM payments 
                                            WHERE MONTH(date) = $currentMonth AND YEAR(date) = $currentYear
                                            GROUP BY MONTH(date)";
                    $resultMonthPayments = mysqli_query($conn, $sqlCurrentMonthPayments);
            
                    if ($resultMonthPayments) {
                        while ($row = mysqli_fetch_assoc($resultMonthPayments)) {
                            echo "<tr>";
                            echo "<td>" . $row["month"] . "</td>";
                            echo "<td>" . $row["total_amount"] . "</td>";
                            echo "<td>" . $row["payment_count"] . "</td>";
                            echo "<td>" . $monthlyTarget . "</td>";
                            echo "<td>" . $row["total_amount"] - $monthlyTarget . "</td>";
                            echo "<td>" . $row["total_amount"] / $monthlyTarget *100 ."%" . "</td>";                            
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No results found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>            
            <br>
            <br>
            <b>Weekly Payments Table </b>
            
            <!-- Add a button to export the table to Excel -->
            <button <?php if (!$export_allowed) { echo 'disabled'; } ?> onclick="exportTableToExcel('weekly-performance-table', 'WeeklyPerformance')" style="width: 120px; height: 30px; padding: 4px;">Export to Excel</button>
        
            <table id="weekly-performance-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Week</th>
                        <th>Start of Week</th>
                        <th>End of Week</th>
                        <th>Total Amount Paid</th>
                        <th>Customer Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query to get the weekly total amounts paid with additional columns
                    $sqlWeeklyPayments = "SELECT 
                                            DATE_FORMAT(date, '%M') AS month,
                                            WEEK(date) AS week,
                                            MIN(date) AS start_of_week,
                                            MAX(date) AS end_of_week,
                                            SUM(amount) AS total_amount,
                                            COUNT(*) AS payment_count
                                        FROM payments 
                                        GROUP BY MONTH(date), WEEK(date)";
                    $resultWeeklyPayments = mysqli_query($conn, $sqlWeeklyPayments);
            
                    if ($resultWeeklyPayments) {
                        while ($row = mysqli_fetch_assoc($resultWeeklyPayments)) {
                            echo "<tr>";
                            echo "<td>" . $row["month"] . "</td>";
                            echo "<td>" . $row["week"] . "</td>";
                            echo "<td>" . $row["start_of_week"] . "</td>";
                            echo "<td>" . $row["end_of_week"] . "</td>";
                            echo "<td>" . $row["total_amount"] . "</td>";
                            echo "<td>" . $row["payment_count"] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No results found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
    
            <br>
            <br>
            
            <b>Weekly Payment Chart</b>
            
            <select id="chart-type-select" style="width: 120px; height: 30px; padding: 4px;">
                <option value="line">Line Chart</option>
                <option value="bar">Bar Chart</option>
                <option value="pie">Pie Chart</option>
            </select>
            
            <button <?php if (!$export_allowed) { echo 'disabled'; } ?> onclick="exportChart()" style="width: 120px; height: 30px; padding: 4px;">Export Chart</button>
            
            <canvas id="weekly-chart"></canvas>
            
            <?php
                // Query to get the weekly total amounts paid with additional columns
                $sqlWeeklyPayments = "SELECT 
                                DATE_FORMAT(date, '%M') AS month,
                                WEEK(date) AS week,
                                MIN(date) AS start_of_week,
                                MAX(date) AS end_of_week,
                                SUM(amount) AS total_amount,
                                COUNT(*) AS payment_count
                                FROM payments 
                                GROUP BY MONTH(date), WEEK(date)";
                $resultWeeklyPayments = mysqli_query($conn, $sqlWeeklyPayments);
                
                // Initialize arrays to store the data for the chart
                $labels = [];
                $sumData = [];
                $countData = [];
                
                if ($resultWeeklyPayments) {
                    while ($row = mysqli_fetch_assoc($resultWeeklyPayments)) {
                        $labels[] = "Week " . $row["week"];
                        $sumData[] = $row["total_amount"];
                        $countData[] = $row["payment_count"];
                    }
                }
                
                $conn->close();
            ?>
            
            <!--Script to create the weekly chart-->
            <script>
                var ctx = document.getElementById('weekly-chart').getContext('2d');
                var chartTypeSelect = document.getElementById('chart-type-select');
                var chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($labels); ?>,
                        datasets: [{
                            label: 'Total Amount Paid',
                            data: <?php echo json_encode($sumData); ?>,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2
                        }, {
                            label: 'Customer Count',
                            data: <?php echo json_encode($countData); ?>,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                
                // Event listener to update the chart when the chart type is changed
                chartTypeSelect.addEventListener('change', function() {
                    var selectedChartType = chartTypeSelect.value;
                    chart.config.type = selectedChartType;
                    chart.update();
                });
            </script>
    
            <!-- Add a script to export the table to Excel -->
            <script>
                function exportTableToExcel(tableId, filename = 'performance'){
                    var downloadLink;
                    var dataType = 'application/vnd.ms-excel';
                    var tableSelect = document.getElementById(tableId);
                    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
                    
                    // Specify the filename
                    filename = filename?filename+'.xls':'excel_data.xls';
                    
                    // Create download link element
                    downloadLink = document.createElement("a");
                    
                    document.body.appendChild(downloadLink);
                    
                    if(navigator.msSaveOrOpenBlob){
                            var blob = new Blob(['\ufeff', tableHTML], {
                                type: dataType
                            });
                        navigator.msSaveOrOpenBlob( blob, filename);
                    } else {
                        // Create a link to the file
                        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
                        
                        // Setting the file name
                        downloadLink.download = filename;
                        
                        //triggering the function
                        downloadLink.click();
                    }
                }
            </script>
            
            <!--Script to export chart line graph -->
            <script>
                function exportChart() {
                    // Get the chart canvas element
                    var canvas = document.getElementById('weekly-chart');
            
                    // Use html2canvas to capture the chart as an image
                    html2canvas(canvas).then(function (canvas) {
                        // Create a temporary link element
                        var link = document.createElement('a');
                        link.href = canvas.toDataURL('image/png');
                        link.download = 'chart.png';
            
                        // Simulate a click to trigger the download
                        link.click();
                    });
                }
            </script>
            
            <script>
                // Add event listener to the dropdown of month selected for the Current Month Performance
                document.getElementById("month-filter").addEventListener("change", function() {
                    var selectedMonth = this.value;
            
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            var tableBody = document.getElementById("current-month-performance-table").getElementsByTagName('tbody')[0];
                            tableBody.innerHTML = this.responseText;
                        }
                    };
                    xhttp.open("GET", "templates/get_month_performance.php?month=" + selectedMonth, true);
                    xhttp.send();
                });
            </script>


        </div>  
        
        <?php include 'templates/sessionTimeoutH.php'; ?>
        
        <?php include 'templates/scrollUp.php'; ?>
        
        
    </body>
</html>
