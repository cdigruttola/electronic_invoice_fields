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

namespace cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\ValueObject;

use cdigruttola\Module\Electronicinvoicefields\Core\Domain\AddressCustomerType\Exception\AddressCustomerTypeConstraintException;

if (!defined('_PS_VERSION_')) {
    exit;
}

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
