<?php

use common\models\City;
use common\models\CommonHelpers;
use common\models\Distributor;
use common\models\Points;
use common\models\States;
// use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;

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
                            'dataProvider'=>$data,
                            'layout' => "{items}\n<div class='float-left'>{summary}</div>\n<div class='float-right'>{pager}</div>",
                            'columns'=>[
                                [
                                    'class'=>'kartik\grid\SerialColumn',
                                    'header'=>'sr.no'
                                ],
                                [
                                    'attribute'=>'item_id',
                                    'format'=>'html',
                                    'label'=>'Item Name',
                                    'value'=> function($data){
                                        return $data->product->item_name;
                                    }
                                ],
                                [
                                    'attribute'=>'quantity',
                                    'format'=>'html',
                                    'label'=>'Quantity',
                                    'value'=> function($data){
                                        return $data->quantity;
                                    }
                                ],
                                [
                                    'attribute'=>'points',
                                    'format'=>'html',
                                    'label'=>'Points',
                                    'value'=> function($data){
                                        return $data->points;
                                    }
                                ],
                                [
                                    'attribute'=>'status',
                                    'format'=>'html',
                                    'label'=>'Status',
                                    'value'=> function($data){
                                        if($data->status==Points::STATUS_ACTIVE){
                                            return '<a class="badge badge-success m-2" href="#">Active</a>';
                                        }else if($data->status==Points::STATUS_INACTIVE){
                                            return '<a class="badge badge-danger m-2" href="#">Inactive</a>';
                                        }
                                    }
                                ],
                                [
                                    'class' => 'kartik\grid\ActionColumn',
                                    'header' => "Action",
                                    'template' => ' {update} {delete} {inactive} {active}',
                                    'buttons'=>[
                                        'update'=>function($url,$model,$key){
                                            return Html::a('<i class="text-20 i-Pen-3"></i>',Url::to(['distributor/update','id'=>$model->id]),[
                                                'title'=>'update',
                                            ]);
                                        },
                                        'delete'=>function($url,$model,$key){
                                            return Html::a('<i class="text-20 i-Delete-File"></i>',Url::to(['distributor/delete','id'=>$model->id]),[
                                                'title'=>'delete',
                                                'data'=>[
                                                    'confirm'=>'Delete confirm?',
                                                    'method'=>'post',
                                                ]
                                            ]);
                                        },
                                        'active'=> function($url,$model,$key){
                                            if($model->status==Distributor::STATUS_INACTIVE){
                                                return Html::a('<i class="text-20 i-Add-User"></i>',Url::to(['distributor/change-status','id'=>$model->id]),[
                                                    'class'=>'',
                                                    'title'=>'active',
                                                    'data'=>[
                                                        'confirm'=>'Active confirm?',
                                                        'method'=>'post',
                                                    ]
                                                ]);
                                            }
                                        },
                                        'inactive'=> function($url,$model,$key){
                                            if($model->status==Distributor::STATUS_ACTIVE){
                                                return Html::a('<i class="text-20 i-Remove-User"></i>',Url::to(['distributor/change-status','id'=>$model->id]),[
                                                    'class'=>'',
                                                    'title'=>'active',
                                                    'data'=>[
                                                        'confirm'=>'Inctive confirm?',
                                                        'method'=>'post',
                                                    ]
                                                ]);
                                            }
                                        }
                                    ]
                                ],
                            ]
                        ])
                    ?>
                </div>
                
            </div>