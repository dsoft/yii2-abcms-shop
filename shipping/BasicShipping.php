<?php

namespace abcms\shop\shipping;

/**
 * Simple shipping price class: predefined shipping price.
 */
class BasicShipping extends ShippingAbstract
{
    
    /**
     * @var integer Predefined shipping price
     */
    public $shippingPrice = 10;
    
    /**
     * @inheritdoc
     */
    public function getShippingPrice()
    {
        return $this->shippingPrice;
    }
}
