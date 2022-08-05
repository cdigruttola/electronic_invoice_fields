<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\CommandHandler;

use AddressCustomerType;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Command\AddAddressCustomerTypeCommand;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeException;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\ValueObject\AddressCustomerTypeId;

/**
 * Handles command that adds new address customer type
 *
 * @internal
 */
final class AddAddressCustomerTypeHandler extends AbstractAddressCustomerTypeHandler implements AddAddressCustomerTypeHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddAddressCustomerTypeCommand $command)
    {
        $AddressCustomerType = new AddressCustomerType();

        $this->fillAddressCustomerTypeWithCommandData($AddressCustomerType, $command);
        $this->assertRequiredFieldsAreNotMissing($AddressCustomerType);

        if (false === $AddressCustomerType->validateFields(false)) {
            throw new AddressCustomerTypeException('Address customer type contains invalid field values');
        }

        $AddressCustomerType->add();

        return new AddressCustomerTypeId((int)$AddressCustomerType->id);
    }

    private function fillAddressCustomerTypeWithCommandData(AddressCustomerType $AddressCustomerType, AddAddressCustomerTypeCommand $command)
    {
        $AddressCustomerType->name = $command->getLocalizedNames();
    }
}
