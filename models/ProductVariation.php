<?php

namespace abcms\shop\models;

use Yii;

/**
 * This is the model class for table "shop_product_variation".
 *
 * @property integer $id
 * @property integer $productId
 * @property integer $quantity
 */
class ProductVariation extends \yii\db\ActiveRecord
{
    
    /**
     * @event Event an event that is triggered after trying to decrease the quantity of an unavailable product variation
     */
    const EVENT_ORDERING_UNAVAILABLE_VARIATION = 'orderingUnavailableVariation';
    
    /**
     * @event Event an event that is triggered after a product variation become unavailable
     */
    const EVENT_VARIATION_BECAME_UNAVAILABLE = 'variationBecameUnavailable';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_variation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quantity'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'productId' => 'Product',
            'quantity' => 'Quantity',
        ];
    }
    
    /**
     * Get ProductVariationAttribute models that belongs to this model
     * @return mixed
     */
    public function getProductVariationAttributes()
    {
        return $this->hasMany(ProductVariationAttribute::className(), ['variationId' => 'id']);
    }
    
    /**
     * Product relation
     * @return mixed
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'productId']);
    }
    
    /**
     * Return variation description: attributes and values as string
     * @param boolean $withAttributeName Include attribute name
     * @return string
     */
    public function getText($withAttributeName = true){
        $attributes = $this->productVariationAttributes;
        $array = [];
        foreach($attributes as $attribute){
            $array[] = $withAttributeName ? $attribute->variationAttributeName. ': '.$attribute->value : $attribute->value;
        }
        return implode(', ', $array);
    }
    
    /**
     * Decrease quantity for this product variation
     * @param int $quantity Quantity that should be decreased
     * @return boolean If quantity available and was decreased
     */
    public function decreaseQuantity($quantity)
    {
        if($this->quantity === null){
            return true;
        }
        elseif($this->quantity === 0){
            $this->orderingUnavailableVariation();
            return false;
        }
        else{
            if($this->quantity >= $quantity){
                $this->quantity -= $quantity;
                if($this->save(false)){
                    if($this->quantity === 0){ // Variation became out of stock
                        $this->variationBecameUnavailable();
                    }
                    return true;
                }else{
                    return false;
                }
            }
            else{
                $this->orderingUnavailableVariation();
                return false;
            }
        }
    }
    
    /**
     * This method is invoked after trying to decrease the quantity of an unavailable product variation.
     * The default implementation raises the [[EVENT_ORDERING_UNAVAILABLE_VARIATION]] event.
     */
    public function orderingUnavailableVariation()
    {
        $this->trigger(self::EVENT_ORDERING_UNAVAILABLE_VARIATION);
    }
    
    /**
     * This method is invoked after decreasing the quantity of a certain product variation and product variation become unavailable
     * The default implementation raises the [[EVENT_VARIATION_BECAME_UNAVAILABLE]] event.
     */
    public function variationBecameUnavailable()
    {
        $this->trigger(self::EVENT_VARIATION_BECAME_UNAVAILABLE);
    }
}
