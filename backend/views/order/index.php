<?php

use yii\helpers\Html;
use yii\grid\GridView;
use abcms\shop\models\Order;

/* @var $this yii\web\View */
/* @var $searchModel abcms\shop\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'userId',
                'value' => function($model) {
                    return $model->userId.' - '.$model->getUserName();
                },
            ],
            [
                'attribute' => 'total',
                'value' => function($model) {
                    return $model->total.'$';
                },
            ],
            'note',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->getStatusName();
                },
                'filter' => Order::getStatusList(),
            ],
            'createdTime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'visibleButtons' => [
                    'update' => function ($model, $key, $index) {
                        return $model->status !== Order::STATUS_PENDING_PAYMENT;
                    }
                ],
            ],
        ],
    ]);
    ?>
</div>
