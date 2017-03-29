<?php

namespace abcms\shop\models;

use Yii;
use abcms\library\behaviors\TimeBehavior;
use abcms\gallery\module\models\GalleryAlbum;
use abcms\gallery\module\models\GalleryImage;
use yii\db\Query;

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
            [
                'class' => \abcms\library\behaviors\SeoBehavior::className(),
                'route' => '/shop/product',
                'titleAttribute' => 'name',
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
     * Get Image Album that belongs to this model
     * @return mixed
     */
    public function getAlbum()
    {
        return $this->hasOne(GalleryAlbum::className(), ['id' => 'albumId'])->active()->with(['activeImages']);
    }

    /**
     * Get images of this product
     * @return GalleryImage[]
     */
    public function getImages()
    {
        if($this->album) {
            return $this->album->activeImages;
        }
        return [];
    }

    /**
     * Get variations of this product
     * @return mixed
     */
    public function getVariations()
    {
        return $this->hasMany(ProductVariation::className(), ['productId' => 'id'])
                        ->andWhere('(quantity IS NULL OR quantity > 0)')
                        ->orderBy('id ASC')->with(['productVariationAttributes', 'productVariationAttributes.variationAttribute']);
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

    /**
     * Return the thumbnail image link
     * @return string
     */
    public function getThumbLink()
    {
        $images = $this->getImages();
        if(isset($images[0])) {
            return $images[0]->returnImageLink('small');
        }
        return null;
    }

    /**
     * Get similar products: products in same category with closest price.
     * @param integer $count
     * @return Product[]
     */
    public function getSimilarProducts($count = 6)
    {
        $query = self::find();
        $query->andWhere(['active' => 1, 'categoryId' => $this->categoryId]);
        $query->andWhere(['!=', 'id', $this->id]);
        $query->with(['album']);
        $query->orderBy(new \yii\db\Expression("ABS($this->finalPrice - finalPrice) ASC"));
        $query->limit($count);
        $models = $query->all();
        return $models;
    }

    
    public function getVariationsArray()
    {
        $attributesQuery = "SELECT DISTINCT(attributeId) FROM `shop_product_variation` as variation INNER JOIN `shop_product_variation_attribute` as attribute ON attribute.variationId = variation.id WHERE productId = :id AND (quantity IS NULL OR quantity > 0) ORDER BY attribute.id ASC";
        $uniqueAttributesIds = Yii::$app->db->createCommand($attributesQuery)->bindValue(':id', $this->id)->queryColumn();
        
        
        $query = new Query();
        $columns = ['variation.id', 'variation.quantity'];
        foreach($uniqueAttributesIds as $attributeId){
            $attributeId = (int)$attributeId;
            $columns[] = "attribute$attributeId.value as value$attributeId";
        }
        $query->select($columns)->from('shop_product_variation as variation');
        $query->andWhere(['productId'=>$this->id]);
        $query->andWhere('(quantity IS NULL OR quantity > 0)');
        foreach($uniqueAttributesIds as $attributeId){
            $attributeId = (int)$attributeId;
            $query->join('INNER JOIN', "shop_product_variation_attribute as attribute$attributeId", "attribute$attributeId.variationId = variation.id AND attribute$attributeId.attributeId = $attributeId");
        }
        $variations = $query->all();
        return $variations;
    }

}
