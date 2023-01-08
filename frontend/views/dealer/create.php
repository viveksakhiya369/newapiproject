<?php
use yii\helpers\Url;
use common\models\CommonHelpers;
use yii\grid\GridView;
use yii\web\View;

// echo'<pre>';print_r($dataProvider->getModels());exit();
// echo'<pre>';print_r($this->context->action->id);exit();
// echo'<pre>';print_r($this->context->id);exit();
?>
<div class="main-content">
                <div class="breadcrumb">
                    <h1 class="mr-2"><?= CommonHelpers::getTitle($this->context->id,$this->context->action->id)?></h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                    <!-- ICON BG-->
                
                <?=   $this->render('_form',['model'=>$model,'usermodel'=>$usermodel]) ?>
</div>
<?php

$this->registerJs('
    if($("#state_id").val()!=""){
        getCityDropDown();
    }
    $("#state_id").change(function(){
        getCityDropDown();
    });
    function getCityDropDown(){
        $.post("'.Url::toRoute(['ajax/get-city-name']).'",{
            state_id:$("#state_id").val(),
        },
        function(data){
            $("#city_id").html(data);
        })
    }
        
',View::POS_END);

?>