<?php
/**
 * Copyright since 2007 Carmine Di Gruttola
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    cdigruttola <c.digruttola@hotmail.it>
 * @copyright Copyright since 2007 Carmine Di Gruttola
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

declare(strict_types=1);

namespace cdigruttola\Module\Electronicinvoicefields\Form\DataHandler;

use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Command\AddAddressCustomerTypeCommand;
use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Command\EditAddressCustomerTypeCommand;
use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\ValueObject\AddressCustomerTypeId;
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
            $data['name'],
            $data['active'] ?? false,
            $data['need_invoice'] ?? false
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
            ->setName($data['name'])
            ->setActive((bool)$data['active'])
            ->setNeedInvoice((bool)$data['need_invoice']);
    }
}
