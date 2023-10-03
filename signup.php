<?php 

    require_once 'vendor/autoload.php';
    require_once 'templates/sendsms.php';
    require_once 'templates/emailing.php';
    require_once 'templates/notifications.php';
    require_once 'templates/getGeoLocation.php';
    require_once 'templates/cryptOtp.php';
    
    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    
    // Database connection
    $servername = $_ENV['DB_HOST'];
    $usernameD = $_ENV['DB_USERNAME'];
    $passwordD = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = mysqli_connect($servername, $usernameD, $passwordD, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    //Get Geo Location
    notify(getGeoLocation($_ENV['IPinfo_API_KEY']));
    
    //Send Signup request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $bizname = $_POST['bizname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        //Generate login code
        $code = substr(str_shuffle("0123456789"), 0, 6);
        //$hashedCode = password_hash($code, PASSWORD_DEFAULT);
        
        if (strlen($phone) === 12){
            //Check whether DEMO user has signup before
            $stmt = $conn->prepare("SELECT * FROM signup WHERE phone=?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $result = $stmt->get_result();
            $num_rows = $result->num_rows;
            $codeEncrypt = encryptOtp($code);
            
            if ($num_rows > 0) {
                //if DEMO user has signup before
                $stmt = $conn->prepare("UPDATE signup SET code=? WHERE phone=? ");
                $stmt->bind_param("ss", $codeEncrypt, $phone);
                
            } else {
                //if it is a new DEMO user
                $stmt = $conn->prepare("INSERT INTO signup (`bizname`, `email`, `phone`, `code`) VALUES (?,?,?,?)");
                $stmt->bind_param("ssss",$bizname, $email, $phone, $codeEncrypt);
            }
            
            //set correct phone format
            $recipient = '+254' . substr($phone, -9);
            
            if ($stmt->execute()) {
                //header("Location: login");
                //Send email to user
                $subject = "DEMO SIGNUP SUCCESSFUL";

                $body = 'Hello ' . $bizname . ', thank you for your interest in our services. Use code: ' . $code . ' for each Login to the Demo. Login here www.essentialtech.site/demo';
                
                $replyTo = "info@essentialtech.site";
                
                sendEmail($email, $subject, $body, $replyTo);
                
                //Send SMS of code to user
                $message = 'Hello ' . $bizname . ', thank you for your interest in our services. Use code: ' . $code . ' for each Login to the Demo. Login here www.essentialtech.site/demo';
                
                //send SMS
                sendSMS($recipient, $message);
                
                $error_message = "Signup successful. Check your email/phone for your login code.";
                echo "<script> alert('$error_message');</script>";
                echo "<script> window.location.href ='login';</script>";
                
                $notification = 'New signup on Demo by:;' . $recipient . ' Email:' . $email;
                notify($notification);
                
                exit;
            } else {
                // login failed, show an error message to the user
                $error_message = "Signup error. Please contact us for assistance via info@essentialtech.site";
                $notification = 'Signup error on Demo by:;' . $recipient . ' Email:' . $email;
                notify($notification);
                exit;
            }
        } else {
            $error_message = "Signup error. Enter correct phone number.";
            echo "<script> alert('$error_message');</script>";
            echo "<script> window.location.href ='signup';</script>";
            
            exit;
        }
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>DEMO Sign Up</title>
        
        <!-- Bootstrap Css -->
        <link href="bootstrap.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        
        <!-- App favicon -->
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
        
    </head>
    <body class="auth-body-bg">
        <div class="bg-overlay"></div>        
        <div class="wrapper-page">
            <div class="container-fluid p-0">
                <div class="card">
                    <div class="card-body">
                        <main class="py-0">
                            <div class="card card-success"> 
                                <div class="card-header p-0 auth-header-box">
                                    <div class="text-center p-3">
                                        <a href="" class="logo logo-admin">
                                            <img src="logos/Logo.jpg" height="80" alt="logo" class="auth-logo">
                                        </a>
                                        <h4 class="mt-3 mb-1 fw-semibold font-18">DEMO Sign Up</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3">
                                <?php if (isset($error_message)) { ?>
                                    <p style="color: red;"><?php echo $error_message; ?></p>
                                <?php } ?>
                                <form id="signup" class="form-horizontal mt-3" action="" method="POST">
                                    <!--
                                    <div class="form-group mb-3 row">
                                        <div class="col-12">
                                            Your Phone Number:<br>
                                            <input id="phone" name="phone" class="form-control" type="number" value="" placeholder="Your Phone" autofocus/> <br>
                                            <button name="sendCode" value="sendCode" type="submit">Request New Code</button>
                                        </div>
                                    </div>
                                    style="color: green; "
                                    -->
                                    
                                    <div class="form-group mb-3 row">
                                        <div class="col-12">
                                            <span>
                                                Sign Up to get the Code that you will use for each Demo Login. 
                                            </span>
                                            <br><br>
                                            <input id="bizname" name="bizname" class="form-control" type="text" required placeholder="Business Name" autofocus/>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3 row">
                                        <div class="col-12">
                                            <input id="email" name="email" class="form-control" type="email" placeholder="Email" required/>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3 row">
                                        <div class="col-12">
                                            <input id="phone" name="phone" class="form-control" type="number" placeholder="Phone Number" required oninput="checkCharacterCount(this)"/>
                                        </div>
                                        <div class="col-12">
                                            <br>
                    					<input type="checkbox" name="TnCs" required> I confirm that Excel Tech Essentials may process and store my personal data provided in this form to reach out to me from time to time. *
        					            </div> <br>
                                    </div>
                                    <!--
                                    <div class="g-recaptcha" data-sitekey="6Lf0x3gnAAAAAD-kvGHVFZgpvZsoBmp5D2NGJXHY">
                                    </div>
                                    -->
                                    <div class="form-group mb-3 text-center row mt-3 pt-1">
                                        <div class="col-12">
                                            <button class="g-recaptcha btn btn-sm btn-success w-100 waves-effect waves-light" data-sitekey="6LfizHgnAAAAAPXwDiinsPL-TBXpLYx_YSIIa-Pi" data-callback='onSubmit' data-action='submit'>Signup</button>
                                            <br>
                                            <br>
                                            <a href="login"> Login?</a>
                                            <span>        </span>
                                            
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </main>
                    </div>
                </div>
            </div>
        </div>
        <script>
            //Character count in the phone input box
                function checkCharacterCount(inputElement) {
                    var prefix = '254';
                    var suffix = inputElement.value.substring(prefix.length); 
                    inputElement.value = prefix + suffix;
                    
                    const inputValue = inputElement.value.trim(); // Remove leading/trailing spaces
                    let maxLength;
                
                    if (inputValue.startsWith("+")) {
                        maxLength = 13;
                    } else if (inputValue.startsWith("2")) {
                        maxLength = 12;
                    } else if (inputValue.startsWith("0")) {
                        maxLength = 10;
                    } else {
                        // Default maximum length
                        maxLength = 13;
                    }
                
                    if (inputValue.length > maxLength) {
                        inputElement.value = inputValue.slice(0, maxLength); // Truncate the input to the maximum length
                    }
                }
                
        </script>
        
        <script>
           function onSubmit(token) {
             document.getElementById("signup").submit();
           }
         </script>
    </body>
</html>