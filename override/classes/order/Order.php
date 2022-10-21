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
        return (bool)$invoice_address->needInvoice();
    }
}