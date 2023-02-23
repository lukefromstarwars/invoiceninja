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

use App\Utils\Ninja;
use App\Models\Client;
use App\Models\Design;
use App\Models\Vendor;
use App\Models\Country;
use App\Models\Currency;
use App\Models\ClientContact;
use App\Models\VendorContact;
use App\Models\QuoteInvitation;
use App\Utils\Traits\MakesHash;
use App\Models\CreditInvitation;
use App\Models\InvoiceInvitation;
use App\DataMapper\CompanySettings;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use App\Models\PurchaseOrderInvitation;
use App\Models\RecurringInvoiceInvitation;
use App\Utils\Traits\AppSetup;

class PdfConfiguration
{
    use MakesHash, AppSetup;

    public ?Client $client;

    public ?ClientContact $contact;
    
    public Country $country;
    
    public Currency $currency;

    public Client | Vendor $currency_entity;
    
    public Design $design;
    
    public $entity;
    
    public string $entity_design_id;
    
    public string $entity_string;
    
    public ?string $path;
    
    public array $pdf_variables;
    
    public object $settings;
    
    public $settings_object;
    
    public ?Vendor $vendor;
    
    public ?VendorContact $vendor_contact;
    
    public string $date_format;

    public string $locale;
    
    /**
     * __construct
     *
     * @param  PdfService $service
     * @return void
     */
    public function __construct(public PdfService $service)
    {
    }
    
    /**
     * init
     *
     * @return self
     */
    public function init(): self
    {
        $this->setEntityType()
             ->setDateFormat()
             ->setPdfVariables()
             ->setDesign()
             ->setCurrencyForPdf()
             ->setLocale();

        return $this;
    }
    
    /**
     * setLocale
     *
     * @return self
     */
    private function setLocale(): self
    {
        App::forgetInstance('translator');

        $t = app('translator');

        App::setLocale($this->settings_object->locale());

        $t->replace(Ninja::transformTranslations($this->settings));

        $this->locale = $this->settings_object->locale();

        return $this;
    }
    
    /**
     * setCurrency
     *
     * @return self
     */
    private function setCurrencyForPdf(): self
    {
        $this->currency = $this->client ? $this->client->currency() : $this->vendor->currency();

        $this->currency_entity = $this->client ? $this->client : $this->vendor;

        return $this;
    }
    
    /**
     * setPdfVariables
     *
     * @return self
     */
    private function setPdfVariables() :self
    {
        $default = (array) CompanySettings::getEntityVariableDefaults();

        // $variables = (array)$this->service->company->settings->pdf_variables;
        $variables = (array)$this->settings->pdf_variables;

        foreach ($default as $property => $value) {
            if (array_key_exists($property, $variables)) {
                continue;
            }

            $variables[$property] = $value;
        }

        $this->pdf_variables = $variables;

        return $this;
    }
    
