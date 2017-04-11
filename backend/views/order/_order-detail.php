<?php
use yii\widgets\DetailView;
?>
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
                'attribute' => 'subTotal',
                'value' => $model->subTotal.'$',
            ],
            [
                'attribute' => 'shippingPrice',
                'value' => $model->shippingPrice.'$',
            ],
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