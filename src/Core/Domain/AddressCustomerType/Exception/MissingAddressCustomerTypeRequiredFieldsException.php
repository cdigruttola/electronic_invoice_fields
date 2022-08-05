<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Exception;

use Exception;

/**
 * Is thrown when adding/editing address customer type with missing required fields
 */
class MissingAddressCustomerTypeRequiredFieldsException extends AddressCustomerTypeException
{
    /**
     * @var string[]
     */
    private $missingRequiredFields;

    /**
     * @param string[] $missingRequiredFields
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(array $missingRequiredFields, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->missingRequiredFields = $missingRequiredFields;
    }

    /**
     * @return string[]
     */
    public function getMissingRequiredFields()
    {
        return $this->missingRequiredFields;
    }
}
