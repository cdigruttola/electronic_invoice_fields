<?php

class OrderSlip extends OrderSlipCore
{

    /** @var Order * */
    private $order;

    public function getOrder()
    {
        if (!$this->order) {
            $this->order = new Order($this->id_order);
        }

        return $this->order;
    }
}