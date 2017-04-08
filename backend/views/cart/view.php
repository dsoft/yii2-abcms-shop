<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model abcms\shop\models\Cart */

$this->title = 'Cart #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Carts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cart-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'hash',
            [
                'attribute' => 'userId',
                'value' => $model->userId.' - '.$model->getUserName()
            ],
            'createdTime',
            'updatedTime',
            'closed:boolean',
            'type',
            [
                'label' => 'Total',
                'value' => $model->total.'$',
            ],
        ],
    ])
    ?>

    <h2>Products</h2>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'productId',
                'value' => function($data){
                    return ($name = $data->getProductName()) ? '('.$data->productId.') - '.$name : $data->productId;
                },
            ],
            [
                'attribute' => 'variationId',
                'value' => function($data){
                    return ($name = $data->getVariationText()) ? '('.$data->variationId.') - '.$name : $data->variationId;
                },
            ],
            'quantity',
            [
                'attribute' => 'price',
                'value' => function($data){
                    return $data->getPrice().'$';
                }
            ],
            'description:ntext',
            'time',
        ],
    ]);
    ?>

</div>
