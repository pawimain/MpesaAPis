<?php
error_reporting(0);
 header('Content-Type: application/json');
$consumerkey    = "Nm96wdpeZReAblNLG6nNnRrG1DFfEYEn"; //use getenv('CONSUMER_KEY') in production,
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
// 
//}
$result = json_decode($result);
$access_token=$result->access_token;
/*----------End Access token-----------*/

$paymentrequesturl = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$passKey = '064cd0f5b0616c529f6d4c9a8af0bc38fe1aa204d1c3ac872bc047e81ce7b9ed';
$shortcode = '4029931';
$timestamp = date("yymdhis");
$password = base64_encode($shortcode.$passKey.$timestamp);

 $aResult = array();

    if( !isset($_POST['functionname']) ) { $aResult['error'] = 'No function name!'; }
    if( !isset($aResult['error']) ) {

        switch($_POST['functionname']) { // as we have set the function name in js, we switch it here, it returns the wc code from safaricom
          // for initializing the push
            case 'push':
            $amount = $_POST['amnt'];
            $number = $_POST['phone'];
            $callBackUrl = 'https://gigpay.net/app/Mpesa/mpesa/c2b/v1';
            $reference = rand(10000000,99999999);
            $description = 'Lipa Na Mpesa';

            /* MAKE PAYMENT REQUEST */
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $paymentrequesturl);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); 
            $curl_post_data = array(  
                'BusinessShortCode' => $shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $amount,
                'PartyA' => $number,
                'PartyB' => $shortcode,
                'PhoneNumber' => $number,
                'CallBackURL' => $callBackUrl,
                'AccountReference' => $reference,
                'TransactionDesc' => $description
            );

            $data_string = json_encode($curl_post_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            // execute curl and get feedback from saf api
            $curl_response = curl_exec($curl);
            
            
            // make the  response above an array
            $arra= json_decode($curl_response,true);

            // response codes, from the above array
            if($arra['ResponseCode'] ==0){ // response code
              $responseData['status']="ok";
              $responseData['details']=$arra;  // details is array, let's see from the js
              $prepareJson=json_encode($responseData);
              echo $prepareJson;
              exit();
            }else{
              $responseData['status']="error"; // here
              $responseData['details']=$arra; // details is an array from saf api
              $prepareJson=json_encode($responseData); // encode as json and send back to js
              echo $prepareJson;
              exit();
            }
            
                        break;
//code for Reversal start

 case 'reverse':
            $transid= $_POST['transcation_id'];
            $callBackUrl = 'https://gigpay.net/app/Mpesa/mpesa/c2b/v1';
            /* MAKE a Reversal Request */
            $Rurl = 'https://api.safaricom.co.ke/mpesa/reversal/v1/request';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $Rurl);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); 
            $curl_post_data = array(  
                "Initiator"=>"Paul",  
                 "SecurityCredential"=> $password,  
                 "CommandID"=>"TransactionReversal",  
                 "TransactionID"=> $transID,  
                 "Amount"=>"1",  
                 "ReceiverParty"=>$shortcode,  
                 "RecieverIdentifierType"=>"4",  
                 "ResultURL"=>"https://gigpay.net/reult",  
                 "QueueTimeOutURL"=>"https://gigpay.net/timeout",  
                 "Remarks"=>"Reverse",  
                 "Occasion"=>"Official"
            );

            $data_string = json_encode($curl_post_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            // execute curl and get feedback from saf api
            $curl_response4 = curl_exec($curl);
               if(curl_errno($curl)){
                echo 'Request Error:' . curl_error($curl);
                exit();
              }
              $response = json_decode($curl_response4);
              echo 'Response';
              echo $curl_response4;
              echo "\n";
            
            // make the  response above an array
            $arra= json_decode($curl_response4,true);

            // response codes, from the above array
            if($arra['ResponseCode'] ==0){ // response code
              $responseData['status']="ok";
              $responseData['details']=$arra;  // details is array, let's see from the js
              $prepareReverseJson=json_encode($responseData);
              echo $prepareReverseJson;
              exit();
            }else{
              $responseData['status']="error"; // here
              $responseData['details']=$arra; // details is an array from saf api
              $$prepareReverseJson=json_encode($responseData); // encode as json and send back to js
              echo $prepareReverseJson;
              exit();
            }
            
                        break;

 // code for reversal Ends

/*--code  for checking account bal starts--*/

case 'checkbalance':
           
            /* Make a checkbalance Request */
            $Burl = 'https://api.safaricom.co.ke/mpesa/accountbalance/v1/query';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $Burl);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); 
            $curl_post_data = array(  
                  'Initiator' => 'Paul',
                  'SecurityCredential' => '$password',
                  'CommandID' => 'AccountBalance',
                  'PartyA' => '4029931',
                  'IdentifierType' => '4',
                  'Remarks' => 'Official',
                  'QueueTimeOutURL' => 'https://gigpay.net/timeout_url',
                  'ResultURL' => 'https://gigpay.net/result_url'
            );

            $data_string = json_encode($curl_post_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            // execute curl and get feedback from saf api
            $curl_response3 = curl_exec($curl);


            if(curl_errno($curl)){
                  echo 'Request Error:' . curl_error($curl);
                  exit();
              }
              $response = json_decode($curl_response3);
              echo 'Response';
              echo $curl_response3;
              echo "\n";
            
            
            // make the  response above an array
            $arra= json_decode($curl_response3,true);

            // response codes, from the above array
            if($arra['ResponseCode'] ==0){ // response code
              $responseData['status']="ok";
              $responseData['details']=$arra;  // details is array, let's see from the js
              $preparebalanceJson=json_encode($responseData);
              echo $prepareBalanceJson;
              exit();
            }else{
              $responseData['status']="error"; // here
              $responseData['details']=$arra; // details is an array from saf api
              $prepareBalanceJson=json_encode($responseData); // encode as json and send back to js
              echo $prepareBalanceJson;
              exit();
            }
            
                        break;
/*--code  for checking account bal ends--*/


      /*-Check completion status */
      case 'checkcomplete':
      $statusurl="https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query";
         /* MAKE PAYMENT REQUEST */
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $statusurl);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); 
            $curl_post_dat = array(  
                'BusinessShortCode' => $shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'CheckoutRequestID'=>$_POST['status_code']  // here we check with the request id we set in ajax request
            );

            $data_string = json_encode($curl_post_dat);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $curl_response2 = curl_exec($curl);
            $arr1= json_decode($curl_response2,true);
               
            // response codes
            if(isset($arr1['errorMessage'])){
                $responseData['status']="error";
                $responseData['state']="incomplete";
                $responseData['info']=$arr1['errorMessage'];
                $prepareJson=json_encode($responseData);
                echo $prepareJson;
                exit();
            }else{
                if($arr1['ResponseCode'] == 0){
                // set the status and state
                // now we use the result codes
                if($arr1['ResultCode']==1){
                  $responseData['status']="ok";
                   $responseData['state']="incomplete";
                   $responseData['info']="Insufficient balance";
                  $prepareJson=json_encode($responseData);
                  echo $prepareJson;
                  exit();
                }elseif($arr1['ResultCode']==0){
                  $responseData['status']="ok";
                  $responseData['state']="complete";
                  $responseData['info']="Completed";
                  $prepareJson=json_encode($responseData);
                  echo $prepareJson;
                  exit();
                }else{
                  $responseData['status']="ok";
                   $responseData['state']="incomplete";
                   $responseData['info']="An error occured while processing your transaction. Please try again later";
                  $prepareJson=json_encode($responseData);
                  echo $prepareJson;
                  exit();
                }
                
            }
            else{
              $responseData['status']="error";
               $responseData['state']="incomplete";
               $responseData['info']=$arr1['ResponseDesc'];
              $prepareJson=json_encode($responseData);
              echo $prepareJson;
              exit();
            }
            }
            
         break;
         exit();
      

      default:
               $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
               break;
        }

    }

       
?>
