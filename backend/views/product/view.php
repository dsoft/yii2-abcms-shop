<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model abcms\shop\models\Product */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'categoryName',
            'finalPrice',
            'originalPrice',
            'description:html',
            'availableQuantity',
            'brandName',
            'active:boolean',
            'time',
        ],
    ])
    ?>

    <?=
    \abcms\multilanguage\widgets\TranslationView::widget([
        'model' => $model,
    ])
    ?>

    <?= \abcms\structure\widgets\SeoView::widget(['model' => $model]) ?>

    <h2><br />Images</h2>

    <?=
    GridView::widget([
        'dataProvider' => $imageDataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'image',
                'value' => function($data) {
                    return $data->returnImageLink();
                },
                'format' => ['image', ['width' => 200]],
            ],
            'ordering',
            [
                'class' => abcms\library\grid\ActivateColumn::className(),
                'controller' => 'image',
            ],
            [
                'class' => yii\grid\ActionColumn::className(),
                'controller' => Yii::$app->controller->module->galleryRoute.'/image',
                'template' => '{update} {delete}',
                'urlCreator' => function($action, $m, $key, $index, $t) {
                    $params = is_array($key) ? $key : ['id' => (string) $key];
                    $params[0] = $t->controller ? $t->controller.'/'.$action : $action;
                    $params['returnUrl'] = Url::current();
                    return Url::toRoute($params);
                },
                    ],
                ],
            ]);
            ?>

</div>
