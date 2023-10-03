<?php    
    require_once __DIR__ . '/../vendor/autoload.php'; // Include the Dotenv library

    use Dotenv\Dotenv;
    
    session_start();
     
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
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
    
    $date = date('Y-m-d H:i:s', strtotime('+3 hours'));
    

    
    $getSender1 = "SELECT * FROM smsQ where status IS NULL ORDER BY s_no ASC";
    $results = $conn->query($getSender1)->fetch_assoc();
    $result1 = $results['sender1'];
    $rowNo = $results['s_no'];
    
    $getSender2 = "SELECT * FROM smsQ where status IS NULL ORDER BY s_no ASC";
    $results = $conn->query($getSender2)->fetch_assoc();
    $result2 = $results['sender2'];
    
    $_SESSION['redirect_url'] = '/demo/test.php';
    
    if (isset($_POST['updateStatus'])) {
        $updateSMSstatus = ("UPDATE smsQ SET status = 'Sent', dateDelivered='$date' WHERE s_no='$rowNo' ");
    
        $conn->query($updateSMSstatus);
    }
    
    try {
    
        /*
        // CSS styles to center the Processing GIF
        echo '<style>
            .processingGIF {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
        </style>';
        
        
    
        // HTML structure to center the Processing GIF
        echo '<div class="processingGIF">
            <img src="fileStore/processing.gif" alt="Processing" />
        </div>';
          */  
          
        echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
        
        // Sub-function for AJAX handling and redirection
        echo "<script>
            function handleSMSResponse(response) {
                //var redirectUrl = '" . $_SESSION['redirect_url'] . "';
                
                if (response === 'Message Sent:1701') {
                
                    console.log('OTP sent:', response);
                    
                    $.ajax({
                        url: '', 
                        method: 'POST', 
                        data: { updateStatus: true }, 
                        success: function(result) {
                            console.log('Status update response: Updated successfully!');
                           
                        },
                        error: function() {
                            console.log('Error updating status');
                        }
                    });
                    
                } else {
                
                    console.log('OTP sending failed:', response);
                }
            }
            
            function handleAJAXError2() {

            }
            
            function handleAJAXError() {
                $.ajax({
                    url: '$result2',
                    method: 'GET',
                    success: handleSMSResponse,
                    error: handleAJAXError2
                });
            }
    
            $.ajax({
                url: '$result1',
                method: 'GET',
                success: handleSMSResponse,
                error: handleAJAXError
            });
        </script>";
    
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }  
    
    //unset($_SESSION['redirect_url']); 
    
 
    
    
    $conn->close();


?>
