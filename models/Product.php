<?php

namespace abcms\shop\models;

use Yii;
use abcms\library\behaviors\TimeBehavior;
use abcms\gallery\module\models\GalleryAlbum;

/**
 * This is the model class for table "shop_product".
 *
 * @property integer $id
 * @property string $name
 * @property integer $categoryId
 * @property string $finalPrice
 * @property string $originalPrice
 * @property string $description
 * @property integer $availableQuantity
 * @property integer $brandId
 * @property integer $albumId
 * @property integer $active
 * @property integer $deleted
 * @property string $time
 */
class Product extends \abcms\library\base\BackendActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'categoryId', 'finalPrice'], 'required'],
            [['categoryId', 'availableQuantity', 'brandId', 'active'], 'integer'],
            [['finalPrice', 'originalPrice'], 'number'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => \abcms\multilanguage\behaviors\ModelBehavior::className(),
                'attributes' => [
                    'name',
                    'description:text-editor',
                ],
            ],
            [
                'class' => TimeBehavior::className(),
            ],
            [
                'class' => \abcms\structure\behaviors\CustomFieldsBehavior::className(),
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'categoryId' => 'Category',
            'finalPrice' => 'Final Price',
            'originalPrice' => 'Original Price',
            'description' => 'Description',
            'availableQuantity' => 'Available Quantity',
            'brandId' => 'Brand',
            'albumId' => 'Album',
            'active' => 'Active',
            'deleted' => 'Deleted',
            'time' => 'Time',
        ];
    }

    /**
     * Get Category model that this product belongs to
     * @return mixed
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'categoryId']);
    }

    /**
     * Get category name
     * @return string|null
     */
    public function getCategoryName()
    {
        return $this->category ? $this->category->name : null;
    }

    /**
     * Get Brand model that this product belongs to
     * @return mixed
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brandId']);
    }

    /**
     * Get brand's name
     * @return string|null
     */
    public function getBrandName()
    {
        return $this->brand ? $this->brand->name : null;
    }

    /**
     * Save product images
     */
    public function saveImages()
    {
        $albumId = GalleryAlbum::saveAlbum($this->albumId, 'product'.$this->id, 'shop-product');
        if($albumId !== $this->albumId) {
            $this->albumId = $albumId;
            $this->save(false);
        }
    }

}