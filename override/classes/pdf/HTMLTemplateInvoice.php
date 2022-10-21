<?php

class HTMLTemplateInvoice extends HTMLTemplateInvoiceCore
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
            $this->smarty->assign(['header' => Context::getContext()->getTranslator()->trans('Invoice', [], 'Shop.Pdf')]);
        } else {
            $this->smarty->assign(['header' => Context::getContext()->getTranslator()->trans('Receipt', [], 'Modules.Electronicinvoicefields.Einvoice')]);
        }
        return $this->smarty->fetch($this->getTemplate('header'));
    }

}