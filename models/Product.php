<?php

namespace abcms\shop\models;

use Yii;
use abcms\library\behaviors\TimeBehavior;
use abcms\gallery\module\models\GalleryAlbum;
use abcms\gallery\module\models\GalleryImage;
use yii\db\Query;
use yii\helpers\ArrayHelper;

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
 * @property integer $featured
 * @property integer $active
 * @property integer $deleted
 * @property string $time
 */
class Product extends \abcms\library\base\BackendActiveRecord
{
    
    /**
     * @event Event an event that is triggered after trying to decrease the quantity of an unavailable product
     */
    const EVENT_ORDERING_UNAVAILABLE_PRODUCT = 'orderingUnavailableProduct';
    
    /**
     * @event Event an event that is triggered after a product become unavailable
     */
    const EVENT_PRODUCT_BECAME_UNAVAILABLE = 'productBecameUnavailable';

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
            [['categoryId', 'availableQuantity', 'brandId', 'active', 'featured'], 'integer'],
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
            'featured' => 'Featured',
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
        return $this->hasMany(ProductVariation::className(), ['productId' => 'id']);
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

    /**
     * Get variations array to be used in frontend.
     * Array contains the following keys: id (attribute ID), name (attribute name) and values:
     * Values is an array containing attribute values as keys and another array containing the same sub attributes.
     * Last attributes level should have an array including the variationId and quantity.
     * exmaple:
     * ```php
     * [
     *       'id' => 1, 'name' => 'Size', 'values' => [
     *           'S' => ['id' => 2, 'name' => 'Color', 'values' => [
     *                   'red' => [
     *                       'id' => 3, 'name' => 'xyz', 'values' => [
     *                           'x' => ['variationId' => 2, 'quantity' => 1],
     *                           'y' => ['variationId' => 3, 'quantity' => 2],
     *                       ]],
     *                   'green' =>
     *                   [
     *                       'id' => 3, 'name' => 'xyz', 'values' => [
     *                           'y' => ['variationId' => 4, 'quantity' => null],
     *                           'z' => ['variationId' => 5, 'quantity' => 2],
     *                       ]],
     *               ]],
     *           'L' => ['id' => 2, 'name' => 'Color', 'values' => [
     *                   'blue' => [
     *                       'id' => 3, 'name' => 'xyz', 'values' => [
     *                           'z' => ['variationId' => 6, 'quantity' => 3],
     *                       ]],
     *               ]],
     *       ],
     *  ]
     * ```
     * @return array
     */
    public function getVariationsArray()
    {
        $attributesQuery = "SELECT DISTINCT(attributeId), va.name FROM `shop_product_variation` as variation INNER JOIN `shop_product_variation_attribute` as attribute ON attribute.variationId = variation.id INNER JOIN shop_variation_attribute as va ON attribute.attributeId = va.id WHERE productId = :id AND (quantity IS NULL OR quantity > 0) ORDER BY attribute.id ASC";
        // Unique variations attributes assigned to this product like: [['attributeId'=>1, 'name'=>'Size'], ['attributeId'=>2, 'name'=>'Color']]
        $uniqueAttributes = Yii::$app->db->createCommand($attributesQuery)->bindValue(':id', $this->id)->queryAll();
        // The array that should be returned
        $variationsArray = [];
        if($uniqueAttributes) {
            $query = new Query();
            $columns = ['variation.id', 'variation.quantity'];
            foreach($uniqueAttributes as $attribute) {
                $attributeId = (int) $attribute['attributeId'];
                $columns[] = "attribute$attributeId.value as value$attributeId";
                $columns[] = "attribute$attributeId.attributeId as attributeId$attributeId";
            }
            $query->select($columns)->from('shop_product_variation as variation');
            $query->andWhere(['productId' => $this->id]);
            $query->andWhere('(quantity IS NULL OR quantity > 0)');
            foreach($uniqueAttributes as $attribute) {
                $attributeId = (int) $attribute['attributeId'];
                $query->join('INNER JOIN', "shop_product_variation_attribute as attribute$attributeId", "attribute$attributeId.variationId = variation.id AND attribute$attributeId.attributeId = $attributeId");
            }
            // List of variations with attributes ids and names is one row
            $variations = $query->all();

            // Recursive function to fill the variations
            $getVariationArray = function($attributes, $variations) use(&$getVariationArray) {
                if($attributes) {
                    $attribute = current($attributes);
                    $variationArray = ['id' => $attribute['attributeId'], 'name' => $attribute['name'], 'values' => []];
                    $attributeId = $attribute['attributeId'];
                    // Remove first attribute
                    array_shift($attributes);
                    foreach($variations as $variation) {
                        $subArray = $getVariationArray($attributes, [$variation]);
                        if(isset($variationArray['values'][$variation["value$attributeId"]]['id']) && $variationArray['values'][$variation["value$attributeId"]]['id'] == $subArray['id']) {                       
                            $variationArray['values'][$variation["value$attributeId"]]['values'] = ArrayHelper::merge($variationArray['values'][$variation["value$attributeId"]]['values'], $subArray['values']);
                        }
                        else {
                            $variationArray['values'][$variation["value$attributeId"]] = $subArray;
                        }
                    }
                    return $variationArray;
                }
                else { // Last level: add variation id and quantity
                    return ['variationId' => $variations[0]['id'], 'quantity' => $variations[0]['quantity']];
                }
            };
            $variationsArray = $getVariationArray($uniqueAttributes, $variations);
        }
        return $variationsArray;
    }

