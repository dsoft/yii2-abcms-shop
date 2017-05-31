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
 * @property integer $quantity
 * @property string $price
 * @property string $description
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
            'quantity' => 'Quantity',
            'price' => 'Price',
            'description' => 'Description',
            'time' => 'Time',
        ];
    }

    /**
     * Product relation
     * @return mixed
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'productId'])->andWhere(['active' => 1]);
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
     * Returns product name
     * @return string|null
     */
    public function getProductName()
    {
        return $this->product ? $this->product->name : null;
    }

    /**
     * Returns variation text
     * @return string|null
     */
    public function getVariationText()
    {
        return $this->variation ? $this->variation->text : null;
    }

    /**
     * Get product total price
     * @return int
     */
    public function getTotal()
    {
        return $this->product->finalPrice * $this->quantity;
    }
    
    /**
     * Get price if saved otherwise calculate it from product.
     * @return int
     */
    public function getPrice(){
        return $this->price ? $this->price : $this->getTotal();
    }
    
    /**
     * Get description from product.
     * @return string
     */
    public function getDescriptionFromProduct(){
        return $this->variation ? $this->getProductName() . ': '.$this->getVariationText() : $this->getProductName();
    }
    
    /**
     * Get description if saved, otherwise from product.
     * @return string
     */
    public function getDescription(){
        if(!$this->description){
            $this->getDescriptionFromProduct();
        }
        else{
            return $this->description;
        }
    }
    
    /**
     * Set price and product description
     * @return boolean
     */
    public function close(){
        $this->price = $this->getTotal();
        $this->description = $this->getDescriptionFromProduct();
        return $this->save(false);
    }
    
    /**
     * Decrease quantity for product or variation
     */
    public function decreaseQuantity()
    {
        $product = $this->product;
        if($this->variationId){
            $variation = $this->variation;
            $variation->decreaseQuantity($this->quantity);
        }
        else{
            $product->decreaseQuantity($this->quantity);
        }
    }

}
