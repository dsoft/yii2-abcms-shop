<?php

namespace abcms\shop\shipping;

use yii\base\Component;
use abcms\shop\models\Cart;

/**
 * Abstract class for shipping component
 */
abstract class ShippingAbstract extends Component
{
    
    /**
     * @var Cart $cart Cart Model
     */
    public $cart = null;
    
    /**
     * @var \yii\base\Model $address Model containing address information
     */
    public $address = null;

    /**
     * Return the shipping price for the specified cart and address
     * @return integer
     */
    abstract public function getShippingPrice();

}
