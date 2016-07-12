<?php

namespace App\Alonti\ANetWrapper;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use App\Alonti\ANetWrapper\ANetConstants;

/**
* A class containing colleciton of payment methods
* @author Avneesh Gupta <avneesh@softway.com>
*/
class PaymentTransactions
{
  
  function __construct()
  {
    // Common setup for API credentials
    $this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $this->merchantAuthentication->setName(ANetConstants::MERCHANT_LOGIN_ID);
    $this->merchantAuthentication->setTransactionKey(ANetConstants::MERCHANT_TRANSACTION_KEY);
  }

  public function authorizeCreditCard($authorizeDetail = []) {
    $refId = 'ref' . time();
    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber($authorizeDetail['card_number']);
    $creditCard->setExpirationDate($authorizeDetail['expiration_date']);
    $creditCard->setCardCode($authorizeDetail['card_code']);
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);
    //create a transaction
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "authOnlyTransaction" ); 
    $transactionRequestType->setAmount($authorizeDetail['amount']);
    $transactionRequestType->setPayment($paymentOne);
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setRefId( $refId );
    $request->setTransactionRequest( $transactionRequestType );
    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if ($response != null)
    {
      $tresponse = $response->getTransactionResponse();
      if (($tresponse != null) && ($tresponse->getResponseCode()== ANetConstants::RESPONSE_OK) )   
      {
        echo " AUTH CODE : " . $tresponse->getAuthCode() . "\n";
        echo " TRANS ID  : " . $tresponse->getTransId() . "\n";
      }
      else
      {
          echo  "ERROR : " . $tresponse->getResponseCode() . "\n";
      }
      
    }
    else
    {
      echo  "No response returned";
    }
    return $response;
  }

  public function capturePreviouslyAuthorizedAmount($authorizeDetail = []){
    $refId = 'ref' . time();
    // Now capture the previously authorized  amount
    echo "Capturing the Authorization with transaction ID : " . $authorizeDetail['transaction_id'] . "\n";
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("priorAuthCaptureTransaction");
    $transactionRequestType->setRefTransId($authorizeDetail['transaction_id']);
    
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setTransactionRequest( $transactionRequestType);
    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    
    if ($response != null)
    {
      $tresponse = $response->getTransactionResponse();
      if (($tresponse != null) && ($tresponse->getResponseCode()== ANetConstants::RESPONSE_OK) )
      {
        echo "Successful." . "\n";
        echo "Capture Previously Authorized Amount, Trans ID : " . $tresponse->getRefTransId() . "\n";
      }
      else
      {
        echo  " Capture Previously Authorized Amount: Invalid response\n";
      }
    }
    else
    {
      echo  "Capture Previously Authorized Amount, NULL Response Error\n";
    }
    return $response;
  }

  public function captureFundsAuthorizedThroughAnotherChannel($authorizeDetail = []){
    $refId = 'ref' . time();
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber( $authorizeDetail['card_number']);
    $creditCard->setExpirationDate( $authorizeDetail['expiration_date'] );
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("captureOnlyTransaction");
    $transactionRequestType->setAmount($authorizeDetail['amount']);
    $transactionRequestType->setPayment($paymentOne);
    //Auth code of the previously authorized  amount
    $transactionRequestType->setAuthCode($authorizeDetail['auth_code']);
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setTransactionRequest( $transactionRequestType);
    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if ($response != null)
    {
        $tresponse = $response->getTransactionResponse();
        if (($tresponse != null) && ($tresponse->getResponseCode()== ANetConstants::RESPONSE_OK) )
        {
            echo "Successful." . "\n";
            echo "Capture funds authorized through another channel TRANS ID  : " . $tresponse->getTransId() . " Amount : $amount \n";
        }
        else
        {
            echo  "Capture funds authorized through another channel ERROR: Invalid response\n";
            $errorMessages = $response->getMessages()->getMessage();
            echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
        }
    }
    else
    {
        echo  "Capture funds authorized through another channel, NULL Response Error\n";
    }
    return $response;
  }

  public function chargeCreditCard($authorizeDetail = []){
    $refId = 'ref' . time();
    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber($authorizeDetail['card_number']);
    $creditCard->setExpirationDate($authorizeDetail['expiration_date']);
    $creditCard->setCardCode($authorizeDetail['card_code']);
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);
    $order = new AnetAPI\OrderType();
    $order->setDescription($authorizeDetail['order_desc']);
    //create a transaction
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
    $transactionRequestType->setAmount($authorizeDetail['amount']);
    $transactionRequestType->setOrder($order);
    $transactionRequestType->setPayment($paymentOne);
    
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setRefId( $refId);
    $request->setTransactionRequest( $transactionRequestType);
    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    
    if ($response != null)
    {
      $tresponse = $response->getTransactionResponse();
      if (($tresponse != null) && ($tresponse->getResponseCode()== ANetConstants::RESPONSE_OK) )   
      {
        echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
        echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";
      }
      else
      {
          echo  "Charge Credit Card ERROR :  Invalid response\n";
      }
      
    }
    else
    {
      echo  "Charge Credit card Null response returned";
    }
    return $response;
  }

  public function authorizeCustomerPaymentProfile($authorizeDetail = []){
    $refId = 'ref' . time();
    $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
    $profileToCharge->setCustomerProfileId($authorizeDetail['profile_id']);
    $paymentProfile = new AnetAPI\PaymentProfileType();
    $paymentProfile->setPaymentProfileId($authorizeDetail['payment_profile_id']);
    $profileToCharge->setPaymentProfile($paymentProfile);
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "authOnlyTransaction" ); 
    $transactionRequestType->setAmount($authorizeDetail['amount']);
    $transactionRequestType->setProfile($profileToCharge);
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setRefId( $refId );
    $request->setTransactionRequest( $transactionRequestType );
    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if ($response != null)
    {
      $tresponse = $response->getTransactionResponse();
      if (($tresponse != null) && ($tresponse->getResponseCode()== ANetConstants::RESPONSE_OK) )   
      {
        echo  "Authorize Customer Profile APPROVED  :" . "\n";
        echo " Authorize Customer Profile AUTH CODE : " . $tresponse->getAuthCode() . "\n";
        echo " Authorize Customer Profile TRANS ID  : " . $tresponse->getTransId() . "\n";
      }
      elseif (($tresponse != null) && ($tresponse->getResponseCode()=="2") )
      {
        echo  "ERROR" . "\n";
      }
      elseif (($tresponse != null) && ($tresponse->getResponseCode()=="4") )
      {
          echo  "ERROR: HELD FOR REVIEW:"  . "\n";
      }
    }
    else
    {
      echo "no response returned";
    }
    return $tresponse;
  }

  public function chargeCustomerProfile($authorizeDetail = []){
    $refId = 'ref' . time();
    $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
    $profileToCharge->setCustomerProfileId($authorizeDetail['profile_id']);
    $paymentProfile = new AnetAPI\PaymentProfileType();
    $paymentProfile->setPaymentProfileId($authorizeDetail['paymentprofile_id']);
    $profileToCharge->setPaymentProfile($paymentProfile);
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "authCaptureTransaction"); 
    $transactionRequestType->setAmount($authorizeDetail['amount']);
    $transactionRequestType->setProfile($profileToCharge);
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setRefId( $refId);
    $request->setTransactionRequest( $transactionRequestType);
    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if ($response != null)
    {
      $tresponse = $response->getTransactionResponse();
      if (($tresponse != null) && ($tresponse->getResponseCode()== ANetConstants::RESPONSE_OK) )   
      {
        echo  "Charge Customer Profile APPROVED  :" . "\n";
        echo " Charge Customer Profile AUTH CODE : " . $tresponse->getAuthCode() . "\n";
        echo " Charge Customer Profile TRANS ID  : " . $tresponse->getTransId() . "\n";
      }
      elseif (($tresponse != null) && ($tresponse->getResponseCode()=="2") )
      {
        echo  "ERROR" . "\n";
      }
      elseif (($tresponse != null) && ($tresponse->getResponseCode()=="4") )
      {
          echo  "ERROR: HELD FOR REVIEW:"  . "\n";
      }
    }
    else
    {
      echo "no response returned";
    }
    return $response;
  }

  public function voidTransaction($authorizeDetail = []){
    $refId = 'ref' . time();
    
    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber( ANetConstants::CREDIT_CARD_NUMBER );
    $creditCard->setExpirationDate( ANetConstants::EXPIRY_DATE);
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);
    //create a transaction
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "voidTransaction"); 
    $transactionRequestType->setPayment($paymentOne);
    $transactionRequestType->setRefTransId($authorizeDetail['transaction_id']);
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setRefId($refId);
    $request->setTransactionRequest( $transactionRequestType);
    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if ($response != null)
    {
      $tresponse = $response->getTransactionResponse();
      if (($tresponse != null) && ($tresponse->getResponseCode()== ANetConstants::RESPONSE_OK) )   
      {
        echo "Void transaction SUCCESS AUTH CODE: " . $tresponse->getAuthCode() . "\n";
        echo "Void transaction SUCCESS TRANS ID  : " . $tresponse->getTransId() . "\n";
      }
      else
      {
          echo  "void transaction ERROR : " . $tresponse->getResponseCode() . "\n";
          $errorMessages = $response->getMessages()->getMessage();
          echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
          //use print_r to see whole $response which will have the specific error messages
      }
    }
    else
    {
      echo  "Void transaction Null esponse returned";
    }
    return $response;
  }
}
