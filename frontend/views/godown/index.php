<?php

use common\models\City;
use common\models\CommonHelpers;
use common\models\Distributor;
use common\models\Orders;
use common\models\States;
use common\models\Transport;
use common\models\User;
use yii\bootstrap5\Modal;
// use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;
use kartik\grid\GridView;

// echo'<pre>';print_r($dataProvider->getModels());exit();
// echo'<pre>';print_r($this->context->action->id);exit();
// echo'<pre>';print_r($this->context->id);exit();
?>
<div class="main-content">
    <div class="breadcrumb">
        <h1 class="mr-2"><?= CommonHelpers::getTitle($this->context->id, $this->context->action->id) ?></h1>
        <!-- <h1 class="mr-2">Manage Pending Orders</h1> -->
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <!-- ICON BG-->
    <?php echo $this->render('_search', ['searchmodel' => $searchmodel]) ?>
    <div class="row">
        <?php
        
        Modal::begin([
            'title' => '<h3>Add items in shop</h3>',
            'id' => 'modal-transport',
            'size' => 'modal-lg',

        ]);

        echo "<div id='transport-content'></div>";

        Modal::end();
        

        ?>
        <?php
        Pjax::begin();
        echo GridView::widget([
            'dataProvider' => $data,
            'layout' => "{items}\n<div class='float-left'>{summary}</div>\n<div class='float-right'>{pager}</div>",
            'columns' => [
                [
                    'class' => 'kartik\grid\SerialColumn',
                    'header' => 'sr.no'
                ],
                [
                    'attribute' => 'item_name',
                    'format' => 'html',
                    'label' => 'Item Name',
                    'value' => function ($data) {
                        return $data->item_name;
                    }
                ],
                [
                    'attribute' => 'qty',
                    'format' => 'html',
                    'label' => 'Quantity',
                    'value' => function ($data) {
                        return $data->total_qty;
                    }
                ],
                [
                    'attribute' => 'created_dt',
                    'format' => 'html',
                    'label' => 'Created Date',
                    'value' => function ($data) {
                        return date('d-m-Y', strtotime($data->latest_created_dt));
                    }
                ],
                // [
                //     'attribute' => 'status',
                //     'format' => 'html',
                //     'label' => 'Status',
                //     'value' => function ($data) {
                //         // if ($data->status == Orders::STATUS_APPROVED) {

                //         //     return '<a class="badge badge-success m-2" href="#">' . Orders::STATUS_APPROVED_LABEL . '</a>';
                //         // } else if ($data->status == Orders::STATUS_QUEUED) {
                //         //     return '<a class="badge badge-danger m-2" href="#">' . Orders::STATUS_QUEUED_LABEL . '</a>';
                //         // }
                //         return '<a class="badge badge-warning m-2" href="#">Pending</a>';
                //     }
                // ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'header' => "Action",
                    'template' => '{delete} ',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<i class="text-20 i-Eye"></i>', Url::to(['pendingorder/view', 'order_no' => $model->order_no]), [
                                'title' => 'view',
                            ]);
                        },
                        'delete' => function($url,$model,$key){
                            if($model->status!=Orders::STATUS_APPROVED){
                                return Html::a('<i class="text-20 i-Delete-File"></i>',Url::to(['pendingorder/delete','order_no'=>$model->order_no]),[
                                    'title'=>'delete',
                                    'data'=>[
                                        'confirm'=>'Delete confirm?',
                                        'method'=>'post',
                                    ]
                                ]);
                            }
                        },
                        'reorder' => function($url,$model,$key){
                            if(!in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN])){
                                // return Html::a('<i class="text-20 i-Restore-Window"></i>',Url::to(['godown/submit-to-shop','item_id'=>$model->item_id]),[
                                //     'title'=>'move to shop',
                                //     'data'=>[
                                //         //'confirm'=>'Resubmit this order confirm?',
                                //         'method'=>'post',
                                //     ],
                                //     'class'=>'transport-show',
                                //     'disabled',
                                // ]);
                                return Html::tag('span', '<i class="text-20 i-Restore-Window"></i>', ['class' => 'transport-show', 'value' => Url::to(['godown/submit-to-shop','item_id'=>$model->item_id]), 'style' => 'color : blue;']);
                            }
                        }
                    ]
                ],
            ]
        ]);
        Pjax::end();
        ?>
    </div>

</div>
<?php

$this->registerJs('
$(document).ready(function(){
    $(".transport-show").click(function(){
        console.log($(this).attr("value"));
        $("#modal-transport").modal("show").find("#transport-content").load($(this).attr("value"));
    })
})
',View::POS_END);

?>