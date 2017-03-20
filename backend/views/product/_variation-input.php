<?php

use yii\helpers\Html;
use abcms\shop\models\VariationAttribute;
$variationsList = VariationAttribute::getVariationsList();

$this->registerCss(<<<EOT
        .variation a{
            line-height: 34px;
            text-decoration: none;
        }
EOT
);
$this->registerJs(<<<EOT
        var emptyRow = $('.variations .variation:first-child').clone();
        emptyRow.find('input').val('');
        emptyRow.find('select').find('option').removeAttr('selected');
        $('.variations').on('click', '.add-variation', function(){
            var newRow = emptyRow.clone();
            var number = $('.variations .variation').length;
            newRow.find('input, select').each(function(){
                $(this).attr('name', $(this).attr('name').replace('[0]', '['+number+']'));
            });
            $('.variations').append(newRow);
        });
        $('.variations').on('click', '.remove-variation', function(){
            $(this).parents('.variation').remove();
            var variations = $('.variations .variation');
            var length = variations.length;
            if(length == 0){
                $('.variations').append(emptyRow.clone());
            }
            else{
                for(var i=0; i<length; i++){
                    var variation = variations.eq(i);
                    variation.find('input, select').each(function(){
                        var name = $(this).attr('name');
                        $(this).attr('name', name.replace(/\[\d*\]/i, '['+i+']'));
                    });
                }
            }
        });
EOT
);
?>
<div class="container-fluid variations">
    <?php foreach($attributes as $key=>$attribute): ?>
    <div class="row variation">
        <div class="col-md-5 <?php if($attribute->hasErrors('attributeId')) echo 'has-error'; ?>">
            <?= Html::activeDropDownList($attribute, "[$key]attributeId", $variationsList, ['class' => 'form-control', 'prompt' => 'Attribute']) ?>
            <?= Html::error($attribute, 'attributeId', ['class' => 'help-block']) ?>
        </div>
        <div class="col-md-5 <?php if($attribute->hasErrors('value')) echo 'has-error'; ?>">
            <?= Html::activeTextInput($attribute, "[$key]value", ['class' => 'form-control', 'placeholder' => 'Value']) ?>
            <?= Html::error($attribute, 'value', ['class' => 'help-block']) ?>
        </div>
        <div class="col-md-2">
            <a href="javascript:void(0);" class="add-variation">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
            <a href="javascript:void(0);" class="remove-variation">
                <span class="glyphicon glyphicon-minus"></span>
            </a>      
        </div>
    </div>
    <?php endforeach; ?>
</div>


