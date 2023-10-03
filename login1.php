<?php 

    require_once 'vendor/autoload.php'; // Include the Dotenv library

    use Dotenv\Dotenv;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
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
            // query the database to check if the credentials are correct
            
            
            // Load the environment variables from .env
            $dotenv = Dotenv::createImmutable(__DIR__);
            $dotenv->load();
            
            // Database connection
            $servername = $_ENV['DB_HOST'];
            $usernameD = $_ENV['DB_USERNAME'];
            $passwordD = $_ENV['DB_PASSWORD'];
            $dbname = $_ENV['DB_NAME'];
            
            $conn = mysqli_connect($servername, $usernameD, $passwordD, $dbname);
            
            $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
            $stmt->bind_param("s",$username);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $storedHashedPassword = $row['password'];
            
            if (password_verify($password, $storedHashedPassword)) {
                // login successful, create a session for the user
                session_start();
                
                // Retrieve the stored redirect URL from the session
                $redirectUrl = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'admins';
                unset($_SESSION['redirect_url']); // Clear the stored URL
                $_SESSION['username'] = $username;
                header("Location: $redirectUrl"); // Redirect to the target page
                //header("Location: $decodedRedirect");
                date_default_timezone_set('Etc/GMT-3');
                $date = date('Y-m-d H:i:s');
                $whoLogged = "INSERT INTO `userlogs`(`username`, `date`) VALUES ('$username','$date')";
                mysqli_query($conn, $whoLogged);
                exit;
            } else {
                // login failed, show an error message to the user
                $error_message = "Invalid username or password.";
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
    
    <title>ADMIN LOGIN</title>
    
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
                                        <h4 class="mt-3 mb-1 fw-semibold font-18">DEMO ADMIN LOGIN</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3">
        					
        						<?php if (isset($error_message)) { ?>
        					        <p style="color: red;"><?php echo $error_message; ?></p>
        						<?php } ?>
        						<form class="form-horizontal mt-3" action="" method="post">

        							<div class="form-group mb-3 row">
        								<div class="col-12">
        								    Username: demo <br> Password: demo.123<br>
        									<input id="username" name="username" class="form-control" type="text" required="" value="" placeholder="Username" autofocus/>
        								</div>
        							</div>
        							<div class="form-group mb-3 row">
        													<div class="col-12">
        														<input id="password" name="password" class="form-control" type="password" placeholder="Password" required autocomplete="current-password"/>
        													</div>
        							</div>
        							<div class="g-recaptcha" data-sitekey="6Lf0x3gnAAAAAD-kvGHVFZgpvZsoBmp5D2NGJXHY"></div>
        							<div class="form-group mb-3 text-center row mt-3 pt-1">
        													<div class="col-12">
        														<button class="btn btn-sm btn-success w-100 waves-effect waves-light" value="Login" type="submit">Login</button>
        														<br>
        														<br>
        														<a href="passwordReset"> Forgot Password?</a>
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