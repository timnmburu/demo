<?php
    use Dotenv\Dotenv;
    
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    //$conn = conn();
    
    session_start();
    if (!isset($_SESSION['username'])) {
        $_SESSION['redirect_url'] = 'admins'; // Store the target page URL
        session_unset();
        header('Location: login'); // Redirect to the login page
        exit;
    } elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false || $_SESSION['admin'] === false){
        header('Location: admins');
    }
    
    //include "templates/updateMpesaBalance.php";
    
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];

    // Database connection
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    
    //Get Mpesa Balance
    $sqlBal = "SELECT * FROM wallet";
    
    $result1 = $conn->query($sqlBal);

    // Loop through the table data and generate HTML code for each row
    if ($result1->num_rows > 0) {
        while ($row = $result1->fetch_assoc()) {
            $accBalMpesa= '100';
        }
    } else {
        $accBalMpesa = 0;
    }
    
    // Initialize Payment MODE to individual by default
    $payMode = '1'; //1=Individual, 2=Buy Goods, 3=Paybill, 4=Bank
    
    // Check if the Edit button is clicked
    if (isset($_GET['mode'])) {
        // User is authorized to export tables
        $payMode = $_GET['mode'];
    } else {
        $payMode = '1';
    }
    
?>
<!DOCTYPE html>
<html en-US>
    <head>
        <title>Withdraw</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css"/>
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
        
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            
            th, td {
                text-align: left;
                padding: 8px;
                border: 2px solid #ddd;
            }
            
            th {
                background-color: #f2f2f2;
            }
            
            .tab-nav {
              list-style: none;
              padding: 0;
              margin: 0;
              display: flex;
            }
            
            .tab-nav li {
              cursor: pointer;
              padding: 10px 20px;
              background-color: #f1f1f1;
              border: 1px solid #ccc;
            }
            
            .tab-nav li.active {
              background-color: #ddd;
            }
            
            /* CSS for the tab content */
            .inputPaymentInfo {
              display: flex;
              flex-direction: column;
            }
            
            .tab-pane {
              display: none;
              padding: 20px;
              border: 1px solid #ccc;
            }
            
            .tab-pane.active {
              display: block;
            }
            
        </style>
    </head> 
    
    <body class="body">
        <?php include "templates/header-admins.php" ?>
        
        <div class="body-content">
            <br>        
            <p><h2>Process Payment </h2></p>

            <ul class="tab-nav">
                <li  onclick="changeTab(0)">Individual</li>
                <li  onclick="changeTab(1)">Buy-Goods Till</li>
                <li  onclick="changeTab(2)">Paybill</li>
                <li  onclick="changeTab(3)">Bank</li>
            </ul>
            <div id="inputPaymentInfo" >
                <div class="tab-pane  <?php if($payMode === '1'){ echo 'active'; } ?>">
                    <!--Payment entry input boxes for Individual Payment -->
                    <div id="individual" class="individual" style="<?php //if ($payMode !== '1') {echo 'display:none;';} ?> border:1px solid grey; padding:5px;" >
                        <input type="number" placeholder="<?php echo "Enter phone.."; ?>" id="account-1" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <input type="number" placeholder="<?php echo "Enter amount.."; ?>" id="amount-1" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <input type="text" placeholder="<?php echo "Payment reason.."; ?>" id="comment1" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <!--Display wallet Balances-->
                        <input disabled value= " Mpesa Bal = <?php echo number_format($accBalMpesa-50, 2); ?>" name="accBal" style=" border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <!--Pay button -->
                        <button  style="border:2px solid grey; width:130px; height:40px;" onclick="openSendMoney()"><b>Send Money</b></button>
    
                    </div>
                </div>
                
                <div class="tab-pane  <?php if($payMode === '2'){ echo 'active'; } ?>">
                    <!--Payment entry input boxes for Buy Goods Till Payment -->
                    <div id="buygoods" class="buygoods" style="<?php //if ($payMode !== '2') {echo 'display:none;';} ?> border:1px solid grey; padding:5px;" >
 
                        <input type="number" placeholder="<?php echo "Enter Till No..."; ?>" id="account-2" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <input type="number" placeholder="<?php echo "Enter amount.."; ?>" id="amount-2" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <input type="text" placeholder="<?php echo "Payment reason.."; ?>" id="comment2" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <!--Display wallet Balances-->
                        <input disabled value= " Mpesa Bal = <?php echo number_format($accBalMpesa-50, 2); ?>" name="accBal" style=" border:2px solid grey; width:80%; height:35px;">
                        <br><br>
    
                        <!--Pay button -->
                        <button  style="border:2px solid grey; width:130px; height:40px;" onclick="verification()"><b>Send Money</b></button>
                    </div>
                </div>
                
                <div class="tab-pane  <?php if($payMode === '3'){ echo 'active'; } ?>">
                    <!--Payment entry input boxes for Paybill Payment -->
                    <div id="paybill" class="paybill" style="<?php //if ($payMode !== '3') {echo 'display:none;';} ?> border:1px solid grey; padding:5px;" >
 
                        <input type="number" placeholder="<?php echo "Enter Paybill No..."; ?>" id="account-3" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                    
                        <input type="text" placeholder="<?php echo "Enter Account Number..."; ?>" id="account-reference-3" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <input type="number" placeholder="<?php echo "Enter amount.."; ?>" id="amount-3" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <input type="text" placeholder="<?php echo "Payment reason.."; ?>" id="comment3" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <!--Display wallet Balances-->
                        <input disabled value= " Mpesa Bal = <?php echo number_format($accBalMpesa-50, 2); ?>" name="accBal" style=" border:2px solid grey; width:80%; height:35px;">
                        <br><br>
    
                        <!--Pay button -->
                        <button  style="border:2px solid grey; width:120px; height:40px;" onclick="verification()"><b>Send Money</b></button>
                    </div>
                </div>
                
                <div class="tab-pane  <?php if($payMode === '4'){ echo 'active'; } ?>">
                    <!--Payment entry input boxes for Bank Payment -->
                    <div id="bankMode" class="bankMode" style="<?php //if ($payMode !== '4') {echo 'display:none;';} ?> border:1px solid grey; padding:5px;" >
                        
                        <!--<input type="text" placeholder="<?php echo "Enter Bank Code..."; ?>" id="bank-code-4" style="border:2px solid grey; width:120px; height:35px;"> -->
                        <select id="bankSelect" style="border:2px solid grey; width:80%; height:35px;" ></select>
                        <br><br>
                        
                        <input type="number" placeholder="<?php echo "Enter account no..."; ?>" id="account-4" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <input type="number" placeholder="<?php echo "Enter amount.."; ?>" id="amount-4" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <input type="text" placeholder="<?php echo "Payment reason.."; ?>" id="comment4" style="border:2px solid grey; width:80%; height:35px;">
                        <br><br>
                        
                        <!--Display wallet Balances-->
                        <input disabled value= " Mpesa Bal = <?php echo number_format($accBalMpesa-50, 2); ?>" name="accBal" style=" border:2px solid grey; width:80%; height:35px;">
                        <br><br>
    
                        <!--Pay button -->
                        <button  style="border:2px solid grey; width:130px; height:40px;" onclick="verification()"><b>Send Money</b></button>
                    </div>
                </div>
            
            </div>
            <br><br>
            <div>
                Frequent Payments 
                <button  onclick= "new_frequent_payment()" style="border:2px solid grey; width:180px; height:30px;"> Add Frequent Payment</button>
                <br>
                <table id="frequent-payments">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Wallet</th>
                        <th>Account</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
            
                    $sqlFrequentPayments = "SELECT * FROM frequentPayments ";
            
                    $result = $conn->query($sqlFrequentPayments);
            
                    // Loop through the table data and generate HTML code for each row
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row["s_no"] . "</td>
                                    <td>" . $row["name"] . "</td>
                                    <td>" . $row["wallet"] . "</td>
                                    <td>" . $row["account"] . "</td>
                                    <td>" . $row["reference"] . "</td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No results found.</td></tr>";
                    }
            
                    ?>
                </tbody>
            </table>
                
            </div>
        </div>
        <br>
        <br>
        

        <script>
            //change tabs from New booking to Booking confirmation
            function changeTab(tabIndex) {
                const tabs = document.querySelectorAll('.tab-pane');
                const tabNavItems = document.querySelectorAll('.tab-nav li');
                
                tabs.forEach((tab, index) => {
                    if (index === tabIndex) {
                        tab.classList.add('active');
                        tabNavItems[index].classList.add('active');
                    } else {
                        tab.classList.remove('active');
                        tabNavItems[index].classList.remove('active');
                    }
                }); 
            }
            
        //Populate bank list for selection
            document.addEventListener('DOMContentLoaded', function () {
              // Wait for the DOM to be fully loaded before populating the drop-down list
            
              const bankSelect = document.getElementById('bankSelect');
            
              // Your bank data as an array
              const bankData = [
                {
                    "bank_name": "KCB",
                    "bank_code": "1"
                },
                {
                    "bank_name": "Standard Charted Bank KE",
                    "bank_code": "2"
                },
                {
                    "bank_name": "Barclays Bank",
                    "bank_code": "3"
                },
                {
                    "bank_name": "NCBA",
                    "bank_code": "7"
                },
                {
                    "bank_name": "Prime Bank",
                    "bank_code": "10"
                },
                {
                    "bank_name": "Cooperative Bank",
                    "bank_code": "11"
                },
                {
                    "bank_name": "National Bank",
                    "bank_code": "12"
                },
                {
                    "bank_name": "Citibank",
                    "bank_code": "16"
                },
                {
                    "bank_name": "Habib Bank AG Zurich",
                    "bank_code": "17"
                },
                {
                    "bank_name": "Middle East Bank",
                    "bank_code": "18"
                },
                {
                    "bank_name": "Bank of Africa",
                    "bank_code": "19"
                },
                {
                    "bank_name": "Consolidated Bank",
                    "bank_code": "23"
                },
                {
                    "bank_name": "Credit Bank Ltd",
                    "bank_code": "25"
                },
                {
                    "bank_name": "Stanbic Bank",
                    "bank_code": "31"
                },
                {
                    "bank_name": "ABC Bank",
                    "bank_code": "35"
                },
                {
                    "bank_name": "Spire Bank",
                    "bank_code": "49"
                },
                {
                    "bank_name": "Paramount Universal Bank",
                    "bank_code": "50"
                },
                {
                    "bank_name": "Jamii Bora Bank",
                    "bank_code": "51"
                },
                {
                    "bank_name": "Guaranty Bank",
                    "bank_code": "53"
                },
                {
                    "bank_name": "Victoria Commercial Bank",
                    "bank_code": "54"
                },
                {
                    "bank_name": "Guardian Bank",
                    "bank_code": "55"
                },
                {
                    "bank_name": "I&M Bank",
                    "bank_code": "57"
                },
                {
                    "bank_name": "DTB",
                    "bank_code": "63"
                },
                {
                    "bank_name": "Sidian Bank",
                    "bank_code": "66"
                },
                {
                    "bank_name": "Equity Bank",
                    "bank_code": "68"
                },
                {
                    "bank_name": "Family Bank",
                    "bank_code": "70"
                },
                {
                    "bank_name": "Gulf African Bank",
                    "bank_code": "72"
                },
                {
                    "bank_name": "First Community Bank",
                    "bank_code": "74"
                },
                {
                    "bank_name": "KWFT Bank",
                    "bank_code": "78"
                },
                {
                    "bank_name": "Housing Finance Company Limited (HFCK)",
                    "bank_code": "61"
                },
                {
                    "bank_name": "Mayfair Bank Limited",
                    "bank_code": "65"
                }
              ];
            
              // Loop through the bank data and create options for the select element
              bankData.forEach(bank => {
                const option = document.createElement('option');
                option.text = bank.bank_name + ' - ' + bank.bank_code;;
                option.value = bank.bank_code;
                bankSelect.appendChild(option);
              });
              
                // Add an event listener to the dropdown
              bankSelect.addEventListener('change', function () {
                    // Retrieve the selected bank code and assign it to bank_code4
                    var selectedBankCode = bankSelect.value;
                    var bank_code4 = selectedBankCode;
                    console.log('Selected bank code: ' + bank_code4);
                    localStorage.setItem('bankCode', bank_code4);
                    // You can now use bank_code4 for further processing as needed.
                  });
            });
            
        //Function to prompt payment through OTP page
            function openSendMoney() {
                var urlAll = 'https://www.essentialtech.site/demo/templates/sendMoney.php';
                
            //Details for individual payment
                var account1 = document.getElementById("account-1").value;
                var amount1 = document.getElementById("amount-1").value;
                var reason1 = document.getElementById("comment1").value;
                var comment1 = reason1 + ' ';
                
            //Details for Buy Goods Till payment
                var account2 = document.getElementById("account-2").value;
                var amount2 = document.getElementById("amount-2").value;
                var reason2 = document.getElementById("comment2").value;
                var comment2 = reason2 + ' ';
                
            //Details for Paybill payment
                var account3 = document.getElementById("account-3").value;
                var accountref3 = document.getElementById("account-reference-3").value;
                var amount3 = document.getElementById("amount-3").value;
                var reason3 = document.getElementById("comment3").value;
                var comment3 = reason3 + ' ';
                
            //Details for Bank payment
                var account4 = document.getElementById("account-4").value;
                var amount4 = document.getElementById("amount-4").value;
                var bank_code4 = localStorage.getItem('bankCode');
                var reason4 = document.getElementById("comment4").value;
                var comment4 = reason4 + ' ';
                
            //Process payment as per payment mode selected
                if (account1 && account1.trim() !== '') {
                    var payUrl = urlAll;
                    payUrl += '?name=' + encodeURIComponent("Excel Tech");
                    payUrl += '&account1=' + encodeURIComponent(account1);
                    payUrl += '&amount1=' + encodeURIComponent('1');
                    payUrl += '&reason1=' + encodeURIComponent(comment1);
                } else if (account2 && account2.trim() !== '') {
                    var payUrl = urlAll;
                    payUrl += '?name=' + encodeURIComponent("Excel Tech");
                    payUrl += '&account2=' + encodeURIComponent(account2);
                    payUrl += '&amount2=' + encodeURIComponent('1');
                    payUrl += '&reason2=' + encodeURIComponent(comment2);
                } else if (account3 && account3.trim() !== ''){
                    var payUrl = urlAll;
                    payUrl += '?name=' + encodeURIComponent("Excel Tech");
                    payUrl += '&account3=' + encodeURIComponent(account3);
                    payUrl += '&accountref3=' + encodeURIComponent(accountref3);
                    payUrl += '&amount3=' + encodeURIComponent('1');
                    payUrl += '&reason3=' + encodeURIComponent(comment3);
                } else if (account4 && account4.trim() !== ''){
                    var payUrl = urlAll;
                    payUrl += '?name=' + encodeURIComponent("Excel Tech");
                    payUrl += '&bankcode4=' + encodeURIComponent(bank_code4);
                    payUrl += '&account4=' + encodeURIComponent(account4);
                    payUrl += '&amount4=' + encodeURIComponent('1');
                    payUrl += '&reason4=' + encodeURIComponent(comment4);
                } else {
                    alert("Account can not be empty");
                }
                    
            // Save payment URL for use later on
                let sourceUrl = window.location.href;
                localStorage.setItem('targetUrl', payUrl);
                localStorage.setItem('sourceUrl', sourceUrl);
                //console.log(payUrl);
                
                window.location.href ="templates/setOtp.php";
                
            }
            
            //To open popup window for new_frequent_payment
            function new_frequent_payment() {
                window.open("new_frequent_payment.php", "New Frequent Payment", "width=400, height=400");
            }
        </script>

        <?php //include 'templates/sessionTimeoutH.php'; ?>
    </body>
</html>