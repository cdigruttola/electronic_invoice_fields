<?php

class EInvoiceAddress extends ObjectModel
{
    /** @var int id_address */
    public $id_address;

    /** @var string pec_email */
    public $customertype;

    /** @var string pec_email */
    public $pec;

    /** @var string sdi_code */
    public $sdi;

    /** @var int is_pa */
    public $pa;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'einvoice_address',
        'primary' => 'id_address',
        'fields' => array(
            'id_address' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'customertype' => array('type' => self::TYPE_BOOL),
            'pec' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail'),
            'sdi' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 7),
            'pa' => array('type' => self::TYPE_BOOL),
        ),
    );
}
