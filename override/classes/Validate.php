<?php
/*
 * 2007-2022 Carmine Di Gruttola
 *
 * NOTICE OF LICENSE
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
 *
 */

class Validate extends ValidateCore
{
    /**
     * @throws Exception
     */
    public static function isBirthDate($date, $format = 'Y-m-d')
    {
        $toReturn = parent::isBirthDate($date, $format);

        $einvoice = Module::getInstanceByName('electronicinvoicefields');
        if (isset($einvoice) && isset($einvoice->active) && $einvoice->active) {
            $id_shop = (int)Context::getContext()->shop->id;
            if (Configuration::get(Electronicinvoicefields::EINVOICE_CHECK_USER_AGE, null, null, $id_shop)) {
                $minimum = (int)Configuration::get(Electronicinvoicefields::EINVOICE_MINIMUM_USER_AGE, null, null, $id_shop);
                $d = DateTime::createFromFormat($format, $date);
                if (!empty(DateTime::getLastErrors()['warning_count']) || false === $d) {
                    return false;
                }
                $d->add(new DateInterval('P' . $minimum . 'Y'));
                return $toReturn && $d->setTime(0, 0, 0)->getTimestamp() <= time();
            }
        }
        return $toReturn;
    }


}