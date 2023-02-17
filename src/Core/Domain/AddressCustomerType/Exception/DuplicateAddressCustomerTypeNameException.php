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

namespace cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Exception;


use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\ValueObject\Name;

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
