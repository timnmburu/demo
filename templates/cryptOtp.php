<?php
    require_once(__DIR__ . '/../vendor/autoload.php');

    use Dotenv\Dotenv;
     
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    function encryptOtp($data){
        $first_key = base64_decode($_ENV['OTP_KEY1']);
        $second_key = base64_decode($_ENV['OTP_KEY2']);   
            
        $method = "aes-256-cbc";    
        $iv_length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_length);
                
        $first_encrypted = openssl_encrypt($data,$method,$first_key, OPENSSL_RAW_DATA ,$iv);    
        $second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
                    
        $otpHash = base64_encode($iv.$second_encrypted.$first_encrypted);    
        
        return $otpHash;
    }

    function decryptOtp($phone){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        //$now = date('H', strtotime('+3 hours'));
        
        $sqlGetHashedOtp = "SELECT * FROM otpQ WHERE phone='$phone' ORDER BY s_no DESC LIMIT 1";
        $results = $conn->query($sqlGetHashedOtp);
        $rows = $results->fetch_assoc();
        
        $storedHashOtp = $rows['otpHash'];
        
        $dateInitiated = $rows['dateInitiated'];
        
        $initial_timestamp = strtotime($dateInitiated);
        $current_timestamp = date('Y-m-d H:i:s', strtotime('+3 hours'));
        $current_timestamp = strtotime($current_timestamp);

        // Calculate the difference in seconds
        $difference = $current_timestamp - $initial_timestamp;
        
        // Convert the difference to minutes
        $timePast = floor($difference);
        
        if ($timePast > 60) {
            return json_encode(["error" => "Timeout: $timePast"]);
        } else {
            $first_key = base64_decode($_ENV['OTP_KEY1']);
            $second_key = base64_decode($_ENV['OTP_KEY2']); 
            
            $mix = base64_decode($storedHashOtp);
                    
            $method = "aes-256-cbc";    
            $iv_length = openssl_cipher_iv_length($method);
                        
            $iv = substr($mix, 0, $iv_length);
            
            $second_encrypted = substr($mix, $iv_length, 64);
            $first_encrypted = substr($mix, $iv_length + 64);
                        
            $data = openssl_decrypt($first_encrypted, $method, $first_key,  OPENSSL_RAW_DATA, $iv);
            $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
                
            if (hash_equals($second_encrypted, $second_encrypted_new)){
                $decrypted = $data;
            } else {
                $decrypted = null;   
            }
            
            //return $decrypted;
            return json_encode(["success" => "$decrypted"]);
        }
    }
    
    function decryptOtpSignup($phone){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $sqlGetHashedOtp = "SELECT * FROM signup WHERE phone=$phone";
        $results = $conn->query($sqlGetHashedOtp);
        $rows = $results->fetch_assoc();
        
        $storedHashOtp = $rows['code'];
    
        $first_key = base64_decode($_ENV['OTP_KEY1']);
        $second_key = base64_decode($_ENV['OTP_KEY2']); 
        
        $mix = base64_decode($storedHashOtp);
                
        $method = "aes-256-cbc";    
        $iv_length = openssl_cipher_iv_length($method);
                    
        $iv = substr($mix, 0, $iv_length);
        
        $second_encrypted = substr($mix, $iv_length, 64);
        $first_encrypted = substr($mix, $iv_length + 64);
                    
        $data = openssl_decrypt($first_encrypted, $method, $first_key,  OPENSSL_RAW_DATA, $iv);
        $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
            
        if (hash_equals($second_encrypted, $second_encrypted_new)){
            $decrypted = $data;
        } else {
            $decrypted = false;   
        }
        
        return $decrypted;
    }
    
    function decryptLogin($encryptedPassword){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $first_key = base64_decode($_ENV['OTP_KEY1']);
        $second_key = base64_decode($_ENV['OTP_KEY2']); 
        
        $mix = base64_decode($encryptedPassword);
                
        $method = "aes-256-cbc";    
        $iv_length = openssl_cipher_iv_length($method);
                    
        $iv = substr($mix, 0, $iv_length);
        
        $second_encrypted = substr($mix, $iv_length, 64);
        $first_encrypted = substr($mix, $iv_length + 64);
                    
        $data = openssl_decrypt($first_encrypted, $method, $first_key,  OPENSSL_RAW_DATA, $iv);
        $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
            
        if (hash_equals($second_encrypted, $second_encrypted_new)){
            $decrypted = $data;
        } else {
            $decrypted = false;   
        }
        
        return $decrypted;
    }


?>