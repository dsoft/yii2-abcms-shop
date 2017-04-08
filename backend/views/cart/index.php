<?php

use yii\helpers\Html;
use yii\grid\GridView;
use abcms\shop\models\Cart;

/* @var $this yii\web\View */
/* @var $searchModel abcms\shop\models\CartSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Carts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cart-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'userId',
                'value' => function($model){
                    return $model->userId.' - '.$model->getUserName();
                }
            ],
            'createdTime',
            'updatedTime',
            'closed:boolean',
            [
                'attribute' => 'typeId',
                'value' => function($model){
                    return $model->getType();
                },
                'filter' => Cart::getTypesList(),
            ],

            ['class' => 'yii\grid\ActionColumn', 'template'=>'{view}'],
        ],
    ]); ?>
</div>