    /**
     * If product has variations return true.
     * @return boolean
     */
    public function hasVariations()
    {
        if($this->variations) {
            return true;
        }
        return false;
    }

    /**
     * Test if product is available:
     * If product doesn't have variations check availableQuantity
     * Otherwise check if there's one variation with available quantity.
     * @param integer $variationId If set check if certain variation is available
     * @return boolean
     */
    public function isAvailable($variationId = null)
    {
        if($this->hasVariations()) {
            if($variationId) { // Check if certain variation is available
                foreach($this->variations as $variation) {
                    if($variation->id == $variationId) {
                        if($variation->quantity !== 0){
                            return true;
                        }
                        else{
                            return false;
                        }
                    }
                }
            }
            else { // Check if any variation is available
                foreach($this->variations as $variation) {
                    if($variation->quantity !== 0) {
                        return true;
                    }
                }
            }
            return false;
        }
        else {
            return ($this->availableQuantity !== 0) ? true : false;
        }
    }
    
    /**
     * Decrease quantity for this product
     * @param int $quantity Quantity that should be decreased
     * @return boolean If quantity available and was decreased
     */
    public function decreaseQuantity($quantity)
    {
        if($this->availableQuantity === null){
            return true;
        }
        elseif($this->availableQuantity === 0){
            $this->orderingUnavailableProduct();
            return false;
        }
        else{
            if($this->availableQuantity >= $quantity){
                $this->availableQuantity -= $quantity;
                if($this->save(false)){
                    if($this->availableQuantity === 0){ // Product became out of stock
                        $this->productBecameUnavailable();
                    }
                    return true;
                }else{
                    return false;
                }
            }
            else{
                $this->orderingUnavailableProduct();
                return false;
            }
        }
    }
    
    /**
     * This method is invoked after trying to decrease the quantity of an unavailable product.
     * The default implementation raises the [[EVENT_ORDERING_UNAVAILABLE_PRODUCT]] event.
     */
    public function orderingUnavailableProduct()
    {
        $this->trigger(self::EVENT_ORDERING_UNAVAILABLE_PRODUCT);
    }
    
    /**
     * This method is invoked after decreasing the quantity of a certain product and product become unavailable
     * The default implementation raises the [[EVENT_PRODUCT_BECAME_UNAVAILABLE]] event.
     */
    public function productBecameUnavailable()
    {
        $this->trigger(self::EVENT_PRODUCT_BECAME_UNAVAILABLE);
    }

}