    /**
     * setEntityType
     *
     * @return self
     */
    private function setEntityType(): self
    {
        $entity_design_id = '';

        if ($this->service->invitation instanceof InvoiceInvitation) {
            $this->entity = $this->service->invitation->invoice;
            $this->entity_string = 'invoice';
            $this->client = $this->entity->client;
            $this->contact = $this->service->invitation->contact;
            $this->path = $this->client->invoice_filepath($this->service->invitation);
            $this->entity_design_id = 'invoice_design_id';
            $this->settings = $this->client->getMergedSettings();
            $this->settings_object = $this->client;
            $this->country = $this->client->country;
        } elseif ($this->service->invitation instanceof QuoteInvitation) {
            $this->entity = $this->service->invitation->quote;
            $this->entity_string = 'quote';
            $this->client = $this->entity->client;
            $this->contact = $this->service->invitation->contact;
            $this->path = $this->client->quote_filepath($this->service->invitation);
            $this->entity_design_id = 'quote_design_id';
            $this->settings = $this->client->getMergedSettings();
            $this->settings_object = $this->client;
            $this->country = $this->client->country;
        } elseif ($this->service->invitation instanceof CreditInvitation) {
            $this->entity = $this->service->invitation->credit;
            $this->entity_string = 'credit';
            $this->client = $this->entity->client;
            $this->contact = $this->service->invitation->contact;
            $this->path = $this->client->credit_filepath($this->service->invitation);
            $this->entity_design_id = 'credit_design_id';
            $this->settings = $this->client->getMergedSettings();
            $this->settings_object = $this->client;
            $this->country = $this->client->country;
        } elseif ($this->service->invitation instanceof RecurringInvoiceInvitation) {
            $this->entity = $this->service->invitation->recurring_invoice;
            $this->entity_string = 'recurring_invoice';
            $this->client = $this->entity->client;
            $this->contact = $this->service->invitation->contact;
            $this->path = $this->client->recurring_invoice_filepath($this->service->invitation);
            $this->entity_design_id = 'invoice_design_id';
            $this->settings = $this->client->getMergedSettings();
            $this->settings_object = $this->client;
            $this->country = $this->client->country;
        } elseif ($this->service->invitation instanceof PurchaseOrderInvitation) {
            $this->entity = $this->service->invitation->purchase_order;
            $this->entity_string = 'purchase_order';
            $this->vendor = $this->entity->vendor;
            $this->vendor_contact = $this->service->invitation->contact;
            $this->path = $this->vendor->purchase_order_filepath($this->service->invitation);
            $this->entity_design_id = 'invoice_design_id';
            $this->entity_design_id = 'purchase_order_design_id';
            $this->settings = $this->vendor->company->settings;
            $this->settings_object = $this->vendor;
            $this->client = null;
            $this->country = $this->vendor->country ?: $this->vendor->company->country();
        } else {
            throw new \Exception('Unable to resolve entity', 500);
        }

        $this->path = $this->path.$this->entity->numberFormatter().'.pdf';

        return $this;
    }
    
    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function setCountry(Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * setDesign
     *
     * @return self
     */
    private function setDesign(): self
    {
        $design_id = $this->entity->design_id ? : $this->decodePrimaryKey($this->settings_object->getSetting($this->entity_design_id));
            
        $this->design = Design::find($design_id ?: 2);

        return $this;
    }
    
    /**
     * formatMoney
     *
     * @param  float $value
     * @return string
     */
    public function formatMoney($value): string
    {
        $value = floatval($value);

        $thousand = $this->currency->thousand_separator;
        $decimal = $this->currency->decimal_separator;
        $precision = $this->currency->precision;
        $code = $this->currency->code;
        $swapSymbol = $this->currency->swap_currency_symbol;

        if (isset($this->country->thousand_separator) && strlen($this->country->thousand_separator) >= 1) {
            $thousand = $this->country->thousand_separator;
        }

        if (isset($this->country->decimal_separator) && strlen($this->country->decimal_separator) >= 1) {
            $decimal = $this->country->decimal_separator;
        }

        if (isset($this->country->swap_currency_symbol) && strlen($this->country->swap_currency_symbol) >= 1) {
            $swapSymbol = $this->country->swap_currency_symbol;
        }

        $value = number_format($value, $precision, $decimal, $thousand);
        $symbol = $this->currency->symbol;

        if ($this->settings->show_currency_code === true && $this->currency->code == 'CHF') {
            return "{$code} {$value}";
        } elseif ($this->settings->show_currency_code === true) {
            return "{$value} {$code}";
        } elseif ($swapSymbol) {
            return "{$value} ".trim($symbol);
        } elseif ($this->settings->show_currency_code === false) {
            return "{$symbol}{$value}";
        } else {

            $value = floatval($value);
            $thousand = $this->currency->thousand_separator;
            $decimal = $this->currency->decimal_separator;
            $precision = $this->currency->precision;

            return number_format($value, $precision, $decimal, $thousand);
        }

    }
    
    /**
     * date_format
     *
     * @return self
     */
    public function setDateFormat(): self
    {
        $date_formats = Cache::get('date_formats');

        if (! $date_formats) {
            $this->buildCache(true);
        }

        $this->date_format = $date_formats->filter(function ($item) {
                                return $item->id == $this->settings->date_format_id;
                             })->first()->format;

        return $this;
    }


}
