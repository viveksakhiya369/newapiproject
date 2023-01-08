<?php
 
use yii\widgets\ActiveForm;
use common\models\CommonHelpers;
use yii\helpers\Html;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\States;
use common\models\City;

use function PHPSTORM_META\type;

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
                                            <?= $form->field($model,'item_group')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Mobile Number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'item_name')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Email']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'size')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Dealer Name']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'pack')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Address']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'unit')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Address']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'point')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Address','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'hsn')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Taluka']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'barcode')->textInput(['class' => 'form-control','placeholder'=>'Enter Your GST NO']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'tax')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Pan','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'purchase_rate')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Owner Name','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'mrp')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Owner Name','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'discount')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Owner Name','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'minimum_stock')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Owner Name','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'order_level')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Owner Name','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'personal_code')->textInput(['class' => 'form-control','placeholder'=>'Enter Your Owner Name']) ?>
                                        </div>
                                        <div class="col-md-12">
                                        <?php echo Html::submitButton("Submit", ['class' => 'btn btn-primary', 'id' => 'submit-dealer']); ?>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
