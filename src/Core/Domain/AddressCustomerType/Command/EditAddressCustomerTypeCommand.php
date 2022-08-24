<?php
/**
 * 2007-2022 Carmine Di Gruttola
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
 * @copyright 2007-2022 Carmine Di Gruttola
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

declare(strict_types=1);

namespace cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Command;

use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\ValueObject\AddressCustomerTypeId;

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
     * @var bool
     */
    private $active;

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
    public function getName(): ?array
    {
        return $this->name;
    }

    /**
     * @param array<string> $name
     *
     * @return self
     */
    public function setName(array $name): EditAddressCustomerTypeCommand
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): EditAddressCustomerTypeCommand
    {
        $this->active = $active;
        return $this;
    }

}
