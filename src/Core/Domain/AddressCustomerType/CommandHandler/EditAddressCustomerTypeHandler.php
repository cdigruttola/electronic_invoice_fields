<?php

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
