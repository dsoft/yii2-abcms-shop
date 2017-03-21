<?php

namespace abcms\shop;

use Yii;
use yii\base\Object;
use abcms\shop\models\Product;
use yii\data\Pagination;

/**
 * Products search class
 */
class Search extends Object
{
    /**
     * @var array|string Search order
     */
    public $orderBy = ['time'=> SORT_DESC];
    
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
     * Returns model search query class.
     * @return \yii\db\ActiveQuery
     */
    public function getQuery(){
        $query = Product::find()->active();
        $query->with(['album']);
        $query->orderBy($this->orderBy);
        if($this->limit) {
            $query->limit($this->limit)->offset($this->offset);
        }
        if($this->keyword){
            $query->andWhere("MATCH (name, description) AGAINST (:keyword IN BOOLEAN MODE)", [':keyword' => $this->keyword]);
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
}
