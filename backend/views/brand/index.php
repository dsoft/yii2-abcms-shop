<?php

use yii\helpers\Html;
use abcms\library\grid\InlineFormGridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel abcms\shop\models\BrandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Brands';
$this->params['breadcrumbs'][] = $this->title;
if($formFocused){
    $this->registerJs("$('html, body').animate({scrollTop: $('#inline-form').offset().top}, 0);");
}
?>
<div class="brand-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    InlineFormGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'footer' => '<b>'.($model->isNewRecord ? 'Add new item:' : 'Update item #'.$model->id).'</b>',
            ],
            [
                'attribute' => 'name',
                'footer' => Html::activeTextInput($model, 'name', ['class' => 'form-control', 'placeholder' => 'Name']).Html::error($model, 'name', ['class' => 'help-block']),
                'footerOptions' => ['class' => $model->hasErrors('name') ? 'has-error' : ''],
            ],
            [
                'class' => 'abcms\library\grid\ActivateColumn',
                'footer' => Html::activeCheckbox($model, 'active').Html::error($model, 'active', ['class' => 'help-block']),
                'footerOptions' => ['class' => $model->hasErrors('active') ? 'has-error' : ''],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'footer' => Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
                .(!$model->isNewRecord ? ' '.Html::a('Cancel', Url::current(['id'=>null]), ['class'=>'btn btn-danger']) : ''),
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-pencil"]), Url::current(['id'=>$model->id]));
                    },
                ],
            ],
        ],
    ]);
    ?>
</div>
