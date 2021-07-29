<?php

namespace App\PaymentDrivers\Mollie;

use App\Exceptions\PaymentFailed;
use App\Http\Requests\ClientPortal\Payments\PaymentResponseRequest;
use App\Jobs\Mail\PaymentFailureMailer;
use App\Jobs\Util\SystemLogger;
use App\Models\GatewayType;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\SystemLog;
use App\PaymentDrivers\MolliePaymentDriver;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class CreditCard
{
    /**
     * @var MolliePaymentDriver
     */
    protected $mollie;

    public function __construct(MolliePaymentDriver $mollie)
    {
        $this->mollie = $mollie;

        $this->mollie->init();
    }

    /**
     * Show the page for credit card payments.
     * 
     * @param array $data 
     * @return Factory|View 
     */
    public function paymentView(array $data)
    {
        $data['gateway'] = $this->mollie;

        return render('gateways.mollie.credit_card.pay', $data);
    }

    /**
     * Create a payment object.
     * 
     * @param PaymentResponseRequest $request 
     * @return mixed 
     */
    public function paymentResponse(PaymentResponseRequest $request)
    {
        dd($this->mollie->gateway->mandates->listForId('cst_6S77wEkuQT'));
        
        // TODO: Unit tests.
        $amount = number_format((float) $this->mollie->payment_hash->data->amount_with_fee, 2, '.', '');

        $this->mollie->payment_hash
            ->withData('gateway_type_id', GatewayType::CREDIT_CARD)
            ->withData('client_id', $this->mollie->client->id);

        try {
            $customer = $this->mollie->gateway->customers->create([
                'name' => $this->mollie->client->name,
                'metadata' => [
                    'id' => $this->mollie->client->hashed_id,
                ],
            ]);

            $payment = $this->mollie->gateway->payments->create([
                'customerId' => $customer->id,
                'sequenceType' => 'first',
                'amount' => [
                    'currency' => $this->mollie->client->currency()->code,
                    'value' => $amount,
                ],
                'description' => \sprintf('Hash: %s', $this->mollie->payment_hash->hash),
                'redirectUrl' => route('mollie.3ds_redirect', [
                    'company_key' => $this->mollie->client->company->company_key,
                    'company_gateway_id' => $this->mollie->company_gateway->hashed_id,
                    'hash' => $this->mollie->payment_hash->hash,
                ]),
                'webhookUrl'  => 'https://invoiceninja.com',
                'cardToken' => $request->token,
            ]);

            if ($payment->status === 'paid') {
                $this->mollie->logSuccessfulGatewayResponse(
                    ['response' => $payment, 'data' => $this->mollie->payment_hash],
                    SystemLog::TYPE_MOLLIE
                );

                return $this->processSuccessfulPayment($payment);
            }

            if ($payment->status === 'open') {
                $this->mollie->payment_hash->withData('payment_id', $payment->id);

                return redirect($payment->getCheckoutUrl());
            }
        } catch (\Exception $e) {
            $this->processUnsuccessfulPayment($e);

            throw new PaymentFailed($e->getMessage(), $e->getCode());
        }
    }

    public function processSuccessfulPayment(\Mollie\Api\Resources\Payment $payment)
    {
        $payment_hash = $this->mollie->payment_hash;

        $data = [
            'gateway_type_id' => GatewayType::CREDIT_CARD,
            'amount' => array_sum(array_column($payment_hash->invoices(), 'amount')) + $payment_hash->fee_total,
            'payment_type' => PaymentType::CREDIT_CARD_OTHER,
            'transaction_reference' => $payment->id,
        ];

        $payment_record = $this->mollie->createPayment($data, Payment::STATUS_COMPLETED);

        SystemLogger::dispatch(
            ['response' => $payment, 'data' => $data],
            SystemLog::CATEGORY_GATEWAY_RESPONSE,
            SystemLog::EVENT_GATEWAY_SUCCESS,
            SystemLog::TYPE_MOLLIE,
            $this->mollie->client,
            $this->mollie->client->company,
        );

        return redirect()->route('client.payments.show', ['payment' => $this->mollie->encodePrimaryKey($payment_record->id)]);
    }

    public function processUnsuccessfulPayment(\Exception $e)
    {
        PaymentFailureMailer::dispatch(
            $this->mollie->client,
            $e->getMessage(),
            $this->mollie->client->company,
            $this->mollie->payment_hash->data->amount_with_fee
        );

        SystemLogger::dispatch(
            $e->getMessage(),
            SystemLog::CATEGORY_GATEWAY_RESPONSE,
            SystemLog::EVENT_GATEWAY_FAILURE,
            SystemLog::TYPE_MOLLIE,
            $this->mollie->client,
            $this->mollie->client->company,
        );

        throw new PaymentFailed($e->getMessage(), $e->getCode());
    }

    /**
     * Show authorization page.
     *  
     * @param array $data 
     * @return Factory|View 
     */
    public function authorizeView(array $data)
    {
        return render('gateways.mollie.credit_card.authorize', $data);
    }

    public function authorizeResponse($request)
    {
        $customer = $this->mollie->gateway->customers->create([
            'name' => $this->mollie->client->name,
            'metadata' => [
                'id' => $this->mollie->client->hashed_id,
            ],
        ]);

        // Save $customer->id to database..
    }
}
