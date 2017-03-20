<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\helpers\Url;
use abcms\library\grid\InlineFormGridView;

/* @var $this yii\web\View */
/* @var $model abcms\shop\models\Product */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if($variationFormFocused){
    $this->registerJs("$('html, body').animate({scrollTop: $('#inline-form').offset().top}, 0);");
}
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

    <h2><br />Variations</h2>

    <?=
    InlineFormGridView::widget([
        'dataProvider' => $variationDataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'footer' => '<b>'.($variation->isNewRecord ? 'Add new item:' : 'Update Variation').'</b>',
            ],
            [
                'header' => 'Variation',
                'value' => function($data) {
                    return $data->getText();
                },
                'footer' => $this->render('_variation-input', ['attributes'=>$attributes]),
            ],
            [
                'attribute' => 'quantity',
                'footer' => Html::activeTextInput($variation, 'quantity', ['class' => 'form-control', 'placeholder' => 'Quantity']).Html::error($variation, 'quantity', ['class' => 'help-block']),
                'footerOptions' => ['class' => $variation->hasErrors('quantity') ? 'has-error' : ''],
            ],
            [
                'class' => yii\grid\ActionColumn::className(),
                'template' => '{update} {delete}',
                'footer' => Html::submitButton($variation->isNewRecord ? 'Create' : 'Update', ['class' => $variation->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
                .(!$variation->isNewRecord ? ' '.Html::a('Cancel', Url::current(['variationId'=>null]), ['class'=>'btn btn-danger']) : ''),
                'buttons' => [
                    'update' => function ($url, $variation, $key) {
                        return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-pencil"]), Url::current(['variationId'=>$variation->id]));
                    },
                    'delete' => function ($url, $variation, $key) {
                        return Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]), ['delete-variation', 'id'=>$variation->id], [
                                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'data-method' => 'post',
                                        ]);
                    },
                ],
            ],
        ],
    ]);
    ?>

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
                'urlCreator' => function($action, $m, $key, $index) {
                    $params = ['id' => (string) $key];
                    $params[0] = Yii::$app->controller->module->galleryRoute.'/image/activate';
                    $params['returnUrl'] = Url::current();
                    return Url::toRoute($params);
                },
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