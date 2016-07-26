<?php

namespace App\Alonti\ANetWrapper;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use App\Alonti\ANetWrapper\ANetConstants;


/**
* A wrapper class to combined logic for nessasary for CIM Authorize.Net
* @author Avneesh Gupta <avneeshroks@gmail.com>
*/
class CustomerProfile
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
        $this->validationMode = ANetConstants::VALIDATION_MODE;
    }

    /**
     * getCustomerProfileIds : Get all existing customer profile ID's
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function getCustomerProfileIds()
    {
        $refId = 'ref' . time();

        $request = new AnetAPI\GetCustomerProfileIdsRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);

        $controller = new AnetController\GetCustomerProfileIdsController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);

        return $response;
    }

    /**
     * Retrieve an existing customer profile along with all the associated payment profiles and shipping addresses
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function getCustomerProfile($authorizeDetail = [])
    {
        $request = new AnetAPI\GetCustomerProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);

        $request->setCustomerProfileId($authorizeDetail['profile_id']);

        $controller = new AnetController\GetCustomerProfileController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);

        return $response;
    }

    /**
     * createCustomerProfile
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function createCustomerProfile($authorizeDetail = [])
    {
        $refId = 'ref' . time();

        $customerprofile = new AnetAPI\CustomerProfileType();
        $customerprofile->setDescription($authorizeDetail['description']);
        $customerprofile->setMerchantCustomerId($authorizeDetail['customer_id']);
        $customerprofile->setEmail($authorizeDetail['email']);

        $request = new AnetAPI\CreateCustomerProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setRefId( $refId );
        $request->setProfile($customerprofile);

        $controller = new AnetController\CreateCustomerProfileController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);

        return $response;
    }

    /**
     * updateCustomerProfile : Update existing customer profile id and shipping address
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function updateCustomerProfile($authorizeDetail = [])
    {
        $updatecustomerprofile = new AnetAPI\CustomerProfileExType();
        $updatecustomerprofile->setCustomerProfileId( $authorizeDetail['existing_customer_profile_id'] );
        $updatecustomerprofile->setDescription( $authorizeDetail['updated_description'] );
        $updatecustomerprofile->setEmail( $authorizeDetail['updated_email'] );
        
        $request = new AnetAPI\UpdateCustomerProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setProfile($updatecustomerprofile);

        $controller = new AnetController\UpdateCustomerProfileController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);
        
        return $response;
    }

    /**
     * deleteCustomerProfile : Delete an existing customer profile
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function deleteCustomerProfile($authorizeDetail = [])
    {
        $refId = 'ref' . time();

        $request = new AnetAPI\DeleteCustomerProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setCustomerProfileId( $authorizeDetail['profile_id'] );

        $controller = new AnetController\DeleteCustomerProfileController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);

        return $response;
    }

    /**
     * getCustomerShippingAddress : An existing customer profile id and shipping address id
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function getCustomerShippingAddress($authorizeDetail = [])
    {
        $customerProfileId = $authorizeDetail['customerprofile_id'];
        $customerAddressId = $authorizeDetail['customeraddress_id'];

        $request = new AnetAPI\GetCustomerShippingAddressRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setCustomerProfileId($customerProfileId);
        $request->setCustomerAddressId($customerAddressId);

        $controller = new AnetController\GetCustomerShippingAddressController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);

        return $response;
    }

    /**
     * createCustomerShippingAddress : new customer shipping address for an existing customer profile
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function createCustomerShippingAddress($authorizeDetail = [])
    {
        $customershippingaddress = new AnetAPI\CustomerAddressType();
        $customershippingaddress->setFirstName( substr($authorizeDetail['first_name'], 0, 9) );
        $customershippingaddress->setLastName( substr($authorizeDetail['last_name'], 0, 9) );
        $customershippingaddress->setCompany( substr($authorizeDetail['company'], 0, 9) );
        $customershippingaddress->setAddress( substr($authorizeDetail['address'], 0, 9) );
        $customershippingaddress->setCity( substr($authorizeDetail['city'], 0, 9) );
        $customershippingaddress->setState( substr($authorizeDetail['state'], 0, 9) );
        $customershippingaddress->setZip( substr($authorizeDetail['zipcode'], 0, 9) );
        $customershippingaddress->setCountry( substr($authorizeDetail['country'], 0, 9) );
        $customershippingaddress->setPhoneNumber( $authorizeDetail['phone_number'] );
        $customershippingaddress->setFaxNumber( $authorizeDetail['fax_number'] );

        
        $request = new AnetAPI\CreateCustomerShippingAddressRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setCustomerProfileId($authorizeDetail['existingcustomer_profile_id']);
        $request->setAddress($customershippingaddress);
        
        $controller = new AnetController\CreateCustomerShippingAddressController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);
        
        return $response;
    }

    /**
     * updateCustomerShippingAddress : update customer shipping address for an existing customer profile
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function updateCustomerShippingAddress($authorizeDetail = [])
    {
        $customershippingaddress = new AnetAPI\CustomerAddressExType();
        $customershippingaddress->setFirstName( substr($authorizeDetail['first_name'], 0, 9) );
        $customershippingaddress->setLastName( substr($authorizeDetail['last_name'], 0, 9) );
        $customershippingaddress->setCompany( substr($authorizeDetail['company'], 0, 9) );
        $customershippingaddress->setAddress( substr($authorizeDetail['address'], 0, 9) );
        $customershippingaddress->setCity( substr($authorizeDetail['city'], 0, 9) );
        $customershippingaddress->setState( substr($authorizeDetail['state'], 0, 9) );
        $customershippingaddress->setZip( substr($authorizeDetail['zipcode'], 0, 9) );
        $customershippingaddress->setCountry( substr($authorizeDetail['country'], 0, 9) );
        $customershippingaddress->setPhoneNumber( $authorizeDetail['phone_number'] );
        $customershippingaddress->setFaxNumber( $authorizeDetail['fax_number'] );
        $customershippingaddress->setCustomerAddressId( $authorizeDetail['customer_address_id'] );

        // Update an existing customer shipping address for an existing customer profile
        $request = new AnetAPI\UpdateCustomerShippingAddressRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setCustomerProfileId( $authorizeDetail['existing_customer_profile_id']);
        $request->setAddress( substr($authorizeDetail['customer_shipping_address'], 0, 9) );
        
        $controller = new AnetController\UpdateCustomerShippingAddressController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);
        
        return $response;
    }

    /**
     * deleteCustomerShippingAddress : delete customer shipping address for an existing customer profile
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function deleteCustomerShippingAddress($authorizeDetail = [])
    {
        $request = new AnetAPI\DeleteCustomerShippingAddressRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setCustomerProfileId($authorizeDetail['customer_profile_id']);
        $request->setCustomerAddressId($authorizeDetail['customer_address_id']);
        
        $controller = new AnetController\DeleteCustomerShippingAddressController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);
        
        return $response;
    }

    /**
     * getCustomerPaymentProfile : get customer payment profile for an existing customer profile
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function getCustomerPaymentProfile($authorizeDetail = [])
    {
        $refId = 'ref' . time();

        $request = new AnetAPI\GetCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setRefId( $refId);
        $request->setCustomerProfileId($authorizeDetail['customer_profile_id']);
        $request->setCustomerPaymentProfileId($authorizeDetail['customer_payment_profile_id']);

        $controller = new AnetController\GetCustomerPaymentProfileController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);

        return $response;
    }

    /**
     * createCustomerPaymentProfile : create customer payment profile for an existing customer profile
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function createCustomerPaymentProfile($authorizeDetail = [])
    {
        $refId = 'ref' . time();

        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber( $authorizeDetail['card_number'] );
        $creditCard->setExpirationDate( $authorizeDetail['expiration_date'] );
        
        $paymentCreditCard = new AnetAPI\PaymentType();
        $paymentCreditCard->setCreditCard($creditCard);

        $billto = new AnetAPI\CustomerAddressType();
        $billto->setFirstName( substr($authorizeDetail['first_name'], 0, 9) );
        $billto->setLastName( substr($authorizeDetail['last_name'], 0, 9) );
        $billto->setCompany( substr($authorizeDetail['company'], 0, 9) );
        $billto->setAddress( substr($authorizeDetail['address'], 0, 9) );
        $billto->setCity( substr($authorizeDetail['city'], 0, 9) );
        $billto->setState( substr($authorizeDetail['state'], 0, 9) );
        $billto->setZip( substr($authorizeDetail['zipcode'], 0, 9) );
        $billto->setPhoneNumber( $authorizeDetail['phone_number'] );
        $billto->setfaxNumber( $authorizeDetail['fax_number'] );
        $billto->setCountry( substr($authorizeDetail['country'], 0, 9) );
        
        $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
        $paymentprofile->setCustomerType('individual');
        $paymentprofile->setBillTo($billto);
        $paymentprofile->setPayment($paymentCreditCard);
        $paymentprofiles[] = $paymentprofile;
        
        $paymentprofilerequest = new AnetAPI\CreateCustomerPaymentProfileRequest();
        $paymentprofilerequest->setMerchantAuthentication($this->merchantAuthentication);
        $paymentprofilerequest->setCustomerProfileId( $authorizeDetail['existing_customer_profile_id'] );
        $paymentprofilerequest->setPaymentProfile( $paymentprofile );
        
        $controller = new AnetController\CreateCustomerPaymentProfileController($paymentprofilerequest);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);

        return $response;
    }

    /**
     * updateCustomerPaymentProfile : update customer payment profile for an existing customer profile
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function updateCustomerPaymentProfile($authorizeDetail = [])
    {
        $refId = 'ref' . time();

        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($authorizeDetail['card_number']);
        $creditCard->setExpirationDate($authorizeDetail['expiration_date']);

        $paymentCreditCard = new AnetAPI\PaymentType();
        $paymentCreditCard->setCreditCard($creditCard);

        $billto = new AnetAPI\CustomerAddressType();
        $billto->setFirstName(substr($authorizeDetail['first_name'], 0, 9));
        $billto->setLastName(substr($authorizeDetail['last_name'], 0, 9));
        $billto->setAddress(substr($authorizeDetail['address'], 0, 9));
        $billto->setCity(substr($authorizeDetail['city'], 0, 9));
        $billto->setState(substr($authorizeDetail['state'], 0, 9));
        $billto->setZip(substr($authorizeDetail['zipcode'], 0, 9));
        $billto->setPhoneNumber($authorizeDetail['phone_number']);
        $billto->setfaxNumber($authorizeDetail['fax_number']);
        $billto->setCountry(substr($authorizeDetail['country'], 0, 9));

        $paymentprofile = new AnetAPI\CustomerPaymentProfileExType();
        $paymentprofile->setCustomerPaymentProfileId($authorizeDetail['customer_payment_profile_id']);
        $paymentprofile->setBillTo($billto);
        $paymentprofile->setPayment($paymentCreditCard);

        $request = new AnetAPI\UpdateCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setCustomerProfileId($authorizeDetail['customer_profile_id']);
        $request->setPaymentProfile( $paymentprofile );

        $controller = new AnetController\UpdateCustomerPaymentProfileController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);

        return $response;
    }

    /**
     * deleteCustomerPaymentProfile : delete customer payment profile for an existing customer profile
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function deleteCustomerPaymentProfile($authorizeDetail = [])
    {
        $request = new AnetAPI\DeleteCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setCustomerProfileId( $authorizeDetail['customer_profile_id'] );
        $request->setCustomerPaymentProfileId($authorizeDetail['customer_payment_profile_id']);

        $controller = new AnetController\DeleteCustomerPaymentProfileController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);
        
        return $response;
    }

    /**
     * validateCustomerPaymentProfile : se an existing payment profile ID for this Merchant name and Transaction key
     * validationmode tests , does not send an email receipt
     * @param  array    $authorizeDetail
     * @return Object   $response
     */
    public function validateCustomerPaymentProfile($authorizeDetail = [])
    {
        $request = new AnetAPI\ValidateCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setCustomerProfileId($authorizeDetail['customer_profile_id']);
        $request->setCustomerPaymentProfileId($authorizeDetail['customer_payment_profile_id']);
        $request->setValidationMode($this->validationmode);

        $controller = new AnetController\ValidateCustomerPaymentProfileController($request);
        $response = $controller->executeWithApiResponse($this->pickEndpointClass);

        return $response;
    }
}