<?php

namespace abcms\shop\models;

use Yii;

/**
 * This is the model class for table "shop_brand".
 *
 * @property integer $id
 * @property string $name
 * @property integer $active
 * @property integer $deleted
 */
class Brand extends \abcms\library\base\BackendActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['active'], 'integer'],
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
            'active' => 'Active',
            'deleted' => 'Deleted',
        ];
    }
}
