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
}
