<?php
/**
 * 2007-2022 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2022 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 *
 */

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
