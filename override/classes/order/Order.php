<?php

class Order extends OrderCore
{

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function addressNeedInvoice(): bool
    {
        $invoice_address = new Address((int)$this->id_address_invoice);
        $address_type = new Addresscustomertype($invoice_address->id_addresscustomertype);
        return (bool)$address_type->need_invoice;
    }
}