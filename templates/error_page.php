<?php
    require_once __DIR__ .'/../templates/notifications.php';
    
    session_start();
    if(isset($_SESSION['redirect_url'])){
        $redirectUrl = $_SESSION['redirect_url'];
    } else {
        $redirectUrl = '../';
    }
    
    $errorPage = "An error occured in the page:" . $redirectUrl;
    
    notify($errorPage);
?>
<!DOCTYPE html>
<html>

    <head>
        <style>
            .logo-container {
                top: 0;
                left:0;
                z-index: 2;
                height: 100px;
                width: 119px;
                position: fixed;
                align-items: center;
                justify-content: center;
            }
            
            .logo-container img {
                height: 100px;
                width:119px;
            }
            
            .horizontalBar {
                background-color:  lavender;
                position: fixed;
                top: 0;
                left: 120px;
                right: 100px;
                z-index: 1;
                height: 100px;
                width: 99.99%;
                box-sizing: border-box;
                border-bottom: 1px solid grey;
                box-shadow: 0px 2px 4px grey;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            
            .body {
                margin-left: 0px; 
                margin-top: 100px; 
                padding: 20px;
            }
            /*////////////////////////////////////////////////////////////////////////////////////*/
            @media only screen and (max-width: 1366px) {
              /* Styles for smaller screens of width 768*/
                .horizontalBar {
                    background-color:  lavender;
                    position: fixed;
                    top: 0;
                    left: 0;
                    z-index: 1;
                    height: 100px;
                    width: 99.99%;
                    box-sizing: border-box;
                    border-bottom: 1px solid grey;
                    box-shadow: 0px 2px 4px grey;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }
                  
                .logo-container {
                    height: 100px;
                    width: 119px;
                    top:0;
                    left:0;
                    z-index: 9999;
                    position: fixed;
                    align-items: center;
                    justify-content: center;
                }
                
                .logo-container img {
                    height: 99px;
                    width:110px;
                }
                
                .body {
                    margin-left: 0; 
                    margin-top: 100px; 
                    padding: 5px;
                }
            
            }
        </style>
        <header class="header">
            <div class="horizontalBar">
                <div class="logo-container">
                    <a href="/demo/admins" target="">
                        <img src="../logos/Logo.jpg" alt="Logo" />
                    </a>
                </div>
                <h1>
                    PAGE LOADING ERROR!
                </h1>
            </div>
        </header>
    </head>
    <body class="body">
        <p>
            <h3>
                No worries! It's not you. It's us. Our developers have been notified and are working to resolve. In the meantime, could you try reloading <a href="<?php echo $redirectUrl; ?>">the Page?</a>
            </h3>
        </p>
    </body>
</html>

