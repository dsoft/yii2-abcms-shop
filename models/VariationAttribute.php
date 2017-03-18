<?php

namespace abcms\shop\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "shop_variation_attribute".
 *
 * @property integer $id
 * @property string $name
 */
class VariationAttribute extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_variation_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Return variations list array, to be used in drop down lists.
     * @return array
     */
    public static function getVariationsList()
    {
        $query = self::find()->orderBy('name ASC');
        $models = $query->all();
        return ArrayHelper::map($models, 'id', 'name');
    }

}
