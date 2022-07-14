<?php

class Address extends AddressCore
{
    /** @var int Customer Type */
    public $customertype;

    /** @var string SDI */
    public $sdi;

    /** @var string PEC */
    public $pec;

    /** @var int PA */
    public $pa;

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __construct($id_address = null, $id_lang = null)
    {
        parent::__construct($id_address, $id_lang);

        $einvoice = Module::getInstanceByName('einvoice');
        if (isset($einvoice) && isset($einvoice->active) && $einvoice->active) {
            $eiaddress = new EInvoiceAddress((int)$this->id);
            if ($eiaddress->id_address) {
                $this->customertype = (string)$eiaddress->customertype;
                $this->sdi = (string)$eiaddress->sdi;
                $this->pec = (string)$eiaddress->pec;
                $this->pa = (int)$eiaddress->ipa;
            }
            unset($eiaddress);
        }
    }
}
