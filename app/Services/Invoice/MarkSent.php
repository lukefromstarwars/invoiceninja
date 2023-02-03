<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2023. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Services\Invoice;

use App\Events\Invoice\InvoiceWasUpdated;
use App\Jobs\Util\WebhookHandler;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Webhook;
use App\Services\AbstractService;
use App\Utils\Ninja;

class MarkSent extends AbstractService
{
    public $client;

    public $invoice;

    public function __construct(Client $client, Invoice $invoice)
    {
        $this->client = $client;

        $this->invoice = $invoice;
    }

    public function run($fire_webhook = false)
    {

        /* Return immediately if status is not draft or invoice has been deleted */
        if ($this->invoice && ($this->invoice->fresh()->status_id != Invoice::STATUS_DRAFT || $this->invoice->is_deleted)) {
            return $this->invoice;
        }

        $adjustment = $this->invoice->amount;

        /*Set status*/
        $this->invoice
             ->service()
             ->setStatus(Invoice::STATUS_SENT)
             ->updateBalance($adjustment, true)
             ->save();

        /*Update ledger*/
        $this->invoice
             ->ledger()
             ->updateInvoiceBalance($adjustment, "Invoice {$this->invoice->number} marked as sent.");

        /* Perform additional actions on invoice */
        $this->invoice
             ->service()
             ->applyNumber()
             ->setDueDate()
             ->touchPdf()
             ->setReminder()
             ->save();

        /*Adjust client balance*/
        $this->invoice->client->service()->updateBalance($adjustment)->save();

        $this->invoice->markInvitationsSent();

        event(new InvoiceWasUpdated($this->invoice, $this->invoice->company, Ninja::eventVars(auth()->user() ? auth()->user()->id : null)));

        if($fire_webhook)
            event('eloquent.updated: App\Models\Invoice', $this->invoice);

        $subscriptions = Webhook::where('company_id', $this->invoice->company_id)
                            ->where('event_id', Webhook::EVENT_SENT_INVOICE)
                            ->exists();

        if ($subscriptions) {
            WebhookHandler::dispatch(Webhook::EVENT_SENT_INVOICE, $this->invoice, $this->invoice->company, 'client')->delay(0);
        }

        return $this->invoice->fresh();
    }
}
