<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Exception;

use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\ValueObject\AddressCustomerTypeId;
use Exception;

/**
 * Is thrown when address customer type is not found
 */
class AddressCustomerTypeNotFoundException extends AddressCustomerTypeException
{
    /**
     * @var AddressCustomerTypeId
     */
    private $addressCustomerTypeId;

    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(AddressCustomerTypeId $addressCustomerTypeId, $message = '', $code = 0, $previous = null)
    {
        $this->addressCustomerTypeId = $addressCustomerTypeId;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return AddressCustomerTypeId
     */
    public function getAddressCustomerTypeId()
    {
        return $this->addressCustomerTypeId;
    }
}
