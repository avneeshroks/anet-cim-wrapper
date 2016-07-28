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
  /**
   * __construct : Common setup for API credentials
   */
  function __construct()
  {
    $this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $this->merchantAuthentication->setName(ANetConstants::MERCHANT_LOGIN_ID);
    $this->merchantAuthentication->setTransactionKey(ANetConstants::MERCHANT_TRANSACTION_KEY);
    $this->pickEndpointClass = ANetConstants::PICK_ENDPOINT_CLASS;
  }

  /**
   * authorizeCreditCard : Create the payment data for a credit card
   * @param  array    $authorizeDetail
   * @return Object   $response
   */
  public function authorizeCreditCard($authorizeDetail = [])
  {
    $refId = 'ref' . time();

    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber($authorizeDetail['card_number']);
    $creditCard->setExpirationDate($authorizeDetail['expiration_date']);
    $creditCard->setCardCode($authorizeDetail['card_code']);

    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);

    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "authOnlyTransaction" ); 
    $transactionRequestType->setAmount($authorizeDetail['amount']);
    $transactionRequestType->setPayment($paymentOne);

    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setRefId( $refId );
    $request->setTransactionRequest( $transactionRequestType );

    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse($this->pickEndpointClass);

    return $response;
  }

  /**
   * capturePreviouslyAuthorizedAmount : Now capture the previously authorized  amount
   * @param  array    $authorizeDetail
   * @return Object   $response
   */
  public function capturePreviouslyAuthorizedAmount($authorizeDetail = [])
  {
    $refId = 'ref' . time();

    $order = new AnetAPI\OrderType();
    $order->setInvoiceNumber($authorizeDetail['invoice_number']);

    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("priorAuthCaptureTransaction");
    $transactionRequestType->setRefTransId($authorizeDetail['transaction_id']);
    $transactionRequestType->setAmount($authorizeDetail['amount']);
    $transactionRequestType->setOrder($order);
    
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setTransactionRequest( $transactionRequestType);

    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse($this->pickEndpointClass);
    
    return $response;
  }

  /**
   * capturePreviouslyAuthorizedAmount : Now capture the previously authorized  amount
   * @param  array    $authorizeDetail
   * @return Object   $response
   */
  public function captureFundsAuthorizedThroughAnotherChannel($authorizeDetail = [])
  {
    $refId = 'ref' . time();


    $order = new AnetAPI\OrderType();
    $order->setInvoiceNumber($authorizeDetail['invoice_number']);

    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("captureOnlyTransaction");
    $transactionRequestType->setAmount($authorizeDetail['amount']);
    $transactionRequestType->setAuthCode($authorizeDetail['auth_code']);
    $transactionRequestType->setOrder($order);

    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setTransactionRequest($transactionRequestType);

    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse($this->pickEndpointClass);

    return $response;
  }

  /**
   * chargeCreditCard : Charge the already existing card
   * @param  array    $authorizeDetail
   * @return Object   $response
   */
  public function chargeCreditCard($authorizeDetail = [])
  {
    $refId = 'ref' . time();

    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber($authorizeDetail['card_number']);
    $creditCard->setExpirationDate($authorizeDetail['expiration_date']);
    $creditCard->setCardCode($authorizeDetail['card_code']);

    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);

    $order = new AnetAPI\OrderType();
    $order->setDescription($authorizeDetail['order_desc']);

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
    $response = $controller->executeWithApiResponse($this->pickEndpointClass);

    return $response;
  }

  /**
   * authorizeCustomerPaymentProfile : Authorize the already existing card
   * @param  array    $authorizeDetail
   * @return Object   $response
   */
  public function authorizeCustomerPaymentProfile($authorizeDetail = [])
  {
    $refId = 'ref' . time();

    $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
    $profileToCharge->setCustomerProfileId($authorizeDetail['profile_id']);

    $order = new AnetAPI\OrderType();
    $order->setInvoiceNumber($authorizeDetail['invoice_number']);

    $paymentProfile = new AnetAPI\PaymentProfileType();
    $paymentProfile->setPaymentProfileId($authorizeDetail['payment_profile_id']);

    $profileToCharge->setPaymentProfile($paymentProfile);

    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "authOnlyTransaction" ); 
    $transactionRequestType->setAmount($authorizeDetail['amount']);
    $transactionRequestType->setProfile($profileToCharge);
    $transactionRequestType->setOrder($order);

    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setRefId( $refId );
    $request->setTransactionRequest( $transactionRequestType );

    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse($this->pickEndpointClass);

    if(!$response) {
      return false;
    }
    
    $tresponse = $response->getTransactionResponse();

    return $tresponse;
  }

  /**
   * chargeCustomerProfile : charge the already existing customer profile
   * @param  array    $authorizeDetail
   * @return Object   $response
   */
  public function chargeCustomerProfile($authorizeDetail = [])
  {
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
    $response = $controller->executeWithApiResponse($this->pickEndpointClass);

    return $response;
  }

  /**
   * voidTransaction : void previously authorized amount
   * @param  array    $authorizeDetail
   * @return Object   $response
   */
  public function voidTransaction($authorizeDetail = [])
  {
    $refId = 'ref' . time();

    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType( "voidTransaction");
    $transactionRequestType->setRefTransId($authorizeDetail['transaction_id']);

    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setRefId($refId);
    $request->setTransactionRequest( $transactionRequestType);

    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse($this->pickEndpointClass);

    return $response;
  }
}
