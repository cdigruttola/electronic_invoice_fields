<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\ValueObject;

use AddressCustomerTypeConstraintException;

/**
 * Stores address customer type's name
 */
class Name
{
    /**
     * @var string Maximum allowed length for name
     */
    public const MAX_LENGTH = 255;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->assertNameDoesNotExceedAllowedLength($name);
        $this->assertNameIsValid($name);

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @throws AddressCustomerTypeConstraintException
     */
    private function assertNameIsValid($name)
    {
        $matchesFirstNamePattern = preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u', stripslashes($name));

        if (!$matchesFirstNamePattern) {
            throw new AddressCustomerTypeConstraintException(sprintf('Address customer type name %s is invalid', var_export($name, true)), AddressCustomerTypeConstraintException::INVALID_NAME);
        }
    }

    /**
     * @param string $name
     *
     * @throws AddressCustomerTypeConstraintException
     */
    private function assertNameDoesNotExceedAllowedLength($name)
    {
        $name = html_entity_decode($name, ENT_COMPAT, 'UTF-8');

        $length = function_exists('mb_strlen') ? mb_strlen($name, 'UTF-8') : strlen($name);
        if (self::MAX_LENGTH < $length) {
            throw new AddressCustomerTypeConstraintException(sprintf('Address customer type name is too long. Max allowed length is %s', self::MAX_LENGTH), AddressCustomerTypeConstraintException::INVALID_NAME);
        }
    }
}
