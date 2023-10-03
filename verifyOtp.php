<?php 
    require_once 'templates/notifications.php';
    require_once 'templates/cryptOtp.php';
    
    if (isset($_POST['submit'])) {
        $userOtp = $_POST['userOtp'];
        session_start();
        $phone = $_SESSION['userphone'];
        
        $decryptedOtp = decryptOtp($phone);
        
        $return = json_decode($decryptedOtp, true);
    
        if(isset($return['error'])) {
            $message = $return['error'];
            $error_message = "Timeout, request new OTP!";
            notify($error_message);
            $showButton = true;
        } elseif (isset($return['success'])) {
            $code = $return['success'];
            
            if ($userOtp === $code) {
                
                echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('processing').hidden = false;
                        document.getElementById('wrapper-page').hidden = true;
                    });
                    
                    fetch(localStorage.getItem('targetUrl'))
                        .then(response => response.ok ? response.text()
                            .then(data => {
                                localStorage.removeItem('targetUrl');
                                window.location.href = localStorage.getItem('sourceUrl');
                                localStorage.removeItem('sourceUrl');
                            }) 
                        : console.error('Network response was not ok'))
                            .catch(error => 
                                    console.error('Error:', error)
                                    );
                </script>";
                
            } else { // password verification failed
                $error_message = "Wrong OTP code.";
                notify($error_message);
            }
        }
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>OTP</title>
        
        <!-- Bootstrap Css -->
        <link href="bootstrap.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        
        <!-- App favicon -->
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
        <style>
            .processing {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
        </style>
    </head>
    <body class="auth-body-bg">
        <div class="processing" id="processing" hidden="true" >
            <!--Running man for Processing Requests -->
                <img src="fileStore/running.gif" alt="running-man">
        </div>
        <div class="bg-overlay"></div>        
        <div class="wrapper-page" id="wrapper-page" >
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
                                        <h4 class="mt-3 mb-1 fw-semibold font-18">Enter OTP Code</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3">
                                <?php if (isset($error_message)) { ?>
                                    <p style="color: red;"><?php echo $error_message; ?></p>
                                <?php } ?>
                                <form id="verifyOTP" class="form-horizontal mt-3" action="" method="post">
                                    <div class="form-group mb-3 row">
                                        
                                        <div id="timer" <?php if(isset($showButton) && $showButton === true) { echo 'hidden'; } ?> >60 secs remaining</div>

                                        <div class="col-12">
                                            <input id="userOtp" name="userOtp" class="form-control" type="number"  placeholder="Enter OTP received.." required />
                                        </div>
                                    </div>
                                    <div class="form-group mb-3 text-center row mt-3 pt-1">
                                        <div class="col-12">
                                            <button id ="verify" class="btn btn-sm btn-success w-100 waves-effect waves-light" name='submit'>Verify Code</button>
                                            <br>
                                            <br>
                                            <button id="newOTP" <?php if(isset($showButton) && $showButton === true) { echo ''; } else { echo "hidden"; } ?> onclick="resetOTP()" >Request New OTP</button>
                                               
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
           //OTP Timer
            let seconds = 60;
            let timerInterval;
    
            function startTimer() {
                timerInterval = setInterval(updateTimer, 1000);
            }
    
            function updateTimer() {
                seconds--;
                if (seconds <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('verify').disabled = true;
                    document.getElementById('newOTP').hidden = false;
                }
                document.getElementById('timer').innerText = seconds + " secs remaining";
            }
            
            startTimer();
            
            function resetOTP() {
                
                clearInterval(timerInterval);
                
                // Send an AJAX request to the server to reset OTP
                let xhr = new XMLHttpRequest();
                xhr.open('GET', 'templates/setOtp.php', true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
                        seconds = 60;
                        document.getElementById('timer').hidden = false;
                        document.getElementById('timer').innerText = seconds + " secs remaining";
                        document.getElementById('newOTP').hidden = true;
                        document.getElementById('verify').disabled = false;
                        
                        startTimer();
                    }
                }
                xhr.send();
            }
        </script>
    </body>
</html>