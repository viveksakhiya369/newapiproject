<?php

use common\models\CommonHelpers;
use yii\grid\GridView;
use common\models\City;
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
    $("#city_id").val('.$model->city.').trigger("change");
    $("#state_id").val('.$model->state.').trigger("change");
',View::POS_END);

?>