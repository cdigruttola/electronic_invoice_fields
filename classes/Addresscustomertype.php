<?php
/**
 * 2007-2022 Carmine Di Gruttola
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
 * @copyright 2007-2022 Carmine Di Gruttola
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

// the name isn't in UpperCamelCase because of AddressFormat::_checkLiableAssociation calls on _checkValidateClassField
class Addresscustomertype extends ObjectModel
{
    /** @var int id_addresscustomertype */
    public int $id_addresscustomertype;

    /** @var string name */
    public $name;

    /**
     * @see ObjectModel::$definition
     */

    public static $definition = [
        'table' => 'einvoice_customer_type',
        'primary' => 'id_addresscustomertype',
        'multilang' => true,
        'fields' => [
            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 40],
        ],
    ];

    /**
     * @param $idLang
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getAddressCustomerType($idLang): array
    {
        PrestaShopLogger::addLog('getAddressCustomerType');
        $customerTypes = [];
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.*, cl.`name`
		FROM `' . _DB_PREFIX_ . 'einvoice_customer_type` c
		LEFT JOIN `' . _DB_PREFIX_ . 'einvoice_customer_type_lang` cl ON (c.`id_addresscustomertype` = cl.`id_addresscustomertype` AND cl.`id_lang` = ' . (int)$idLang . ')');

        foreach ($result as $row) {
            $customerTypes[$row['id_addresscustomertype']] = $row;
        }
        return $customerTypes;
    }

    /**
     * @param $idLang
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getAddressCustomerTypeChoice($idLang): array
    {
        $choices = [];
        $customerTypes = self::getAddressCustomerType($idLang);
        foreach ($customerTypes as $customerType) {
            $choices[$customerType['name']] = $customerType['id_addresscustomertype'];
        }
        return $choices;
    }

}
