<?php
/*
 * Copyright since 2007 Carmine Di Gruttola
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
 * @copyright Copyright since 2007 Carmine Di Gruttola
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 */

final class EvenPositionAndControlCharTranslationTable extends BasicEnum
{
    const A = 0;
    const B = 1;
    const C = 2;
    const D = 3;
    const E = 4;
    const F = 5;
    const G = 6;
    const H = 7;
    const I = 8;
    const J = 9;
    const K = 10;
    const L = 11;
    const M = 12;
    const N = 13;
    const O = 14;
    const P = 15;
    const Q = 16;
    const R = 17;
    const S = 18;
    const T = 19;
    const U = 20;
    const V = 21;
    const W = 22;
    const X = 23;
    const Y = 24;
    const Z = 25;

    public static function getValue($var)
    {
        if (is_numeric($var)) {
            return $var;
        }
        $constants = parent::getConstants();
        return $constants[$var];
    }

    public static function fromOrdinal($var)
    {
        $constants = parent::getConstants();

        $values = array_keys($constants);
        return $values[$var];
    }

}