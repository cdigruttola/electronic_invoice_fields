<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\QueryHandler;

use Addresscustomertype;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeNotFoundException;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Query\GetAddressCustomerTypeForEditing;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\QueryResult\EditableAddressCustomerType;

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
            $addressCustomerType->name
        );
    }
}
