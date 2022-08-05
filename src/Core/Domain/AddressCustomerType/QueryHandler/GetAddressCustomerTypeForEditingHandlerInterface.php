<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\QueryHandler;

use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Query\GetAddressCustomerTypeForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\QueryResult\EditableOrderReturnState;

/**
 * Interface for service that gets address customer type data for editing
 */
interface GetAddressCustomerTypeForEditingHandlerInterface
{
    /**
     * @return EditableOrderReturnState
     */
    public function handle(GetAddressCustomerTypeForEditing $query);
}
