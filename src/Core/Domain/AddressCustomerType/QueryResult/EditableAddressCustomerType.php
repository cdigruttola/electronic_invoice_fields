<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\QueryResult;

use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\ValueObject\AddressCustomerTypeId;

/**
 * Stores editable data for address customer type
 */
class EditableAddressCustomerType
{
    /**
     * @var AddressCustomerTypeId
     */
    private $addressCustomerTypeId;
    /**
     * @var array
     */
    private $localizedNames;

    public function __construct(
        AddressCustomerTypeId $addressCustomerTypeId,
        array                 $name
    )
    {
        $this->addressCustomerTypeId = $addressCustomerTypeId;
        $this->localizedNames = $name;
    }

    /**
     * @return AddressCustomerTypeId
     */
    public function getAddressCustomerTypeId()
    {
        return $this->addressCustomerTypeId;
    }

    /**
     * @return array
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

}
