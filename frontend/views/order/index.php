<?php

use common\models\City;
use common\models\CommonHelpers;
use common\models\Distributor;
use common\models\States;
use common\models\User;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

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
                    <?php echo $this->render('_search',['searchmodel'=>$searchmodel]) ?>
                <div class="row">
                    <?php
                        echo GridView::widget([
                            'dataProvider'=>$searchdata,
                            'options' => [
                                'class'=>'card-body table table-striped table-bordered table-responsive'
                                ],
                            'layout' => "{items}\n{summary}\n{pager}",
                            'columns'=>[
                                [
                                    'class'=>'yii\grid\SerialColumn',
                                    'header'=>'sr.no'
                                ],
                                [
                                    'attribute'=>'order_no',
                                    'format'=>'html',
                                    'label'=>'Order No',
                                    'value'=> function($data){
                                        return $data->order_no;
                                    }
                                ],
                                [
                                    'attribute'=>'created_dt',
                                    'format'=>'html',
                                    'label'=>'Created Date',
                                    'value'=> function($data){
                                        return date('d-m-Y',strtotime($data->created_dt));
                                    }
                                ],
                                [
                                    'attribute'=>'dealer_name',
                                    'format'=>'html',
                                    'label'=>'Dealer Name',
                                    // 'visible'=>function($data){
                                    //     return (User::findOne( $data->orders->parent_id)->role_id==User::DEALER);
                                    // },
                                    'value'=> function($data){
                                        return isset($data->dealer->dealer_name) ? $data->dealer->dealer_name : "-";
                                    }
                                ],
                                [
                                    'attribute'=>'status',
                                    'format'=>'html',
                                    'label'=>'Status',
                                    'value'=> function($data){
                                        if($data->status==Distributor::STATUS_ACTIVE){

                                            return '<a class="badge badge-success m-2" href="#">Active</a>';
                                        }else if($data->status==Distributor::STATUS_INACTIVE){
                                            return '<a class="badge badge-danger m-2" href="#">Inactive</a>';
                                        }
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => "Action",
                                    'template' => ' {view} ',
                                    'buttons'=>[
                                        'view'=>function($url,$model,$key){
                                            return Html::a('<i class="text-20 i-Eye"></i>',Url::to(['order/view','order_no'=>$model->order_no]),[
                                                'title'=>'view',
                                            ]);
                                        },
                                    ]
                                ],
                            ]
                        ])
                    ?>
                </div>
                
            </div>