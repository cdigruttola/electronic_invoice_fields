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

declare(strict_types=1);

namespace cdigruttola\Module\Electronic_invoice_fields\Core\Domain\AddressCustomerType\ValueObject;

use cdigruttola\Module\Electronic_invoice_fields\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeException;

/**
 * Defines AddressCustomerType ID with it's constraints
 */
class AddressCustomerTypeId
{
    /**
     * @var int
     */
    private $addressCustomerTypeId;

    /**
     * @param int $addressCustomerTypeId
     */
    public function __construct($addressCustomerTypeId)
    {
        $this->assertIntegerIsGreaterThanZero($addressCustomerTypeId);

        $this->addressCustomerTypeId = $addressCustomerTypeId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->addressCustomerTypeId;
    }

    /**
     * @param int $addressCustomerTypeId
     * @throws AddressCustomerTypeException
     */
    private function assertIntegerIsGreaterThanZero($addressCustomerTypeId)
    {
        if (!is_int($addressCustomerTypeId) || 0 > $addressCustomerTypeId) {
            throw new AddressCustomerTypeException(sprintf('OrderReturnState id %s is invalid. OrderReturnState id must be number that is greater than zero.', var_export($addressCustomerTypeId, true)));
        }
    }
}
