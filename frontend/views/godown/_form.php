<?php

use common\models\Products;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
?>
<div class="customer-form">

    <?php
    $type_inward=[
        "Godown",
        "Store"
    ];
    $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="panel panel-default">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 100, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $model[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'id',
                    'item_id',
                    'item_name',
                    'qty',
                    'pack',
                    'rate',
                    'amount',
                    'status',
                    'barcode',
                    'inward_type',
                ],
            ]); ?>

            <div class="container-items"><!-- widgetContainer -->
                <?php foreach ($model as $i => $modelAddress) : ?>
                    <div class="item panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            <div class="float-right">
                                <button type="button" class="remove-item btn btn-danger btn-sm" value="<?php echo $i ?>-remove"><i class="text-20 i-Remove"></i></button>
                            </div>

                        </div>
                        <div class="panel-body">
                            <?php
                            // necessary for update action.
                            if (!$modelAddress->isNewRecord) {
                                echo Html::activeHiddenInput($modelAddress, "[{$i}]id");
                            }
                            ?>
                            <div class="row">
                                <div class="col-sm-4">
                                    <?= $form->field($modelAddress, "[{$i}]item_name")->textInput(['maxlength' => true, 'class' => 'item-qty form-control','readonly'=>true]) ?>                          
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($modelAddress, "[{$i}]qty")->textInput(['maxlength' => true, 'type' => 'number', 'class' => 'item-qty form-control']) ?>
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($modelAddress, "[{$i}]inward_type")->dropDownList($type_inward, ["prompt" => "Select Items", 'class' => 'select2 form-control']) ?>
                                    <?= $form->field($modelAddress, "[{$i}]item_id",['template'=>'{input}'])->hiddenInput(['maxlength' => true,'class'=>'test']); ?>
                                    <?php echo $form->field($modelAddress, "[{$i}]barcode",['template'=>'{input}'])->hiddenInput(['maxlength' => true]); ?>
                                    <?= $form->field($modelAddress, "[{$i}]rate",['template'=>'{input}'])->hiddenInput(['maxlength' => true,'class'=>'test']); ?>
                                    <?= $form->field($modelAddress, "[{$i}]amount",['template'=>'{input}'])->hiddenInput(['maxlength' => true,'class'=>'test']); ?>
                                    <?= $form->field($modelAddress, "[{$i}]tax",['template'=>'{input}'])->hiddenInput(['maxlength' => true,'class'=>'test']); ?>
                                    <?= $form->field($modelAddress, "[{$i}]discount",['template'=>'{input}'])->hiddenInput(['maxlength' => true,'class'=>'test']); ?>
                                </div>
                            </div><!-- .row -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton($modelAddress->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary']) ?>
                <button type="button" class="add-item btn btn-success btn-sm pull-right"><i class="text-20 i-Add"></i></button>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
<?php

$this->registerJs('
function getalldetails(count,product_id){
    $.post("'.Url::to(['ajax/get-product-details']).'",{
        product_id:product_id,
    },function(data){
        console.log(data);
        var response=JSON.parse(data);
        $("#orders-"+count+"-barcode").val(response.barcode);
    })
}
function getCalculate(i){
    var rate=$("#orders-"+i+"-rate").val();
    var quantity=$("#orders-"+i+"-qty").val();
    var amount=quantity * rate;
    var tax=($("#orders-"+i+"-tax").val()*amount)/100;
    amount=amount+tax;
    amount=amount-($("#orders-"+i+"-discount").val()*amount)/100;
    $("#orders-"+i+"-amount").val(parseInt(amount));
}
',View::POS_END);

?>