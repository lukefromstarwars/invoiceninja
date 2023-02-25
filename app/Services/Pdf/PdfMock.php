<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Services\Pdf;

use App\Models\Quote;
use App\Models\Client;
use App\Models\Credit;
use App\Models\Design;
use App\Models\Vendor;
use App\Models\Account;
use App\Models\Company;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Currency;
use App\Models\PurchaseOrder;
use App\Services\Pdf\PdfBuilder;
use App\Services\Pdf\PdfService;
use App\Models\InvoiceInvitation;
use App\Services\Pdf\PdfDesigner;
use App\Services\Pdf\PdfConfiguration;

class PdfMock
{
    private mixed $mock;


    public function __construct(public mixed $entity_type)
    {}

    public function getPdf(): mixed
    {

        $pdf_service = new PdfService($this->mock->invitation);

        $pdf_config = (new PdfConfiguration($pdf_service));
        $pdf_config->entity = $this->mock;
        $pdf_config->setTaxMap($this->mock->tax_map);
        $pdf_config->setTotalTaxMap($this->mock->total_tax_map);
        $pdf_config->setCurrency(Currency::find(1));
        $pdf_config->setCountry(Country::find(840));
        $pdf_config->client = $this->mock->client;
        $pdf_config->entity_design_id = 'invoice_design_id';
        $pdf_config->settings_object = $this->mock->client;
        $pdf_config->entity_string = 'invoice';
        $pdf_config->settings = (object)$pdf_config->service->company->settings;
        $pdf_config->setPdfVariables();
        $pdf_config->design = Design::find(2);
        $pdf_config->currency_entity = $this->mock->client;
        
        $pdf_service->config = $pdf_config;

        $pdf_designer = (new PdfDesigner($pdf_service))->build();
        $pdf_service->designer = $pdf_designer;

        $pdf_service->html_variables = $this->getStubVariables();

        $pdf_builder = (new PdfBuilder($pdf_service))->build();
        $pdf_service->builder = $pdf_builder;

        $html = $pdf_service->getHtml();

        return $pdf_service->resolvePdfEngine($html);
    }

    public function build(): self
    {

        $this->mock = $this->initEntity();

        return $this;

    }

    private function initEntity(): mixed
    {
        match ($this->entity_type) {
            Invoice::class => $entity = Invoice::factory()->make(),
            Quote::class => $entity = Quote::factory()->make(),
            Credit::class => $entity = Credit::factory()->make(),
            PurchaseOrder::class => $entity = PurchaseOrder::factory()->make(),
            default => $entity = Invoice::factory()->make()
        };

        if($this->entity_type == PurchaseOrder::class){
            $entity->vendor = Vendor::factory()->make();
        }
        else{
            $entity->client = Client::factory()->make();
        }
    
        $entity->tax_map = $this->getTaxMap();
        $entity->total_tax_map = $this->getTotalTaxMap();
        $entity->invitation = InvoiceInvitation::factory()->make();
        $entity->invitation->company = Company::factory()->make();
        $entity->invitation->company->account = Account::factory()->make();

        return $entity;
    
    }

    private function getTaxMap()
    {
        return collect( [['name' => 'GST', 'total' => 10]]);
    }

    private function getTotalTaxMap()
    {
        return [['name' => 'GST', 'total' => 10]];
    }

