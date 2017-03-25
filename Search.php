<?php

namespace abcms\shop;

use Yii;
use yii\base\Object;
use abcms\shop\models\Product;
use yii\data\Pagination;
use abcms\shop\models\Category;
use abcms\shop\models\VariationAttribute;

/**
 * Products search class
 */
class Search extends Object
{

    /**
     * @var array|string Search order.
     */
    public $orderBy = null;

    /**
     * @var int|null $limit Number of results to return.
     */
    public $limit = null;

    /**
     * @var int $offset return results after this number.
     */
    public $offset = 0;

    /**
     * @var string $keyword the keyword to be searched.
     */
    public $keyword = null;

    /**
     * @var int $categoryId
     */
    public $categoryId = null;
    
    /**
     * @var array Variations array where key is the attributeId and value is the attributes value or array.
     */
    public $variations = null;
    
    /**
     * @var int min price.
     */
    public $min = null;
    
    /**
     * @var int max price.
     */
    public $max = null;
    
    /**
     * @var boolean If true return product on sale only.
     */
    public $sale = false;

    /**
     * Returns model search query class.
     * @return \yii\db\ActiveQuery
     */
    public function getQuery($exclusion = [])
    {
        $query = Product::find()->active();
        $query->with(['album']);
        if(!$this->orderBy && !$this->keyword) { // Default order by
            $this->orderBy = ['time' => SORT_DESC];
        }
        if($this->orderBy) {
            $query->orderBy($this->orderBy);
        }
        if($this->limit) {
            $query->limit($this->limit)->offset($this->offset);
        }
        if($this->keyword) {
            $query->andWhere("MATCH (name, description) AGAINST (:keyword IN BOOLEAN MODE)", [':keyword' => $this->keyword.'*']);
        }
        if($this->categoryId) {
            $query->andWhere(['categoryId' => $this->getCategoriesForSearch()]);
        }
        if($this->variations && is_array($this->variations) && !in_array('variations', $exclusion)){
            $i = 0;
            foreach($this->variations as $attributeId => $attributes){
                $i++;
                $query->innerJoin("shop_product_variation as variation$i", "variation$i.productId = shop_product.id");
                $query->innerJoin("shop_product_variation_attribute as attribute$i", "attribute$i.variationId = variation$i.id");
                $query->andWhere([
                    "attribute$i.attributeId"=>$attributeId,
                    "attribute$i.value" => $attributes,
                        ]);
                if($i>1){
                    $query->andWhere("variation$i.id = variation1.id");
                }
            }
        }
        if($this->min && !in_array('min', $exclusion)){
            $query->andWhere(['>=', 'finalPrice', $this->min]);
        }
        if($this->max && !in_array('max', $exclusion)){
            $query->andWhere(['<=', 'finalPrice', $this->max]);
        }
        if($this->sale){
            $query->andWhere('finalPrice < originalPrice');
        }
        return $query;
    }

    /**
     * Return array of models
     * @return \yii\db\ActiveRecord[]
     */
    public function getModels()
    {
        $query = $this->getQuery();
        $models = $query->all();
        return $models;
    }

    /**
     * Get pagination object
     * @return Pagination
     */
    public function getPagination()
    {
        $pages = new Pagination([
            'validatePage' => false,
            'defaultPageSize' => $this->limit,
        ]);
        return $pages;
    }

    /**
     * Get total number of products
     * @return integer
     */
    public function getTotal()
    {
        $query = $this->getQuery();
        $query->offset = 0;
        $query->limit = null;
        $total = $query->count();
        return $total;
    }

    /**
     * @var int[] array of categories ids
     */
    private $_categoriesForSearch = null;

    /**
     * Returns array of categories ids that should be searched,
     * including category id and children ids.
     * @return int[]
     */
    protected function getCategoriesForSearch()
    {
        if(!$this->_categoriesForSearch) {
            $subIds = Category::getChildrenIds($this->categoryId);
            $array = array_unique(array_merge([$this->categoryId], $subIds));
            $this->_categoriesForSearch = $array;
        }
        return $this->_categoriesForSearch;
    }

    /**
     * Returns categories related to this search: 
     * Only categories with products in this search.
     * @return Category[]
     */
    public function getCategories()
    {
        // product count per category
        $count = $this->getCategoriesProductsCount();
        // Categories with products in this search
        $categories = [];
        if($this->categoryId) { // Sub categories dislay
            $parentCategory = Category::findOne(['active' => 1, 'id' => $this->categoryId]);
            if($parentCategory) {
                $allCategories = $parentCategory->children;
                foreach($allCategories as $c) {
                    if(isset($count[$c->id]) && $count[$c->id]) {
                        $c->productCount = $count[$c->id];
                        $categories[] = $c;
                    }
                }
            }
        }
        else { // First level categories display
            // children ids per category
            $subIds = Category::getChildrenIds();
            $allCategories = Category::find()->andWhere(['parentId' => null, 'active' => 1])->all();
            foreach($allCategories as $c) {
                $productCount = 0;
                $idsToCount = [$c->id];
                if(isset($subIds[$c->id])) {
                    $idsToCount = array_merge($idsToCount, $subIds[$c->id]);
                    foreach($idsToCount as $id) {
                        $id = (int) $id;
                        if(isset($count[$id])) {
                            $productCount += $count[$id];
                        }
                    }
                }
                if($productCount) {
                    $c->productCount = $productCount;
                    $categories[] = $c;
                }
            }
        }

        return $categories;
    }

    /**
     * Returns an array where key is the category ID and val is the products count for this search
     * @return array
     */
    protected function getCategoriesProductsCount()
    {
        $query = $this->getQuery();
        $query->offset = 0;
        $query->limit = null;
        $query->with = null;
        $query->groupBy('categoryId');
        $query->select('*, count(*) AS count');
        $array = $query->asArray()->all();
        $categories = [];
        foreach($array as $val) {
            $categories[$val['categoryId']] = $val['count'];
        }
        return $categories;
    }
    
    /**
     * Return variations attributes that are available in this search. 
     * @return VariationAttribute[]
     */
    public function getVariationAttributes(){
        $query = $this->getQuery(['variations']);
        $query->offset = 0;
        $query->limit = null;
        $query->with = null;
        $query->innerJoin("shop_product_variation as variation", "variation.productId = shop_product.id");
        $query->innerJoin("shop_product_variation_attribute as attribute", "attribute.variationId = variation.id");
        $query->groupBy('attribute.attributeId, attribute.value');
        $query->select('*, attribute.attributeId, attribute.value, count(*) as count');
        $array = $query->asArray()->all();
        $attributesIds = [];
        foreach($array as $val) {
            $attributesIds[$val['attributeId']][$val['value']] = $val['count'];
        }
        if($attributesIds){
            $models = VariationAttribute::find()->andWhere(['id'=> array_keys($attributesIds)])->all();
            foreach($models as $model){
                $model->values = $attributesIds[$model->id];
            }
            return $models;
        }
        return [];
    }
    
    /**
     * Returns an array containing the minimum price and the maximum price
     * @return array
     */
    public function getPriceLimit()
    {
        $query = $this->getQuery(['min', 'max']);
        $query->offset = 0;
        $query->limit = null;
        $query->with = null;
        $query->select('min(finalPrice) as min, max(finalPrice) as max');
        $command = $query->createCommand();
        $return = $command->queryOne();
        if(isset($return['min']) && isset($return['max'])){
            return [(int)$return['min'], (int)$return['max']];
        }
        return null;
    }

}
