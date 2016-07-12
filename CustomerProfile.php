<?php

namespace App\Alonti\ANetWrapper;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use App\Alonti\ANetWrapper\ANetConstants;


/**
* A wrapper class to combined logic for nessasary CIM Authorize.Net
* @author Avneesh Gupta <avneesh@softway.com>
*/
class CustomerProfile
{
  
  function __construct()
  {
    // Common setup for API credentials
    $this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $this->merchantAuthentication->setName(ANetConstants::MERCHANT_LOGIN_ID);
    $this->merchantAuthentication->setTransactionKey(ANetConstants::MERCHANT_TRANSACTION_KEY);
  }

  public function getCustomerProfile($authorizeDetail = []){
    $refId = 'ref' . time();
    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber( $authorizeDetail['card_number'] );
    $creditCard->setExpirationDate( $authorizeDetail['expiration_date'] );
    $paymentCreditCard = new AnetAPI\PaymentType();
    $paymentCreditCard->setCreditCard($creditCard);
    // Create the Bill To info
    $billto = new AnetAPI\CustomerAddressType();
    $billto->setFirstName($authorizeDetail['first_name']);
    $billto->setLastName($authorizeDetail['last_name']);
    $billto->setCompany($authorizeDetail['company']);
    $billto->setAddress($authorizeDetail['address']);
    $billto->setCity($authorizeDetail['city']);
    $billto->setState($authorizeDetail['state']);
    $billto->setZip($authorizeDetail['zipcode']);
    $billto->setCountry($authorizeDetail['country']);
  
    // Create a Customer Profile Request
    //  1. create a Payment Profile
    //  2. create a Customer Profile   
    //  3. Submit a CreateCustomerProfile Request
    //  4. Validate Profiiel ID returned
    $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
    $paymentprofile->setCustomerType('individual');
    $paymentprofile->setBillTo($billto);
    $paymentprofile->setPayment($paymentCreditCard);
    $paymentprofiles[] = $paymentprofile;
    $customerprofile = new AnetAPI\CustomerProfileType();
    $customerprofile->setDescription($authorizeDetail['description']);
    $merchantCustomerId = time().rand(1,150);
    $customerprofile->setMerchantCustomerId($merchantCustomerId);
    $customerprofile->setEmail($authorizeDetail['email']);
    $customerprofile->setPaymentProfiles($paymentprofiles);
    $request = new AnetAPI\CreateCustomerProfileRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setRefId( $refId );
    $request->setProfile($customerprofile);
    $controller = new AnetController\CreateCustomerProfileController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
      echo "SUCCESS: CreateCustomerProfile PROFILE ID : " . $response->getCustomerProfileId() . "\n";
      $profileIdRequested = $response->getCustomerProfileId();
     }
    else
    {
      echo "ERROR :  CreateCustomerProfile: Invalid response\n";
      $errorMessages = $response->getMessages()->getMessage();
      echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    // Retrieve an existing customer profile along with all the associated payment profiles and shipping addresses
    $request = new AnetAPI\GetCustomerProfileRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setCustomerProfileId($profileIdRequested);
    $controller = new AnetController\GetCustomerProfileController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
      echo "GetCustomerProfile SUCCESS : " .  "\n";
      $profileSelected = $response->getProfile();
      $paymentProfilesSelected = $profileSelected->getPaymentProfiles();
      echo "Profile Has " . count($paymentProfilesSelected). " Payment Profiles" . "\n";
      if($response->getSubscriptionIds() != null) 
      {
        if($response->getSubscriptionIds() != null)
        {
          echo "List of subscriptions:";
          foreach($response->getSubscriptionIds() as $subscriptionid)
            echo $subscriptionid . "\n";
        }
      }
    }
    else
    {
    echo "ERROR :  GetCustomerProfile: Invalid response\n";
    $errorMessages = $response->getMessages()->getMessage();
    echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
  }

