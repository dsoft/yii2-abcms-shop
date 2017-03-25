<?php

namespace abcms\shop\models;

use Yii;
use yii\base\Model;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use abcms\shop\models\Category;

/**
 * SearchForm is the model behind the search form.
 */
class ProductSearchForm extends Model
{

    /**
     * @var int search query
     */
    public $q;

    /**
     * @var int Order by ID
     */
    public $orderBy;
    
    /**
     * @var int Category ID
     */
    public $categoryId;
    
    /**
     * @var array Variations array where key is the attribute id and value is the attribute values
     */
    public $variations;
    
    /**
     * @var int min price
     */
    public $min;
    
    /**
     * @var int max price
     */
    public $max;
    
    /**
     * @var boolean true to search only for items on sale.
     */
    public $sale;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    /**
     * Populates the model with input data.
     * Override the parent function and populate the data manually 
     */
    public function load($data, $formName = null)
    {
        if(isset($data['q']) && $q = $data['q']) {
            $this->q = HtmlPurifier::process($q);
        }
        if(isset($data['order']) && $data['order']) {
            $orderBy =(int)$data['order'];
            $orderByList = $this->getOrderByList();
            if(isset($orderByList[$orderBy])){
                $this->orderBy = $orderBy;
            }
        }
        if(isset($data['category']) && $data['category']) {
            $this->categoryId =(int)$data['category'];
        }
        if(isset($data['v']) && $data['v'] && is_array($data['v'])) {
            $variations = [];
            foreach($data['v'] as $key=>$values){
                if(!is_array($values)){
                    $values = [$values];
                }
                foreach($values as $value){
                    $variations[(int)$key][] = HtmlPurifier::process($value);
                }
            }
            $this->variations = $variations;
        }
        if(isset($data['min']) && $data['min']) {
            $this->min = (int)$data['min'];
        }
        if(isset($data['max']) && $data['max']) {
            $this->max = (int)$data['max'];
        }
        if(isset($data['sale']) && $data['sale'] === '1') {
            $this->sale = true;
        }
        return true;
    }

    /**
     * Returns if any search parameter is set.
     * @return boolean
     */
    public function hasSearch()
    {
        if($this->q || $this->orderBy || $this->categoryId || $this->variations || $this->min || $this->max || $this->sale) {
            return true;
        }
        return false;
    }

    /**
     * Array containing route and parameters
     * @return array
     */
    public function getSearchRoute()
    {
        return [
            '/shop/search', 
            'q' => $this->q, 
            'order' => $this->orderBy, 
            'category' => $this->categoryId,
            'v' => $this->variations,
            'min' => $this->min,
            'max' => $this->max,
            'sale' => $this->sale,
                ];
    }
    
    /**
     * Returns search url
     * @param array $attribute attributes that you want to overwrite
     * @return string
     */
    public function getSearchUrl($attribute = []){
        $route = $this->getSearchRoute();
        foreach($attribute as $key=>$val){
            $route[$key] = $val;
        }
        return Url::to($route);
    }

    /**
     * Return the search url without certain variable
     * @param string|array $attribute attribute that should be removed from search url
     * @return string
     */
    public function getResetUrl($attribute)
    {
        $route = $this->getSearchRoute();
        if(!is_array($attribute)) {
            $attribute = [$attribute];
        }
        foreach($attribute as $att) {
            unset($route[$att]);
        }
        return Url::to($route);
    }
    
    /**
     * Return the variations search url
     * @param int $attributeId
     * @param string $value
     * @param string $action add or remove
     * @return string
     */
    public function getVariationSearchUrl($attributeId, $value, $action = 'add'){
        $variations = $this->variations;
        if($action == 'add' 
                && (!isset($variations[$attributeId]) || !in_array($value, $variations[$attributeId]))){
            $variations[$attributeId][] = $value;
        }
        elseif($action == 'remove' && isset($variations[$attributeId])){
            if($value === null){
                unset($variations[$attributeId]);
            }
            elseif(($key = array_search($value, $variations[$attributeId])) !== false){
                unset($variations[$attributeId][$key]);
            }
        }
        return $this->getSearchUrl(['v'=>$variations]);
    }
    
    /**
     * Check if variation exists in variations attribute.
     * @param int $attributeId
     * @param string $value
     * @return boolean
     */
    public function isVariationSelected($attributeId, $value){
        if(isset($this->variations[$attributeId]) && in_array($value, $this->variations[$attributeId])){
            return true;
        }
        return false;
    }

    /**
     * Get array of order by values, to be used in frontend drop down list.
     * @return array
     */
    public static function getOrderByList()
    {
        $list = [
            1 => 'Best match',
            2 => 'Price: lowest first',
            3 => 'Price: highest first',
        ];
        return $list;
    }
    
    /**
     * Get order by name from order by list
     * @return string|null
     */
    public function getOrderByName()
    {
        $list = $this->getOrderByList();
        $value = $this->orderBy;
        if($value) {
            return (isset($list[$value])) ? $list[$value] : null;
        }
        return null;
    }

    /**
     * Get order by string that can be used in a query
     * @return string|null
     */
    public function getOrderByQuery()
    {
        $list = [
            1 => null,
            2 => 'finalPrice ASC',
            3 => 'finalPrice DESC',
        ];
        $value = $this->orderBy;
        if($value) {
            return (isset($list[$value])) ? $list[$value] : null;
        }
        return null;
    }
    
    /**
     * @var Category
     */
    private $_category = null;
    
    /**
     * Get selected category model
     * @return Category
     */
    public function getCategory(){
        if($this->categoryId && !$this->_category){
            $this->_category = Category::findOne(['active'=>1, 'id'=>$this->categoryId]);
        }
        return $this->_category;
    }

}
