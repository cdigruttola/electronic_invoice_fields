<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Command;

use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\ValueObject\AddressCustomerTypeId;

/**
 * Edits provided address customer type.
 * It can edit either all or partial data.
 *
 * Only not-null values are considered when editing address customer type.
 * For example, if the name is null, then the original value is not modified,
 * however, if name is set, then the original value will be overwritten.
 */
class EditAddressCustomerTypeCommand
{
    /**
     * @var AddressCustomerTypeId
     */
    private $addressCustomerTypeId;

    /**
     * @var array<string>|null
     */
    private $name;

    /**
     * @param int $addressCustomerTypeId
     */
    public function __construct($addressCustomerTypeId)
    {
        $this->addressCustomerTypeId = new AddressCustomerTypeId($addressCustomerTypeId);
    }

    /**
     * @return AddressCustomerTypeId
     */
    public function getAddressCustomerTypeId()
    {
        return $this->addressCustomerTypeId;
    }

    /**
     * @return array<string>|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array<string> $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

}
