<?php

declare(strict_types=1);

use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeException;

/**
 * Is thrown when address customer type constraint is violated
 */
class AddressCustomerTypeConstraintException extends AddressCustomerTypeException
{
    /**
     * @var int Code is used when invalid name is provided for address customer type
     */
    public const INVALID_NAME = 1;
    /**
     * @var int Code is used when empty name is provided for address customer type
     */
    public const EMPTY_NAME = 2;
}
