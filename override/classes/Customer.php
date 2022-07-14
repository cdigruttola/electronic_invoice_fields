<?php

class Customer extends CustomerCore
{
    public function getAddresses($id_lang)
    {
        $group = Context::getContext()->shop->getGroup();
        $shareOrder = isset($group->share_order) ? (bool)$group->share_order : false;
        $cacheId = 'Customer::getAddresses'
            . '-' . (int)$this->id
            . '-' . (int)$id_lang
            . '-' . ($shareOrder ? 1 : 0);
        if (!Cache::isStored($cacheId)) {
            $sql = 'SELECT DISTINCT a.*, cl.`name` AS country, s.name AS state, s.iso_code AS state_iso,
                    ei.customertype AS customertype, ei.pec AS pec, ei.sdi AS sdi, ei.pa AS pa
                    FROM `' . _DB_PREFIX_ . 'address` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'country` c ON (a.`id_country` = c.`id_country`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (c.`id_country` = cl.`id_country`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'state` s ON (s.`id_state` = a.`id_state`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'einvoice_address` ei ON (ei.`id_address` = a.`id_address`)
                    ' . ($shareOrder ? '' : Shop::addSqlAssociation('country', 'c')) . '
                    WHERE `id_lang` = ' . (int)$id_lang . ' AND `id_customer` = ' . (int)$this->id . ' AND a.`deleted` = 0';

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }
}
