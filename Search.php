<?php

namespace abcms\shop;

use Yii;
use yii\base\Object;
use abcms\shop\models\Product;
use yii\data\Pagination;
use abcms\shop\models\Category;

/**
 * Products search class
 */
class Search extends Object
{
    /**
     * @var array|string Search order
     */
    public $orderBy = null;
    
    /**
     * @var int|null $limit Number of results to return
     */
    public $limit = null;
    
    /**
     * @var int $offset return results after this number
     */
    public $offset = 0;
    
    /**
     * @var string $keyword the keyword to be searched
     */
    public $keyword = null;
    
    /**
     * @var int $categoryId 
     */
    public $categoryId = null;
    
    /**
     * Returns model search query class.
     * @return \yii\db\ActiveQuery
     */
    public function getQuery(){
        $query = Product::find()->active();
        $query->with(['album']);
        if(!$this->orderBy && !$this->keyword){ // Default order by
            $this->orderBy = ['time'=> SORT_DESC];
        }
        if($this->orderBy){
            $query->orderBy($this->orderBy);
        }
        if($this->limit) {
            $query->limit($this->limit)->offset($this->offset);
        }
        if($this->keyword){
            $query->andWhere("MATCH (name, description) AGAINST (:keyword IN BOOLEAN MODE)", [':keyword' => $this->keyword.'*']);
        }
        if($this->categoryId){
            $query->andWhere(['categoryId'=>$this->getCategoriesForSearch()]);
        }
        return $query;
    }
    
    /**
     * Return array of models
     * @return \yii\db\ActiveRecord[]
     */
    public function getModels(){
        $query = $this->getQuery();
        $models = $query->all();
        return $models;
    }
    
    /**
     * Get pagination object
     * @return Pagination
     */
    public function getPagination(){
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
    public function getTotal(){
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
    protected function getCategoriesForSearch(){
        if(!$this->_categoriesForSearch){
            $subIds = Category::find()->select('id')->andWhere(['parentId' => $this->categoryId, 'active'=>1])->column();
            $array = array_unique(array_merge([$this->categoryId], $subIds));
            $this->_categoriesForSearch = $array;
        }
        return $this->_categoriesForSearch;
    }
    
    /**
     * Returns categories related to this search: 
     * products of this search belongs to these categories.
     * @return Category[]
     */
    public function getCategories(){        
        $array = Category::find()->andWhere(['parentId'=>null, 'active'=>1])->all();
        return $array;
    }
}
