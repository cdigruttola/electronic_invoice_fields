<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\CommandHandler;

use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Command\EditAddressCustomerTypeCommand;

/**
 * Interface for service that handles order return  state editing command
 */
interface EditAddressCustomerTypeHandlerInterface
{
    public function handle(EditAddressCustomerTypeCommand $command);
}
