<?php
    $certificatePath = 'cert.cer';
    $shortcode="174379";
    $initiatorname="Shortcode 1";
    $initiatorpassword="Shortcode 1";
    $consumerkey    = "OpXxMg9c7hWZAfOASg4e1WqLjp53hw1W"; //use getenv('CONSUMER_KEY') in production,
    $consumersecret ="HV4GGPzcQsXcMVwl";
    $commandid='BusinessPayment';
    $recipient=$_POST['b2cPhone'];
    $amount=$_POST['b2cAmnt'];
    $remarks='TEST BUSINESS DISBURSAL';
    $occassion='Sep 2020';
    $QueueTimeOutURL="https://gigpay.net/mpesa/b2c/v1";
    $ResultURL="https://gigpay.net/mpesa/b2c/v1";
    /*The below are production URLS CHANGE TO SANDBOX IF YOU HAVEN'T GONE LIVE */
    $authorizationurl='https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $paymentrequesturl="https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest";


    /*---------------
      Get Access Token
    ------------------*/
    $credentials = base64_encode("$consumerkey:$consumersecret");
    $ch = curl_init($authorizationurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . $credentials
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    if(curl_errno($ch)){
        echo 'Request Error:' . curl_error($ch);
        exit();
    }
    $result = json_decode($result);
    $access_token=$result->access_token;
    /*----------End Access token-----------*/


    // curl_close($ch);
    /* END AUTHENTICATION */
    /* BEGIN PAYMENT REQUEST IF WE GOT ACCESS TOKEN*/
    if(!$access_token)
    {
      echo " Invalid access token ". print_r($result);
    }
    else
    {
      echo "\n $access_token \n";
      $publicKey = file_get_contents($certificatePath);
      openssl_public_encrypt($initiatorpassword, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);
      //GENERATE SECURITY CREDENTIAL USING THE PUBLIC KEY ABOVE
      $securitycredential=base64_encode($encrypted);


      /* MAKE PAYMENT REQUEST */
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $paymentrequesturl);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); 
      $curl_post_data = array(  
       
        'InitiatorName' => $initiatorname,
        'SecurityCredential' => $securitycredential,
        'CommandID' =>  $commandid,
        'Amount' =>  $amount,
        'PartyA' => $shortcode,
        'PartyB' =>  $recipient,  
        'Remarks' => $remarks,
        'QueueTimeOutURL' => $QueueTimeOutURL,
        'ResultURL' => $ResultURL,
        'Occassion' => $occassion
      );
      echo "\n$initiatorname\n$securitycredential\n";
      $data_string = json_encode($curl_post_data);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
      $curl_response = curl_exec($curl);
        $response = json_decode($curl_response);
        if(!isset($response->ConversationID))
        {
        echo "There was an error when submitting your request: $response->errorMessage";
        
        }
        else
        {
        echo "Success, $response->ResponseDescription ";
        echo $response->ConversationID;
        }
    }
?>