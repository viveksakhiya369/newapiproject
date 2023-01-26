<?php
 
use yii\widgets\ActiveForm;
use common\models\CommonHelpers;
use yii\helpers\Html;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\States;
use common\models\City;

?>
<?php
 $form = ActiveForm::begin([
    'id' => 'search_form',
    'options' => ['data-pjax' => false],
   ]);
?>
<div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <!-- <div class="card-title mb-3">Form Inputs</div> -->
                                    <div class="row">
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($usermodel,'mobile_num')->textInput(['class' => 'form-control','placeholder'=>'Enter Mobile Number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($usermodel,'email')->textInput(['class' => 'form-control','placeholder'=>'Enter Email']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'name')->textInput(['class' => 'form-control','placeholder'=>'Enter Full Name']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'address')->textInput(['class' => 'form-control','placeholder'=>'Enter Address']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'state')->dropDownList(ArrayHelper::map(States::find()->asArray()->all(), 'id', 'name'),['prompt'=>'Select States','class'=>'form-control select2','id'=>'state_id']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                        <?= $form->field($model,'city')->dropDownList(ArrayHelper::map(City::find()->asArray()->all(), 'id', 'name'),['prompt'=>'Select City','class'=>'form-control select2','id'=>'city_id']) ?>
                                        </div>
                                        <div class="col-md-12">
                                        <?php echo Html::submitButton("Submit", ['class' => 'btn btn-primary', 'id' => 'submit-salesman']); ?>

                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