  public function createCustomerProfile($authorizeDetail = []){
    $refId = 'ref' . time();
    // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber( $authorizeDetail['card_number'] );
    $creditCard->setExpirationDate( $authorizeDetail['expiration_date'] );
    $paymentCreditCard = new AnetAPI\PaymentType();
    $paymentCreditCard->setCreditCard($creditCard);
    // Create the Bill To info
    $billto = new AnetAPI\CustomerAddressType();
    $billto->setFirstName($authorizeDetail['first_name']);
    $billto->setLastName($authorizeDetail['last_name']);
    $billto->setCompany($authorizeDetail['company']);
    $billto->setAddress($authorizeDetail['address']);
    $billto->setCity($authorizeDetail['city']);
    $billto->setState($authorizeDetail['state']);
    $billto->setZip($authorizeDetail['zipcode']);
    $billto->setCountry($authorizeDetail['country']);
    
   // Create a Customer Profile Request
   //  1. create a Payment Profile
   //  2. create a Customer Profile   
   //  3. Submit a CreateCustomerProfile Request
   //  4. Validate Profiiel ID returned
    $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
    $paymentprofile->setCustomerType('individual');
    $paymentprofile->setBillTo($billto);
    $paymentprofile->setPayment($paymentCreditCard);
    $paymentprofiles[] = $paymentprofile;
    $customerprofile = new AnetAPI\CustomerProfileType();
    $customerprofile->setDescription($authorizeDetail['description']);
    $customerprofile->setMerchantCustomerId($authorizeDetail['customer_id']);
    $customerprofile->setEmail($authorizeDetail['email']);
    $customerprofile->setPaymentProfiles($paymentprofiles);
    $request = new AnetAPI\CreateCustomerProfileRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setRefId( $refId );
    $request->setProfile($customerprofile);
    $controller = new AnetController\CreateCustomerProfileController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
      echo "Succesfully create customer profile : " . $response->getCustomerProfileId() . "\n";
      $paymentProfiles = $response->getCustomerPaymentProfileIdList();
      echo "SUCCESS: PAYMENT PROFILE ID : " . $paymentProfiles[0] . "\n";
     }
    else
    {
      echo "ERROR :  Invalid response\n";
      $errorMessages = $response->getMessages()->getMessage();
          echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
  }

  public function getCustomerProfileIds(){
    $refId = 'ref' . time();
    // Get all existing customer profile ID's
    $request = new AnetAPI\GetCustomerProfileIdsRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $controller = new AnetController\GetCustomerProfileIdsController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
        echo "GetCustomerProfileId's SUCCESS: " . "\n";
        $profileIds[] = $response->getIds();
        echo "There are " . count($profileIds[0]) . " Customer Profile ID's for this Merchant Name and Transaction Key" . "\n";
     }
    else
    {
        echo "GetCustomerProfileId's ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
  }

  public function getCustomerShippingAddress($authorizeDetail = []){
    // An existing customer profile id and shipping address id for this merchant name and transaction key
    $customerProfileId = $authorizeDetail['customerprofile_id'];
    $customerAddressId = $authorizeDetail['customeraddress_id'];
    $request = new AnetAPI\GetCustomerShippingAddressRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setCustomerProfileId($customerProfileId);
    $request->setCustomerAddressId($customerAddressId);
    
    $controller = new AnetController\GetCustomerShippingAddressController($request);
    
    //Retrieving existing customer shipping address
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
      echo "Get Customer Shipping Address SUCCESS" . "\n";
      echo "  FirstName   : " . $response->getAddress()->getFirstName() . "\n";
      echo "  LastName  : " . $response->getAddress()->getLastName() . "\n";
      echo "  Company   : " . $response->getAddress()->getCompany() . "\n";
      echo "  Address   : " . $response->getAddress()->getAddress() . "\n";
      echo "  City    : " . $response->getAddress()->getCity() . "\n";
      echo "  State     : " . $response->getAddress()->getState() . "\n";
      echo "  Zip     : " . $response->getAddress()->getZip() . "\n";
      echo "  Country   : " . $response->getAddress()->getCountry() . "\n";
      echo "  Phone Number  : " . $response->getAddress()->getPhoneNumber() . "\n";
      echo "  FAX Number  : " . $response->getAddress()->getFaxNumber() . "\n";
      echo "Customer AddressId  : " . $response->getAddress()->getCustomerAddressId() . "\n";
    if($response->getSubscriptionIds() != null) 
    {
      if($response->getSubscriptionIds() != null)
      {
        echo "List of subscriptions:";
        foreach($response->getSubscriptionIds() as $subscriptionid)
          echo $subscriptionid . "\n";
      }
    }
     }
    else
    {
      echo "Get Customer Shipping Address  ERROR :  Invalid response\n";
      $errorMessages = $response->getMessages()->getMessage();
      echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
  }

