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
                                <form>
                                    <div class="row">
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($usermodel,'mobile_num')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Mobile Number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($usermodel,'email')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Email']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'dist_name')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Distributor Name']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'address')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Address']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'state')->dropDownList(ArrayHelper::map(States::find()->asArray()->all(), 'id', 'name'),['prompt'=>'Select States','class'=>'form-control select2','id'=>'state_id']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                        <?= $form->field($model,'city')->dropDownList(ArrayHelper::map(City::find()->asArray()->all(), 'id', 'name'),['prompt'=>'Select City','class'=>'form-control select2','id'=>'city_id']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'taluka')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Taluka']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'district')->textInput(['class' => 'form-control','placeholder'=>'Enter Your District']) ?>
                                        </div>
                                       
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'gstin')->textInput(['class' => 'form-control','placeholder'=>'Enter Your GST NO']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'pan')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Pan']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'owner_name')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Owner Name']) ?>
                                        </div>
                                        <div class="col-md-12">
                                        <?php echo Html::submitButton("Submit", ['class' => 'btn btn-primary', 'id' => 'submit-distributor']); ?>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
