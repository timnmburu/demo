<?php

function request_payment($phone_number, $total_amount){

    // Initialize the variables
    $consumer_key = '44ueCAY7hcvXLmWlCVo2xRrUkXw9jnks';
    $consumer_secret = 'At0ZjSacahpkAZo6';
    $Business_Code = '8581214';
    $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    $Type_of_Transaction = 'CustomerBuyGoodsOnline';
    $Token_URL = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $OnlinePayment = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $CallBackURL = 'https://www.essentialtech.site/callbackurls/requests/';
    $Time_Stamp = date('YmdHis', strtotime('+3 hours'));
    $password = base64_encode($Business_Code . $Passkey . $Time_Stamp);
    $credentials = base64_encode($consumer_key . ':' . $consumer_secret);

    
    $curl_Tranfer = curl_init();
    curl_setopt($curl_Tranfer, CURLOPT_URL, $Token_URL);

    curl_setopt($curl_Tranfer, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
    curl_setopt($curl_Tranfer, CURLOPT_HEADER, false);
    curl_setopt($curl_Tranfer, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_Tranfer, CURLOPT_SSL_VERIFYPEER, false);
    $curl_Tranfer_response = curl_exec($curl_Tranfer);
    
    $token = json_decode($curl_Tranfer_response)->access_token;
    
    $curl_Tranfer2 = curl_init();
    curl_setopt($curl_Tranfer2, CURLOPT_URL, $OnlinePayment);
    curl_setopt($curl_Tranfer2, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $token));
    
    $curl_Tranfer2_post_data = [
        'BusinessShortCode' => $Business_Code,
        'Password' => $password,
        'Timestamp' =>$Time_Stamp,
        'TransactionType' =>$Type_of_Transaction,
        'Amount' => $total_amount,
        'PartyA' => $phone_number,
        'PartyB' => $Business_Code,
        'PhoneNumber' => $phone_number,
        'CallBackURL' => $CallBackURL,
        'AccountReference' => 'Lourice',
        'TransactionDesc' => 'Lourice',
    ];
    
    $data2_string = json_encode($curl_Tranfer2_post_data);
    
    curl_setopt($curl_Tranfer2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_Tranfer2, CURLOPT_POST, true);
    curl_setopt($curl_Tranfer2, CURLOPT_POSTFIELDS, $data2_string);
    curl_setopt($curl_Tranfer2, CURLOPT_HEADER, false);
    curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYHOST, 0);
    $curl_Tranfer2_response = json_decode(curl_exec($curl_Tranfer2));
    
    json_encode($curl_Tranfer2_response, JSON_PRETTY_PRINT);
    
    //$curl_Tranfer2_response = $curl_Tranfer2_response->ResponseCode;
    
    //header ("Location: test.php?rc=$curl_Tranfer2_response");
    //echo $curl_Tranfer2_response;

//{ "MerchantRequestID": "32185-58074012-1", "CheckoutRequestID": "ws_CO_11082023171925284725887269", "ResponseCode": "0", "ResponseDescription": "Success. Request accepted for processing", "CustomerMessage": "Success. Request accepted for processing" }

    return $curl_Tranfer2_response;
}


/*
request_payment_status($transID) {
        // Initialize the variables
    $consumer_key = '44ueCAY7hcvXLmWlCVo2xRrUkXw9jnks';
    $consumer_secret = 'At0ZjSacahpkAZo6';
    $Business_Code = '174379';
    $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    $Type_of_Transaction = 'CustomerPayBillOnline';
    $Token_URL = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $OnlinePayment = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $CallBackURL = 'https://www.essentialtech.site/callbackurls/requests/';
    $Time_Stamp = date('YmdHis', strtotime('+3 hours'));
    $password = base64_encode($Business_Code . $Passkey . $Time_Stamp);
    $credentials = base64_encode($consumer_key . ':' . $consumer_secret);

    
    $curl_Tranfer = curl_init();
    curl_setopt($curl_Tranfer, CURLOPT_URL, $Token_URL);

    curl_setopt($curl_Tranfer, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
    curl_setopt($curl_Tranfer, CURLOPT_HEADER, false);
    curl_setopt($curl_Tranfer, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_Tranfer, CURLOPT_SSL_VERIFYPEER, false);
    $curl_Tranfer_response = curl_exec($curl_Tranfer);
    
    $token = json_decode($curl_Tranfer_response)->access_token;
    
    $curl_Tranfer2 = curl_init();
    curl_setopt($curl_Tranfer2, CURLOPT_URL, $OnlinePayment);
    curl_setopt($curl_Tranfer2, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $token));
    
    $curl_Tranfer2_post_data = [
        'BusinessShortCode' => $Business_Code,
        'Password' => $password,
        'Timestamp' =>$Time_Stamp,
        'TransactionType' =>$Type_of_Transaction,
        'Amount' => $total_amount,
        'PartyA' => $phone_number,
        'PartyB' => $Business_Code,
        'PhoneNumber' => $phone_number,
        'CallBackURL' => $CallBackURL,
        'AccountReference' => 'Lourice',
        'TransactionDesc' => 'Lourice',
    ];
    
    $data2_string = json_encode($curl_Tranfer2_post_data);
    
    curl_setopt($curl_Tranfer2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_Tranfer2, CURLOPT_POST, true);
    curl_setopt($curl_Tranfer2, CURLOPT_POSTFIELDS, $data2_string);
    curl_setopt($curl_Tranfer2, CURLOPT_HEADER, false);
    curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYHOST, 0);
    $curl_Tranfer2_response = json_decode(curl_exec($curl_Tranfer2));
    
    json_encode($curl_Tranfer2_response, JSON_PRETTY_PRINT);
    
    
    
    
    
    
    return $curl_Tranfer2_response;
}

*/

?>