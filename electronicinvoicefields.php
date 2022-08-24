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

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

if (!defined('_PS_VERSION_')) {
    exit;
}
require 'vendor/autoload.php';

class Electronicinvoicefields extends Module
{
    public const EINVOICE_PEC_REQUIRED = 'EINVOICE_PEC_REQUIRED';
    public const EINVOICE_SDI_REQUIRED = 'EINVOICE_SDI_REQUIRED';
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'electronicinvoicefields';
        $this->tab = 'administration';
        $this->version = '2.0.0';
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

        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
    }

    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install(): bool
    {
        include dirname(__FILE__) . '/sql/install.php';

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHooks() &&
            $this->insertAddressCustomerType();
    }

    public function registerHooks(): bool
    {
        if (!$this->registerHook('header') ||
            !$this->registerHook('newOrder') ||
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
            !$this->registerHook('addWebserviceResources')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall($delete_params = true): bool
    {
        if ($delete_params) {
            include dirname(__FILE__) . '/sql/uninstall.php';
        }
        Configuration::deleteByName(self::EINVOICE_PEC_REQUIRED);
        Configuration::deleteByName(self::EINVOICE_SDI_REQUIRED);

        return parent::uninstall();
    }

    public function reset(): bool
    {
        return $this->uninstall(false);
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /*
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitEinvoiceModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);
        $link = new Link();
        $symfonyUrl = $link->getAdminLink('AdminAddressCustomerType', true, array('route' => 'admin_address_customer_type'));
        $this->context->smarty->assign('url_type_config', $symfonyUrl);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

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
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path . 'views/js/back.js');
        $this->context->controller->addCSS($this->_path . 'views/css/back.css');
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $id_shop = (int)$this->context->shop->id;

        $sdi_required = (int)Configuration::get(self::EINVOICE_PEC_REQUIRED, null, null, $id_shop);
        $pec_required = (int)Configuration::get(self::EINVOICE_SDI_REQUIRED, null, null, $id_shop);

        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');

        Media::addJsDef(
            [
                'sdi_required' => (int)$sdi_required,
                'pec_required' => (int)$pec_required,
            ]
        );
    }


    public function hookDisplayPDFInvoice($params)
    {
        //return 'your content goes here.  You can also choose to use smarty and templates if it is complex';
    }

    public function hookDisplayPDFOrderSlip($params)
    {
        //return 'your content goes here.  You can also choose to use smarty and templates if it is complex';
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
        $pec_required = Configuration::get(self::EINVOICE_SDI_REQUIRED, null, null, $id_shop);

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

        return $is_valid;
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
                    if (isset($country) && (string)$country->iso_code != 'IT') {
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
        $sql[] = 'INSERT INTO `' . _DB_PREFIX_ . 'einvoice_customer_type` (`id_addresscustomertype`,`removable`,`date_add`,`date_upd`) VALUES 
        (1, 0, NOW(), NOW()),
        (2, 0, NOW(), NOW()),
        (3, 0, NOW(), NOW()),
        (4, 0, NOW(), NOW());';

        foreach (Language::getLanguages() as $lang) {
            PrestaShopLogger::addLog('insertAddressCustomerType ' . $lang['id_lang']);
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
