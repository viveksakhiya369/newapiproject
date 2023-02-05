<?php
use common\models\City;
use common\models\CommonHelpers;
use common\models\Distributor;
use common\models\GodownStock;
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
use kartik\grid\GridViewAsset;

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
            // 'title' => '<h3>Transport Information</h3>',
            'id' => 'modal-transport',
            'size' => 'modal-lg',

        ]);

        echo "<div id='transport-content'></div>";

        Modal::end();
        

        ?>
        <?php
        Pjax::begin();
        if((Yii::$app->user->identity->role_id==User::DISTRIBUTOR) && (Yii::$app->request->get('receieved'))){

            echo GridView::widget([
                'dataProvider' => $searchdata,
                'responsiveWrap' => false,
                'layout' => "{items}\n<div class='float-left'>{summary}</div>\n<div class='float-right'>{pager}</div>",
                'columns' => [
                    [
                        'class' => 'kartik\grid\SerialColumn',
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
                        'attribute' => 'total_discount',
                        'format' => 'html',
                        'label' => 'Total Discount',
                        'value' => function ($data) {
                            return $data->all_discount;
                        }
                    ],
                    [
                        'attribute' => 'total_amount',
                        'format' => 'html',
                        'label' => 'Total Amount',
                        'value' => function ($data) {
                            return $data->all_amount;
                        }
                    ],
                    [
                        'attribute' => 'total_points',
                        'format' => 'html',
                        'label' => 'Total Points',
                        'value' => function ($data) {
                            return $data->total_points;
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
                    'attribute' => 'status',
                    'format' => 'html',
                    'label' => 'Status',
                    'value' => function ($data) {
                        if ($data->status == Orders::STATUS_APPROVED) {  
                            return '<a class="badge badge-success m-2" href="#">' . Orders::STATUS_APPROVED_LABEL . '</a>';
                        } else if ($data->status == Orders::STATUS_QUEUED) {
                            return '<a class="badge badge-danger m-2" href="#">' . Orders::STATUS_QUEUED_LABEL . '</a>';
                        }else if( $data->status == Orders::STATUS_INPROGRESS){
                            return '<a class="badge badge-danger m-2" href="#">' . Orders::STATUS_INPROGRESS_LABEL . '</a>';
                        }
                    }
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'header' => "Action",
                    'template' => ' {view} {update} {transport} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<i class="text-20 i-Eye"></i>', Url::to(['order/view', 'order_no' => $model->order_no]), [
                                'title' => 'view',
                            ]);
                        },
                        'update' => function ($url, $model, $key) {
                            if (Yii::$app->request->get('receieved') && in_array(Yii::$app->user->identity->role_id, [User::DISTRIBUTOR, User::SUPER_ADMIN]) && ($model->status!=Orders::STATUS_APPROVED)) {
                                
                                return Html::a('<i class="text-20 i-Pen-3"></i>', (Yii::$app->request->get('receieved')) ? Url::to(['order/update', 'order_no' => $model->order_no]) : Url::to(['order/send-update','order_no'=>$model->order_no]), [
                                    'title' => 'update',
                                ]);
                            }
                        },
                        'transport' => function ($url, $model, $key) {
                            if ( in_array(Yii::$app->user->identity->role_id, [User::DISTRIBUTOR, User::SUPER_ADMIN])) {
                                $transport=Transport::find()->where(['order_no'=>$model->order_no])->one();
                                // echo'<pre>';print_r();exit();
                                if(isset($transport) && (Yii::$app->request->get('sent')) && (!empty($transport))){
                                    return Html::tag('span', '<i class="text-20 i-Jeep"></i>', ['class' => 'transport-show','value'=>Url::to(['transport/update','order_no' => $model->order_no]), 'style' => 'color : blue;']);
                                }
                                if(isset($transport) && (Yii::$app->request->get('receieved'))){
                                    return Html::tag('span', '<i class="text-20 i-Jeep"></i>', ['class' => 'transport-show','value'=>Url::to(['transport/update','order_no' => $model->order_no,'receieved'=>true]), 'style' => 'color : blue;']);
                                }
                                if(Yii::$app->request->get('receieved')){
                                    return Html::tag('span', '<i class="text-20 i-Jeep"></i>', ['class' => 'transport-show','value'=>Url::to(['transport/create','order_no' => $model->order_no,'receieved'=>true]), 'style' => 'color : blue;']);
                                }
                            }
                        },
                        'delete' => function($url,$model,$key){
                            if($model->status!=Orders::STATUS_APPROVED){
                                return Html::a('<i class="text-20 i-Delete-File"></i>',Url::to(['order/delete','order_no'=>$model->order_no]),[
                                    'title'=>'delete',
                                    'data'=>[
                                        'confirm'=>'Delete confirm?',
                                        'method'=>'post',
                                        ]
                                    ]);
                                }
                            },
                            'stockinward' => function ($url, $model, $key) {
                                if ($model->status == Orders::STATUS_APPROVED) {
                                    return Html::tag('span', '<i class="text-20 i-Jeep"></i>', ['class' => 'transport-show', 'value' => Url::to(['godown/create', 'order_no' => $model->order_no]), 'style' => 'color : blue;']);
                                }
                            }
                            ]
                        ],
                        ]
                    ]);
                }else if((Yii::$app->user->identity->role_id==User::SUPER_ADMIN) && (Yii::$app->request->get('receieved'))){
                    echo GridView::widget([
                        'dataProvider' => $searchdata,
                        'responsiveWrap' => false,
                        'layout' => "{items}\n<div class='float-left'>{summary}</div>\n<div class='float-right'>{pager}</div>",
                        'columns' => [
                            [
                                'class' => 'kartik\grid\SerialColumn',
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
                                'attribute' => 'distributor_name',
                                'format' => 'html',
                                'label' => 'Distributor Name',
                            // 'visible'=>function($data){
                            //     return (User::findOne( $data->orders->parent_id)->role_id==User::DEALER);
                            // },
                            'value' => function ($data) {
                                return isset($data->dealer->distributor->dist_name) ? $data->dealer->distributor->dist_name : (isset($data->distributor->dist_name) ? $data->distributor->dist_name : "-");
                            }
                            ],
                            [
                                'attribute' => 'total_discount',
                                'format' => 'html',
                                'label' => 'Total Discount',
                                'value' => function ($data) {
                                    return $data->all_discount;
                                }
                            ],
                            [
                                'attribute' => 'total_amount',
                                'format' => 'html',
                                'label' => 'Total Amount',
                                'value' => function ($data) {
                                    return $data->all_amount;
                                }
                            ],
                            [
                                'attribute' => 'total_points',
                                'format' => 'html',
                                'label' => 'Total Points',
                                'value' => function ($data) {
                                    return $data->total_points;
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
                            'attribute' => 'status',
                            'format' => 'html',
                            'label' => 'Status',
                            'value' => function ($data) {
                                if ($data->status == Orders::STATUS_APPROVED) {
                                    
                                    return '<a class="badge badge-success m-2" href="#">' . Orders::STATUS_APPROVED_LABEL . '</a>';
                                } else if ($data->status == Orders::STATUS_QUEUED) {
                                    return '<a class="badge badge-danger m-2" href="#">' . Orders::STATUS_QUEUED_LABEL . '</a>';
                                }else if( $data->status == Orders::STATUS_INPROGRESS){
                                    return '<a class="badge badge-danger m-2" href="#">' . Orders::STATUS_INPROGRESS_LABEL . '</a>';
                                }
                            }
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => "Action",
                            'template' => ' {view} {update} {transport} {delete} ',
                            'buttons' => [
                                'view' => function ($url, $model, $key) {
                                    return Html::a('<i class="text-20 i-Eye"></i>', Url::to(['order/view', 'order_no' => $model->order_no]), [
                                        'title' => 'view',
                                    ]);
                                },
                                'update' => function ($url, $model, $key) {
                                    if (Yii::$app->request->get('receieved') && in_array(Yii::$app->user->identity->role_id, [User::DISTRIBUTOR, User::SUPER_ADMIN]) && ($model->status!=Orders::STATUS_APPROVED)) {
                                        if($model->status==Orders::STATUS_QUEUED && isset($model->dealer)){
                                            return "";
                                        }
                                        return Html::a('<i class="text-20 i-Pen-3"></i>', (Yii::$app->request->get('receieved')) ? Url::to(['order/update', 'order_no' => $model->order_no]) : Url::to(['order/send-update','order_no'=>$model->order_no]), [
                                            'title' => 'update',
                                        ]);
                                    }
                                },
                                'transport' => function ($url, $model, $key) {
                                    if ( in_array(Yii::$app->user->identity->role_id, [User::DISTRIBUTOR, User::SUPER_ADMIN])) {
                                        $transport=Transport::find()->where(['order_no'=>$model->order_no])->one();
                                        // echo'<pre>';print_r();exit();
                                        if(isset($transport) && (Yii::$app->request->get('sent')) && (!empty($transport))){
                                            return Html::tag('span', '<i class="text-20 i-Jeep"></i>', ['class' => 'transport-show','value'=>Url::to(['transport/update','order_no' => $model->order_no]), 'style' => 'color : blue;']);
                                        }
                                        if(isset($transport) && (Yii::$app->request->get('receieved'))){
                                            return Html::tag('span', '<i class="text-20 i-Jeep"></i>', ['class' => 'transport-show','value'=>Url::to(['transport/update','order_no' => $model->order_no,'receieved'=>true]), 'style' => 'color : blue;']);
                                        }
                                        if(Yii::$app->request->get('receieved')){
                                            return Html::tag('span', '<i class="text-20 i-Jeep"></i>', ['class' => 'transport-show','value'=>Url::to(['transport/create','order_no' => $model->order_no,'receieved'=>true]), 'style' => 'color : blue;']);
                                        }
                                    }
                                },
                                'delete' => function($url,$model,$key){
                                    if($model->status!=Orders::STATUS_APPROVED){
                                        return Html::a('<i class="text-20 i-Delete-File"></i>',Url::to(['order/delete','order_no'=>$model->order_no]),[
                                            'title'=>'delete',
                                            'data'=>[
                                                'confirm'=>'Delete confirm?',
                                                'method'=>'post',
                                                ]
                                            ]);
                                        }
                            },
                            'stockinward' => function ($url, $model, $key) {
                                if ($model->status == Orders::STATUS_APPROVED) {
                                    return Html::tag('span', '<i class="text-20 i-Jeep"></i>', ['class' => 'transport-show', 'value' => Url::to(['godown/create', 'order_no' => $model->order_no]), 'style' => 'color : blue;']);
                                }
                                    }
                                    ]
                                ],
                                ]
                            ]);
                }else{
                    echo GridView::widget([
                        'dataProvider' => $searchdata,
                        'responsiveWrap' => false,
                        'layout' => "{items}\n<div class='float-left'>{summary}</div>\n<div class='float-right'>{pager}</div>",
                        'columns' => [
                            [
                                'class' => 'kartik\grid\SerialColumn',
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
                                'attribute' => 'total_discount',
                                'format' => 'html',
                                'label' => 'Total Discount',
                                'value' => function ($data) {
                                    return $data->all_discount;
                                }
                            ],
                            [
                                'attribute' => 'total_amount',
                                'format' => 'html',
                                'label' => 'Total Amount',
                                'value' => function ($data) {
                                    return $data->all_amount;
                                }
                            ],
                            [
                                'attribute' => 'total_points',
                                'format' => 'html',
                                'label' => 'Total Points',
                                'value' => function ($data) {
                                    return $data->total_points;
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
                            'attribute' => 'status',
                            'format' => 'html',
                            'label' => 'Status',
                            'value' => function ($data) {
                                if ($data->status == Orders::STATUS_APPROVED) {
                                    
                                    return '<a class="badge badge-success m-2" href="#">' . Orders::STATUS_APPROVED_LABEL . '</a>';
                                } else if ($data->status == Orders::STATUS_QUEUED) {
                                    return '<a class="badge badge-danger m-2" href="#">' . Orders::STATUS_QUEUED_LABEL . '</a>';
                                }else if( $data->status == Orders::STATUS_INPROGRESS){
                                    return '<a class="badge badge-danger m-2" href="#">' . Orders::STATUS_INPROGRESS_LABEL . '</a>';
                                }
                            }
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => "Action",
                            'template' => ' {view} {update} {transport} {delete}',
                            'buttons' => [
                                'view' => function ($url, $model, $key) {
                                    return Html::a('<i class="text-20 i-Eye"></i>', Url::to(['order/view', 'order_no' => $model->order_no]), [
                                        'title' => 'view',
                                    ]);
                                },
                                'update' => function ($url, $model, $key) {
                                    if (Yii::$app->request->get('receieved') && in_array(Yii::$app->user->identity->role_id, [User::DISTRIBUTOR, User::SUPER_ADMIN]) && ($model->status!=Orders::STATUS_APPROVED)) {
                                        
                                        return Html::a('<i class="text-20 i-Pen-3"></i>', (Yii::$app->request->get('receieved')) ? Url::to(['order/update', 'order_no' => $model->order_no]) : Url::to(['order/send-update','order_no'=>$model->order_no]), [
                                            'title' => 'update',
                                        ]);
                                    }
                                },
                                'transport' => function ($url, $model, $key) {
                                    if ( in_array(Yii::$app->user->identity->role_id, [User::DISTRIBUTOR, User::SUPER_ADMIN,User::DEALER])) {
                                        $transport=Transport::find()->where(['order_no'=>$model->order_no])->one();
                                        // echo'<pre>';print_r();exit();
                                        if(isset($transport) && (Yii::$app->request->get('sent')) && (!empty($transport))){
                                            return Html::tag('span', '<i class="text-20 i-Jeep"></i>', ['class' => 'transport-show','value'=>Url::to(['transport/update','order_no' => $model->order_no]), 'style' => 'color : blue;']);
                                        }
                                        if(isset($transport) && (Yii::$app->request->get('receieved'))){
                                            return Html::tag('span', '<i class="text-20 i-Jeep"></i>', ['class' => 'transport-show','value'=>Url::to(['transport/update','order_no' => $model->order_no,'receieved'=>true]), 'style' => 'color : blue;']);
                                        }
                                        if(Yii::$app->request->get('receieved')){
                                            return Html::tag('span', '<i class="text-20 i-Jeep"></i>', ['class' => 'transport-show','value'=>Url::to(['transport/create','order_no' => $model->order_no,'receieved'=>true]), 'style' => 'color : blue;']);
                                        }
                                    }
                                },
                                'delete' => function($url,$model,$key){
                                    if($model->status!=Orders::STATUS_APPROVED){
                                        return Html::a('<i class="text-20 i-Delete-File"></i>',Url::to(['order/delete','order_no'=>$model->order_no]),[
                                            'title'=>'delete',
                                            'data'=>[
                                                'confirm'=>'Delete confirm?',
                                                'method'=>'post',
                                                ]
                                            ]);
                                        }
                            },
                            'stockinward' => function ($url, $model, $key) {
                                if ($model->status == Orders::STATUS_APPROVED) {
                                    if(CommonHelpers::StockInwardCheck($model->order_no)){
                                    
                                        return Html::tag('span', '<i class="text-20 i-Shop-2"></i>', ['class' => 'transport-show', 'value' => Url::to(['godown/create', 'order_no' => $model->order_no]), 'style' => 'color : blue;']);
                                    }
                                }
                                    }
                                    ]
                                ],
                                ]
                            ]);
                }
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