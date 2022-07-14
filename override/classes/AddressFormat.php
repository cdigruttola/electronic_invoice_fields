<?php

class AddressFormat extends AddressFormatCore
{
    public static function getFormattedAddressFieldsValues($address, $addressFormat, $id_lang = null)
    {
        $tab = parent::getFormattedAddressFieldsValues($address, $addressFormat, $id_lang);

        $einvoice = Module::getInstanceByName('einvoice');
        if (isset($einvoice) && isset($einvoice->active) && $einvoice->active) {
            if (isset($tab['pa'])) {
                if ((int)$tab['pa'] == 1) {
                    $tab['pa'] = $einvoice->getTranslator()->trans('Public Administration');
                } else {
                    $tab['pa'] = '';
                }
            }
            if (isset($tab['sdi'])) {
                if (in_array((string)$tab['sdi'], array('000000', '0000000', 'XXXXXX', 'XXXXXXX'))) {
                    $tab['sdi'] = '';
                }
            }
        }

        return $tab;
    }

    public static function getFieldsRequired()
    {
        $address = new CustomerAddress();
        $einvoice = Module::getInstanceByName('einvoice');
        if ($einvoice->active) {
            $id_shop = (int)Context::getContext()->shop->id;
            if ((int)Configuration::get('EINVOICE_PEC_REQUIRED', null, null, $id_shop)) {
                AddressFormat::$requireFormFieldsList[] = 'pec';
            }
            if ((int)Configuration::get('EINVOICE_SDI_REQUIRED', null, null, $id_shop)) {
                AddressFormat::$requireFormFieldsList[] = 'sdi';
            }
        }

        return array_unique(array_merge($address->getFieldsRequiredDB(), AddressFormat::$requireFormFieldsList));
    }
}
