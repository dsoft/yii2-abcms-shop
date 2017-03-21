<?php

namespace abcms\shop\models;

use Yii;
use yii\base\Model;
use yii\helpers\HtmlPurifier;

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
        return true;
    }

}
