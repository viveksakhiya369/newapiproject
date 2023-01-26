<?php

use yii\widgets\ActiveForm;
use common\models\CommonHelpers;
use yii\helpers\Html;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\States;
use common\models\City;
use yii\widgets\Pjax;

?>
<?php
Pjax::begin([
    'enablePushState' => false,
    'id' => uniqid('pjax_')
]);
$form = ActiveForm::begin();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <!-- <div class="card-title mb-3">Form Inputs</div> -->
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <?= $form->field($model, 'driver_name')->textInput(['class' => 'form-control', 'placeholder' => 'Enter Mobile Number']) ?>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <?= $form->field($model, 'vehicle_number')->textInput(['class' => 'form-control', 'placeholder' => 'Enter Mobile Number']) ?>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <?= $form->field($model, 'transpotation_id')->textInput(['class' => 'form-control', 'placeholder' => 'Enter Mobile Number','readonly'=>true,'value'=>"CONVAXTID".Yii::$app->request->get('order_no')]) ?>
                        </div>
                        <?php if(Yii::$app->request->get('receieved')){ ?>
                        <div class="col-md-12">
                            <?php echo Html::submitButton("Submit", ['class' => 'btn btn-primary', 'id' => 'submit-salesman']); ?>

                        </div>
                        <?php } ?>
                    </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php Pjax::end() ?>