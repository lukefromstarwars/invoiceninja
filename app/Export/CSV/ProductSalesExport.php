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

namespace App\Export\CSV;

use App\Utils\Ninja;
use League\Csv\Writer;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Product;
use App\Libraries\MultiDB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class ProductSalesExport extends BaseExport
{
    private Company $company;

    protected array $input;

    protected $date_key = 'created_at';

    protected $products;

    public Writer $csv;

    private $sales;

    //translations => keys
    protected array $entity_keys = [
        'custom_value1' => 'custom_value1',
        'custom_value2' => 'custom_value2',
        'custom_value3' => 'custom_value3',
        'custom_value4' => 'custom_value4',
        'product_key' => 'product_key',
        'notes' => 'notes',
        'quantity' => 'quantity',
        'cost' => 'price',
        'price' => 'cost',
        'markup' => 'markup',
        'discount' => 'discount',
        'net_total' => 'net_total',
        'profit' => 'profit',
        'tax_name1' => 'tax_name1',
        'tax_name2' => 'tax_name2',
        'tax_name3' => 'tax_name3',
        'tax_rate1' => 'tax_rate1',
        'tax_rate2' => 'tax_rate2',
        'tax_rate3' => 'tax_rate3',
        'tax_amount1' => 'tax_amount1',
        'tax_amount2' => 'tax_amount2',
        'tax_amount3' => 'tax_amount3',
        'is_amount_discount' => 'is_amount_discount',
        'line_total' => 'line_total',
        'gross_line_total' => 'gross_line_total',
        'status' => 'status',
        'date' => 'date',
        'currency' => 'currency',
        'client' => 'client',

    ];

    private array $decorate_keys = [
        'client',
        'currency',
        'date',
    ];

    public function __construct(Company $company, array $input)
    {
        $this->company = $company;
        $this->input = $input;
        $this->sales = collect();
    }

    public function run()
    {
        MultiDB::setDb($this->company->db);
        App::forgetInstance('translator');
        App::setLocale($this->company->locale());
        $t = app('translator');
        $t->replace(Ninja::transformTranslations($this->company->settings));

        $this->products = Product::where('company_id', $this->company->id)->withTrashed()->get();
        
        //load the CSV document from a string
        $this->csv = Writer::createFromString();

        if (count($this->input['report_keys']) == 0) {
            $this->input['report_keys'] = array_values($this->entity_keys);
        }

        //insert the header
        $this->csv->insertOne($this->buildHeader());

        $query = Invoice::query()
                        ->withTrashed()
                        ->where('company_id', $this->company->id)
                        ->where('is_deleted', 0)
                        ->whereIn('status_id', [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL, Invoice::STATUS_PAID]);

        $query = $this->addDateRange($query);

        $query->cursor()
              ->each(function ($invoice) {
                  foreach ($invoice->line_items as $item) {
                      $this->csv->insertOne($this->buildRow($invoice, $item));
                  }
              });

        
        $grouped = $this->sales->groupBy('product_key')->map(function ($key, $value){

            $data =  [
                'product' => $value, 
                'quantity' => $key->sum('quantity'),
                'markup' => $key->sum('markup'),
                'profit' => $key->sum('profit'),
                'net_total' => $key->sum('net_total'),
                'discount' => $key->sum('discount'),
                'line_total' => $key->sum('line_total'),
                'tax_name1' => $key->whereNotNull('tax_name1')->where('tax_name1', '!=', '')->first() ? $key->whereNotNull('tax_name1')->where('tax_name1', '!=', '')->first()['tax_name1'] : '',
                'tax_name2' => $key->whereNotNull('tax_name2')->where('tax_name2', '!=', '')->first() ? $key->whereNotNull('tax_name2')->where('tax_name2', '!=', '')->first()['tax_name2'] : '',
                'tax_name3' => $key->whereNotNull('tax_name3')->where('tax_name3', '!=', '')->first() ? $key->whereNotNull('tax_name3')->where('tax_name3', '!=', '')->first()['tax_name3'] : '',
                'tax_amount1' => $key->sum('tax_amount1'),
                'tax_amount2' => $key->sum('tax_amount2'),
                'tax_amount3' => $key->sum('tax_amount3'),
            ];

            return $data;
        });


        nlog($grouped);

        return $this->csv->toString();
    }

    private function buildRow($invoice, $invoice_item) :array
    {
        $transformed_entity = (array)$invoice_item;

        $entity = [];

        foreach (array_values($this->input['report_keys']) as $key) {
            $keyval = array_search($key, $this->entity_keys);

            if (array_key_exists($key, $transformed_entity)) {
                $entity[$keyval] = $transformed_entity[$key];
            } else {
                $entity[$keyval] = '';
            }
        }
        $entity = $this->decorateAdvancedFields($invoice, $entity);
        
        $this->sales->push($entity);

        return $entity;

    }

    private function decorateAdvancedFields(Invoice $invoice, $entity) :array
    {   
        $product = $this->getProduct($entity['product_key']);

        $entity['cost'] = $product->cost ?? 0;
        $unit_cost = $entity['cost'] == 0 ? 1 : $entity['cost'];

        $entity['client'] = $invoice->client->present()->name();
        $entity['currency'] = $invoice->client->currency()->code;
        $entity['status'] = $invoice->stringStatus($invoice->status_id);
        $entity['date'] = Carbon::parse($invoice->date)->format($this->company->date_format());

        $entity['discount'] = $this->calculateDiscount($invoice, $entity);
        $entity['markup'] = round(((($entity['price'] - $entity['discount'] - $entity['cost']) / $unit_cost) * 100),2);

        $entity['net_total'] = $entity['price'] - $entity['discount'];
        $entity['profit'] = $entity['price'] - $entity['discount'] - $entity['cost'];
        
        if(strlen($entity['tax_name1']) > 1) {
            $entity['tax_name1'] = $entity['tax_name1'] . ' [' . $entity['tax_rate1'] . '%]';
            $entity['tax_amount1'] = $this->calculateTax($invoice, $entity['line_total'], $entity['tax_rate1']);
        }
        else 
            $entity['tax_amount1'] = 0;

        if(strlen($entity['tax_name2']) > 1) {
            $entity['tax_name2'] = $entity['tax_name2'] . ' [' . $entity['tax_rate2'] . '%]';
            $entity['tax_amount2'] = $this->calculateTax($invoice, $entity['line_total'], $entity['tax_rate2']);
        }
        else 
            $entity['tax_amount2'] = 0;
        
        if(strlen($entity['tax_name3']) > 1) {
            $entity['tax_name3'] = $entity['tax_name3'] . ' [' . $entity['tax_rate3'] . '%]';
            $entity['tax_amount3'] = $this->calculateTax($invoice, $entity['line_total'], $entity['tax_rate3']);
        }
        else 
            $entity['tax_amount3'] = 0;

        return $entity;
    }
        
    /**
     * calculateTax
     *
     * @param  mixed $invoice
     * @param  float $amount
     * @param  float $tax_rate
     * @return float
     */
    private function calculateTax(Invoice $invoice, float $amount, float $tax_rate): float
    {
        $amount = $amount - ($amount * ($invoice->discount / 100));

        if($invoice->uses_inclusive_taxes) {
            return round($amount - ($amount / (1 + ($tax_rate / 100))), 2);
        }
        else {
            return round(($amount * $tax_rate / 100), 2);
        }

    }

    

    /**
     * calculateDiscount
     *
     * @param  mixed $invoice
     * @param  mixed $entity
     * @return float
     */
    private function calculateDiscount(Invoice $invoice , $entity) :float
    {
        if($entity['discount'] == 0)
            return 0;

        if($invoice->is_amount_discount && $entity['discount'] != 0) {
            return $entity['discount'];
        }
        elseif(!$invoice->is_amount_discount && $entity['discount'] != 0) {
            return round($entity['line_total'] * ($entity['discount'] / 100), 2);
        }

        return 0;
    }
    
    /**
     * getProduct
     *
     * @param  string $product_key
     * @return Product
     */
    private function getProduct(string $product_key) :?Product
    {
        return $this->products->firstWhere('product_key', $product_key);
    }
}
