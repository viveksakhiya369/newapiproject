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
        $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <!-- <h4><i class="glyphicon glyphicon-envelope"></i> Addresses</h4> -->
            </div>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 100, // the maximum times, an element can be cloned (default 999)
                    'min' => 0, // 0 or 1 (default 1)
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
                                    <div class="col-sm-2">
                                        <?= $form->field($modelAddress, "[{$i}]item_id")->dropDownList(ArrayHelper::map(Products::find()->where(['status' => Products::STATUS_ACTIVE])->all(), 'id', 'item_name'), ["prompt" => "Select Items", 'class' => 'form-control select2']); ?>
                                        <?= $form->field($modelAddress, "[{$i}]item_name", ['template' => '{input}'])->hiddenInput(['maxlength' => true]) ?>
                                        <?= $form->field($modelAddress, "[{$i}]barcode", ['template' => '{input}'])->hiddenInput(['maxlength' => true]) ?>
                                    </div>
                                    <div class="col-sm-1">
                                        <?= $form->field($modelAddress, "[{$i}]pack")->textInput(['maxlength' => true,'readonly'=>true]) ?>
                                    </div>
                                    <div class="col-sm-1">
                                        <?= $form->field($modelAddress, "[{$i}]total_pack")->textInput(['maxlength' => true,'type' => 'number','min'=>1]) ?>
                                    </div>
                                    <div class="col-sm-2">
                                        <?= $form->field($modelAddress, "[{$i}]qty")->textInput(['maxlength' => true, 'type' => 'number', 'class' => 'item-qty form-control' ,'min'=>1]) ?>

                                        <?= $form->field($modelAddress, "[{$i}]tax", ['template' => '{input}'])->hiddenInput(['maxlength' => true, "readonly" => true]) ?>
                                    </div>
                                    <!-- <div class="col-sm-1">
                                    </div> -->
                                    <div class="col-sm-2">
                                        <?= $form->field($modelAddress, "[{$i}]rate")->textInput(['maxlength' => true, "readonly" => true]) ?>
                                    </div>
                                    <div class="col-sm-2">
                                        <?= $form->field($modelAddress, "[{$i}]amount")->textInput(['maxlength' => true, "readonly" => true, 'class' => 'item-amt form-control']) ?>
                                    </div>
                                    <div class="col-sm-1">
                                        <?= $form->field($modelAddress, "[{$i}]discount")->textInput(['maxlength' => true]) ?>
                                    </div>
                                    <div class="col-sm-1">
                                        <?= $form->field($modelAddress, "[{$i}]total_points")->textInput(['maxlength' => true,"readonly"=>true,'class'=>'item-point form-control']) ?>
                                        <?= $form->field($modelAddress, "[{$i}]point",['template'=>'{input}'])->hiddenInput(['maxlength' => true,"readonly"=>true]) ?>
                                    </div>
                                </div><!-- .row -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="form-group">
                    <?= Html::submitButton($modelAddress->isNewRecord ? 'Create' : 'Approve Order', ['class' => 'btn btn-primary']) ?>
                    <button type="button" class="add-item btn btn-success btn-sm pull-right"><i class="text-20 i-Add"></i></button>
                    <div class="float-right col-md-2">Overall Discount:<input type="number" id="over_dis" class="form-control"></div>
                    <div class="float-right col-md-2">Total Quantity:<input type="number" id="total_qty" class="form-control"></div>
                    <div class="float-right col-md-2">Total Amount:<input type="number" id="total_amt" class="form-control"></div>
            <!-- <div class="float-right col-md-2">Total Point:<input type="number" id="total_point" class="form-control"></div> -->
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>


        <?php ActiveForm::end(); ?>

    </div>
    <?php
$this->registerJs('
var total_amt=0;
var total_qty=0;
var total_point=0;
var count='.(count($model)-1).';
var count_items='.count($model).';
var initial_count='.(count($model)).'
$(".dynamicform_wrapper").on("beforeInsert", function(e, item) {
    console.log("beforeInsert");
});

$(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    $(".select2").select2({
        width: "100%"
    });
    initial_count++;
    count_items++;
    count++;
    
    getCleanObj(count);
    $("#orders-"+count+"-qty").blur(function(){
        getCalculate(i);
        getTotalQtyAmt()
    })
    for (let i = 0; i <= count; i++) {
        $("#orders-"+i+"-item_id").change(function(){
            getalldetails(i,$(this).val());
            getCalculate(i);
            getTotalQtyAmt();
        });
        
        $("#orders-"+i+"-qty").keyup(function(){
            getTotalFromQty(i);
            getCalculate(i);
            getTotalQtyAmt();
        })
        $("#orders-"+i+"-qty").change(function(){
            getTotalFromQty(i);
            getCalculate(i);
            getTotalQtyAmt();
        });
        $("#orders-"+i+"-total_pack").keyup(function(){
            getQtyfromTotalItems(i);
            getCalculate(i);
            getTotalQtyAmt();
        })
        $("#orders-"+i+"-total_pack").change(function(){
            getQtyfromTotalItems(i);
            getCalculate(i);
            getTotalQtyAmt();
        });
        $("#orders-"+i+"-discount").change(function(){
            getQtyfromTotalItems(i);
            getCalculate(i);
            getTotalQtyAmt();
        });
        $("#orders-"+i+"-discount").keyup(function(){
            getQtyfromTotalItems(i);
            getCalculate(i);
            getTotalQtyAmt();
        });
        $("#over_dis").keyup(function(){
            if($(this).val()!=""){
                
                $("#orders-"+i+"-overall_discount").val($(this).val());
                var rate=$("#orders-"+i+"-rate").val();
                var quantity=$("#orders-"+i+"-qty").val();
                var amount=quantity * rate;
                var tax=($("#orders-"+i+"-tax").val()*amount)/100;
                // amount=amount+tax;
                amount=amount-($(this).val()*amount)/100;
                $("#orders-"+i+"-amount").val(parseInt(amount));
                $("#orders-"+i+"-discount").val($(this).val())
            }
            getTotalQtyAmt()

        })
        $("#over_dis").change(function(){
            if($(this).val()!=""){
                
                $("#orders-"+i+"-overall_discount").val($(this).val());
                var rate=$("#orders-"+i+"-rate").val();
                var quantity=$("#orders-"+i+"-qty").val();
                var amount=quantity * rate;
                var tax=($("#orders-"+i+"-tax").val()*amount)/100;
                // amount=amount+tax;
                amount=amount-($(this).val()*amount)/100;
                $("#orders-"+i+"-amount").val(parseInt(amount));
                $("#orders-"+i+"-discount").val($(this).val())
            }
            getTotalQtyAmt()

        })
    }
    
});

