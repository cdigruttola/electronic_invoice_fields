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

final class OddPositionTranslationTable extends BasicEnum
{
    const A = 1;
    const B = 0;
    const C = 5;
    const D = 7;
    const E = 9;
    const F = 13;
    const G = 15;
    const H = 17;
    const I = 19;
    const J = 21;
    const K = 2;
    const L = 4;
    const M = 18;
    const N = 20;
    const O = 11;
    const P = 3;
    const Q = 6;
    const R = 8;
    const S = 12;
    const T = 14;
    const U = 16;
    const V = 10;
    const W = 22;
    const X = 25;
    const Y = 24;
    const Z = 23;

    public static function getValue($var)
    {
        $constants = parent::getConstants();

        if (is_numeric($var)) {
            $values = array_values($constants);
            return $values[$var];
        }
        return $constants[$var];
    }

}