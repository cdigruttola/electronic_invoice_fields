<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Exception;


use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\ValueObject\Name;

/**
 * Exception is thrown when name which already exists is being used to create or update other address customer type
 */
class DuplicateAddressCustomerTypeNameException extends AddressCustomerTypeException
{
    /**
     * @var Name
     */
    private $name;

    /**
     * @param string $message
     * @param int $code
     * @param null $previous
     */
    public function __construct(Name $name, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->name = $name;
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }
}
