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
                
                <?=   $this->render('_form',['model'=>$model]) ?>
</div>
<?php

foreach($model as $i => $val){
    $this->registerJs('
        $("#orders-'.$i.'-qty").keyup(function(){
            var rate=$("#orders-'.$i.'-rate").val();
            var quantity=$(this).val();
            var amount=quantity * rate;
            var tax=($("#orders-'.$i.'-tax").val()*amount)/100;
            amount=amount+tax;
            $("#orders-'.$i.'-amount").val(amount);
        });
        $("#orders-'.$i.'-qty").change(function(){
            var rate=$("#orders-'.$i.'-rate").val();
            var quantity=$(this).val();
            var amount=quantity * rate;
            var tax=($("#orders-'.$i.'-tax").val()*amount)/100;
            amount=amount+tax;
            $("#orders-'.$i.'-amount").val(amount);
        })
    ',View::POS_END);
}

// $this->registerJs('
//    $(".add-item").hide();
//    $(".remove-item").hide();
// ',View::POS_END);

?>