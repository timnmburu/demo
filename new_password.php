<?php 
    
    require_once 'vendor/autoload.php';
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
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $passToken = $_POST['passToken'];
        $newPassword = $_POST['newPassword'];
        $repeatPassword = $_POST['repeatPassword'];
        $captcha = $_POST['g-recaptcha-response']; // reCAPTCHA response
        
        // verify the reCAPTCHA response
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array(
            'secret' => '6Lf0x3gnAAAAAB70cyIDuAZ4RaJFj0xLKAIQLmIV',
            'response' => $captcha
        );
        
        $options = array(
            'http' => array (
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($data)
            )
        );
        
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result, true);
        
        if ($response['success']) { // reCAPTCHA verification successful
        
            if($newPassword === $repeatPassword){
                // Generate the hash of the password
                $stmt =$conn->prepare("SELECT token FROM users WHERE email=?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                $token = $row['token'];
                
                $decryptedToken = decryptLogin($token);
                $encryptedPassword = encryptOtp($newPassword);
                
                if ($passToken === $decryptedToken) {
                        $changePass = "UPDATE users SET password='$encryptedPassword', token='' WHERE email='$email'";
                        $conn->query($changePass);
                        header('Location: login');
                   
                } else {
                    // login failed, show an error message to the user
                    $error_message = "Invalid Emailed Password";
                }
            } else {
                $error_message = "Passwords do not match";
            }
            
        } else { // reCAPTCHA verification failed
            $error_message = "Please verify that you are not a robot.";
        }
    }
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Password Reset</title>
    
    <!-- Bootstrap Css -->
    <link href="bootstrap.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
   
    <!-- App favicon -->
    <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	
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
                                        <h4 class="mt-3 mb-1 fw-semibold font-18">Password Reset</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3">
        					
        						<?php if (isset($error_message)) { ?>
        						  <p style="color: red;"><?php echo $error_message; ?></p>
        						<?php } ?>
        						<form class="form-horizontal mt-3" action="" method="post">
        							<div class="g-recaptcha" data-sitekey="6Lf0x3gnAAAAAD-kvGHVFZgpvZsoBmp5D2NGJXHY"></div>
									<div class="form-group mb-3 row">
        							<div class="col-12">
        									<input id="email" name="email" class="form-control" type="email" required  placeholder="Email.." autofocus/>
        								</div>
        							</div>
        							<div class="col-12">
        									<input id="passToken" name="passToken" class="form-control" type="password" required="" value="" placeholder="Emailed Password" autofocus/>
        								</div>
        							</div>
        							<div class="form-group mb-3 row">
        								<div class="col-12">
        									<input id="newPassword" name="newPassword" class="form-control" type="password" required="" value="" placeholder="New Password" autofocus/>
        								</div>
        							</div>
        							<div class="form-group mb-3 row">
        								<div class="col-12">
        									<input id="repeatPassword" name="repeatPassword" class="form-control" type="password" required="" value="" placeholder="Repeat Password" autofocus/>
        								</div>
        							</div>	
        							<div class="form-group mb-3 text-center row mt-3 pt-1">
        													<div class="col-12">
        														<button class="btn btn-sm btn-success w-100 waves-effect waves-light" value="reset" type="submit">Reset Password</button>
        													</div>
        							</div>
        						</form>
        
                            </div>
                        </main>
                    </div>
                </div>
            </div>
        </div>
        

        
    </body>
</html>