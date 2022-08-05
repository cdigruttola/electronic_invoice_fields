<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\CommandHandler;

use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Command\AddAddressCustomerTypeCommand;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\ValueObject\AddressCustomerTypeId;

/**
 * Interface for service that handles command that adds new address customer type
 */
interface AddAddressCustomerTypeHandlerInterface
{
    /**
     * @return AddressCustomerTypeId
     */
    public function handle(AddAddressCustomerTypeCommand $command);
}
