<?php

use yii\helpers\Html;
use abcms\shop\models\VariationAttribute;
$variationsList = VariationAttribute::getVariationsList();
?>
<div class="container-fluid">
    <?php foreach($attributes as $key=>$attribute): ?>
    <div class="row">
        <div class="col-md-5 <?php if($attribute->hasErrors('attributeId')) echo 'has-error'; ?>">
            <?= Html::activeDropDownList($attribute, "[$key]attributeId", $variationsList, ['class' => 'form-control', 'prompt' => 'Attribute']) ?>
            <?= Html::error($attribute, 'attributeId', ['class' => 'help-block']) ?>
        </div>
        <div class="col-md-5 <?php if($attribute->hasErrors('value')) echo 'has-error'; ?>">
            <?= Html::activeTextInput($attribute, "[$key]value", ['class' => 'form-control', 'placeholder' => 'Value']) ?>
            <?= Html::error($attribute, 'value', ['class' => 'help-block']) ?>
        </div>
        <div class="col-md-2">
            <a href="javascript:void(0);">
                <span class="glyphicon glyphicon-plus"></span>
                <span class="glyphicon glyphicon-minus"></span>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>


