 <?php
$consumerkey    = 'Nm96wdpeZReAblNLG6nNnRrG1DFfEYEn'; 
$consumersecret = 'oM1soMUrp3GqcKa4';
$shortcode = '4029931';
$passkey = 'Stability4423$';
$SCredential = base64_encode($passkey);
$authorizationurl='https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$credentials = base64_encode("$consumerkey:$consumersecret");
$ch = curl_init($authorizationurl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . $credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

// if(curl_errno($ch)){
//     echo 'Request Error:' . curl_error($ch);
//     exit();
// }
$result = json_decode($result);
$access_token=$result->access_token;
$transID = $_POST['transcation_id'];

 //end of access token
  $Rurl = 'https://sandbox.safaricom.co.ke/mpesa/reversal/v1/request';
  
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $Rurl);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Host:sandbox.safaricom.co.ke','Content-Type:application/json','Authorization:Bearer'.$access_token)); 
  
  
  $curl_post_data = array(
    //Fill in the request parameters with valid values
     "Initiator"=>"TestInit610",  
   "SecurityCredential"=> $SCredential,  
   "CommandID"=>"TransactionReversal",  
   "TransactionID"=> $transID,  
   "Amount"=>"1",  
   "ReceiverParty"=>"600610",  
   "RecieverIdentifierType"=>"4",  
   "ResultURL"=>"https://gigpay.net/reult",  
   "QueueTimeOutURL"=>"https://gigpay.net/timeout",  
   "Remarks"=>"please",  
   "Occasion"=>"work"
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

