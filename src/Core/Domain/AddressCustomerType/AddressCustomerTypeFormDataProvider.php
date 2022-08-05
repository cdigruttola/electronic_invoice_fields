<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType;

use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Query\GetAddressCustomerTypeForEditing;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\QueryResult\EditableAddressCustomerType;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

/**
 * Provides data for  AddressCustomerTypeDataProvider form.
 */
final class AddressCustomerTypeFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    public function __construct(
        CommandBusInterface $queryBus
    )
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($orderStateId)
    {
        /** @var EditableAddressCustomerType $editableAddressCustomerType */
        $editableAddressCustomerType = $this->queryBus->handle(new GetAddressCustomerTypeForEditing((int)$orderStateId));

        return [
            'name' => $editableAddressCustomerType->getLocalizedNames(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $data = [
            'is_enabled' => true,
        ];

        return $data;
    }
}
