<?php

use Symfony\Component\Translation\TranslatorInterface;

class CustomerAddressFormatter extends CustomerAddressFormatterCore
{
    private $country;
    private $translator;
    private $availableCountries;
    private $definition;

    public function __construct(
        Country             $country,
        TranslatorInterface $translator,
        array               $availableCountries
    )
    {
        $this->country = $country;
        $this->translator = $translator;
        $this->availableCountries = $availableCountries;
        $this->definition = Address::$definition['fields'];
    }

    public function setCountry(Country $country)
    {
        $this->country = $country;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getFormat()
    {
        $einvoice = Module::getInstanceByName('einvoice');
        if ($einvoice->active) {
            $fields = AddressFormat::getOrderedAddressFields(
                $this->country->id,
                true,
                true
            );
            $required = array_flip(AddressFormat::getFieldsRequired());

            $format = array(
                'id_address' => (new FormField())
                    ->setName('id_address')
                    ->setType('hidden'),
                'id_customer' => (new FormField())
                    ->setName('id_customer')
                    ->setType('hidden'),
                'back' => (new FormField())
                    ->setName('back')
                    ->setType('hidden'),
                'token' => (new FormField())
                    ->setName('token')
                    ->setType('hidden'),
                'alias' => (new FormField())
                    ->setName('alias')
                    ->setLabel(
                        $this->getFieldLabel('alias')
                    ),
            );

            foreach ($fields as $field) {
                $formField = new FormField();
                $formField->setName($field);

                $fieldParts = explode(':', $field, 2);

                if (count($fieldParts) === 1) {
                    if ($field === 'postcode') {
                        if ($this->country->need_zip_code) {
                            $formField->setRequired(true);
                        }
                    } elseif ($field === 'phone') {
                        $formField->setType('tel');
                    } elseif ($field === 'customertype') {
                        $formField->setType('radio-buttons');
                        $formField->addAvailableValue(0, $einvoice->getTranslator()->trans('Private', [], 'Modules.Einvoice.Einvoice'));
                        $formField->addAvailableValue(1, $einvoice->getTranslator()->trans('Company', [], 'Modules.Einvoice.Einvoice'));
                    } elseif ($field === 'pa') {
                        $formField->setType('checkbox');
                    } elseif ($field === 'dni' && null !== $this->country) {
                        if ($this->country->need_identification_number) {
                            $formField->setRequired(true);
                        }
                    }
                } elseif (count($fieldParts) === 2) {
                    list($entity, $entityField) = $fieldParts;

                    // Fields specified using the Entity:field
                    // notation are actually references to other
                    // entities, so they should be displayed as a select
                    $formField->setType('select');

                    // Also, what we really want is the id of the linked entity
                    $formField->setName('id_' . Tools::strtolower($entity));

                    if ($entity === 'Country') {
                        $formField->setType('countrySelect');
                        $formField->setValue($this->country->id);
                        foreach ($this->availableCountries as $country) {
                            $formField->addAvailableValue(
                                $country['id_country'],
                                $country[$entityField]
                            );
                        }
                    } elseif ($entity === 'State') {
                        if ($this->country->contains_states) {
                            $states = State::getStatesByIdCountry($this->country->id, true);
                            foreach ($states as $state) {
                                $formField->addAvailableValue(
                                    $state['id_state'],
                                    $state[$entityField]
                                );
                            }
                            $formField->setRequired(true);
                        }
                    }
                }

                $formField->setLabel($this->getFieldLabel($field));
                if (!$formField->isRequired()) {
                    // Only trust the $required array for fields
                    // that are not marked as required.
                    // $required doesn't have all the info, and fields
                    // may be required for other reasons than what
                    // AddressFormat::getFieldsRequired() says.
                    $formField->setRequired(
                        array_key_exists($field, $required)
                    );
                }

                $format[$formField->getName()] = $formField;
            }

            return $this->addConstraints($this->addMaxLength($format));
        } else {
            return parent::getFormat();
        }
    }

    private function addConstraints(array $format)
    {
        foreach ($format as $field) {
            if (!empty($this->definition[$field->getName()]['validate'])) {
                $field->addConstraint(
                    $this->definition[$field->getName()]['validate']
                );
            }
        }

        return $format;
    }

    private function addMaxLength(array $format)
    {
        foreach ($format as $field) {
            if (!empty($this->definition[$field->getName()]['size'])) {
                $field->setMaxLength(
                    $this->definition[$field->getName()]['size']
                );
            }
        }

        return $format;
    }

    private function getFieldLabel($field)
    {
        // Country:name => Country, Country:iso_code => Country,
        // same label regardless of which field is used for mapping.
        $field = explode(':', $field)[0];

        $einvoice = Module::getInstanceByName('einvoice');

        switch ($field) {
            case 'alias':
                return $this->translator->trans('Alias', array(), 'Shop.Forms.Labels');
            case 'firstname':
                return $this->translator->trans('First name', array(), 'Shop.Forms.Labels');
            case 'lastname':
                return $this->translator->trans('Last name', array(), 'Shop.Forms.Labels');
            case 'address1':
                return $this->translator->trans('Address', array(), 'Shop.Forms.Labels');
            case 'address2':
                return $this->translator->trans('Address Complement', array(), 'Shop.Forms.Labels');
            case 'postcode':
                return $this->translator->trans('Zip/Postal Code', array(), 'Shop.Forms.Labels');
            case 'city':
                return $this->translator->trans('City', array(), 'Shop.Forms.Labels');
            case 'Country':
                return $this->translator->trans('Country', array(), 'Shop.Forms.Labels');
            case 'State':
                return $this->translator->trans('State', array(), 'Shop.Forms.Labels');
            case 'phone':
                return $this->translator->trans('Phone', array(), 'Shop.Forms.Labels');
            case 'phone_mobile':
                return $this->translator->trans('Mobile phone', array(), 'Shop.Forms.Labels');
            case 'company':
                return $this->translator->trans('Company', array(), 'Shop.Forms.Labels');
            case 'vat_number':
                return $this->translator->trans('VAT number', array(), 'Shop.Forms.Labels');
            case 'dni':
                return $this->translator->trans('Identification number', array(), 'Shop.Forms.Labels');
            case 'other':
                return $this->translator->trans('Other', array(), 'Shop.Forms.Labels');
            case 'customertype':
                return $einvoice->getTranslator()->trans('Customer type', [], 'Modules.Einvoice.Einvoice');
            case 'sdi':
                return $einvoice->getTranslator()->trans('SDI Code', [], 'Modules.Einvoice.Einvoice');
            case 'pec':
                return $einvoice->getTranslator()->trans('PEC Email', [], 'Modules.Einvoice.Einvoice');
            case 'pa':
                return $einvoice->getTranslator()->trans('Public Administration', [], 'Modules.Einvoice.Einvoice');
            default:
                return $field;
        }
    }
}
