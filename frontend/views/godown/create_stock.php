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
                
                <?=   $this->render('_stock_form',['model'=>$model]) ?>
</div>
<?php
$this->registerJs('
    // $(".add-item").hide();
    // $(".remove-item").hide();
    // setTimeout(function(){
    //     $(".select2").select2(
    //         {
    //             dropdownParent:document.getElementById("modal-transport")
    //         }
    //     );
    // }, 100);
',View::POS_END);

// foreach($model as $i => $val){
//     $this->registerJs('
//         getalldetails('.$i.','.$val->item_id.');
//         $("#orders-'.$i.'-qty").keyup(function(){
//             getCalculate('.$i.');
//         });
//         $("#orders-'.$i.'-qty").change(function(){
//             getCalculate('.$i.');
//         })
//     ',View::POS_END);
// }
?>