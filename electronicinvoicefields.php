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

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

if (!defined('_PS_VERSION_')) {
    exit;
}
require 'vendor/autoload.php';

class Electronicinvoicefields extends Module
{
    public const EINVOICE_PEC_REQUIRED = 'EINVOICE_PEC_REQUIRED';
    public const EINVOICE_SDI_REQUIRED = 'EINVOICE_SDI_REQUIRED';
    public const EINVOICE_DNI_VALIDATE = 'EINVOICE_DNI_VALIDATE';
    public const EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API = 'EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API';
    public const EINVOICE_CHECK_USER_AGE = 'EINVOICE_CHECK_USER_AGE';
    public const EINVOICE_MINIMUM_USER_AGE = 'EINVOICE_MINIMUM_USER_AGE';
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'electronicinvoicefields';
        $this->tab = 'administration';
        $this->version = '2.3.2';
        $this->author = 'cdigruttola';
        $this->need_instance = 0;
        $this->module_key = '313961649878a2c1b5c13a42d213c3e9';

        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        $tabNames = [];
        foreach (Language::getLanguages() as $lang) {
            $tabNames[$lang['locale']] = $this->trans('Setting Address Customer Type', [], 'Modules.Electronicinvoicefields.Einvoice', $lang['locale']);
        }

        $this->tabs = [
            [
                'name' => $tabNames,
                'class_name' => 'AdminAddressCustomerType',
                'visible' => true,
                'route_name' => 'admin_address_customer_type',
                'parent_class_name' => 'ShopParameters',
                'wording' => 'Setting Address Customer Type',
                'wording_domain' => 'Modules.Electronicinvoicefields.Einvoice',
            ],
        ];

        parent::__construct();

        $this->displayName = $this->trans('Electronic Invoice - fields', [], 'Modules.Electronicinvoicefields.Einvoice');
        $this->description = $this->trans('This module adds the new fields for E-Invoice in Customer Address', [], 'Modules.Electronicinvoicefields.Einvoice');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Electronicinvoicefields.Einvoice');

        $this->ps_versions_compliancy = ['min' => '1.7.7', 'max' => _PS_VERSION_];
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install($reset = false): bool
    {
        if (!$reset) {
            $this->_clearCache('*');
            include dirname(__FILE__) . '/sql/install.php';
        }

        return parent::install() &&
            $this->registerHooks() &&
            $this->insertAddressCustomerType();
    }

