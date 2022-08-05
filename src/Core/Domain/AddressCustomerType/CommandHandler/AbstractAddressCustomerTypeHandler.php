<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\CommandHandler;

use AddressCustomerType;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeNotFoundException;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Exception\MissingAddressCustomerTypeRequiredFieldsException;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\ValueObject\AddressCustomerTypeId;

/**
 * Provides reusable methods for address customer type command handlers.
 *
 * @internal
 */
abstract class AbstractAddressCustomerTypeHandler
{
    /**
     * @throws AddressCustomerTypeNotFoundException
     */
    protected function assertAddressCustomerTypeWasFound(AddressCustomerTypeId $addressCustomerTypeId, AddressCustomerType $addressCustomerType)
    {
        if ($addressCustomerType->id !== $addressCustomerTypeId->getValue()) {
            throw new AddressCustomerTypeNotFoundException($addressCustomerTypeId, sprintf('AddressCustomerType with id "%s" was not found.', $addressCustomerTypeId->getValue()));
        }
    }

    /**
     * @throws MissingAddressCustomerTypeRequiredFieldsException
     */
    protected function assertRequiredFieldsAreNotMissing(AddressCustomerType $addressCustomerType)
    {
        $errors = $addressCustomerType->validateFieldsRequiredDatabase();

        if (!empty($errors)) {
            $missingFields = array_keys($errors);

            throw new MissingAddressCustomerTypeRequiredFieldsException($missingFields, sprintf('One or more required fields for address customer type are missing. Missing fields are: %s', implode(',', $missingFields)));
        }
    }
}
