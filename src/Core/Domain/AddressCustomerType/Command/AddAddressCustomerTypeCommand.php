<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Command;

use AddressCustomerTypeConstraintException;

/**
 * Adds new address customer type with provided data
 */
class AddAddressCustomerTypeCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

    public function __construct(
        array $localizedNames)
    {
        $this->setLocalizedNames($localizedNames);
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return $this
     *
     * @throws AddressCustomerTypeConstraintException
     */
    public function setLocalizedNames(array $localizedNames)
    {
        if (empty($localizedNames)) {
            throw new AddressCustomerTypeConstraintException('Address customer name name cannot be empty', AddressCustomerTypeConstraintException::EMPTY_NAME);
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

}
