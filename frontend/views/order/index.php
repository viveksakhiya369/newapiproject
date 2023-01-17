<?php

use common\models\City;
use common\models\CommonHelpers;
use common\models\Distributor;
use common\models\Orders;
use common\models\States;
use common\models\Transport;
use common\models\User;
use yii\bootstrap5\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

// echo'<pre>';print_r($dataProvider->getModels());exit();
// echo'<pre>';print_r($this->context->action->id);exit();
// echo'<pre>';print_r($this->context->id);exit();
?>
<div class="main-content">
    <div class="breadcrumb">
        <h1 class="mr-2"><?= CommonHelpers::getTitle($this->context->id, $this->context->action->id) ?></h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>
    <!-- ICON BG-->
    <?php echo $this->render('_search', ['searchmodel' => $searchmodel]) ?>
    <div class="row">
        <?php
        
        Modal::begin([
            'title' => '<h3>Transport Information</h3>',
            'id' => 'modal-transport',
            'size' => 'modal-lg',

        ]);

        echo "<div id='transport-content'></div>";

        Modal::end();
        

        ?>
        <?php
        Pjax::begin();
        echo GridView::widget([
            'dataProvider' => $searchdata,
            'options' => [
                'class' => 'card-body table table-striped table-bordered table-responsive'
            ],
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'header' => 'sr.no'
                ],
                [
                    'attribute' => 'order_no',
                    'format' => 'html',
                    'label' => 'Order No',
                    'value' => function ($data) {
                        return $data->order_no;
                    }
                ],
                [
                    'attribute' => 'created_dt',
                    'format' => 'html',
                    'label' => 'Created Date',
                    'value' => function ($data) {
                        return date('d-m-Y', strtotime($data->created_dt));
                    }
                ],
                [
                    'attribute' => 'dealer_name',
                    'format' => 'html',
                    'label' => 'Dealer Name',
                    // 'visible'=>function($data){
                    //     return (User::findOne( $data->orders->parent_id)->role_id==User::DEALER);
                    // },
                    'value' => function ($data) {
                        return isset($data->dealer->dealer_name) ? $data->dealer->dealer_name : "-";
                    }
                ],
                [
                    'attribute' => 'status',
                    'format' => 'html',
                    'label' => 'Status',
                    'value' => function ($data) {
                        if ($data->status == Orders::STATUS_APPROVED) {

                            return '<a class="badge badge-success m-2" href="#">' . Orders::STATUS_APPROVED_LABEL . '</a>';
                        } else if ($data->status == Orders::STATUS_QUEUED) {
                            return '<a class="badge badge-danger m-2" href="#">' . Orders::STATUS_QUEUED_LABEL . '</a>';
                        }
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => "Action",
                    'template' => ' {view} {update} {transport} ',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<i class="text-20 i-Eye"></i>', Url::to(['order/view', 'order_no' => $model->order_no]), [
                                'title' => 'view',
                            ]);
                        },
                        'update' => function ($url, $model, $key) {
                            if ((Yii::$app->request->get('receieved')) && in_array(Yii::$app->user->identity->role_id, [User::DISTRIBUTOR, User::SUPER_ADMIN])) {

                                return Html::a('<i class="text-20 i-Pen-3"></i>', Url::to(['order/update', 'order_no' => $model->order_no]), [
                                    'title' => 'update',
                                ]);
                            }
                        },
                        'transport' => function ($url, $model, $key) {
                            if ((Yii::$app->request->get('receieved')) && in_array(Yii::$app->user->identity->role_id, [User::DISTRIBUTOR, User::SUPER_ADMIN])) {
                                $transport=Transport::find()->where(['order_no'=>$model->order_no])->one();
                                if(isset($transport)){
                                    return Html::tag('span', '<i class="text-20 i-Pen-3"></i>', ['class' => 'transport-show','value'=>Url::to(['transport/update','order_no' => $model->order_no]), 'style' => 'color : blue;']);
                                }else{
                                    return Html::tag('span', '<i class="text-20 i-Pen-3"></i>', ['class' => 'transport-show','value'=>Url::to(['transport/create','order_no' => $model->order_no]), 'style' => 'color : blue;']);
                                }
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