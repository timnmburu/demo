<?php 
    require_once 'phpmailer/src/PHPMailer.php';
    require_once 'phpmailer/src/Exception.php';
    require_once 'phpmailer/src/SMTP.php';
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/emailing.php';
    require_once 'templates/cryptOtp.php';
    
    use Dotenv\Dotenv;

    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = mysqli_connect($db_servername, $db_username, $db_password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = $_POST['email'];
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
        
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $results = $stmt->get_result();
    
        if ($results->num_rows === 1) {
            // email found, generate password token
            $passToken = substr(str_shuffle("0123456789aaaaabbbbbcccccddddddeeeee"), 0, 15);
            $current_date_time = date('Y-m-d H:i:s', strtotime('+3 hours'));
            $encryptedToken = encryptOtp($passToken);
            
            $addPassToken = "UPDATE users SET token='$encryptedToken', password='', lastResetDate='$current_date_time' WHERE email='$email'";
            $conn->query($addPassToken);
            header('Location: new_password');
    
            
            // Fetch the username from the result
            $row = $results->fetch_assoc();
            $username = $row['username'];
        
            // Call the sendEmail() function to notify the user about the password change
            $subject = 'Password Change Notification';
            $body = 'Hi <b>' . $username . '</b>,<br><br>Your new password is <b>' . $passToken . '</b><br><br>Please copy and use it to reset your password.<br>Thank you.<br><br>If you did not request for a new password, please notify the Admin immediately by replying to this email.';
            
            $replyTo = "info@lfhcompany.site";
            
            sendEmail($email, $subject, $body, $replyTo);
            
        } else {
          // query failed, show an error message to the user
          $error_message = "Invalid details";
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
        									<input id="email" name="email" class="form-control" type="email" required="" value="" placeholder="Email" autofocus/>
        								</div>
        							</div>
        							<div class="form-group mb-3 text-center row mt-3 pt-1">
        													<div class="col-12">
        														<button class="btn btn-sm btn-success w-100 waves-effect waves-light" value="reset" type="submit">Reset</button>
        
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