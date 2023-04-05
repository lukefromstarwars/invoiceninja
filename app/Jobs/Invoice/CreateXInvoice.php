<?php

namespace App\Jobs\Invoice;

use App\Models\Invoice;
use App\Models\Country;
use horstoeko\zugferd\ZugferdDocumentBuilder;
use horstoeko\zugferd\ZugferdDocumentPdfBuilder;
use horstoeko\zugferd\ZugferdProfiles;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;


class CreateXInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Invoice $invoice;

    public function __construct(Invoice $invoice, bool $alterPDF, string $custompdfpath = "")
    {
        $this->invoice = $invoice;
        $this->alterpdf = $alterPDF;
        $this->custompdfpath = $custompdfpath;
    }

    /**
     * Execute the job.
     *
     *
     * @return string
     */
    public function handle(): string
    {
        $invoice = $this->invoice;
        $company = $invoice->company;
        $client = $invoice->client;
        $profile = "";
        switch ($company->xinvoice_type){
            case "EN16931":
                $profile = ZugferdProfiles::PROFILE_EN16931;
                break;
            case "XInvoice_2_2":
                $profile = ZugferdProfiles::PROFILE_XRECHNUNG_2_2;
                break;
            case "XInvoice_2_1":
                $profile = ZugferdProfiles::PROFILE_XRECHNUNG_2_1;
                break;
            case "XInvoice_2_0":
                $profile = ZugferdProfiles::PROFILE_XRECHNUNG_2;
                break;
            case "XInvoice_1_0":
                $profile = ZugferdProfiles::PROFILE_XRECHNUNG;
                break;
            case "XInvoice-Extended":
                $profile = ZugferdProfiles::PROFILE_EXTENDED;
                break;
            case "XInvoice-BasicWL":
                $profile = ZugferdProfiles::PROFILE_BASICWL;
                break;
            case "XInvoice-Basic":
                $profile = ZugferdProfiles::PROFILE_BASIC;
                break;
        }
        $xrechnung =  ZugferdDocumentBuilder::CreateNew($profile);

        $xrechnung
            ->setDocumentInformation($invoice->number, "380", date_create($invoice->date), $invoice->client->getCurrencyCode())
            ->setDocumentSupplyChainEvent(date_create($invoice->date))
            ->setDocumentSeller($company->getSetting('name'))
            ->setDocumentSellerAddress($company->getSetting("address1"), "", "", $company->getSetting("postal_code"), $company->getSetting("city"), $company->country()->iso_3166_2)
            ->setDocumentBuyer($client->name, $client->number)
            ->setDocumentBuyerAddress($client->address1, "", "", $client->postal_code, $client->city, $client->country->iso_3166_2)
            ->setDocumentBuyerReference($client->leitweg_id)
            ->setDocumentBuyerContact($client->primary_contact()->first()->first_name." ".$client->primary_contact()->first()->last_name, "", $client->primary_contact()->first()->phone, "", $client->primary_contact()->first()->email)
            ->addDocumentPaymentTerm(ctrans("texts.xinvoice_payable", ['payeddue' => date_create($invoice->date)->diff(date_create($invoice->due_date))->format("%d"), 'paydate' => $invoice->due_date]));
        if (!empty($invoice->public_notes)){
            $xrechnung->addDocumentNote($invoice->public_notes);
        }
        if(!empty($invoice->po_number)){
            $xrechnung->setDocumentBuyerOrderReferencedDocument($invoice->po_number);
        }

        if (str_contains($company->getSetting('vat_number'), "/")){
            $xrechnung->addDocumentSellerTaxRegistration("FC", $company->getSetting('vat_number'));
         }
        else {
            $xrechnung->addDocumentSellerTaxRegistration("VA", $company->getSetting('vat_number'));
        }

        $invoicingdata = $invoice->calc();


        //Create line items and calculate taxes
        $taxtype1 = "";
        switch ($company->tax_type1){
            case "ZeroRate":
                $taxtype1 = "Z";
                break;
            case "Tax Exempt":
                $taxtype1 = "E";
                break;
            case "Reversal of tax liabilty":
                $taxtype1 = "AE";
                break;
            case "intra-community delivery":
                $taxtype1 = "K";
                break;
            case "Out of EU":
                $taxtype1 = "G";
                break;
            case "Outside the tax scope":
                $taxtype1 = "O";
                break;
            case "Canary Islands":
                $taxtype1 = "L";
                break;
            case "Ceuta / Melila":
                $taxtype1 = "M";
                break;
            default:
                $taxtype1 = "S";
                break;
        }
        $taxtype2 = "";
        switch ($company->tax_type2){
            case "ZeroRate":
                $taxtype2 = "Z";
                break;
            case "Tax Exempt":
                $taxtype2 = "E";
                break;
            case "Reversal of tax liabilty":
                $taxtype2 = "AE";
                break;
            case "intra-community delivery":
                $taxtype2 = "K";
                break;
            case "Out of EU":
                $taxtype2 = "G";
                break;
            case "Outside the tax scope":
                $taxtype2 = "O";
                break;
            case "Canary Islands":
                $taxtype2 = "L";
                break;
            case "Ceuta / Melila":
                $taxtype2 = "M";
                break;
            default:
                $taxtype2 = "S";
                break;
        }
        $taxtype3 = "";
        switch ($company->tax_type3){
            case "ZeroRate":
                $taxtype3 = "Z";
                break;
            case "Tax Exempt":
                $taxtype3 = "E";
                break;
            case "Reversal of tax liabilty":
                $taxtype3 = "AE";
                break;
            case "intra-community delivery":
                $taxtype3 = "K";
                break;
            case "Out of EU":
                $taxtype3 = "G";
                break;
            case "Outside the tax scope":
                $taxtype3 = "O";
                break;
            case "Canary Islands":
                $taxtype3 = "L";
                break;
            case "Ceuta / Melila":
                $taxtype3 = "M";
                break;
            default:
                $taxtype3 = "S";
                break;
        }
        foreach ($invoice->line_items as $index => $item){
            $xrechnung->addNewPosition($index)
                ->setDocumentPositionProductDetails($item->notes)
                ->setDocumentPositionGrossPrice($item->gross_line_total)
                ->setDocumentPositionNetPrice($item->line_total);
            if (isset($item->task_id)){
                $xrechnung->setDocumentPositionQuantity($item->quantity, "HUR");
            }
            else{
                $xrechnung->setDocumentPositionQuantity($item->quantity, "H87");
            }
            // According to european law, each artical can only have one tax percentage
            if (empty($item->tax_name1) && empty($item->tax_name2) && empty($item->tax_name3)){
                if (!empty($invoice->tax_name1)){
                    $xrechnung->addDocumentPositionTax($taxtype1, 'VAT', $invoice->tax_rate1);
                }
                elseif (!empty($invoice->tax_name2)){
                    $xrechnung->addDocumentPositionTax($taxtype2, 'VAT', $invoice->tax_rate2);
                }
                elseif (!empty($invoice->tax_name3)){
                    $xrechnung->addDocumentPositionTax($taxtype3, 'VAT', $invoice->tax_rate3);
                }
                else{
                    nlog("Can't add correct tax position");
                }
            }
            else {
                if ($item->tax_name1 != "" && $item->tax_name2 == "" && $item->tax_name3 == ""){
                    $xrechnung->addDocumentPositionTax($taxtype1, 'VAT', $item->tax_rate1);
                }
                elseif ($item->tax_name1 == "" && $item->tax_name2 != "" && $item->tax_name3 == ""){
                    $xrechnung->addDocumentPositionTax($taxtype2, 'VAT', $item->tax_rate2);
                }
                elseif ($item->tax_name1 == "" && $item->tax_name2 == "" && $item->tax_name3 != ""){
                    $xrechnung->addDocumentPositionTax($taxtype3, 'VAT', $item->tax_rate3);
                }
            }
        }


        if ($invoice->isPartial()){
            $xrechnung->setDocumentSummation($invoice->amount, $invoice->amount-$invoice->balance, $invoicingdata->getSubTotal(), $invoicingdata->getTotalSurcharges(), $invoicingdata->getTotalDiscount(), $invoicingdata->getSubTotal(), $invoicingdata->getItemTotalTaxes(), null, $invoice->partial);
        } else {
            $xrechnung->setDocumentSummation($invoice->amount, $invoice->amount-$invoice->balance, $invoicingdata->getSubTotal(), $invoicingdata->getTotalSurcharges(), $invoicingdata->getTotalDiscount(), $invoicingdata->getSubTotal(), $invoicingdata->getItemTotalTaxes(), null, 0.0);
        }

        if (count($invoicingdata->getTaxMap()) > 0){
            $tax = explode(" ", $invoicingdata->getTaxMap()[0]["name"]);
            $xrechnung->addDocumentTax($taxtype1, "VAT", $invoicingdata->getTaxMap()[0]["total"]/(explode("%", end($tax))[0]/100), $invoicingdata->getTaxMap()[0]["total"], explode("%", end($tax))[0]);
        }
        if (count($invoicingdata->getTaxMap()) > 1) {
            $tax = explode(" ", $invoicingdata->getTaxMap()[1]["name"]);
            $xrechnung->addDocumentTax($taxtype2, "VAT", $invoicingdata->getTaxMap()[1]["total"]/(explode("%", end($tax))[0]/100), $invoicingdata->getTaxMap()[1]["total"], explode("%", end($tax))[0]);
        }
        if (count($invoicingdata->getTaxMap()) > 2) {
            $tax = explode(" ", $invoicingdata->getTaxMap()[2]["name"]);
            $xrechnung->addDocumentTax($taxtype3, "VAT", $invoicingdata->getTaxMap()[2]["total"]/(explode("%", end($tax))[0]/100), $invoicingdata->getTaxMap()[2]["total"], explode("%", end($tax))[0]);
        }

        $disk = config('filesystems.default');
        if(!Storage::exists($client->xinvoice_filepath($invoice->invitations->first()))){
            Storage::makeDirectory($client->xinvoice_filepath($invoice->invitations->first()));
        }
        $xrechnung->writeFile(Storage::disk($disk)->path($client->xinvoice_filepath($invoice->invitations->first()) . $invoice->getFileName("xml")));

        if ($this->alterpdf){
            if ($this->custompdfpath != ""){
                $pdfBuilder = new ZugferdDocumentPdfBuilder($xrechnung, $this->custompdfpath);
                $pdfBuilder->generateDocument();
                $pdfBuilder->saveDocument($this->custompdfpath);
            }
            else {
                $filepath_pdf = $client->invoice_filepath($invoice->invitations->first()).$invoice->getFileName();
                $file = Storage::disk($disk)->exists($filepath_pdf);
                if ($file) {
                    $pdfBuilder = new ZugferdDocumentPdfBuilder($xrechnung, Storage::disk($disk)->path($filepath_pdf));
                    $pdfBuilder->generateDocument();
                    $pdfBuilder->saveDocument(Storage::disk($disk)->path($filepath_pdf));
                }
            }
        }
        return $client->invoice_filepath($invoice->invitations->first()).$invoice->getFileName("xml");
    }
}
