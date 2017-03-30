<?php

namespace abcms\shop\models;

use Yii;
use abcms\library\behaviors\TimeBehavior;

/**
 * This is the model class for table "shop_cart_product".
 *
 * @property integer $id
 * @property integer $cartId
 * @property integer $productId
 * @property integer $variationId
 * @property string $price
 * @property integer $quantity
 * @property string $time
 */
class CartProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_cart_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimeBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cartId' => 'Cart',
            'productId' => 'Product',
            'variationId' => 'Variation',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'time' => 'Time',
        ];
    }
    
    /**
     * Product relation
     * @return mixed
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'productId'])->andWhere(['active'=>1]);
    }
    
    /**
     * ProductVariation relation
     * @return mixed
     */
    public function getVariation()
    {
        return $this->hasOne(ProductVariation::className(), ['id' => 'variationId']);
    }
    
    /**
     * Get product total price
     * @return int
     */
    public function getTotal(){
        return $this->product->finalPrice * $this->quantity;
    }
}
