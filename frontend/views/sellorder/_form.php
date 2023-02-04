
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

    <?php if(Yii::$app->request->get('sell_order') == 1 ) { ?>
        <div class="row">
            <div class="col-sm-2 mb-3">
                <input type="text" class="form-control" name="karigar_mob_num" id="karigar_mob_num" placeholder="Enter karigar mobile number">
                Karigar Name: <span id="karigar_name"></span>
                
            </div>
            <div class="col-sm-2 mb-3">
                <button class="btn btn-primary" id="karigar_submit">Search</button>
            </div>
        </div>
    <?php  } ?>
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
        ],
    ]); ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
            <input type="hidden" name="karigar_parent_id" id="karigar_parent_id">
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
                            <div class="col-sm-2">
                                <?= $form->field($modelAddress, "[{$i}]item_id")->dropDownList(ArrayHelper::map(Products::find()->where(['status'=>Products::STATUS_ACTIVE])->all(),'id','item_name'),["prompt"=>"Select Items",'class' => 'select2 form-control']); ?>
                                <?= $form->field($modelAddress, "[{$i}]item_name",['template'=>'{input}'])->hiddenInput(['maxlength' => true]) ?>           
                            </div>
                            <div class="col-sm-2">
                            <?= $form->field($modelAddress, "[{$i}]pack")->textInput(['maxlength' => true,'readonly'=>true]) ?>
                            </div>
                            <div class="col-sm-1">
                                <?= $form->field($modelAddress, "[{$i}]total_pack")->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-1">
                            <?= $form->field($modelAddress, "[{$i}]qty")->textInput(['maxlength' => true,'type'=>'number','class'=>'item-qty form-control']) ?>
                            <?= $form->field($modelAddress, "[{$i}]tax",['template'=>'{input}'])->hiddenInput(['maxlength' => true,"readonly"=>true]) ?>
                            </div>
                            <!-- <div class="col-sm-1">
                            </div> -->
                       
                            <div class="col-sm-2">
                                <?= $form->field($modelAddress, "[{$i}]rate")->textInput(['maxlength' => true,"readonly"=>true]) ?>
                            </div>
                            <div class="col-sm-2">
                                <?= $form->field($modelAddress, "[{$i}]amount")->textInput(['maxlength' => true,"readonly"=>true,'class'=>'item-amt form-control']) ?>
                            </div>
                            <div class="col-sm-1">
                                <?= $form->field($modelAddress, "[{$i}]discount")->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-1">
                                <?= $form->field($modelAddress, "[{$i}]total_points")->textInput(['maxlength' => true,"readonly"=>true,'class'=>'item-point form-control']) ?>
                                <?= $form->field($modelAddress, "[{$i}]point",['template'=>'{input}'])->hiddenInput(['maxlength' => true,"readonly"=>true]) ?>
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
            <div class="float-right col-md-2">Total Amount:<input type="number" id="total_amt" class="form-control"></div>
            <!-- <div class="float-right col-md-2">Total Point:<input type="number" id="total_point" class="form-control"></div> -->
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
var total_amt=0;
var total_qty=0;
var total_point=0;
var count=0;
var count_items=1;
$(".dynamicform_wrapper").on("beforeInsert", function(e, item) {
    console.log("beforeInsert");
});

$(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    $(".select2").select2({
        width: "100%"
    });
    count_items++;
    count++;
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
            getTotalFromQty(i)
            getCalculate(i);
            getTotalQtyAmt();
        })
        $("#orders-"+i+"-qty").change(function(){
            getTotalFromQty(i)
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

    
});

$(".dynamicform_wrapper").on("beforeDelete", function(e, item) {
    if (! confirm("Are you sure you want to delete this item?")) {
        return false;
    }
    return true;
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
            getTotalFromQty(i)
            getCalculate(i);
            getTotalQtyAmt();
        })
        $("#orders-"+i+"-qty").change(function(){
            getTotalFromQty(i)
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

$("#orders-0-item_id").change(function(){
    getalldetails(0,$(this).val())
});

$("#orders-0-qty").keyup(function(){
    getTotalFromQty(0)
    getCalculate(0)
    getTotalQtyAmt();
});

$("#orders-0-qty").change(function(){
    getTotalFromQty(0)
    getCalculate(0)
    getTotalQtyAmt();
});
$("#orders-0-discount").change(function(){
    getQtyfromTotalItems(0);
    getCalculate(0);
    getTotalQtyAmt();
});
$("#orders-0-discount").keyup(function(){
    getQtyfromTotalItems(0);
    getCalculate(0);
    getTotalQtyAmt();
});

$("#orders-0-total_pack").keyup(function(){
    getQtyfromTotalItems(0);
    getCalculate(0)
    getTotalQtyAmt();
});

$("#orders-0-total_pack").change(function(){
    getQtyfromTotalItems(0);
    getCalculate(0)
    getTotalQtyAmt();
});

function getCalculate(i){
    var rate=$("#orders-"+i+"-rate").val();
    var quantity=$("#orders-"+i+"-qty").val();
    var amount=quantity * rate;
    var tax=($("#orders-"+i+"-tax").val()*amount)/100;
    // amount=amount+tax;
    amount=amount-($("#orders-"+i+"-discount").val()*amount)/100;
    $("#orders-"+i+"-amount").val(parseInt(amount));
    $("#orders-"+i+"-total_points").val(($("#orders-"+i+"-point").val())*quantity);

}
function getTotalFromQty(n){
    $("#orders-"+n+"-total_pack").val(($("#orders-"+n+"-qty").val())/($("#orders-"+n+"-pack").val()));
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
        $("#orders-"+count+"-point").val(response.point);
        getCalculate(count)
        getTotalQtyAmt();
    })
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
    $("#total_qty").val(total_qty);
    $("#total_amt").val(total_amt);
    $("#total_point").val(total_point);
}
$("#total_qty").prop("disabled",true);
$("#total_amt").prop("disabled",true);
$("#total_point").prop("disabled",true);
',View::POS_END);

?>