  public function updateCustomerProfile($authorizeDetail = []){
    $refId = 'ref' . time();
      // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber( $authorizeDetail['card_number'] );
    $creditCard->setExpirationDate( $authorizeDetail['expiration_date'] );
    $paymentCreditCard = new AnetAPI\PaymentType();
    $paymentCreditCard->setCreditCard($creditCard);
    // Create the Bill To info
    $billto = new AnetAPI\CustomerAddressType();
    $billto->setFirstName($authorizeDetail['first_name']);
    $billto->setLastName($authorizeDetail['last_name']);
    $billto->setCompany($authorizeDetail['company']);
    $billto->setAddress($authorizeDetail['address']);
    $billto->setCity($authorizeDetail['city']);
    $billto->setState($authorizeDetail['state']);
    $billto->setZip($authorizeDetail['zipcode']);
    $billto->setCountry($authorizeDetail['country']);
    
   // Create a Customer Profile Request
   //  1. create a Payment Profile
   //  2. create a Customer Profile   
   //  3. Submit a CreateCustomerProfile Request
   //  4. Validate Profiiel ID returned
    $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
    $paymentprofile->setCustomerType('individual');
    $paymentprofile->setBillTo($billto);
    $paymentprofile->setPayment($paymentCreditCard);
    $paymentprofiles[] = $paymentprofile;
    $customerprofile = new AnetAPI\CustomerProfileType();
    $customerprofile->setDescription($authorizeDetail['description']);
    $merchantCustomerId = time().rand(1,150);
    $customerprofile->setMerchantCustomerId($merchantCustomerId);
    $customerprofile->setEmail($authorizeDetail['email']);
    $customerprofile->setPaymentProfiles($paymentprofiles);
    $request = new AnetAPI\CreateCustomerProfileRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setRefId( $refId );
    $request->setProfile($customerprofile);
    $controller = new AnetController\CreateCustomerProfileController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
        echo "SUCCESS: CreateCustomerProfile PROFILE ID : " . $response->getCustomerProfileId() . "\n";
        $profileidcreated = $response->getCustomerProfileId();
    }
    else
    {
        echo "ERROR :  CreateCustomerProfile: Invalid response\n";
    }
    // Update an existing customer profile
    $updatecustomerprofile = new AnetAPI\CustomerProfileExType();
    $updatecustomerprofile->setCustomerProfileId($profileidcreated);
    $updatecustomerprofile->setDescription( $authorizeDetail['updated_description'] );
    $updatecustomerprofile->setEmail( $authorizeDetail['updated_email'] );
    $request = new AnetAPI\UpdateCustomerProfileRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setProfile($updatecustomerprofile);
    $controller = new AnetController\UpdateCustomerProfileController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
      echo "UpdateCustomerProfile SUCCESS : " .  "\n";
    // Validate the description and e-mail that was updated
        $request = new AnetAPI\GetCustomerProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setCustomerProfileId( $profileidcreated );
        $controller = new AnetController\GetCustomerProfileController($request);
        $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
        {
            echo "Get updated CustomerProfile  SUCCESS : " .  "\n";
            $profileselected = $response->getProfile();
            echo "Updated Customer Profile Customer description : " . $profileselected->getDescription() . "\n";
            echo "Updated Customer Profile EMail description : " . $profileselected->getEmail() . "\n";
        }
        else
        {
          echo "ERROR :  GetCustomerProfile: Invalid response\n";
          $errorMessages = $response->getMessages()->getMessage();
          echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
        }
    }
    else
    {
      echo "ERROR :  UpdateCustomerProfile: Invalid response\n";
      $errorMessages = $response->getMessages()->getMessage();
      echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
  }

  public function updateCustomerPaymentProfile($authorizeDetail = [])
  {
    $refId = 'ref' . time();

    //Set profile ids of profile to be updated
    $request = new AnetAPI\UpdateCustomerPaymentProfileRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setCustomerProfileId($authorizeDetail['customer_profile_id']);
    $controller = new AnetController\GetCustomerProfileController($request);

    // We're updating the billing address but everything has to be passed in an update
    // For card information you can pass exactly what comes back from an GetCustomerPaymentProfile
    // if you don't need to update that info
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber($authorizeDetail['card_number']);
    $creditCard->setExpirationDate($authorizeDetail['expiration_date']);
    $paymentCreditCard = new AnetAPI\PaymentType();
    $paymentCreditCard->setCreditCard($creditCard);

    // Create the Bill To info for new payment type
    $billto = new AnetAPI\CustomerAddressType();
    $billto->setFirstName($authorizeDetail['first_name']);
    $billto->setLastName($authorizeDetail['last_name']);
    $billto->setAddress($authorizeDetail['address']);
    $billto->setCity($authorizeDetail['city']);
    $billto->setState($authorizeDetail['state']);
    $billto->setZip($authorizeDetail['zipcode']);
    $billto->setPhoneNumber($authorizeDetail['phone_number']);
    $billto->setfaxNumber($authorizeDetail['fax_number']);
    $billto->setCountry($authorizeDetail['country']);

    // Create the Customer Payment Profile object
    $paymentprofile = new AnetAPI\CustomerPaymentProfileExType();
    $paymentprofile->setCustomerPaymentProfileId($authorizeDetail['customer_payment_profile_id']);
    $paymentprofile->setBillTo($billto);
    $paymentprofile->setPayment($paymentCreditCard);

    // Submit a UpdatePaymentProfileRequest
    $request->setPaymentProfile( $paymentprofile );

    $controller = new AnetController\UpdateCustomerPaymentProfileController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    return $response;
  }

  public function createCustomerShippingAddress($authorizeDetail = []){
    
    // Use An existing customer profile id for this merchant name and transaction key
    // Create the customer shipping address
    $customershippingaddress = new AnetAPI\CustomerAddressType();
    $customershippingaddress->setFirstName( $authorizeDetail['first_name'] );
    $customershippingaddress->setLastName( $authorizeDetail['last_name'] );
    $customershippingaddress->setCompany( $authorizeDetail['company'] );
    $customershippingaddress->setAddress( $authorizeDetail['address'] );
    $customershippingaddress->setCity( $authorizeDetail['city'] );
    $customershippingaddress->setState( $authorizeDetail['state'] );
    $customershippingaddress->setZip( $authorizeDetail['zipcode'] );
    $customershippingaddress->setCountry( $authorizeDetail['country'] );
    $customershippingaddress->setPhoneNumber( $authorizeDetail['phone_number'] );
    $customershippingaddress->setFaxNumber( $authorizeDetail['fax_number'] );
    // Create a new customer shipping address for an existing customer profile
    $request = new AnetAPI\CreateCustomerShippingAddressRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setCustomerProfileId($authorizeDetail['existingcustomer_profile_id']);
    $request->setAddress($customershippingaddress);
    $controller = new AnetController\CreateCustomerShippingAddressController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
      echo "Create Customer Shipping Address SUCCESS: ADDRESS ID : " . $response-> getCustomerAddressId() . "\n";
     }
    else
    {
      echo "Create Customer Shipping Address  ERROR :  Invalid response\n";
      $errorMessages = $response->getMessages()->getMessage();
          echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
  }

  public function updateCustomerShippingAddress($authorizeDetail = []){
    // An existing customer profile id for this merchant name and transaction key
    $existingcustomerprofileid = $customerprofileid;
    // Create the customer shipping address
    $customershippingaddress = new AnetAPI\CustomerAddressExType();
    $customershippingaddress->setFirstName( $authorizeDetail['first_name'] );
    $customershippingaddress->setLastName( $authorizeDetail['last_name'] );
    $customershippingaddress->setCompany( $authorizeDetail['company'] );
    $customershippingaddress->setAddress( $authorizeDetail['address'] );
    $customershippingaddress->setCity( $authorizeDetail['city'] );
    $customershippingaddress->setState( $authorizeDetail['state'] );
    $customershippingaddress->setZip( $authorizeDetail['zipcode'] );
    $customershippingaddress->setCountry( $authorizeDetail['country'] );
    $customershippingaddress->setPhoneNumber( $authorizeDetail['phone_number'] );
    $customershippingaddress->setFaxNumber( $authorizeDetail['fax_number'] );
    $customershippingaddress->setCustomerAddressId( $authorizeDetail['customer_address_id'] );
    // Update an existing customer shipping address for an existing customer profile
    $request = new AnetAPI\UpdateCustomerShippingAddressRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setCustomerProfileId( $authorizeDetail['existing_customer_profile_id']);
    $request->setAddress( $authorizeDetail['customer_shipping_address'] );
    $controller = new AnetController\UpdateCustomerShippingAddressController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
        echo "Update Customer Shipping Address SUCCESS.\n";
     }
    else
    {
        echo "Update Customer Shipping Address  ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
  }

  public function deleteCustomerShippingAddress($authorizeDetail = []){
    // Use an existing customer profile and address id for this merchant name and transaction key
    // Delete an existing customer shipping address for an existing customer profile
    $request = new AnetAPI\DeleteCustomerShippingAddressRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setCustomerProfileId($authorizeDetail['customer_profile_id']);
    $request->setCustomerAddressId($authorizeDetail['customer_address_id']);
    $controller = new AnetController\DeleteCustomerShippingAddressController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
      echo "Delete Customer Shipping Address SUCCESS" . "\n";
     }
    else
    {
      echo "Delete Customer Shipping Address  ERROR :  Invalid response\n";
      $errorMessages = $response->getMessages()->getMessage();
      echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
  }

  public function validateCustomerPaymentProfile($authorizeDetail = []){
    
    // Use an existing payment profile ID for this Merchant name and Transaction key
    //validationmode tests , does not send an email receipt
    $validationmode = "testMode";

    $request = new AnetAPI\ValidateCustomerPaymentProfileRequest();
    
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setCustomerProfileId($authorizeDetail['customer_profile_id']);
    $request->setCustomerPaymentProfileId($authorizeDetail['customer_payment_profile_id']);
    $request->setValidationMode($validationmode);
    
    $controller = new AnetController\ValidateCustomerPaymentProfileController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);

    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
        $validationMessages = $response->getMessages()->getMessage();
        echo "Response : " . $validationMessages[0]->getCode() . "  " .$validationMessages[0]->getText() . "\n";
     }
    else
    {
        echo "ERROR :  Validate Customer Payment Profile: Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
        echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
  }

  public function createCustomerPaymentProfile($authorizeDetail = []){
    $refId = 'ref' . time();
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber( $authorizeDetail['card_number'] );
    $creditCard->setExpirationDate( $authorizeDetail['expiration_date'] );
    $paymentCreditCard = new AnetAPI\PaymentType();
    $paymentCreditCard->setCreditCard($creditCard);
    // Create the Bill To info for new payment type
    $billto = new AnetAPI\CustomerAddressType();
    $billto->setFirstName( $authorizeDetail['first_name'] );
    $billto->setLastName( $authorizeDetail['last_name'] );
    $billto->setCompany( $authorizeDetail['company'] );
    $billto->setAddress( $authorizeDetail['address'] );
    $billto->setCity( $authorizeDetail['city'] );
    $billto->setState( $authorizeDetail['state'] );
    $billto->setZip( $authorizeDetail['zipcode'] );
    $billto->setPhoneNumber( $authorizeDetail['phone_number'] );
    $billto->setfaxNumber( $authorizeDetail['fax_number'] );
    $billto->setCountry( $authorizeDetail['country'] );
    // Create a new Customer Payment Profile
    $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
    $paymentprofile->setCustomerType('individual');
    $paymentprofile->setBillTo($billto);
    $paymentprofile->setPayment($paymentCreditCard);
    $paymentprofiles[] = $paymentprofile;
    // Submit a CreateCustomerPaymentProfileRequest to create a new Customer Payment Profile
    $paymentprofilerequest = new AnetAPI\CreateCustomerPaymentProfileRequest();
    $paymentprofilerequest->setMerchantAuthentication($this->merchantAuthentication);
    //Use an existing profile id
    $paymentprofilerequest->setCustomerProfileId( $authorizeDetail['existing_customer_profile_id'] );
    $paymentprofilerequest->setPaymentProfile( $paymentprofile );
    $controller = new AnetController\CreateCustomerPaymentProfileController($paymentprofilerequest);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
     echo "Create Customer Payment Profile SUCCESS: " . $response->getCustomerPaymentProfileId() . "\n";
     }
    else
    {
     echo "Create Customer Payment Profile: ERROR Invalid response\n";
     $errorMessages = $response->getMessages()->getMessage();
     echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
     
    }
    return $response;
  }

  public function getCustomerPaymentProfile($authorizeDetail = []){
    $refId = 'ref' . time();
    //request requires customerProfileId and customerPaymentProfileId
    $request = new AnetAPI\GetCustomerPaymentProfileRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setRefId( $refId);
    $request->setCustomerProfileId($authorizeDetail['customer_profile_id']);
    $request->setCustomerPaymentProfileId($authorizeDetail['customer_payment_profile_id']);
    $controller = new AnetController\GetCustomerPaymentProfileController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    /*if(($response != null)){
      if ($response->getMessages()->getResultCode() == "Ok")
      {
        echo "GetCustomerPaymentProfile SUCCESS: " . "\n";
        echo "Customer Payment Profile Id: " . $response->getPaymentProfile()->getCustomerPaymentProfileId() . "\n";
        echo "Customer Payment Profile Billing Address: " . $response->getPaymentProfile()->getbillTo()->getAddress(). "\n";
        echo "Customer Payment Profile Card Last 4 " . $response->getPaymentProfile()->getPayment()->getCreditCard()->getCardNumber(). "\n";
        if($response->getPaymentProfile()->getSubscriptionIds() != null) 
        {
          if($response->getPaymentProfile()->getSubscriptionIds() != null)
          {
            echo "List of subscriptions:";
            foreach($response->getPaymentProfile()->getSubscriptionIds() as $subscriptionid)
              echo $subscriptionid . "\n";
          }
        }
      }
      else
      {
        echo "GetCustomerPaymentProfile ERROR :  Invalid response\n";
        $errorMessages = $response->getMessages()->getMessage();
          echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
      }
    }
    else{
      echo "NULL Response Error";
    }*/
    return $response;
  }

  public function deleteCustomerPaymentProfile($authorizeDetail = []) {
    // Use an existing payment profile ID for this Merchant name and Transaction key
    $request = new AnetAPI\DeleteCustomerPaymentProfileRequest();
    $request->setMerchantAuthentication($this->merchantAuthentication);
    $request->setCustomerProfileId( $authorizeDetail['customer_profile_id'] );
    $request->setCustomerPaymentProfileId($authorizeDetail['customer_payment_profile_id']);
    $controller = new AnetController\DeleteCustomerPaymentProfileController($request);
    $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
    {
      echo "SUCCESS: Delete Customer Payment Profile  SUCCESS  :" . "\n";
    }
    else
    {
      echo "ERROR :  Delete Customer Payment Profile: Invalid response\n";
      $errorMessages = $response->getMessages()->getMessage();
      echo "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText() . "\n";
    }
    return $response;
  }

}