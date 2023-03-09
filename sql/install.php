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
$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'einvoice_address` (
    `id_address` int(10) unsigned NOT NULL,
    `id_addresscustomertype` int(10),
    `pec` varchar(128) DEFAULT NULL,
    `sdi` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id_address`),
  UNIQUE KEY `id_address_UNIQUE` (`id_address`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'einvoice_customer_type` (
    `id_addresscustomertype` int(10) NOT NULL AUTO_INCREMENT,
    `removable` tinyint(1) unsigned DEFAULT 1 NOT NULL,
    `active` tinyint(1) unsigned DEFAULT 1 NOT NULL,
    `need_invoice` tinyint(1) unsigned DEFAULT 0 NOT NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_addresscustomertype`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'einvoice_customer_type_lang` (
    `id_addresscustomertype` int(10) NOT NULL,
    `id_lang` int(10) unsigned NOT NULL,
    `name` varchar(40),
  PRIMARY KEY (`id_addresscustomertype`, `id_lang`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (!Db::getInstance()->execute($query)) {
        return false;
    }
}

return true;
