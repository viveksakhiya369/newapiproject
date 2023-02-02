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
if(!(Yii::$app->request->get('receieved'))){
    $this->registerJs('
    
    $("#transport-driver_name").prop("disabled",true);
    $("#transport-vehicle_number").prop("disabled",true);
    $("#transport-transpotation_id").prop("disabled",true);
        
',View::POS_END);
}


?>