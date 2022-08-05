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

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\CommandHandler;

use AddressCustomerType;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Command\EditAddressCustomerTypeCommand;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeException;

/**
 * Handles commands which edits given address customer type with provided data.
 *
 * @internal
 */
final class EditAddressCustomerTypeHandler extends AbstractAddressCustomerTypeHandler implements EditAddressCustomerTypeHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditAddressCustomerTypeCommand $command)
    {
        $AddressCustomerTypeId = $command->getAddressCustomerTypeId();
        $AddressCustomerType = new AddressCustomerType($AddressCustomerTypeId->getValue());

        $this->assertAddressCustomerTypeWasFound($AddressCustomerTypeId, $AddressCustomerType);

        $this->updateAddressCustomerTypeWithCommandData($AddressCustomerType, $command);

        $this->assertRequiredFieldsAreNotMissing($AddressCustomerType);

        if (false === $AddressCustomerType->validateFields(false)) {
            throw new AddressCustomerTypeException('AddressCustomerType contains invalid field values');
        }

        if (false === $AddressCustomerType->update()) {
            throw new AddressCustomerTypeException('Failed to update address customer type');
        }
    }

    private function updateAddressCustomerTypeWithCommandData(AddressCustomerType $AddressCustomerType, EditAddressCustomerTypeCommand $command)
    {
        if (null !== $command->getName()) {
            $AddressCustomerType->name = $command->getName();
        }
    }
}
