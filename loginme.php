<?php 

    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/notifications.php';
    require_once 'templates/getGeoLocation.php';
    require_once 'templates/cryptOtp.php';
    require_once 'templates/standardize_phone.php';
    
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
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        //$phoneNo = $_POST['phone'];
        //$phone = '254'. standardizePhoneNumber($phoneNo);
        //$code = $_POST['code'];
        
        try{
            //Get Geo Location
            notify(getGeoLocation($_ENV['IPinfo_API_KEY']));
                
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s",$username);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if($result->num_rows === 1){
                $storedPassword = $row['password'];
            } else {
                $storedPassword = '';
            }
            
            $correctPassword = decryptLogin($storedPassword);
            
            if ($password === $correctPassword) {
                //$decryptedCode = decryptOtpSignup($phone);
                
                //if ($code === $decryptedCode) {
                    // login successful, create a session for the user
                    //update account status
                    try{
                        file_get_contents("https://www.essentialtech.site/api/jobs/access_rights/");
                    } catch (Exception $e){
                        session_start();
                        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
                        header("Location: errorpage");
                        exit();
                    }
                    
                    try{
                        include "api/requests/get_account_details/get_account_details.php";
                    } catch (Exception $e){
                        session_start();
                        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
                        header("Location: errorpage");
                        exit();
                    }
                    
                    session_start();
                    
                    $checkAccess = $conn->query("SELECT SUM(subAmount) as totalDue, SUM(lastPayAmount) as totalPaid FROM account");
                    
                    if($checkAccess->num_rows < 1){
                        $_SESSION['access'] = false;
                    } else {
                        $rows = $checkAccess->fetch_assoc();
                        $totalBilled = $rows['totalDue'];
                        $totalPaid = $rows['totalPaid'];
                        
                        if($totalBilled > $totalPaid){
                            $_SESSION['access'] = false;
                            $_SESSION['amount_due'] = $totalBilled - $totalPaid;
                        } else {
                            $_SESSION['access'] = true;
                            $_SESSION['amount_due'] = 0;
                        }
                    }
                    
                    $_SESSION['username'] = $username;
                    
                    $checkUserRole = "SELECT * FROM users WHERE username='$username'";
                    $resultRole = $conn->query($checkUserRole);
                    $rowRole = $resultRole->fetch_assoc();
                    $role = $rowRole['role'];
                    $phone = $rowRole['phone'];
                    
                    $_SESSION['userphone'] = $phone;
                    
                    if ($role === 'admin') {
                        $_SESSION['admin'] = true;
                    } else {
                        $_SESSION['admin'] = false;
                    }
                    
                    // Retrieve the stored redirect URL from the session
                    $redirectUrl = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'admins';
                    unset($_SESSION['redirect_url']); // Clear the stored URL
                    header("Location: $redirectUrl"); // Redirect to the target page
                    //header("Location: $decodedRedirect");
                    date_default_timezone_set('Etc/GMT-3');
                    $date = date('Y-m-d H:i:s');
                    $whoLogged = "INSERT INTO `userlogs`(`username`, `date`) VALUES ('$username','$date')";
                    mysqli_query($conn, $whoLogged);
                    exit;
                //} else {
                    // login failed, show an error message to the user
                    //$error_message = "Invalid Login Code.";
                    //notify("Invalid login code entered by a user.");
                //}
                
            } else { // password verification failed
                $error_message = "Wrong Username or Password.";
                notify("Wrong Username or Password entered by a user.");
            }
            
            $conn->close();
        } catch (Exception $e){
            session_start();
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            header("Location: errorpage");
            exit();
        }
        
        
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>DEMO LOGIN</title>
        
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
                                        <h4 class="mt-3 mb-1 fw-semibold font-18">DEMO LOGIN</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3">
                                <?php if (isset($error_message)) { ?>
                                    <p style="color: red;"><?php echo $error_message; ?></p>
                                <?php } ?>
                                <form id="login" class="form-horizontal mt-3" action="" method="post">
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
                                            <!-- Username: demo <br> Password: demo.123<br> -->
                                            <input id="username" name="username" class="form-control" type="text" required="" value="demo" placeholder="Username" autofocus/>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3 row">
                                        <div class="col-12">
                                            <input id="password" name="password" class="form-control" type="password" value="demo.123" placeholder="Password" required autocomplete="current-password"/>
                                        </div>
                                        <!--
                                        <div class="col-12">
                                            <br>
                                            <input id="phone" name="phone" class="form-control" type="number" placeholder="Phone Number" required/>
                                        </div>
                                        
                                        <div class="col-12">
                                            <br>
                                            Enter Login Code: (No code? <a  href="signup"> Get Login Code</a>)<br>
                                            <input id="code" name="code" class="form-control" type="number" required="" value="" placeholder="Login code" autofocus/>
                                        </div><br>
                                        -->
                                    </div>
                                    <!--
                                    <div class="g-recaptcha" data-sitekey="6Lf0x3gnAAAAAD-kvGHVFZgpvZsoBmp5D2NGJXHY">
                                    </div>
                                    -->
                                    <div class="form-group mb-3 text-center row mt-3 pt-1">
                                        <div class="col-12">
                                            <!--
                                            <button class="btn btn-sm btn-success w-100 waves-effect waves-light" value="Login" type="submit">Login</button>
                                            -->
                                            <button class="g-recaptcha btn btn-sm btn-success w-100 waves-effect waves-light" data-sitekey="6LfizHgnAAAAAPXwDiinsPL-TBXpLYx_YSIIa-Pi" data-callback='onSubmit' data-action='submit'>Login</button>
                                            <br>
                                            <br>
                                            <a href="passwordReset"> Forgot Password?</a>
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
           function onSubmit(token) {
             document.getElementById("login").submit();
           }
         </script>
        
        
    </body>
</html>