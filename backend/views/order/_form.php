<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use abcms\shop\models\Order;

/* @var $this yii\web\View */
/* @var $model abcms\shop\models\Order */
/* @var $form yii\widgets\ActiveForm */
$statusList = Order::getStatusList();
unset($statusList[Order::STATUS_PENDING_PAYMENT]);
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'status')->dropDownList($statusList) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
