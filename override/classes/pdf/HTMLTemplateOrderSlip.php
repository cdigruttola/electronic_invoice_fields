<?php

class HTMLTemplateOrderSlip extends HTMLTemplateOrderSlipCore
{

    /**
     * Returns the template's HTML header.
     *
     * @return string HTML header
     */
    public function getHeader()
    {
        $this->assignCommonHeaderData();
        if ($this->order->addressNeedInvoice()) {
            $this->smarty->assign(['header' => Context::getContext()->getTranslator()->trans('Credit slip', [], 'Shop.Pdf')]);
        } else {
            $this->smarty->assign(['header' => Context::getContext()->getTranslator()->trans('Credit slip on receipt', [], 'Modules.Electronicinvoicefields.Einvoice')]);
        }
        return $this->smarty->fetch($this->getTemplate('header'));
    }

}