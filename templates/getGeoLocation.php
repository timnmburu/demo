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
    
        $url = "https://ipinfo.io/{$ipAddress}/json?token={$IPinfo_API_KEY}";
        
        $response = file_get_contents($url);
        
        $data = json_decode($response);
        
        $ip = $data->ip;
        $city = $data->city;
        $region = $data->region;
        $country = $data->country;
        $lat = explode(',', $data->loc)[0];
        $long = explode(',', $data->loc)[1];
    
        $notifyLocation = "Prospect loaded page from IP: " . trim($ip) . " City: " . trim($city) . " Region: " . trim($region) . " Country: " . trim($country);
        
        return $notifyLocation;
    }
    
?>
