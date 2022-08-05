<?php

declare(strict_types=1);

namespace cdigruttola\Module\Einvoice\Form\DataHandler;

use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Command\AddAddressCustomerTypeCommand;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\Command\EditAddressCustomerTypeCommand;
use cdigruttola\Module\Einvoice\Core\Domain\AddressCustomerType\ValueObject\AddressCustomerTypeId;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;

/**
 * Saves or updates order return state data submitted in form
 */
final class AddressCustomerTypeFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    public function __construct(
        CommandBusInterface $bus
    )
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $command = $this->buildAddressCustomerTypeAddCommandFromFormData($data);

        /** @var AddressCustomerTypeId $addressCustomerTypeId */
        $addressCustomerTypeId = $this->bus->handle($command);

        return $addressCustomerTypeId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($AddressCustomerTypeId, array $data)
    {
        $command = $this->buildAddressCustomerTypeEditCommand($AddressCustomerTypeId, $data);

        $this->bus->handle($command);
    }

    /**
     * @return AddAddressCustomerTypeCommand
     */
    private function buildAddressCustomerTypeAddCommandFromFormData(array $data)
    {
        $command = new AddAddressCustomerTypeCommand(
            $data['name']
        );

        return $command;
    }

    /**
     * @param int $AddressCustomerTypeId
     *
     * @return EditAddressCustomerTypeCommand
     */
    private function buildAddressCustomerTypeEditCommand($AddressCustomerTypeId, array $data)
    {
        return (new EditAddressCustomerTypeCommand($AddressCustomerTypeId))
            ->setName($data['name']);
    }
}
