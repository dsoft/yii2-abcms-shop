<?php

use yii\helpers\Html;
use yii\grid\GridView;
use abcms\shop\models\Category;

/* @var $this yii\web\View */
/* @var $searchModel abcms\shop\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Category', ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'parentId',
                'value' => function($model){
                    return $model->parentName;
                },
                'filter' => Category::getParentsList(),
            ],
            'ordering',
            ['class' => 'abcms\library\grid\ActivateColumn'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
</div>
