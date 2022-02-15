<?php

/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2021. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\PaymentDrivers\Authorize;

use App\Exceptions\GenericPaymentDriverFailure;
use App\Factory\ClientContactFactory;
use App\Factory\ClientFactory;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\ClientGatewayToken;
use App\PaymentDrivers\AuthorizePaymentDriver;
use Illuminate\Support\Facades\Cache;
use net\authorize\api\contract\v1\CreateCustomerProfileRequest;
use net\authorize\api\contract\v1\CustomerProfileType;
use net\authorize\api\contract\v1\GetCustomerProfileIdsRequest;
use net\authorize\api\contract\v1\GetCustomerProfileRequest;
use net\authorize\api\controller\CreateCustomerProfileController;
use net\authorize\api\controller\GetCustomerProfileController;
use net\authorize\api\controller\GetCustomerProfileIdsController;

/**
 * Class AuthorizeCustomer.
 */
class AuthorizeCustomer
{
    public $authorize;

    public function __construct(AuthorizePaymentDriver $authorize)
    {
        $this->authorize = $authorize;
    }

    private function getCustomerProfileIds()
    {

        // Get all existing customer profile ID's
        $request = new GetCustomerProfileIdsRequest();
        $request->setMerchantAuthentication($this->authorize->merchant_authentication);
        $controller = new GetCustomerProfileIdsController($request);
        $response = $controller->executeWithApiResponse($this->authorize->mode());
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
        {

            return $response->getIds();

        }
        else
        {
            return [];

            nlog( "GetCustomerProfileId's ERROR :  Invalid response");
            $errorMessages = $response->getMessages()->getMessage();
            nlog( "Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText());
        }

    }

    private function getCustomerProfile($customer_profile_id)
    {

      $request = new GetCustomerProfileRequest();
      $request->setMerchantAuthentication($this->authorize->merchant_authentication);
      $request->setCustomerProfileId($customer_profile_id);
      $controller = new GetCustomerProfileController($request);
      $response = $controller->executeWithApiResponse($this->authorize->mode());
      if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") )
      {
        $profileSelected = $response->getProfile();
        $paymentProfilesSelected = $profileSelected->getPaymentProfiles();

        return [
            'email' => $profileSelected->getEmail(),
            'payment_profiles' => $paymentProfilesSelected,
            'error' => ''
        ];

      }
      else
      {

        nlog("ERROR :  GetCustomerProfile: Invalid response");
        $errorMessages = $response->getMessages()->getMessage();
        nlog("Response : " . $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText());

        return [
            'profile' => NULL,
            'payment_profiles' => NULL,
            'error' => $errorMessages[0]->getCode() . "  " .$errorMessages[0]->getText(),
        ];

      }
    }

    public function importCustomers()
    {
        $auth_customers = $this->getCustomerProfileIds();
        $company = $this->authorize->company_gateway->company;
        $user = $company->owner();

        foreach($auth_customers as $customer)
        {


            $profile = $this->getCustomerProfile($customer);

            //if the profile ID already exists in ClientGatewayToken we continue else - add.
            if($client = ClientGatewayToken::where('company_id', $company->id)->where('gateway_customer_reference', $customer)->first()){
                
            }
            elseif($client_contact = ClientContact::where('company_id', $company->id)->where('email', $profile['email'])->first()){
                $client = $client_contact->client;
            }
            else {

                $first_payment_profile = $profile['payment_profiles'][0];

                $client = ClientFactory::create($company->id, $user->id);
                $billTo = $first_payment_profile->getBillTo();
                $client->address1 = $billTo->getAddress();
                $client->city = $billTo->getCity();
                $client->state = $billTo->getState();
                $client->postal_code = $billTo->getZip();
                $client->country_id = $billTo->getCountry() ? $this->getCountryCode($billTo->getCountry()) : $company->settings->country_id;
                $client->save();

                $client_contact = ClientContactFactory::create($company->id, $user->id);
                $client_contact->client_id = $client->id;
                $client_contact->first_name = $billTo->getFirstName();
                $client_contact->last_name = $billTo->getLastName();
                $client_contact->email = $profile['email'];
                $client_contact->phone = $billTo->getPhoneNumber();
                $client_contact->save();
            }

            if($client){

                foreach($profile['payment_profiles'] as $payment_profile)
                {

                    $data['payment_profile_id'] = $payment_profile->getCustomerPaymentProfileId();
                    $data['card_number'] = $payment_profile->getPayment()->getCreditCard()->getCardNumber();
                    $data['card_expiry'] = $payment_profile->getPayment()->getCreditCard()->getExpirationDate();
                    $data['card_type'] = $payment_profile->getPayment()->getCreditCard()->getCardType();

                    return $data;
                }
            }

        }
        //iterate through auth.net list
        
        //exclude any existing customers (ie. only import their missing payment profiles)
        
    }

    private function getCountryCode($country_code)
    {
        $countries = Cache::get('countries');

        $country = $countries->filter(function ($item) use ($country_code) {
            return $item->iso_3166_2 == $country_code || $item->iso_3166_3 == $country_code;
        })->first();

        return (string) $country->id;
    }
}    


