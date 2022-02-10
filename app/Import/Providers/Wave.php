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

namespace App\Import\Providers;

use App\Factory\ClientFactory;
use App\Factory\InvoiceFactory;
use App\Factory\VendorFactory;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Vendor\StoreVendorRequest;
use App\Import\Transformer\Wave\ClientTransformer;
use App\Import\Transformer\Wave\InvoiceTransformer;
use App\Import\Transformer\Wave\VendorTransformer;
use App\Models\Client;
use App\Repositories\ClientRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\VendorRepository;

class Wave extends BaseImport implements ImportInterface
{

    public array $entity_count = [];

    public function import(string $entity)
    {
        if (
            in_array($entity, [
                'client',
                'invoice',
                // 'product',
                // 'payment',
                'vendor',
                // 'expense',
            ])
        ) {
            $this->{$entity}();
        }

        //collate any errors

        $this->finalizeImport();
    }

    public function client()
    {
        $entity_type = 'client';

        $data = $this->getCsvData($entity_type);

        $data = $this->preTransform($data, $entity_type);

        if (empty($data)) {
            $this->entity_count['clients'] = 0;
            return;
        }

        $this->request_name = StoreClientRequest::class;
        $this->repository_name = ClientRepository::class;
        $this->factory_name = ClientFactory::class;

        $this->repository = app()->make($this->repository_name);
        $this->repository->import_mode = true;

        $this->transformer = new ClientTransformer($this->company);

        $client_count = $this->ingest($data, $entity_type);

        $this->entity_count['clients'] = $client_count;

    }


    public function product() {

        //done automatically inside the invoice() method as we need to harvest the products from the line items

    }

    public function invoice() {

        //make sure we update and create products with wave
        $initial_update_products_value = $this->company->update_products; 
        $this->company->update_products = true;

        $this->company->save();

        $entity_type = 'invoice';

        $data = $this->getCsvData($entity_type);

        $data = $this->preTransform($data, $entity_type);

        if (empty($data)) {
            $this->entity_count['invoices'] = 0;
            return;
        }

        $this->request_name = StoreInvoiceRequest::class;
        $this->repository_name = InvoiceRepository::class;
        $this->factory_name = InvoiceFactory::class;

        $this->repository = app()->make($this->repository_name);
        $this->repository->import_mode = true;

        $this->transformer = new InvoiceTransformer($this->company);

        $invoice_count = $this->ingestInvoices($data, 'Invoice Number');

        $this->entity_count['invoices'] = $invoice_count;

        $this->company->update_products = $initial_update_products_value;
        $this->company->save();    

    }

    public function payment() 
    {
        //these are pulled in when processing invoices
    }

    public function vendor() 
    {

        $entity_type = 'vendor';

        $data = $this->getCsvData($entity_type);
nlog($data);
        $data = $this->preTransform($data, $entity_type);
nlog($data);
        if (empty($data)) {
            $this->entity_count['vendors'] = 0;
            return;
        }

        $this->request_name = StoreVendorRequest::class;
        $this->repository_name = VendorRepository::class;
        $this->factory_name = VendorFactory::class;

        $this->repository = app()->make($this->repository_name);
        $this->repository->import_mode = true;

        $this->transformer = new VendorTransformer($this->company);

        $vendor_count = $this->ingest($data, $entity_type);

        $this->entity_count['vendors'] = $vendor_count;

    }

    public function expense() {}

    public function transform(array $data){}
}
