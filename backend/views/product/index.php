<?php

use yii\helpers\Html;
use yii\grid\GridView;
use abcms\shop\models\Category;

/* @var $this yii\web\View */
/* @var $searchModel abcms\shop\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            [
                'attribute' => 'categoryId',
                'value' => function($model) {
                    return $model->categoryName;
                },
                'filter' => Category::getCategoriesList(),
            ],
            'finalPrice',
            'time',
            ['class' => 'abcms\library\grid\ActivateColumn'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
</div>