$(".dynamicform_wrapper").on("beforeDelete", function(e, item) {
   
});

$(".dynamicform_wrapper").on("afterDelete", function(e) {
    $(".select2").select2({
        width: "100%"
    });
    count_items--;
    count--;
    getTotalQtyAmt();
    for (let i = 0; i <= count; i++) {
        $("#orders-"+i+"-item_id").change(function(){
            getalldetails(i,$(this).val());
            getTotalQtyAmt();
        });
        
        $("#orders-"+i+"-qty").keyup(function(){
            getTotalFromQty(i);
            getCalculate(i);
            getTotalQtyAmt();
        })
        $("#orders-"+i+"-qty").change(function(){
            getTotalFromQty(i);
            getCalculate(i);
            getTotalQtyAmt();
        })
        $("#orders-"+i+"-discount").change(function(){
            getQtyfromTotalItems(i);
            getCalculate(i);
            getTotalQtyAmt();
        });
        $("#orders-"+i+"-discount").keyup(function(){
            getQtyfromTotalItems(i);
            getCalculate(i);
            getTotalQtyAmt();
        });

        $("#orders-"+i+"-total_pack").keyup(function(){
            getQtyfromTotalItems(i);
            getCalculate(i);
            getTotalQtyAmt();
        })
        $("#orders-"+i+"-total_pack").change(function(){
            getQtyfromTotalItems(i);
            getCalculate(i);
            getTotalQtyAmt();
        })

    }
    console.log("Deleted item!");
});

$(".dynamicform_wrapper").on("limitReached", function(e, item) {
    alert("Limit reached");
});


function getCalculate(i){
    var rate=$("#orders-"+i+"-rate").val();
    var quantity=$("#orders-"+i+"-qty").val();
    var amount=quantity * rate;
    var tax=($("#orders-"+i+"-tax").val()*amount)/100;
    // amount=amount+tax;
    if(($("#over_dis").val()!=0) && ($("#over_dis").val()!="")){
        amount=amount-($("#over_dis").val()*amount)/100;
    }else{
        amount=amount-($("#orders-"+i+"-discount").val()*amount)/100;
    }
    $("#orders-"+i+"-amount").val(parseInt(amount));
    $("#orders-"+i+"-total_points").val(($("#orders-"+i+"-point").val())*quantity);
}

function getalldetails(count,product_id){
    $.post("'.Url::to(['ajax/get-product-details-update']).'",{
        product_id:product_id,
    },function(data){
        var response=JSON.parse(data);
        console.log(response);
        $("#orders-"+count+"-item_name").val(response.item_name);
        $("#orders-"+count+"-pack").val(response.pack);
        $("#orders-"+count+"-rate").val(response.current_rate);
        $("#orders-"+count+"-tax").val(response.taxName.percentage);
        $("#orders-"+count+"-discount").val(response.discount);
        $("#orders-"+count+"-barcode").val(response.barcode);
        $("#orders-"+count+"-point").val(response.point);
        getCalculate(count)
        getTotalQtyAmt();
    })
}

function getTotalFromQty(n){
    $("#orders-"+n+"-total_pack").val(($("#orders-"+n+"-qty").val())/($("#orders-"+n+"-pack").val()));
}

function getQtyfromTotalItems(n){
    $("#orders-"+n+"-qty").val($("#orders-"+n+"-total_pack").val()*$("#orders-"+n+"-pack").val());
}

$("#orders-"+count+"-qty").blur(function(){
    getTotalQtyAmt();
});
function getTotalQtyAmt(){
    total_qty=0;
    total_amt=0;
    total_point=0;
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
    $(".item-point").each(function(index,element){
        if($(element).val()==""){
            total_point=parseInt(total_point)+0;   
        }else{
            total_point=parseInt(total_point)+parseInt($(element).val());   
        }
    })
    // new Intl.NumberFormat("en-IN").format(total_qty)
    $("#total_qty").val(total_qty);
    $("#total_amt").val(total_amt);
    $("#total_point").val(total_point);
}
$("#total_qty").prop("disabled",true);
$("#total_amt").prop("disabled",true);
$("#total_point").prop("disabled",true);
function getCleanObj(i){
        $("#orders-"+i+"-item_id").val("").trigger("change");
        $("#orders-"+i+"-item_name").val("");
        $("#orders-"+i+"-pack").val("");
        $("#orders-"+i+"-rate").val("");
        $("#orders-"+i+"-tax").val("");
        $("#orders-"+i+"-discount").val("");
        $("#orders-"+i+"-barcode").val("");
        $("#orders-"+i+"-point").val("");
        $("#orders-"+i+"-total_pack").val("");
        $("#orders-"+i+"-qty").val("");
        $("#orders-"+i+"-amount").val("");
        $("#orders-"+i+"-total_points").val("");
}
',View::POS_END);

?>
