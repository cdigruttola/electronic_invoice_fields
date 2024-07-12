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
if (!defined('_PS_VERSION_')) {
    exit;
}

class Customer extends CustomerCore
{
    public function getAddresses($id_lang)
    {
        $group = Context::getContext()->shop->getGroup();
        $shareOrder = isset($group->share_order) ? (bool) $group->share_order : false;
        $cacheId = 'Customer::getAddresses'
            . '-' . (int) $this->id
            . '-' . (int) $id_lang
            . '-' . ($shareOrder ? 1 : 0);
        if (!Cache::isStored($cacheId)) {
            $sql = 'SELECT DISTINCT a.*, cl.`name` AS country, s.name AS state, s.iso_code AS state_iso,
                    ei.id_addresscustomertype, ei.pec, ei.sdi
                    FROM `' . _DB_PREFIX_ . 'address` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'country` c ON (a.`id_country` = c.`id_country`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (c.`id_country` = cl.`id_country`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'state` s ON (s.`id_state` = a.`id_state`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'einvoice_address` ei ON (ei.`id_address` = a.`id_address`)
                    ' . ($shareOrder ? '' : Shop::addSqlAssociation('country', 'c')) . '
                    WHERE `id_lang` = ' . (int) $id_lang . ' AND `id_customer` = ' . (int) $this->id . ' AND a.`deleted` = 0';

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }
}
