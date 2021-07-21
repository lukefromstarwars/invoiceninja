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

namespace App\PaymentDrivers\PayTrace;

use App\Exceptions\PaymentFailed;
use App\Jobs\Mail\PaymentFailureMailer;
use App\Jobs\Util\SystemLogger;
use App\Models\ClientGatewayToken;
use App\Models\GatewayType;
use App\Models\Payment;
use App\Models\PaymentHash;
use App\Models\PaymentType;
use App\Models\SystemLog;
use App\PaymentDrivers\PayFastPaymentDriver;
use App\PaymentDrivers\PaytracePaymentDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CreditCard
{

    public $paytrace_driver;

    public function __construct(PaytracePaymentDriver $paytrace_driver)
    {
        $this->paytrace_driver = $paytrace_driver;
    }

    public function authorizeView($data)
    {
        
        $data['client_key'] = $this->paytrace_driver->getAuthToken();
        $data['gateway'] = $this->paytrace_driver;

        return render('gateways.paytrace.authorize', $data);
    }


 	public function authorizeResponse($request)
 	{
        $data = $request->all();
        
        nlog($data);

        $post_data = [
            'customer_id' => Str::random(32),
            'hpf_token' => $data['HPF_Token'],
            'enc_key' => $data['enc_key'],
            'integrator_id' => '959195xd1CuC',
            'billing_address' => [
                'name' => $this->paytrace_driver->client->present()->name(),
                'street_address' => $this->paytrace_driver->client->address1,
                'city' => $this->paytrace_driver->client->city,
                'state' => $this->paytrace_driver->client->state,
                'zip' => $this->paytrace_driver->client->postal_code
            ],
        ];
        
        nlog($post_data);

        //  "_token" => "Vl1xHflBYQt9YFSaNCPTJKlY5x3rwcFE9kvkw71I"
        //   "company_gateway_id" => "1"
        //   "HPF_Token" => "e484a92c-90ed-4468-ac4d-da66824c75de"
        //   "enc_key" => "zqz6HMHCXALWdX5hyBqrIbSwU7TBZ0FTjjLB3Cp0FQY="
        //   "amount" => "Amount"
        //   "q" => "/client/payment_methods"
        //   "method" => "1"
        // ]

        // "customer_id":"customer789",
        // "hpf_token":"e369847e-3027-4174-9161-fa0d4e98d318",
        // "enc_key":"lI785yOBMet4Rt9o4NLXEyV84WBU3tdStExcsfoaOoo=",
        // "integrator_id":"xxxxxxxxxx",
        // "billing_address":{
        //     "name":"Mark Smith",
        //     "street_address":"8320 E. West St.",
        //     "city":"Spokane",
        //     "state":"WA",
        //     "zip":"85284"
        // }
        $response = $this->paytrace_driver->gatewayRequest('/v1/customer/pt_protect_create', $post_data);

        // dd($response);

  // +"success": true
  // +"response_code": 160
  // +"status_message": "The customer profile for PLS5U60OoLUfQXzcmtJYNefPA0gTthzT/11 was successfully created."
  // +"customer_id": "PLS5U60OoLUfQXzcmtJYNefPA0gTthzT"


        // make a cc card out of that bra
        return redirect()->route('client.payment_methods.index');

 	}  

    public function paymentView($data)
    {


        return render('gateways.paytrace.pay', $data);

    }


    public function paymentResponse(Request $request)
    {
        $response_array = $request->all();



        // if($response_array['payment_status'] == 'COMPLETE') {

        //     $this->payfast->logSuccessfulGatewayResponse(['response' => $response_array, 'data' => $this->paytrace_driver->payment_hash], SystemLog::TYPE_PAYFAST);

        //     return $this->processSuccessfulPayment($response_array);
        // }
        // else {
        //     $this->processUnsuccessfulPayment($response_array);
        // }
    }

    private function processSuccessfulPayment($response_array)
    {



        // $payment = $this->paytrace_driver->createPayment($payment_record, Payment::STATUS_COMPLETED);


    }

    private function processUnsuccessfulPayment($server_response)
    {
        // PaymentFailureMailer::dispatch($this->paytrace_driver->client, $server_response->cancellation_reason, $this->paytrace_driver->client->company, $server_response->amount);

        // PaymentFailureMailer::dispatch(
        //     $this->paytrace_driver->client,
        //     $server_response,
        //     $this->paytrace_driver->client->company,
        //     $server_response['amount_gross']
        // );

        // $message = [
        //     'server_response' => $server_response,
        //     'data' => $this->paytrace_driver->payment_hash->data,
        // ];

        // SystemLogger::dispatch(
        //     $message,
        //     SystemLog::CATEGORY_GATEWAY_RESPONSE,
        //     SystemLog::EVENT_GATEWAY_FAILURE,
        //     SystemLog::TYPE_PAYFAST,
        //     $this->payfast->client,
        //     $this->payfast->client->company,
        // );

        // throw new PaymentFailed('Failed to process the payment.', 500);
    }

}