    public function getStubVariables()
    {
       return ['values' => 
        [
    '$client.shipping_postal_code' => '46420',
    '$client.billing_postal_code' => '11243',
    '$company.city_state_postal' => '90210',
    '$company.postal_city_state' => 'CA',
    '$product.gross_line_total' => '100',
    '$client.postal_city_state' => '11243 Aufderharchester, North Carolina',
    '$client.shipping_address1' => '453',
    '$client.shipping_address2' => '66327 Waters Trail',
    '$client.city_state_postal' => 'Aufderharchester, North Carolina 11243',
    '$client.shipping_address' => '453<br/>66327 Waters Trail<br/>Aufderharchester, North Carolina 11243<br/>Afghanistan<br/>',
    '$client.billing_address2' => '63993 Aiyana View',
    '$client.billing_address1' => '8447',
    '$client.shipping_country' => 'USA',
    '$invoiceninja.whitelabel' => 'https://raw.githubusercontent.com/invoiceninja/invoiceninja/v5-develop/public/images/new_logo.png',
    '$client.billing_address' => '8447<br/>63993 Aiyana View<br/>Aufderharchester, North Carolina 11243<br/>Afghanistan<br/>',
    '$client.billing_country' => 'USA',
    '$task.gross_line_total' => '100',
    '$contact.portal_button' => '<a class="button" href="http://ninja.test:8000/client/key_login/zJJEjlUtXPiNnnnyO2tcYia64PSwauidy61eDnMU?client_hash=nzikYQITs1kyUK61GScTNW67JwhTRkOBVdvsHzIv">View client portal</a>',
    '$client.shipping_state' => 'Delaware',
    '$invoice.public_notes' => 'These are some public notes for your document',
    '$client.shipping_city' => 'Kesslerport',
    '$client.billing_state' => 'North Carolina',
    '$product.description' => 'A Product Description',
    '$product.product_key' => 'A Product Key',
    '$entity.public_notes' => 'Entity Public notes',
    '$invoice.balance_due' => '$0.00',
    '$client.public_notes' => '&nbsp;',
    '$company.postal_code' => '&nbsp;',
    '$client.billing_city' => 'Aufderharchester',
    '$secondary_font_name' => 'Roboto',
    '$product.line_total' => '',
    '$product.tax_amount' => '',
    '$company.vat_number' => '&nbsp;',
    '$invoice.invoice_no' => '0029',
    '$quote.quote_number' => '0029',
    '$client.postal_code' => '11243',
    '$contact.first_name' => 'Benedict',
    '$secondary_font_url' => 'https://fonts.googleapis.com/css2?family=Roboto&display=swap',
    '$contact.signature' => '',
    '$product.tax_name1' => '',
    '$product.tax_name2' => '',
    '$product.tax_name3' => '',
    '$product.unit_cost' => '',
    '$quote.valid_until' => '2023-10-24',
    '$custom_surcharge1' => '$0.00',
    '$custom_surcharge2' => '$0.00',
    '$custom_surcharge3' => '$0.00',
    '$custom_surcharge4' => '$0.00',
    '$quote.balance_due' => '$0.00',
    '$company.id_number' => '&nbsp;',
    '$invoice.po_number' => '&nbsp;',
    '$invoice_total_raw' => 0.0,
    '$postal_city_state' => '11243 Aufderharchester, North Carolina',
    '$client.vat_number' => '975977515',
    '$city_state_postal' => 'Aufderharchester, North Carolina 11243',
    '$contact.full_name' => 'Benedict Eichmann',
    '$contact.last_name' => 'Eichmann',
    '$company.country_2' => 'US',
    '$product.product1' => '',
    '$product.product2' => '',
    '$product.product3' => '',
    '$product.product4' => '',
    '$statement_amount' => '',
    '$task.description' => '',
    '$product.discount' => '',
    '$entity_issued_to' => '',
    '$assigned_to_user' => '',
    '$product.quantity' => '',
    '$total_tax_labels' => '',
    '$total_tax_values' => '',
    '$invoice.discount' => '$0.00',
    '$invoice.subtotal' => '$0.00',
    '$company.address2' => '&nbsp;',
    '$partial_due_date' => '&nbsp;',
    '$invoice.due_date' => '&nbsp;',
    '$client.id_number' => '&nbsp;',
    '$credit.po_number' => '&nbsp;',
    '$company.address1' => '&nbsp;',
    '$credit.credit_no' => '0029',
    '$invoice.datetime' => '25/Feb/2023 1:10 am',
    '$contact.custom1' => NULL,
    '$contact.custom2' => NULL,
    '$contact.custom3' => NULL,
    '$contact.custom4' => NULL,
    '$task.line_total' => '',
    '$line_tax_labels' => '',
    '$line_tax_values' => '',
    '$secondary_color' => '#7081e0',
    '$invoice.balance' => '$0.00',
    '$invoice.custom1' => '&nbsp;',
    '$invoice.custom2' => '&nbsp;',
    '$invoice.custom3' => '&nbsp;',
    '$invoice.custom4' => '&nbsp;',
    '$company.custom1' => '&nbsp;',
    '$company.custom2' => '&nbsp;',
    '$company.custom3' => '&nbsp;',
    '$company.custom4' => '&nbsp;',
    '$quote.po_number' => '&nbsp;',
    '$company.website' => '&nbsp;',
    '$balance_due_raw' => '0.00',
    '$entity.datetime' => '25/Feb/2023 1:10 am',
    '$credit.datetime' => '25/Feb/2023 1:10 am',
    '$client.address2' => '63993 Aiyana View',
    '$client.address1' => '8447',
    '$user.first_name' => 'Derrick Monahan DDS',
    '$created_by_user' => 'Derrick Monahan DDS Erna Wunsch',
    '$client.currency' => 'USD',
    '$company.country' => 'United States',
    '$company.address' => 'United States<br/>',
    '$tech_hero_image' => 'http://ninja.test:8000/images/pdf-designs/tech-hero-image.jpg',
    '$task.tax_name1' => '',
    '$task.tax_name2' => '',
    '$task.tax_name3' => '',
    '$client.balance' => '$0.00',
    '$client_balance' => '$0.00',
    '$credit.balance' => '$0.00',
    '$credit_balance' => '$0.00',
    '$gross_subtotal' => '$0.00',
    '$invoice.amount' => '$0.00',
    '$client.custom1' => '&nbsp;',
    '$client.custom2' => '&nbsp;',
    '$client.custom3' => '&nbsp;',
    '$client.custom4' => '&nbsp;',
    '$emailSignature' => '&nbsp;',
    '$invoice.number' => '0029',
    '$quote.quote_no' => '0029',
    '$quote.datetime' => '25/Feb/2023 1:10 am',
    '$client_address' => '8447<br/>63993 Aiyana View<br/>Aufderharchester, North Carolina 11243<br/>Afghanistan<br/>',
    '$client.address' => '8447<br/>63993 Aiyana View<br/>Aufderharchester, North Carolina 11243<br/>Afghanistan<br/>',
    '$payment_button' => '<a class="button" href="http://ninja.test:8000/client/pay/UAUY8vIPuno72igmXbbpldwo5BDDKIqs">Pay Now</a>',
    '$payment_qrcode' => '<svg class=\'pqrcode\' viewBox=\'0 0 200 200\' width=\'200\' height=\'200\' x=\'0\' y=\'0\' xmlns=\'http://www.w3.org/2000/svg\'>
          <rect x=\'0\' y=\'0\' width=\'100%\'\' height=\'100%\' /><?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="200" height="200" viewBox="0 0 200 200"><rect x="0" y="0" width="200" height="200" fill="#fefefe"/><g transform="scale(4.878)"><g transform="translate(4,4)"><path fill-rule="evenodd" d="M9 0L9 1L8 1L8 2L9 2L9 3L8 3L8 4L10 4L10 7L11 7L11 4L12 4L12 5L13 5L13 4L12 4L12 2L14 2L14 7L15 7L15 6L16 6L16 8L15 8L15 10L14 10L14 11L16 11L16 12L14 12L14 13L15 13L15 14L14 14L14 15L15 15L15 14L17 14L17 15L16 15L16 16L14 16L14 17L15 17L15 18L14 18L14 19L13 19L13 18L11 18L11 15L8 15L8 12L9 12L9 13L10 13L10 14L11 14L11 13L12 13L12 14L13 14L13 13L12 13L12 11L13 11L13 10L11 10L11 11L10 11L10 9L11 9L11 8L6 8L6 9L5 9L5 8L0 8L0 10L1 10L1 12L2 12L2 11L3 11L3 10L4 10L4 11L5 11L5 12L3 12L3 13L7 13L7 14L6 14L6 15L5 15L5 14L1 14L1 15L0 15L0 19L1 19L1 20L0 20L0 25L1 25L1 20L2 20L2 19L3 19L3 20L4 20L4 21L5 21L5 20L6 20L6 21L8 21L8 23L7 23L7 22L5 22L5 24L4 24L4 25L8 25L8 27L10 27L10 28L11 28L11 29L9 29L9 28L8 28L8 33L9 33L9 30L11 30L11 29L12 29L12 32L13 32L13 33L14 33L14 32L15 32L15 33L17 33L17 32L19 32L19 31L18 31L18 30L16 30L16 28L17 28L17 29L18 29L18 28L19 28L19 27L18 27L18 26L17 26L17 27L16 27L16 26L15 26L15 25L16 25L16 24L18 24L18 25L19 25L19 23L18 23L18 22L19 22L19 20L17 20L17 19L20 19L20 25L21 25L21 26L22 26L22 28L21 28L21 27L20 27L20 33L21 33L21 30L24 30L24 32L25 32L25 33L27 33L27 32L29 32L29 33L32 33L32 32L33 32L33 31L31 31L31 32L29 32L29 30L32 30L32 29L33 29L33 27L32 27L32 26L31 26L31 25L32 25L32 24L31 24L31 25L30 25L30 23L29 23L29 21L30 21L30 22L31 22L31 21L32 21L32 22L33 22L33 21L32 21L32 20L33 20L33 18L32 18L32 20L31 20L31 21L30 21L30 19L29 19L29 18L28 18L28 17L25 17L25 16L28 16L28 15L30 15L30 14L31 14L31 17L30 17L30 18L31 18L31 17L32 17L32 16L33 16L33 15L32 15L32 14L31 14L31 13L32 13L32 12L33 12L33 11L32 11L32 10L31 10L31 9L32 9L32 8L31 8L31 9L30 9L30 8L29 8L29 10L28 10L28 11L30 11L30 14L29 14L29 12L27 12L27 11L26 11L26 10L25 10L25 9L26 9L26 8L25 8L25 9L23 9L23 8L24 8L24 7L25 7L25 5L23 5L23 3L24 3L24 4L25 4L25 3L24 3L24 2L25 2L25 0L24 0L24 1L23 1L23 0L21 0L21 1L20 1L20 4L21 4L21 5L22 5L22 7L23 7L23 8L22 8L22 9L18 9L18 8L19 8L19 6L20 6L20 8L21 8L21 6L20 6L20 5L19 5L19 6L18 6L18 5L17 5L17 2L18 2L18 1L19 1L19 0L18 0L18 1L17 1L17 0L16 0L16 1L17 1L17 2L16 2L16 3L15 3L15 2L14 2L14 1L15 1L15 0L14 0L14 1L11 1L11 2L10 2L10 0ZM21 1L21 2L22 2L22 3L23 3L23 2L22 2L22 1ZM10 3L10 4L11 4L11 3ZM15 4L15 5L16 5L16 4ZM8 5L8 7L9 7L9 5ZM12 6L12 9L14 9L14 8L13 8L13 6ZM17 6L17 7L18 7L18 6ZM23 6L23 7L24 7L24 6ZM16 8L16 9L17 9L17 10L16 10L16 11L17 11L17 10L18 10L18 11L20 11L20 10L18 10L18 9L17 9L17 8ZM27 8L27 9L28 9L28 8ZM1 9L1 10L2 10L2 9ZM4 9L4 10L5 10L5 11L6 11L6 12L7 12L7 11L9 11L9 10L8 10L8 9L6 9L6 10L5 10L5 9ZM22 9L22 10L21 10L21 11L22 11L22 10L23 10L23 11L24 11L24 12L23 12L23 13L22 13L22 14L21 14L21 12L18 12L18 13L17 13L17 12L16 12L16 13L17 13L17 14L21 14L21 15L20 15L20 16L19 16L19 15L17 15L17 16L16 16L16 18L21 18L21 19L22 19L22 18L21 18L21 17L22 17L22 16L23 16L23 19L25 19L25 18L24 18L24 16L23 16L23 13L24 13L24 14L25 14L25 12L26 12L26 15L27 15L27 14L28 14L28 13L27 13L27 12L26 12L26 11L24 11L24 10L23 10L23 9ZM6 10L6 11L7 11L7 10ZM30 10L30 11L31 11L31 10ZM10 12L10 13L11 13L11 12ZM1 15L1 17L2 17L2 18L1 18L1 19L2 19L2 18L3 18L3 19L4 19L4 20L5 20L5 19L6 19L6 20L8 20L8 21L10 21L10 23L8 23L8 24L10 24L10 27L11 27L11 26L14 26L14 25L15 25L15 24L16 24L16 23L17 23L17 22L18 22L18 21L17 21L17 20L16 20L16 19L14 19L14 21L13 21L13 19L12 19L12 21L10 21L10 20L11 20L11 18L10 18L10 17L8 17L8 15L6 15L6 16L7 16L7 17L5 17L5 16L4 16L4 15ZM12 15L12 17L13 17L13 15ZM3 16L3 18L4 18L4 19L5 19L5 17L4 17L4 16ZM17 16L17 17L18 17L18 16ZM20 16L20 17L21 17L21 16ZM6 18L6 19L7 19L7 18ZM8 18L8 20L9 20L9 19L10 19L10 18ZM26 18L26 19L27 19L27 20L26 20L26 21L25 21L25 22L24 22L24 20L22 20L22 22L21 22L21 23L22 23L22 25L23 25L23 28L22 28L22 29L24 29L24 30L25 30L25 32L27 32L27 31L28 31L28 30L27 30L27 31L26 31L26 29L24 29L24 24L23 24L23 23L27 23L27 24L29 24L29 23L27 23L27 20L29 20L29 19L27 19L27 18ZM15 20L15 21L14 21L14 23L12 23L12 25L13 25L13 24L14 24L14 23L16 23L16 22L15 22L15 21L16 21L16 20ZM2 21L2 22L3 22L3 23L4 23L4 22L3 22L3 21ZM12 21L12 22L13 22L13 21ZM22 22L22 23L23 23L23 22ZM6 23L6 24L7 24L7 23ZM10 23L10 24L11 24L11 23ZM2 24L2 25L3 25L3 24ZM25 25L25 28L28 28L28 25ZM26 26L26 27L27 27L27 26ZM29 26L29 27L30 27L30 28L29 28L29 29L32 29L32 27L31 27L31 26ZM12 27L12 28L13 28L13 30L14 30L14 29L15 29L15 28L16 28L16 27L15 27L15 28L14 28L14 27ZM17 27L17 28L18 28L18 27ZM15 30L15 31L16 31L16 30ZM10 31L10 32L11 32L11 31ZM13 31L13 32L14 32L14 31ZM22 32L22 33L23 33L23 32ZM0 0L0 7L7 7L7 0ZM1 1L1 6L6 6L6 1ZM2 2L2 5L5 5L5 2ZM26 0L26 7L33 7L33 0ZM27 1L27 6L32 6L32 1ZM28 2L28 5L31 5L31 2ZM0 26L0 33L7 33L7 26ZM1 27L1 32L6 32L6 27ZM2 28L2 31L5 31L5 28Z" fill="#000000"/></g></g></svg>
</svg>',
    '$client.country' => 'Afghanistan',
    '$user.last_name' => 'Erna Wunsch',
    '$client.website' => 'http://www.parisian.org/',
    '$dir_text_align' => 'left',
    '$entity_images' => '',
    '$task.discount' => '',
    '$contact.email' => '',
    '$primary_color' => '#298AAB',
    '$credit_amount' => '$0.00',
    '$invoice.total' => '$0.00',
    '$invoice.taxes' => '$0.00',
    '$quote.custom1' => '&nbsp;',
    '$quote.custom2' => '&nbsp;',
    '$quote.custom3' => '&nbsp;',
    '$quote.custom4' => '&nbsp;',
    '$company.email' => '&nbsp;',
    '$client.number' => '&nbsp;',
    '$company.phone' => '&nbsp;',
    '$company.state' => '&nbsp;',
    '$credit.number' => '0029',
    '$entity_number' => '0029',
    '$credit_number' => '0029',
    '$global_margin' => '6.35mm',
    '$contact.phone' => '681-480-9828',
    '$portal_button' => '<a class="button" href="http://ninja.test:8000/client/key_login/zJJEjlUtXPiNnnnyO2tcYia64PSwauidy61eDnMU?client_hash=nzikYQITs1kyUK61GScTNW67JwhTRkOBVdvsHzIv">View client portal</a>',
    '$paymentButton' => '<a class="button" href="http://ninja.test:8000/client/pay/UAUY8vIPuno72igmXbbpldwo5BDDKIqs">Pay Now</a>',
    '$entity_footer' => 'Default invoice footer',
    '$client.lang_2' => 'en',
    '$product.date' => '',
    '$client.email' => '',
    '$product.item' => '',
    '$public_notes' => '',
    '$task.service' => '',
    '$credit.total' => '$0.00',
    '$net_subtotal' => '$0.00',
    '$paid_to_date' => '$0.00',
    '$quote.amount' => '$0.00',
    '$company.city' => '&nbsp;',
    '$payment.date' => '&nbsp;',
    '$client.phone' => '&nbsp;',
    '$number_short' => '0029',
    '$quote.number' => '0029',
    '$invoice.date' => '25/Feb/2023',
    '$company.name' => '434343',
    '$portalButton' => '<a class="button" href="http://ninja.test:8000/client/key_login/zJJEjlUtXPiNnnnyO2tcYia64PSwauidy61eDnMU?client_hash=nzikYQITs1kyUK61GScTNW67JwhTRkOBVdvsHzIv">View client portal</a>',
    '$contact.name' => 'Benedict Eichmann',
    '$entity.terms' => 'Default company invoice terms',
    '$client.state' => 'North Carolina',
    '$company.logo' => 'data:image/png;base64, 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',
    '$company_logo' => 'data:image/png;base64, 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',
    '$payment_link' => 'http://ninja.test:8000/client/pay/UAUY8vIPuno72igmXbbpldwo5BDDKIqs',
    '$status_logo' => '',
    '$description' => '',
    '$product.tax' => '',
    '$valid_until' => '',
    '$your_entity' => '',
    '$balance_due' => '$0.00',
    '$outstanding' => '$0.00',
    '$partial_due' => '$0.00',
    '$quote.total' => '$0.00',
    '$payment_due' => '&nbsp;',
    '$credit.date' => '25/Feb/2023',
    '$invoiceDate' => '25/Feb/2023',
    '$view_button' => '<a class="button" href="http://ninja.test:8000/client/invoice/UAUY8vIPuno72igmXbbpldwo5BDDKIqs">View Invoice</a>',
    '$client.city' => 'Aufderharchester',
    '$spc_qr_code' => 'SPC
0200
1

K
434343

 


CH







0.000000
USD







NON

0029
EPD
',
    '$client_name' => 'cypress',
    '$client.name' => 'cypress',
    '$paymentLink' => 'http://ninja.test:8000/client/pay/UAUY8vIPuno72igmXbbpldwo5BDDKIqs',
    '$payment_url' => 'http://ninja.test:8000/client/pay/UAUY8vIPuno72igmXbbpldwo5BDDKIqs',
    '$page_layout' => 'portrait',
    '$task.task1' => '',
    '$task.task2' => '',
    '$task.task3' => '',
    '$task.task4' => '',
    '$task.hours' => '',
    '$amount_due' => '$0.00',
    '$amount_raw' => '0.00',
    '$invoice_no' => '0029',
    '$quote.date' => '25/Feb/2023',
    '$vat_number' => '975977515',
    '$viewButton' => '<a class="button" href="http://ninja.test:8000/client/invoice/UAUY8vIPuno72igmXbbpldwo5BDDKIqs">View Invoice</a>',
    '$portal_url' => 'http://ninja.test:8000/client/',
    '$task.date' => '',
    '$task.rate' => '',
    '$task.cost' => '',
    '$statement' => '',
    '$user_iban' => '&nbsp;',
    '$signature' => '&nbsp;',
    '$id_number' => '&nbsp;',
    '$credit_no' => '0029',
    '$font_size' => '16px',
    '$view_link' => '<a class="button" href="http://ninja.test:8000/client/invoice/UAUY8vIPuno72igmXbbpldwo5BDDKIqs">View Invoice</a>',
    '$page_size' => 'A4',
    '$country_2' => 'AF',
    '$firstName' => 'Benedict',
    '$user.name' => 'Derrick Monahan DDS Erna Wunsch',
    '$font_name' => 'Roboto',
    '$auto_bill' => 'This invoice will automatically be billed to your credit card on file on the due date.',
    '$payments' => '',
    '$task.tax' => '',
    '$discount' => '$0.00',
    '$subtotal' => '$0.00',
    '$company1' => '&nbsp;',
    '$company2' => '&nbsp;',
    '$company3' => '&nbsp;',
    '$company4' => '&nbsp;',
    '$due_date' => '&nbsp;',
    '$poNumber' => '&nbsp;',
    '$quote_no' => '0029',
    '$address2' => '63993 Aiyana View',
    '$address1' => '8447',
    '$viewLink' => '<a class="button" href="http://ninja.test:8000/client/invoice/UAUY8vIPuno72igmXbbpldwo5BDDKIqs">View Invoice</a>',
    '$autoBill' => 'This invoice will automatically be billed to your credit card on file on the due date.',
    '$view_url' => 'http://ninja.test:8000/client/invoice/UAUY8vIPuno72igmXbbpldwo5BDDKIqs',
    '$font_url' => 'https://fonts.googleapis.com/css2?family=Roboto&display=swap',
    '$details' => '',
    '$balance' => '$0.00',
    '$partial' => '$0.00',
    '$client1' => '&nbsp;',
    '$client2' => '&nbsp;',
    '$client3' => '&nbsp;',
    '$client4' => '&nbsp;',
    '$dueDate' => '&nbsp;',
    '$invoice' => '0029',
    '$account' => '434343',
    '$country' => 'Afghanistan',
    '$contact' => 'Benedict Eichmann',
    '$app_url' => 'http://ninja.test:8000',
    '$website' => 'http://www.parisian.org/',
    '$entity' => '',
    '$thanks' => '',
    '$amount' => '$0.00',
    '$method' => '&nbsp;',
    '$number' => '0029',
    '$footer' => 'Default invoice footer',
    '$client' => 'cypress',
    '$email' => '',
    '$notes' => '',
    '_rate1' => '',
    '_rate2' => '',
    '_rate3' => '',
    '$taxes' => '$0.00',
    '$total' => '$0.00',
    '$phone' => '&nbsp;',
    '$terms' => 'Default company invoice terms',
    '$from' => '',
    '$item' => '',
    '$date' => '25/Feb/2023',
    '$tax' => '',
    '$dir' => 'ltr',
    '$to' => '',
  ],
  'labels' => 
  [
    '$client.shipping_postal_code_label' => 'Shipping Postal Code',
    '$client.billing_postal_code_label' => 'Postal Code',
    '$company.city_state_postal_label' => 'City/State/Postal',
    '$company.postal_city_state_label' => 'Postal/City/State',
    '$product.gross_line_total_label' => 'Gross line total',
    '$client.postal_city_state_label' => 'Postal/City/State',
    '$client.shipping_address1_label' => 'Shipping Street',
    '$client.shipping_address2_label' => 'Shipping Apt/Suite',
    '$client.city_state_postal_label' => 'City/State/Postal',
    '$client.shipping_address_label' => 'Shipping Address',
    '$client.billing_address2_label' => 'Apt/Suite',
    '$client.billing_address1_label' => 'Street',
    '$client.shipping_country_label' => 'Shipping Country',
    '$invoiceninja.whitelabel_label' => '',
    '$client.billing_address_label' => 'Address',
    '$client.billing_country_label' => 'Country',
    '$task.gross_line_total_label' => 'Gross line total',
    '$contact.portal_button_label' => 'view_client_portal',
    '$client.shipping_state_label' => 'Shipping State/Province',
    '$invoice.public_notes_label' => 'Public Notes',
    '$client.shipping_city_label' => 'Shipping City',
    '$client.billing_state_label' => 'State/Province',
    '$product.description_label' => 'Description',
    '$product.product_key_label' => 'Product',
    '$entity.public_notes_label' => 'Public Notes',
    '$invoice.balance_due_label' => 'Balance Due',
    '$client.public_notes_label' => 'Notes',
    '$company.postal_code_label' => 'Postal Code',
    '$client.billing_city_label' => 'City',
    '$secondary_font_name_label' => '',
    '$product.line_total_label' => 'Line Total',
    '$product.tax_amount_label' => 'Tax',
    '$company.vat_number_label' => 'VAT Number',
    '$invoice.invoice_no_label' => 'Invoice Number',
    '$quote.quote_number_label' => 'Quote Number',
    '$client.postal_code_label' => 'Postal Code',
    '$contact.first_name_label' => 'First Name',
    '$secondary_font_url_label' => '',
    '$contact.signature_label' => '',
    '$product.tax_name1_label' => 'Tax',
    '$product.tax_name2_label' => 'Tax',
    '$product.tax_name3_label' => 'Tax',
    '$product.unit_cost_label' => 'Unit Cost',
    '$quote.valid_until_label' => 'Valid Until',
    '$custom_surcharge1_label' => '',
    '$custom_surcharge2_label' => '',
    '$custom_surcharge3_label' => '',
    '$custom_surcharge4_label' => '',
    '$quote.balance_due_label' => 'Balance Due',
    '$company.id_number_label' => 'ID Number',
    '$invoice.po_number_label' => 'PO Number',
    '$invoice_total_raw_label' => 'Invoice Total',
    '$postal_city_state_label' => 'Postal/City/State',
    '$client.vat_number_label' => 'VAT Number',
    '$city_state_postal_label' => 'City/State/Postal',
    '$contact.full_name_label' => 'Name',
    '$contact.last_name_label' => 'Last Name',
    '$company.country_2_label' => 'Country',
    '$product.product1_label' => '',
    '$product.product2_label' => '',
    '$product.product3_label' => '',
    '$product.product4_label' => '',
    '$statement_amount_label' => 'Amount',
    '$task.description_label' => 'Description',
    '$product.discount_label' => 'Discount',
    '$entity_issued_to_label' => 'Invoice issued to',
    '$assigned_to_user_label' => 'Name',
    '$product.quantity_label' => 'Quantity',
    '$total_tax_labels_label' => 'Taxes',
    '$total_tax_values_label' => 'Taxes',
    '$invoice.discount_label' => 'Discount',
    '$invoice.subtotal_label' => 'Subtotal',
    '$company.address2_label' => 'Apt/Suite',
    '$partial_due_date_label' => 'Due Date',
    '$invoice.due_date_label' => 'Due Date',
    '$client.id_number_label' => 'ID Number',
    '$credit.po_number_label' => 'PO Number',
    '$company.address1_label' => 'Street',
    '$credit.credit_no_label' => 'Invoice Number',
    '$invoice.datetime_label' => 'Date',
    '$contact.custom1_label' => '',
    '$contact.custom2_label' => '',
    '$contact.custom3_label' => '',
    '$contact.custom4_label' => '',
    '$task.line_total_label' => 'Line Total',
    '$line_tax_labels_label' => 'Taxes',
    '$line_tax_values_label' => 'Taxes',
    '$secondary_color_label' => '',
    '$invoice.balance_label' => 'Balance',
    '$invoice.custom1_label' => '',
    '$invoice.custom2_label' => '',
    '$invoice.custom3_label' => '',
    '$invoice.custom4_label' => '',
    '$company.custom1_label' => '',
    '$company.custom2_label' => '',
    '$company.custom3_label' => '',
    '$company.custom4_label' => '',
    '$quote.po_number_label' => 'PO Number',
    '$company.website_label' => 'Website',
    '$balance_due_raw_label' => 'Balance Due',
    '$entity.datetime_label' => 'Date',
    '$credit.datetime_label' => 'Date',
    '$client.address2_label' => 'Apt/Suite',
    '$client.address1_label' => 'Street',
    '$user.first_name_label' => 'First Name',
    '$created_by_user_label' => 'Name',
    '$client.currency_label' => '',
    '$company.country_label' => 'Country',
    '$company.address_label' => 'Address',
    '$tech_hero_image_label' => '',
    '$task.tax_name1_label' => 'Tax',
    '$task.tax_name2_label' => 'Tax',
    '$task.tax_name3_label' => 'Tax',
    '$client.balance_label' => 'Account balance',
    '$client_balance_label' => 'Account balance',
    '$credit.balance_label' => 'Balance',
    '$credit_balance_label' => 'Credit Balance',
    '$gross_subtotal_label' => 'Subtotal',
    '$invoice.amount_label' => 'Total',
    '$client.custom1_label' => '',
    '$client.custom2_label' => '',
    '$client.custom3_label' => '',
    '$client.custom4_label' => '',
    '$emailSignature_label' => '',
    '$invoice.number_label' => 'Invoice Number',
    '$quote.quote_no_label' => 'Quote Number',
    '$quote.datetime_label' => 'Date',
    '$client_address_label' => 'Address',
    '$client.address_label' => 'Address',
    '$payment_button_label' => 'Pay Now',
    '$payment_qrcode_label' => 'Pay Now',
    '$client.country_label' => 'Country',
    '$user.last_name_label' => 'Last Name',
    '$client.website_label' => 'Website',
    '$dir_text_align_label' => '',
    '$entity_images_label' => '',
    '$task.discount_label' => 'Discount',
    '$contact.email_label' => 'Email',
    '$primary_color_label' => '',
    '$credit_amount_label' => 'Credit Amount',
    '$invoice.total_label' => 'Invoice Total',
    '$invoice.taxes_label' => 'Taxes',
    '$quote.custom1_label' => '',
    '$quote.custom2_label' => '',
    '$quote.custom3_label' => '',
    '$quote.custom4_label' => '',
    '$company.email_label' => 'Email',
    '$client.number_label' => 'Number',
    '$company.phone_label' => 'Phone',
    '$company.state_label' => 'State/Province',
    '$credit.number_label' => 'Credit Number',
    '$entity_number_label' => 'Invoice Number',
    '$credit_number_label' => 'Invoice Number',
    '$global_margin_label' => '',
    '$contact.phone_label' => 'Phone',
    '$portal_button_label' => 'view_client_portal',
    '$paymentButton_label' => 'Pay Now',
    '$entity_footer_label' => '',
    '$client.lang_2_label' => '',
    '$product.date_label' => 'Date',
    '$client.email_label' => 'Email',
    '$product.item_label' => 'Item',
    '$public_notes_label' => 'Public Notes',
    '$task.service_label' => 'Service',
    '$credit.total_label' => 'Credit Total',
    '$net_subtotal_label' => 'Net',
    '$paid_to_date_label' => 'Paid to Date',
    '$quote.amount_label' => 'Quote Total',
    '$company.city_label' => 'City',
    '$payment.date_label' => 'Payment Date',
    '$client.phone_label' => 'Phone',
    '$number_short_label' => 'Invoice #',
    '$quote.number_label' => 'Quote Number',
    '$invoice.date_label' => 'Invoice Date',
    '$company.name_label' => 'Company Name',
    '$portalButton_label' => 'view_client_portal',
    '$contact.name_label' => 'Contact Name',
    '$entity.terms_label' => 'Invoice Terms',
    '$client.state_label' => 'State/Province',
    '$company.logo_label' => 'Logo',
    '$company_logo_label' => 'Logo',
    '$payment_link_label' => 'Pay Now',
    '$status_logo_label' => '',
    '$description_label' => 'Description',
    '$product.tax_label' => 'Tax',
    '$valid_until_label' => 'Valid Until',
    '$your_entity_label' => 'Your Invoice',
    '$balance_due_label' => 'Balance Due',
    '$outstanding_label' => 'Balance Due',
    '$partial_due_label' => 'Partial Due',
    '$quote.total_label' => 'Total',
    '$payment_due_label' => 'Payment due',
    '$credit.date_label' => 'Credit Date',
    '$invoiceDate_label' => 'Invoice Date',
    '$view_button_label' => 'View Invoice',
    '$client.city_label' => 'City',
    '$spc_qr_code_label' => '',
    '$client_name_label' => 'Client Name',
    '$client.name_label' => 'Client Name',
    '$paymentLink_label' => 'Pay Now',
    '$payment_url_label' => 'Pay Now',
    '$page_layout_label' => '',
    '$task.task1_label' => '',
    '$task.task2_label' => '',
    '$task.task3_label' => '',
    '$task.task4_label' => '',
    '$task.hours_label' => 'Hours',
    '$amount_due_label' => 'Amount due',
    '$amount_raw_label' => 'Amount',
    '$invoice_no_label' => 'Invoice Number',
    '$quote.date_label' => 'Quote Date',
    '$vat_number_label' => 'VAT Number',
    '$viewButton_label' => 'View Invoice',
    '$portal_url_label' => '',
    '$task.date_label' => 'Date',
    '$task.rate_label' => 'Rate',
    '$task.cost_label' => 'Rate',
    '$statement_label' => 'Statement',
    '$user_iban_label' => '',
    '$signature_label' => '',
    '$id_number_label' => 'ID Number',
    '$credit_no_label' => 'Invoice Number',
    '$font_size_label' => '',
    '$view_link_label' => 'View Invoice',
    '$page_size_label' => '',
    '$country_2_label' => 'Country',
    '$firstName_label' => 'First Name',
    '$user.name_label' => 'Name',
    '$font_name_label' => '',
    '$auto_bill_label' => '',
    '$payments_label' => 'Payments',
    '$task.tax_label' => 'Tax',
    '$discount_label' => 'Discount',
    '$subtotal_label' => 'Subtotal',
    '$company1_label' => '',
    '$company2_label' => '',
    '$company3_label' => '',
    '$company4_label' => '',
    '$due_date_label' => 'Due Date',
    '$poNumber_label' => 'PO Number',
    '$quote_no_label' => 'Quote Number',
    '$address2_label' => 'Apt/Suite',
    '$address1_label' => 'Street',
    '$viewLink_label' => 'View Invoice',
    '$autoBill_label' => '',
    '$view_url_label' => 'View Invoice',
    '$font_url_label' => '',
    '$details_label' => 'Details',
    '$balance_label' => 'Balance',
    '$partial_label' => 'Partial Due',
    '$client1_label' => '',
    '$client2_label' => '',
    '$client3_label' => '',
    '$client4_label' => '',
    '$dueDate_label' => 'Due Date',
    '$invoice_label' => 'Invoice Number',
    '$account_label' => 'Company Name',
    '$country_label' => 'Country',
    '$contact_label' => 'Name',
    '$app_url_label' => '',
    '$website_label' => 'Website',
    '$entity_label' => 'Invoice',
    '$thanks_label' => 'Thanks',
    '$amount_label' => 'Total',
    '$method_label' => 'Method',
    '$number_label' => 'Invoice Number',
    '$footer_label' => '',
    '$client_label' => 'Client Name',
    '$email_label' => 'Email',
    '$notes_label' => 'Public Notes',
    '_rate1_label' => 'Tax',
    '_rate2_label' => 'Tax',
    '_rate3_label' => 'Tax',
    '$taxes_label' => 'Taxes',
    '$total_label' => 'Total',
    '$phone_label' => 'Phone',
    '$terms_label' => 'Invoice Terms',
    '$from_label' => 'From',
    '$item_label' => 'Item',
    '$date_label' => 'Invoice Date',
    '$tax_label' => 'Tax',
    '$dir_label' => '',
    '$to_label' => 'To',
  ],
];
    }
}
