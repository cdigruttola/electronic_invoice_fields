<?php
/**
 * Copyright since 2007 Carmine Di Gruttola
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    cdigruttola <c.digruttola@hotmail.it>
 * @copyright Copyright since 2007 Carmine Di Gruttola
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class EInvoiceAddress extends ObjectModel
{
    /** @var int id_address */
    public $id_address;

    /** @var int customertype */
    public $id_addresscustomertype;

    /** @var string pec_email */
    public $pec;

    /** @var string sdi_code */
    public $sdi;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'einvoice_address',
        'primary' => 'id_address',
        'fields' => [
            'id_address' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'id_addresscustomertype' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'pec' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail'],
            'sdi' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 7],
        ],
    ];
}
