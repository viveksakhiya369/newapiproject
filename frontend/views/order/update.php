<?php

use common\models\CommonHelpers;
use yii\grid\GridView;
use common\models\City;
use yii\web\View;

?>
<div class="main-content">
                <div class="breadcrumb">
                    <h1 class="mr-2"><?= CommonHelpers::getTitle($this->context->id,$this->context->action->id)?></h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                    <!-- ICON BG-->
                
                <?=   $this->render('_up_form',['model'=>$model]) ?>
</div>
<?php
$total_qty=0;
$total_amount=0;
foreach($model as $i => $val){
    $total_qty=$total_qty+$val->qty;
    $total_amount=$total_amount+$val->amount;
}
foreach($model as $i => $val){
    $this->registerJs('
        var old_discount'.$i.'='.$val->discount.';
        var old_amount'.$i.'='.$val->amount.';
        $("#total_qty").val('.$total_qty.');
        $("#total_amt").val('.$total_amount.');
        $("#orders-'.$i.'-qty").keyup(function(){
            getCalculate('.$i.')
            getTotalQtyAmt();
        });
        
        $("#orders-'.$i.'-item_id").change(function(){
            getalldetails('.$i.',$(this).val());
            setTimeout(function(){
                getCalculate('.$i.');
                getTotalQtyAmt();
            }, 500);

        });
        $("#orders-'.$i.'-qty").keyup(function(){
            getCalculate('.$i.');
            getTotalQtyAmt();
        });
        $("#orders-'.$i.'-qty").change(function(){
            getCalculate('.$i.');
            getTotalQtyAmt();
        })
        $("#over_dis").keyup(function(){
            if($(this).val()!=""){
                
                $("#orders-'.$i.'-overall_discount").val($(this).val());
                var rate=$("#orders-'.$i.'-rate").val();
                var quantity=$("#orders-'.$i.'-qty").val();
                var amount=quantity * rate;
                var tax=($("#orders-'.$i.'-tax").val()*amount)/100;
                amount=amount+tax;
                amount=amount-($(this).val()*amount)/100;
                $("#orders-'.$i.'-amount").val(parseInt(amount));
                $("#orders-'.$i.'-discount").val($(this).val())
            }else{
                $("#orders-'.$i.'-amount").val('.$val->amount.');
                $("#orders-'.$i.'-discount").val('.$val->discount.');
                $("#orders-'.$i.'-qty").val('.$val->qty.');
            }
            getTotalQtyAmt()

        })
        $("#over_dis").change(function(){
            if($(this).val()!=""){
                
                $("#orders-'.$i.'-overall_discount").val($(this).val());
                var rate=$("#orders-'.$i.'-rate").val();
                var quantity=$("#orders-'.$i.'-qty").val();
                var amount=quantity * rate;
                var tax=($("#orders-'.$i.'-tax").val()*amount)/100;
                amount=amount+tax;
                amount=amount-($(this).val()*amount)/100;
                $("#orders-'.$i.'-amount").val(parseInt(amount));
                $("#orders-'.$i.'-discount").val($(this).val())
            }else{
                $("#orders-'.$i.'-amount").val('.$val->amount.');
                $("#orders-'.$i.'-discount").val('.$val->discount.');
                $("#orders-'.$i.'-qty").val('.$val->qty.');
            }
            getTotalQtyAmt()

        })
    ',View::POS_END);


}

$this->registerJs('
    // $(".remove-item").hide();
    // $(".add-item").hide();
')
?>