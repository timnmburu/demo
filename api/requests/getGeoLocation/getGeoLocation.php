<?php
    
    function getGeoLocation($IPinfo_API_KEY){
        //get IP address and GeoLocation
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        
        $ipAddresses = explode(',', $ipAddress);
        $ipAddress = trim($ipAddresses[0]);
        
        //$IPinfo_API_KEY = $_GET['IPinfo_API_KEY'];
    
        $url = "https://ipinfo.io/{$ipAddress}/json?token={$IPinfo_API_KEY}";
        
        $response = file_get_contents($url);
        
        return $response;
    }
    
?>
