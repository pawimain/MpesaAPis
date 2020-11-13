 <?php
$consumerkey    = "Nm96wdpeZReAblNLG6nNnRrG1DFfEYEn"; 
$consumersecret ="oM1soMUrp3GqcKa4";
$authorizationurl='https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$credentials = base64_encode("$consumerkey:$consumersecret");
$ch = curl_init($authorizationurl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . $credentials
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

// if(curl_errno($ch)){
//     echo 'Request Error:' . curl_error($ch);
//     exit();
// }
$result = json_decode($result);
$access_token=$result->access_token;

 //end of access token
$url = 'https://api.safaricom.co.ke/mpesa/accountbalance/v1/query';
  
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer'.$access_token)); //setting custom header
  
  
  $curl_post_data = array(
    //Fill in the request parameters with valid values
    'Initiator' => 'Paul',
    'SecurityCredential' => '',
    'CommandID' => 'AccountBalance',
    'PartyA' => '4029931',
    'IdentifierType' => '4',
    'Remarks' => ' ',
    'QueueTimeOutURL' => 'https://gigpay.net/timeout_url',
    'ResultURL' => 'https://gigpay.net/result_url'
  );
  
  $data_string = json_encode($curl_post_data);
  
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
  
  $curl_response = curl_exec($curl);

  if(curl_errno($curl)){
    echo 'Request Error:' . curl_error($curl);
    exit();
}
$response = json_decode($curl_response);
echo 'Response';
echo $curl_response;
echo "\n";
  ?>

