<?php
    // Function to standardize the phone number
    function standardizePhoneNumber($phoneNumbers) {
        // Remove non-digit characters
        $digits = preg_replace('/\D/', '', $phoneNumbers);
        // Extract the last 9 digits
        $standardizedNumber = substr($digits, -9);
        return $standardizedNumber;
    }
?>