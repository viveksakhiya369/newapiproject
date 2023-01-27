
<?php
use common\models\Products;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use kartik\select2\Select2;

$items=Products::find()->where(['status'=>Products::STATUS_ACTIVE])->asArray()->all();
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => max(count($items),100), // the maximum times, an element can be added (default 999)
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
            'barcode',
        ],
    ]); ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
            <?php foreach ($model as $i => $modelAddress): ?>
                <div class="item panel panel-default"><!-- widgetItem -->
                    <div class="panel-heading">
                        <div class="float-right">
                            <button type="button" class="remove-item btn btn-danger btn-sm"><i class="text-20 i-Remove"></i></button>
                        </div>
                        
                    </div>
                    <div class="panel-body">
                        <?php
                            // necessary for update action.
                            if (! $modelAddress->isNewRecord) {
                                echo Html::activeHiddenInput($modelAddress, "[{$i}]id");
                            }
                        ?>
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($modelAddress, "[{$i}]item_id")->dropDownList(ArrayHelper::map(Products::find()->where(['status'=>Products::STATUS_ACTIVE])->all(),'id','item_name'),["prompt"=>"Select Items",'class' => 'form-control select2']); ?>
                                <?= $form->field($modelAddress, "[{$i}]item_name",['template'=>'{input}'])->hiddenInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($modelAddress, "[{$i}]qty")->textInput(['maxlength' => true,'type'=>'number','class'=>'item-qty form-control']) ?>
                                <?= $form->field($modelAddress, "[{$i}]pack",['template'=>'{input}'])->hiddenInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-2">
                            <?= $form->field($modelAddress, "[{$i}]tax",['template'=>'{input}'])->hiddenInput(['maxlength' => true,"readonly"=>true]) ?>
                                <?= $form->field($modelAddress, "[{$i}]barcode",['template'=>'{input}'])->hiddenInput(['maxlength' => true,"readonly"=>true]) ?>
                            </div>
                       
                            <div class="col-sm-2">
                                <?= $form->field($modelAddress, "[{$i}]rate",['template'=>'{input}'])->hiddenInput(['maxlength' => true,"readonly"=>true]) ?>
                            </div>
                            <div class="col-sm-2">
                                <?= $form->field($modelAddress, "[{$i}]amount",['template'=>'{input}'])->hiddenInput(['maxlength' => true,"readonly"=>true,'class'=>'item-amt form-control']) ?>
                            </div>
                            <div class="col-sm-2">
                                <?= $form->field($modelAddress, "[{$i}]discount",['template'=>'{input}'])->hiddenInput(['maxlength' => true,"readonly"=>true]) ?>
                            </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div><!-- .panel -->
    <?php DynamicFormWidget::end(); ?>

    <div class="row">   
        <div class="form-group">
            <?= Html::submitButton($modelAddress->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary']) ?>
            <button type="button" class="add-item btn btn-success btn-sm pull-right"><i class="text-20 i-Add"></i></button>
            <div class="float-right col-md-2">Total Quantity:<input type="number" id="total_qty" class="form-control"></div>
            <!-- <div class="float-right col-md-2">Total Amount:<input type="number" id="total_amt" class="form-control"></div> -->
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
var total_amt=0;
var total_qty=0;
var count=0;
var count_items=1;
$(".dynamicform_wrapper").on("beforeInsert", function(e, item) {
    console.log("beforeInsert");
});

$(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    $(".select2").select2();
    count_items++;
    count++;
    $("#godownstock-"+count+"-qty").blur(function(){
        getTotalQtyAmt()
    })
    for (let i = 0; i <= count; i++) {
        $("#godownstock-"+i+"-item_id").change(function(){
            getalldetails(i,$(this).val());
        });
        
        $("#godownstock-"+i+"-qty").keyup(function(){
            getCalculate(i);
            getTotalQtyAmt();
        })
        $("#godownstock-"+i+"-qty").change(function(){
            getCalculate(i);
            getTotalQtyAmt();
        })
    }

    
});

$(".dynamicform_wrapper").on("beforeDelete", function(e, item) {
    if (! confirm("Are you sure you want to delete this item?")) {
        return false;
    }
    return true;
});

$(".dynamicform_wrapper").on("afterDelete", function(e) {
    $(".select2").select2();
    count_items--;
    count--;
    getTotalQtyAmt();
    for (let i = 0; i <= count; i++) {
        $("#godownstock-"+i+"-item_id").change(function(){
            getalldetails(i,$(this).val());
        });
        
        $("#godownstock-"+i+"-qty").keyup(function(){
            getCalculate(i);
            getTotalQtyAmt();
        })
        $("#godownstock-"+i+"-qty").change(function(){
            getCalculate(i);
            getTotalQtyAmt();
        })
    }
    console.log("Deleted item!");
});

$(".dynamicform_wrapper").on("limitReached", function(e, item) {
    alert("Limit reached");
});

$("#godownstock-0-item_id").change(function(){
    getalldetails(0,$(this).val());
});

$("#godownstock-0-qty").keyup(function(){
    getCalculate(0)
    getTotalQtyAmt();
});

$("#godownstock-0-qty").change(function(){
    getCalculate(0)
    getTotalQtyAmt();
});

function getCalculate(i){
    var rate=$("#godownstock-"+i+"-rate").val();
    var quantity=$("#godownstock-"+i+"-qty").val();
    var amount=quantity * rate;
    var tax=($("#godownstock-"+i+"-tax").val()*amount)/100;
    amount=amount+tax;
    amount=amount-($("#godownstock-"+i+"-discount").val()*amount)/100;
    $("#godownstock-"+i+"-amount").val(parseInt(amount));
}

function getalldetails(count,product_id){
    $.post("'.Url::to(['ajax/get-product-details']).'",{
        product_id:product_id,
    },function(data){
        var response=JSON.parse(data);
        console.log(response);
        $("#godownstock-"+count+"-item_name").val(response.item_name);
        $("#godownstock-"+count+"-pack").val(response.pack);
        $("#godownstock-"+count+"-rate").val(response.current_rate);
        $("#godownstock-"+count+"-tax").val(response.taxName.percentage);
        $("#godownstock-"+count+"-discount").val(response.discount);
        $("#godownstock-"+count+"-barcode").val(response.barcode);
        getCalculate(count);
        getTotalQtyAmt();
    })
}

$("#godownstock-"+count+"-qty").blur(function(){
    getTotalQtyAmt();
});
function getTotalQtyAmt(){
    total_qty=0;
    total_amt=0;
    $(".item-qty").each(function(index,element){
        if($(element).val()==""){
            total_qty=parseInt(total_qty)+0;   
        }else{
            total_qty=parseInt(total_qty)+parseInt($(element).val());   
        }
    })
    $(".item-amt").each(function(index,element){
        if($(element).val()==""){
            total_amt=parseInt(total_amt)+0;   
        }else{
            total_amt=parseInt(total_amt)+parseInt($(element).val());   
        }
    })
    $("#total_qty").val(total_qty);
    $("#total_amt").val(total_amt);
}
$("#total_qty").prop("disabled",true);
$("#total_amt").prop("disabled",true);
',View::POS_END);

?>