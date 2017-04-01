<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use abcms\shop\models\Order;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model abcms\shop\models\Order */

$this->title = 'Order #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if($model->status != Order::STATUS_PENDING_PAYMENT): ?>
        <p>
            <?= Html::a('Update Status', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </p>
    <?php endif; ?>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'userId',
                'value' => $model->getUserName(),
            ],
            'cartId',
            [
                'attribute' => 'total',
                'value' => $model->total.'$',
            ],
            'note',
            'firstName',
            'lastName',
            'email:email',
            'phone',
            'country',
            'city',
            'address',
            [
                'attribute' => 'status',
                'value' => $model->getStatusName(),
            ],
            'createdTime',
            'updatedTime',
            'ipAddress',
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
                'value' => function($data) {
                    $name = Html::encode(($name = $data->getProductName()) ? '('.$data->productId.') - '.$name : $data->productId);
                    $url = Url::to(['product/view', 'id'=>$data->productId]);
                    return "<a href='$url' target='_blank'>$name</a>";
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'variationId',
                'value' => function($data) {
                    return ($name = $data->getVariationText()) ? '('.$data->variationId.') - '.$name : $data->variationId;
                },
            ],
            'quantity',
            [
                'attribute' => 'price',
                'value' => function($data) {
                    return $data->getPrice().'$';
                }
            ],
            'description:ntext',
            'time',
        ],
    ]);
    ?>
</div>
