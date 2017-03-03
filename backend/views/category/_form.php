<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use abcms\shop\models\Category;

/* @var $this yii\web\View */
/* @var $model abcms\shop\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parentId')->dropDownList(Category::getParentsList($model->id), ['prompt' => 'Select parent.']) ?>

    <?= $form->field($model, 'active')->checkbox() ?>


    <?= \abcms\multilanguage\widgets\TranslationForm::widget(['model' => $model]) ?>

    <?= \abcms\structure\widgets\SeoForm::widget(['model' => $model]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