    public function registerHooks(): bool
    {
        if (!$this->registerHook('displayHeader') ||
            !$this->registerHook('displayPDFInvoice') ||
            !$this->registerHook('displayPDFOrderSlip') ||
            !$this->registerHook('actionCustomerAddressFormBuilderModifier') ||
            !$this->registerHook('actionAdminAddressesFormModifier') ||
            !$this->registerHook('actionValidateCustomerAddressForm') ||
            !$this->registerHook('actionObjectAddressAddAfter') ||
            !$this->registerHook('actionObjectAddressUpdateAfter') ||
            !$this->registerHook('actionObjectAddressDeleteAfter') ||
            !$this->registerHook('actionObjectCustomerAddressAddAfter') ||
            !$this->registerHook('actionObjectCustomerAddressUpdateAfter') ||
            !$this->registerHook('actionSubmitCustomerAddressForm') ||
            !$this->registerHook('actionAfterUpdateCustomerAddressFormHandler') ||
            !$this->registerHook('actionAfterCreateCustomerAddressFormHandler') ||
            !$this->registerHook('additionalCustomerFormFields') ||
            !$this->registerHook('addWebserviceResources')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall($reset = false): bool
    {
        if (!$reset) {
            include dirname(__FILE__) . '/sql/uninstall.php';

            Configuration::deleteByName(self::EINVOICE_PEC_REQUIRED);
            Configuration::deleteByName(self::EINVOICE_SDI_REQUIRED);
            Configuration::deleteByName(self::EINVOICE_DNI_VALIDATE);
            Configuration::deleteByName(self::EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API);
            Configuration::deleteByName(self::EINVOICE_CHECK_USER_AGE);
            Configuration::deleteByName(self::EINVOICE_MINIMUM_USER_AGE);

            return parent::uninstall();
        }

        return true;
    }

    public function onclickOption($opt, $href)
    {
        if ($opt === 'reset') {
            return $this->uninstall(true) && $this->install(true);
        }

        return true;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /*
         * If values have been submitted in the form, process.
         */
        $output = '';
        if (Tools::isSubmit('submitEinvoiceModule')) {
            if ($this->postProcess()) {
                $output .= $this->displayConfirmation($this->trans('Settings updated succesfully', [], 'Modules.Electronicinvoicefields.Einvoice'));
            } else {
                $output .= $this->displayError($this->trans('Error occurred during settings update', [], 'Modules.Electronicinvoicefields.Einvoice'));
            }
        }

        $this->context->smarty->assign('module_dir', $this->_path);
        $link = new Link();
        $symfonyUrl = $link->getAdminLink('AdminAddressCustomerType', true, ['route' => 'admin_address_customer_type']);
        $this->context->smarty->assign('url_type_config', $symfonyUrl);

        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEinvoiceModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Modules.Electronicinvoicefields.Einvoice'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->trans('PEC field required', [], 'Modules.Electronicinvoicefields.Einvoice'),
                        'name' => self::EINVOICE_PEC_REQUIRED,
                        'is_bool' => true,
                        'desc' => $this->trans('This options set the PEC field mandatory only for Italian customer.', [], 'Modules.Electronicinvoicefields.Einvoice'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->trans('Enabled', [], 'Modules.Electronicinvoicefields.Einvoice'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->trans('Disabled', [], 'Modules.Electronicinvoicefields.Einvoice'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('SDI field required', [], 'Modules.Electronicinvoicefields.Einvoice'),
                        'name' => self::EINVOICE_SDI_REQUIRED,
                        'is_bool' => true,
                        'desc' => $this->trans('This options set the SDI field mandatory only for Italian customer.', [], 'Modules.Electronicinvoicefields.Einvoice'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->trans('Enabled', [], 'Modules.Electronicinvoicefields.Einvoice'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->trans('Disabled', [], 'Modules.Electronicinvoicefields.Einvoice'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('DNI field validation', [], 'Modules.Electronicinvoicefields.Einvoice'),
                        'name' => self::EINVOICE_DNI_VALIDATE,
                        'is_bool' => true,
                        'desc' => $this->trans('This options set enable the DNI validation only for Italian customer.', [], 'Modules.Electronicinvoicefields.Einvoice'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->trans('Enabled', [], 'Modules.Electronicinvoicefields.Einvoice'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->trans('Disabled', [], 'Modules.Electronicinvoicefields.Einvoice'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Mio Codice Fiscale API Token', [], 'Modules.Electronicinvoicefields.Einvoice'),
                        'name' => self::EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API,
                        'desc' => $this->trans('Use <a href="https://www.miocodicefiscale.com/it/api-rest-verifica-e-calcolo-codice-fiscale">Mio Codice Fiscale</a> API to validate DNI.', [], 'Modules.Electronicinvoicefields.Einvoice'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Check user age', [], 'Modules.Electronicinvoicefields.Einvoice'),
                        'name' => self::EINVOICE_CHECK_USER_AGE,
                        'is_bool' => true,
                        'desc' => $this->trans('This options set enable the check of user age during registration.', [], 'Modules.Electronicinvoicefields.Einvoice'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->trans('Enabled', [], 'Modules.Electronicinvoicefields.Einvoice'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->trans('Disabled', [], 'Modules.Electronicinvoicefields.Einvoice'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'html',
                        'label' => $this->trans('Minimum age for user', [], 'Modules.Electronicinvoicefields.Einvoice'),
                        'desc' => $this->trans('Minimum age for customer, if not set default is 16', [], 'Modules.Exportorderstodanea.Main'),
                        'name' => self::EINVOICE_MINIMUM_USER_AGE,
                        'html_content' => '<input type="number" name="EINVOICE_MINIMUM_USER_AGE" min="16" max="150" value="' . $this->getConfigFormValues()[self::EINVOICE_MINIMUM_USER_AGE] . '"/>',
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Modules.Electronicinvoicefields.Einvoice'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $id_shop = (int)$this->context->shop->id;

        return [
            self::EINVOICE_PEC_REQUIRED => Configuration::get(self::EINVOICE_PEC_REQUIRED, null, null, $id_shop),
            self::EINVOICE_SDI_REQUIRED => Configuration::get(self::EINVOICE_SDI_REQUIRED, null, null, $id_shop),
            self::EINVOICE_DNI_VALIDATE => Configuration::get(self::EINVOICE_DNI_VALIDATE, null, null, $id_shop),
            self::EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API => Configuration::get(self::EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API, null, null, $id_shop),
            self::EINVOICE_CHECK_USER_AGE => Configuration::get(self::EINVOICE_CHECK_USER_AGE, null, null, $id_shop),
            self::EINVOICE_MINIMUM_USER_AGE => Configuration::get(self::EINVOICE_MINIMUM_USER_AGE, null, null, $id_shop, 16),
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $res = true;
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            $res &= Configuration::updateValue($key, Tools::getValue($key));
        }

        return $res;
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        if (!$this->active) {
            return;
        }
        $id_shop = (int)$this->context->shop->id;

        $sdi_required = (int)Configuration::get(self::EINVOICE_SDI_REQUIRED, null, null, $id_shop);
        $pec_required = (int)Configuration::get(self::EINVOICE_PEC_REQUIRED, null, null, $id_shop);

        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');

        if (isset($this->context->cart)) {
            $virtual = $this->context->cart->isVirtualCart();
        }
        Media::addJsDef(
            [
                'virtual' => $virtual ?? false,
                'sdi_required' => (int)$sdi_required,
                'pec_required' => (int)$pec_required,
                'ajax_link' => $this->context->link->getModuleLink($this->name, 'ajax'),
            ]
        );

        Media::addJsDefL('receipt', $this->trans('The receipt is valid for exercising the right of withdrawal and guarantee (where possible), but not for tax purposes.', [], 'Modules.Electronicinvoicefields.Einvoice'));
        Media::addJsDefL('invoice_virtual', $this->trans('The selected address will be used as your personal address (for invoice).', [], 'Shop.Theme.Checkout'));
        Media::addJsDefL('receipt_virtual', $this->trans('The selected address will be used as your personal address (for receipt)', [], 'Modules.Electronicinvoicefields.Einvoice'));
        Media::addJsDefL('invoice_no_virtual', $this->trans('The selected address will be used both as your personal address (for invoice) and as your delivery address.', [], 'Shop.Theme.Checkout'));
        Media::addJsDefL('receipt_no_virtual', $this->trans('The selected address will be used both as your personal address (for receipt) and as your delivery address.', [], 'Modules.Electronicinvoicefields.Einvoice'));
        Media::addJsDefL('address_delivery_as_receipt', $this->trans('Use this address for receipt too', [], 'Modules.Electronicinvoicefields.Einvoice'));
        Media::addJsDefL('address_delivery_as_invoice', $this->trans('Use this address for invoice too', [], 'Shop.Theme.Checkout'));
    }

    public function hookDisplayPDFInvoice($params)
    {
        if (!$this->active) {
            return '';
        }
        if ($params['object']->getOrder()->addressNeedInvoice()) {
            return $this->trans('Courtesy page, you\'ll receive the invoice in XML format via the revenue agency exchange system.', [], 'Modules.Electronicinvoicefields.Einvoice');
        } else {
            return $this->trans('Courtesy page, you\'ll receive the receipt with your pack.', [], 'Modules.Electronicinvoicefields.Einvoice');
        }
    }

    public function hookDisplayPDFOrderSlip($params)
    {
        if (!$this->active) {
            return '';
        }
        if ($params['object']->getOrder()->addressNeedInvoice()) {
            return $this->trans('Courtesy page, you\'ll receive the credit slip in XML format via the revenue agency exchange system.', [], 'Modules.Electronicinvoicefields.Einvoice');
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionCustomerAddressFormBuilderModifier($params)
    {
        if (!$this->active) {
            return;
        }

        $id_shop = $this->context->shop->id;

        $sdi_required = Configuration::get(self::EINVOICE_SDI_REQUIRED, null, null, $id_shop);
        $pec_required = Configuration::get(self::EINVOICE_PEC_REQUIRED, null, null, $id_shop);

        $id_address = isset($params['id']) ? (int)$params['id'] : null;
        $obj = new EInvoiceAddress($id_address);

        $formBuilder = $params['form_builder'];

        $formBuilder->add(
            'sdi',
            \Symfony\Component\Form\Extension\Core\Type\TextType::class,
            [
                'label' => $this->trans('SDI Code', [], 'Modules.Electronicinvoicefields.Einvoice'),
                'required' => $sdi_required,
                'constraints' => [
                    new PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml(),
                    new \Symfony\Component\Validator\Constraints\Length([
                        'max' => 7,
                        'maxMessage' => $this->trans('Max caracters allowed : 7', [], 'Modules.Electronicinvoicefields.Einvoice'),
                    ]),
                ],
            ]
        );

        $params['data']['sdi'] = Tools::strtoupper((string)$obj->sdi);

        $formBuilder->add(
            'pec',
            PrestaShopBundle\Form\Admin\Type\EmailType::class,
            [
                'label' => $this->trans('PEC Address', [], 'Modules.Electronicinvoicefields.Einvoice'),
                'required' => $pec_required,
                'constraints' => [
                    new PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml(),
                ],
            ]
        );

        $params['data']['pec'] = (string)$obj->pec;

        $formBuilder->add(
            'id_addresscustomertype',
            ChoiceType::class,
            [
                'choices' => Addresscustomertype::getAddressCustomerTypeChoice($this->context->language->id),
                'required' => true,
                'label' => $this->trans('Customer Type', [], 'Modules.Electronicinvoicefields.Einvoice'),
            ]
        );

        $params['data']['id_addresscustomertype'] = (int)$obj->id_addresscustomertype;

        $formBuilder->setData($params['data']);
        unset($obj);
    }

    public function hookActionAdminAddressesFormModifier($params)
    {
        if (!$this->active) {
            return;
        }
        $switch = 'radio';
        if (version_compare(_PS_VERSION_, '1.6', '>=') === true) {
            $switch = 'switch';
        }

        foreach ($params['fields'][0]['form']['input'] as $key => $value) {
            if ($value['name'] == 'vat_number') {
                break;
            }
        }

        $part1 = array_slice($params['fields'][0]['form']['input'], 0, $key + 1);
        $part2 = array_slice($params['fields'][0]['form']['input'], $key + 1);

        $fields = [
            [
                'type' => 'text',
                'label' => $this->trans('PEC Email', [], 'Modules.Electronicinvoicefields.Einvoice'),
                'name' => 'pec',
                'prefix' => "<i class='icon-envelope-o'></i>",
                'class' => 'fixed-width-xxl',
                'hint' => $this->trans('Invalid characters:', [], 'Modules.Electronicinvoicefields.Einvoice') . ' <>;=#{}',
            ],
            [
                'type' => 'text',
                'label' => $this->trans('SDI Code'),
                'name' => 'sdi',
                'class' => 'fixed-width-xxl',
                'hint' => $this->trans('Invalid characters:', [], 'Modules.Electronicinvoicefields.Einvoice') . ' <>;=#{}',
            ],
            [
                'type' => $switch,
                'label' => $this->trans('Customer Type', [], 'Modules.Electronicinvoicefields.Einvoice'),
                'name' => 'id_addresscustomertype',
                'class' => 't',
                'is_bool' => true,
                'values' => Addresscustomertype::getAddressCustomerTypeChoice($this->context->language->id),
            ],
        ];

        $params['fields'][0]['form']['input'] = array_merge($part1, $fields, $part2);

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $id_address = (int)$params['object']->id;
        } else {
            $id_address = (int)Tools::getValue('id_address');
        }
        $obj = new EInvoiceAddress($id_address);

        $params['fields_value']['sdi'] = Tools::strtoupper((string)$obj->sdi);
        $params['fields_value']['pec'] = (string)$obj->pec;
        $params['fields_value']['id_addresscustomertype'] = (int)$obj->id_addresscustomertype;
        unset($obj);
    }

    public function hookActionValidateCustomerAddressForm($params)
    {
        $is_valid = true;
        $form = $params['form'];

        $id_country = $form->getField('id_country')->getValue();
        $iso_country = Country::getIsoById($id_country);
        if ($iso_country === 'IT') {
            $pec = $form->getField('pec');
            if (isset($pec)) {
                $pec_value = $pec->getValue();
                if (!empty($pec_value) && !Validate::isEmail($pec_value)) {
                    $is_valid &= false;
                    $pec->addError($this->trans('Invalid email address format', [], 'Modules.Electronicinvoicefields.Einvoice'));
                }
            }

            $sdi = $form->getField('sdi');
            if (isset($sdi)) {
                $sdi_value = $sdi->getValue();
                if (!empty($sdi_value) && Tools::strlen($sdi_value) != 7) {
                    $is_valid &= false;
                    $sdi->addError($this->trans('Invalid SDI Code', [], 'Modules.Electronicinvoicefields.Einvoice'));
                }
            }

            $dni = $form->getField('dni');
            $id_shop = $this->context->shop->id;
            if (isset($dni) && Configuration::get(self::EINVOICE_DNI_VALIDATE, null, null, $id_shop)) {
                $dni_value = $dni->getValue();
                if (!empty($dni_value) && !Validate::checkDNICode($dni_value, Configuration::get(self::EINVOICE_DNI_VALIDATE_MIOCODICEFISCALE_API, null, null, $id_shop))) {
                    $is_valid &= false;
                    $dni->addError($this->trans('Invalid DNI Code', [], 'Modules.Electronicinvoicefields.Einvoice'));
                }
            }
        }

        $vat_number = $form->getField('vat_number');
        if (isset($vat_number)) {
            $vat_number_value = $vat_number->getValue();
            if (!empty($vat_number_value) && !Validate::checkVatNumber($vat_number_value, $iso_country)) {
                $is_valid &= false;
                $vat_number->addError($this->trans('Invalid VAT Code', [], 'Modules.Electronicinvoicefields.Einvoice'));
            }
        }

        return $is_valid;
    }

    public function hookAdditionalCustomerFormFields($params)
    {
        if ($this->active) {
            $format = $params['fields'];
            $format['birthday']->setRequired(true);
        }
    }

    public function hookActionAfterUpdateCustomerAddressFormHandler($params)
    {
        $this->retrieveValuesFromFormData($params);
    }

    public function hookActionAfterCreateCustomerAddressFormHandler($params)
    {
        $this->retrieveValuesFromFormData($params);
    }

    public function hookActionSubmitCustomerAddressForm($params)
    {
        if (!isset($params['address'])) {
            return false;
        }
        if (!isset($params['object'])) {
            $params['object'] = $params['address'];
        }
        $this->setAddressParams($params);
    }

    public function hookActionObjectCustomerAddressAddAfter($params)
    {
        $this->retrieveValuesFromHttpMethod($params);
    }

    public function hookActionObjectCustomerAddressUpdateAfter($params)
    {
        $this->retrieveValuesFromHttpMethod($params);
    }

    public function hookActionObjectAddressAddAfter($params)
    {
        $this->retrieveValuesFromCustomerAddress($params);
    }

    public function hookActionObjectAddressUpdateAfter($params)
    {
        if (isset($params['object']) && !$params['object']->deleted) {
            $this->retrieveValuesFromCustomerAddress($params);
        }
    }

    public function hookActionObjectAddressDeleteAfter($params)
    {
        if (!$this->active) {
            return;
        }
        $id_address = (int)$params['object']->id;
        $address = new Address((int)$id_address);
        if (!$address->isUsed()) {
            $eiaddress = new EInvoiceAddress($id_address);
            $eiaddress->delete();
        }
    }

    public function hookAddWebserviceResources($params)
    {
        if (Module::isEnabled('electronicinvoicefields')) {
            $def = [
                'pec' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName'],
                'sdi' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName'],
                'id_addresscustomertype' => ['type' => ObjectModel::TYPE_INT, 'validate' => 'isUnsignedInt'],
            ];
            Address::$definition['fields'] = array_merge(Address::$definition['fields'], $def);
            ksort(Address::$definition['fields']);
        }

        return true;
    }

    private function setAddressParams($params)
    {
        if (!$this->active) {
            return;
        }
        if (!$params['object']->id) {
            return;
        }

        $datas = [];
        $datas[$params['object']->id] = [
            'id_addresscustomertype' => isset($params['object']->id_addresscustomertype) ? (int)$params['object']->id_addresscustomertype : '',
            'sdi' => isset($params['object']->sdi) ? (string)$params['object']->sdi : '',
            'pec' => isset($params['object']->pec) ? (string)$params['object']->pec : '',
        ];

        foreach ($datas as $id_address => $data) {
            $id_addresscustomertype = isset($data['id_addresscustomertype']) ? trim((int)$data['id_addresscustomertype']) : 0;
            $sdi = isset($data['sdi']) ? trim((string)$data['sdi']) : '';
            $pec = isset($data['pec']) ? trim((string)$data['pec']) : '';

            if (empty($sdi)) {
                $address = new Address((int)$id_address);
                if (isset($address) && $address->id) {
                    $country = new Country((int)$address->id_country);
                    if ($country->iso_code !== 'IT') {
                        if (!empty($address->company) || !empty($address->vat_number)) {
                            $sdi = 'XXXXXXX';
                        } else {
                            $sdi = '0000000';
                        }
                    } else {
                        $sdi = '0000000';
                    }
                }
            }

            $eiaddress = new EInvoiceAddress();
            if ($id_address) {
                $eiaddress = new $eiaddress($id_address);
            }
            $eiaddress->id_address = (int)$id_address;
            $eiaddress->sdi = Tools::strtoupper((string)$sdi);
            $eiaddress->pec = (string)$pec;
            $eiaddress->id_addresscustomertype = (int)$id_addresscustomertype;
            $eiaddress->save();
        }
    }

    /**
     * @param $params
     *
     * @return void
     */
    private function retrieveValuesFromHttpMethod($params): void
    {
        if (!$this->active) {
            return;
        }
        $id_addresscustomertype = (int)Tools::getValue('id_addresscustomertype');
        $sdi = (string)Tools::getValue('sdi');
        $pec = (string)Tools::getValue('pec');

        $params['object']->id_addresscustomertype = (int)$id_addresscustomertype;
        $params['object']->sdi = (string)$sdi;
        $params['object']->pec = (string)$pec;

        $this->setAddressParams($params);
    }

    /**
     * @param $params
     *
     * @return void
     */
    private function retrieveValuesFromFormData($params): void
    {
        if (!$this->active) {
            return;
        }
        if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
            if (!isset($params['object'])) {
                $params['object'] = (object)null;
            }

            $params['object']->id = (int)$params['id'];
            $params['object']->id_addresscustomertype = isset($params['form_data']['id_addresscustomertype']) ? (int)$params['form_data']['id_addresscustomertype'] : '';
            $params['object']->sdi = isset($params['form_data']['sdi']) ? (string)$params['form_data']['sdi'] : '';
            $params['object']->pec = isset($params['form_data']['pec']) ? (string)$params['form_data']['pec'] : '';

            $this->setAddressParams($params);
        }
    }

    /**
     * @param $params
     *
     * @return void
     */
    private function retrieveValuesFromCustomerAddress($params): void
    {
        if (!$this->active) {
            return;
        }
        $customer_address = Tools::getValue('customer_address');
        if (isset($customer_address) && !empty($customer_address)) {
            $params['object']->id_addresscustomertype = (int)$customer_address['id_addresscustomertype'];
            $params['object']->sdi = (string)$customer_address['sdi'];
            $params['object']->pec = (string)$customer_address['pec'];
        }
        $this->setAddressParams($params);
    }

    private function insertAddressCustomerType(): bool
    {
        $sql = [];
        $sql[] = 'INSERT INTO `' . _DB_PREFIX_ . 'einvoice_customer_type` (`id_addresscustomertype`,`removable`,`need_invoice`,`date_add`,`date_upd`) VALUES 
        (1, 0, 0, NOW(), NOW()),
        (2, 0, 1, NOW(), NOW()),
        (3, 0, 1, NOW(), NOW()),
        (4, 0, 1, NOW(), NOW());';

        foreach (Language::getLanguages() as $lang) {
            $sql[] = 'INSERT INTO ' . _DB_PREFIX_ . 'einvoice_customer_type_lang (`id_addresscustomertype`, `id_lang`, `name`) VALUES '
                . '(1, ' . $lang['id_lang'] . ", '" . $this->trans('Private', [], 'Modules.Electronicinvoicefields.Einvoice', $lang['locale']) . "'),"
                . '(2, ' . $lang['id_lang'] . ", '" . $this->trans('Company/Professional', [], 'Modules.Electronicinvoicefields.Einvoice', $lang['locale']) . "'),"
                . '(3, ' . $lang['id_lang'] . ", '" . $this->trans('Association', [], 'Modules.Electronicinvoicefields.Einvoice', $lang['locale']) . "'),"
                . '(4, ' . $lang['id_lang'] . ", '" . $this->trans('Public Administration', [], 'Modules.Electronicinvoicefields.Einvoice', $lang['locale']) . "');";
        }

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
}
