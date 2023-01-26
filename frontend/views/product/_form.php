<?php
 
use yii\widgets\ActiveForm;
use common\models\CommonHelpers;
use yii\helpers\Html;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\States;
use common\models\City;
use common\models\TaxMaster;

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
                                    <div class="row">
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'item_group')->textInput(['class' => 'form-control','placeholder'=>'Enter Item Group']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'item_name')->textInput(['class' => 'form-control','placeholder'=>'Enter Item Name']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'size')->textInput(['class' => 'form-control','placeholder'=>'Enter Size of Item']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'pack')->textInput(['class' => 'form-control','placeholder'=>'Enter Pack size']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'unit')->textInput(['class' => 'form-control','placeholder'=>'Enter Unit of Item']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'point')->textInput(['class' => 'form-control','placeholder'=>'Enter Points per Item','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'hsn')->textInput(['class' => 'form-control','placeholder'=>'Enter HSN of Item']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'barcode')->textInput(['class' => 'form-control','placeholder'=>'Enter Barcode of Item']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'tax')->dropDownList(ArrayHelper::map(TaxMaster::find()->select(['id','concat(name,"-",percentage,"%") as percent_with_name'])->where(['!=','status',TaxMaster::STATUS_DELETED])->asArray()->all(),'id','percent_with_name'),['class'=>'select2 form-control','id'=>'tax-drop','prompt'=>'Select Tax']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'purchase_rate')->textInput(['class' => 'form-control','placeholder'=>'Enter Purchase Rate','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'wholesale_rate')->textInput(['class' => 'form-control','placeholder'=>'Enter Distributor Rate','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'dealer_rate')->textInput(['class' => 'form-control','placeholder'=>'Enter Dealer Rate','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'mrp')->textInput(['class' => 'form-control','placeholder'=>'Enter MRP of Item','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'discount')->textInput(['class' => 'form-control','placeholder'=>'Enter Discount in Percentage','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'minimum_stock')->textInput(['class' => 'form-control','placeholder'=>'Enter Minimum Stock','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'order_level')->textInput(['class' => 'form-control','placeholder'=>'Enter Order Level','type'=>'number']) ?>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <?= $form->field($model,'personal_code')->textInput(['class' => 'form-control','placeholder'=>'Enter Personal Code']) ?>
                                        </div>
                                        <div class="col-md-12">
                                        <?php echo Html::submitButton("Submit", ['class' => 'btn btn-primary', 'id' => 'submit-dealer']); ?>

                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
