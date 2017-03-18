<?php

namespace abcms\shop\models;

use Yii;

/**
 * This is the model class for table "shop_product_variation_attribute".
 *
 * @property integer $id
 * @property integer $variationId
 * @property integer $attributeId
 * @property string $value
 */
class ProductVariationAttribute extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product_variation_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['attributeId', 'value'], 'required'],
            [['attributeId'], 'integer'],
            [['value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'variationId' => 'Variation',
            'attributeId' => 'Attribute',
            'value' => 'Value',
        ];
    }

    /**
     * Get VariationAttribute model
     * @return mixed
     */
    public function getVariationAttribute()
    {
        return $this->hasOne(VariationAttribute::className(), ['id' => 'attributeId']);
    }

    /**
     * Get VariationAttribute name
     * @return string|null
     */
    public function getVariationAttributeName()
    {
        return $this->variationAttribute ? $this->variationAttribute->name : null;
    }

}
