<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use abcms\shop\models\Category;
use abcms\shop\models\Brand;
use abcms\gallery\module\models\GalleryAlbum;

/* @var $this yii\web\View */
/* @var $model abcms\shop\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'categoryId')->dropDownList(Category::getCategoriesList(), ['prompt' => 'Select Category']) ?>

    <?= $form->field($model, 'finalPrice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'originalPrice')->textInput(['maxlength' => true]) ?>

    <?=
    $form->field($model, 'description')->widget(vova07\imperavi\Widget::className(), [
        'settings' => [
            'minHeight' => 200,
        ]
    ])
    ?>

    <?= $form->field($model, 'availableQuantity')->textInput()->hint("0: not available, >0 : available, keep empty if available and quanitity not tracked. <br />"
            . "If variations are available, this field will be ignored.") ?>

    <?= $form->field($model, 'brandId')->dropDownList(Brand::getBrandsList(), ['prompt' => 'Select Brand']) ?>
    
    <?= $form->field(new GalleryAlbum(), 'images[]')->fileInput(['multiple' => true])->label('Add Images'); ?>

    <?= $form->field($model, 'active')->checkbox() ?>

    <?= \abcms\multilanguage\widgets\TranslationForm::widget(['model' => $model]) ?>

    <?= \abcms\structure\widgets\SeoForm::widget(['model' => $model]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
