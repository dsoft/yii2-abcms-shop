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
        return true;
    }

    /**
     * Returns if any search parameter is set.
     * @return boolean
     */
    public function hasSearch()
    {
        if($this->q || $this->orderBy || $this->categoryId) {
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
        return ['/shop/search', 'q' => $this->q, 'order' => $this->orderBy, 'category' => $this->categoryId];
    }
    
    /**
     * Returns serach url
     * @param array $attribute attriutes that you want to overwrite
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
     * Return the search url without certain vriable
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
