<?php

namespace abcms\shop\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use abcms\shop\models\Order;

/**
 * OrderSearch represents the model behind the search form about `abcms\shop\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'userId', 'cartId', 'country', 'status'], 'integer'],
            [['total'], 'number'],
            [['note', 'firstName', 'lastName', 'email', 'phone', 'city', 'address', 'createdTime', 'updatedTime', 'ipAddress'], 'safe'],
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
        $query = Order::find();

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
            'userId' => $this->userId,
            'cartId' => $this->cartId,
            'total' => $this->total,
            'country' => $this->country,
            'status' => $this->status,
            'createdTime' => $this->createdTime,
            'updatedTime' => $this->updatedTime,
        ]);

        $query->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'firstName', $this->firstName])
            ->andFilterWhere(['like', 'lastName', $this->lastName])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'ipAddress', $this->ipAddress]);

        return $dataProvider;
    }
}
