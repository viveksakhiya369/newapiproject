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
                
                <?=   $this->render('update_form',['model'=>$model]) ?>
</div>
<?php

foreach($model as $i => $val){
    $this->registerJs('
        var old_discount'.$i.'='.$val->discount.';
        var old_amount'.$i.'='.$val->amount.';
        $("#orders-'.$i.'-item_name").prop("disabled",true);
        
        $("#orders-'.$i.'-qty").keyup(function(){
            getCalculate('.$i.')
            getTotalQtyAmt();
        });
        
        $("#orders-'.$i.'-qty").change(function(){
            getCalculate('.$i.')
            getTotalQtyAmt();
        });
        $("#orders-'.$i.'-item_name").val('.$val->item_id.').trigger("change");
        $("#orders-'.$i.'-qty").keyup(function(){
            var rate=$("#orders-'.$i.'-rate").val();
            var quantity=$(this).val();
            var amount=quantity * rate;
            var tax=($("#orders-'.$i.'-tax").val()*amount)/100;
            amount=amount+tax;
            amount=amount-($("#orders-'.$i.'-discount").val()*amount)/100;
            $("#orders-'.$i.'-amount").val(parseInt(amount));
        });
        $("#orders-'.$i.'-qty").change(function(){
            var rate=$("#orders-'.$i.'-rate").val();
            var quantity=$(this).val();
            var amount=quantity * rate;
            var tax=($("#orders-'.$i.'-tax").val()*amount)/100;
            amount=amount+tax;
            amount=amount-($("#orders-'.$i.'-discount").val()*amount)/100;
            $("#orders-'.$i.'-amount").val(parseInt(amount));
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

        })
    ',View::POS_END);


}


?>