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

namespace cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\QueryHandler;

use Addresscustomertype;
use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeNotFoundException;
use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Query\GetAddressCustomerTypeForEditing;
use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\QueryResult\EditableAddressCustomerType;

/**
 * Handles command that gets orderReturnState for editing
 *
 * @internal
 */
final class GetAddressCustomerTypeForEditingHandler implements GetAddressCustomerTypeForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetAddressCustomerTypeForEditing $query)
    {
        $addressCustomerTypeId = $query->getAddressCustomerTypeId();
        $addressCustomerType = new Addresscustomertype($addressCustomerTypeId->getValue());

        if ($addressCustomerType->id !== $addressCustomerTypeId->getValue()) {
            throw new AddressCustomerTypeNotFoundException($addressCustomerTypeId, sprintf('AddressCustomerType with id "%s" was not found', $addressCustomerTypeId->getValue()));
        }

        return new EditableAddressCustomerType(
            $addressCustomerTypeId,
            $addressCustomerType->name,
            (bool)$addressCustomerType->active,
            (bool)$addressCustomerType->need_invoice
        );
    }
}
