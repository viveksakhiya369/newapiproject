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
                <?=   $this->render('_form',['model'=>$model]) ?>
                
</div>
<?php
    $this->registerJs('
    $(document).ready(function(){

        $("#dynamic-form").hide();
        $("#karigar_submit").click(function(){
            $.post("'.Url::to(['ajax/get-karigar-from-mobile']).'",{
                mobile_num:$("#karigar_mob_num").val(),
            },function(data,status){
                if(data!=""){
                    var response=JSON.parse(data);
                    $("#karigar_name").html(response.name);
                    $("#karigar_parent_id").val(response.parent_id);
                    $("#dynamic-form").show();
                }else{
                    toastr["error"]("Karigar can not be found");
                    // alert("karigar can not be found!");
                }
                console.log(response);
            });
        }); 
       
    });
    ',View::POS_END);
?>