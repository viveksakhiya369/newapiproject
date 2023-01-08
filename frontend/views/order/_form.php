
<?php

use common\models\Products;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;

?>

<div class="customer-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 10, // the maximum times, an element can be added (default 999)
        'min' => 1, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $model[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'item_id',
            'item_name',
            'qty',
            'pack',
            'rate',
            'amount',
            'status',
        ],
    ]); ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-envelope"></i> Orders
                <button type="button" class="add-item btn btn-success btn-sm pull-right"><i class="text-20 i-Add"></i></button>
            </h4>
        </div>
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
            <?php foreach ($model as $i => $modelAddress): ?>
                <div class="item panel panel-default"><!-- widgetItem -->
                    <div class="panel-heading">
                        <h3 class="panel-title float-left">Order</h3>
                        <div class="float-right">
                            <button type="button" class="remove-item btn btn-danger btn-sm"><i class="text-20 i-Remove"></i></button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <?php
                            // necessary for update action.
                            if (! $modelAddress->isNewRecord) {
                                echo Html::activeHiddenInput($modelAddress, "[{$i}]id");
                            }
                        ?>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= $form->field($modelAddress, "[{$i}]item_id")->dropDownList(ArrayHelper::map(Products::find()->where(['status'=>Products::STATUS_ACTIVE])->asArray()->all(),'id','item_name'),['prompt'=>"Select Products","class"=>"form-control select2"]) ?>
                            </div>
                            <div class="col-sm-4">
                                <?= $form->field($modelAddress, "[{$i}]item_name")->textInput(['maxlength' => true,"readonly"=>true]) ?>
                            </div>
                            <div class="col-sm-4">
                                <?= $form->field($modelAddress, "[{$i}]qty")->textInput(['maxlength' => true,'type'=>'number']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <?= $form->field($modelAddress, "[{$i}]pack")->textInput(['maxlength' => true,"readonly"=>true]) ?>
                            </div>
                            <div class="col-sm-4">
                                <?= $form->field($modelAddress, "[{$i}]rate")->textInput(['maxlength' => true,"readonly"=>true]) ?>
                            </div>
                            <div class="col-sm-4">
                                <?= $form->field($modelAddress, "[{$i}]amount")->textInput(['maxlength' => true,"readonly"=>true]) ?>
                            </div>
                        </div><!-- .row -->
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div><!-- .panel -->
    <?php DynamicFormWidget::end(); ?>

    <div class="form-group">
        <?= Html::submitButton($modelAddress->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

$this->registerJs('
var count=0;
$(".dynamicform_wrapper").on("beforeInsert", function(e, item) {
    console.log("beforeInsert");
});

$(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    $(".select2").select2();
    count++;
    $("#orders-"+count+"-item_id").change(function(){
        getalldetails(count,$(this).val());
    });
    $("#orders-"+count+"-qty").keyup(function(){
        var rate=$("#orders-"+count+"-rate").val();
        var quantity=$(this).val();
        var amount=quantity * rate;
        $("#orders-"+count+"-amount").val(amount);
    })
    console.log(count);
    console.log("afterInsert");
});

$(".dynamicform_wrapper").on("beforeDelete", function(e, item) {
    if (! confirm("Are you sure you want to delete this item?")) {
        return false;
    }
    return true;
});

$(".dynamicform_wrapper").on("afterDelete", function(e) {
    console.log("Deleted item!");
});

$(".dynamicform_wrapper").on("limitReached", function(e, item) {
    alert("Limit reached");
});

$("#orders-0-item_id").change(function(){
    getalldetails(0,$(this).val())
});

$("#orders-0-qty").keyup(function(){
    var rate=$("#orders-0-rate").val();
    var quantity=$(this).val();
    var amount=quantity * rate;
    $("#orders-0-amount").val(amount);
});

function getalldetails(count,product_id){
    $.post("'.Url::to(['ajax/get-product-details']).'",{
        product_id:product_id,
    },function(data){
        var response=JSON.parse(data);
        $("#orders-"+count+"-item_name").val(response.item_name);
        $("#orders-"+count+"-pack").val(response.pack);
        $("#orders-"+count+"-rate").val(response.mrp);
    })
}

',View::POS_END);

?>