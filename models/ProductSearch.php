<?php

namespace abcms\shop\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use abcms\shop\models\Product;

/**
 * ProductSearch represents the model behind the search form about `abcms\shop\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'categoryId', 'availableQuantity', 'brandId', 'active', 'deleted'], 'integer'],
            [['name', 'description', 'time'], 'safe'],
            [['finalPrice', 'originalPrice'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'categoryId' => $this->categoryId,
            'finalPrice' => $this->finalPrice,
            'originalPrice' => $this->originalPrice,
            'availableQuantity' => $this->availableQuantity,
            'brandId' => $this->brandId,
            'active' => $this->active,
            'deleted' => $this->deleted,
            'time' => $this->time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
