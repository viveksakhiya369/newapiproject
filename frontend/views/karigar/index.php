<?php

use common\models\City;
use common\models\CommonHelpers;
use common\models\Distributor;
use common\models\Karigar;
use common\models\Points;
use common\models\States;
use common\models\User;
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
                            'dataProvider'=>$searchdata,
                            'responsiveWrap' => false,
                            'layout' => "{items}\n<div class='float-left'>{summary}</div>\n<div class='float-right'>{pager}</div>",
                            'columns'=>[
                                [
                                    'class'=>'yii\grid\SerialColumn',
                                    'header'=>'Sr.no'
                                ],
                                [
                                    'attribute'=>'name',
                                    'format'=>'html',
                                    'label'=>'Name',
                                    'value'=> function($data){
                                        return $data->name;
                                    }
                                ],
                                [
                                    'attribute'=>'address',
                                    'format'=>'html',
                                    'label'=>'Address',
                                    'value'=> function($data){
                                        return $data->address;
                                    }
                                ],
                                [
                                    'attribute'=>'city',
                                    'format'=>'html',
                                    'label'=>'City',
                                    'value'=> function($data){
                                        return City::getNameFromId($data->city);
                                    }
                                ],
                                [
                                    'attribute'=>'state',
                                    'format'=>'html',
                                    'label'=>'State',
                                    'value'=> function($data){
                                        return States::getStatenamefromId($data->state);
                                    }
                                ],
                                [
                                    'attribute'=>'mobile_num',
                                    'format'=>'html',
                                    'label'=>'Mobile Number',
                                    'value'=> function($data){
                                        return $data->user->mobile_num;
                                    }
                                ],
                                [
                                    'attribute'=>'parent_id',
                                    'format'=>'html',
                                    'label'=>'Dealer name',
                                    'value'=> function($data){
                                        return $data->dealer->dealer_name;
                                    }
                                ],
                                [
                                    'attribute'=>'total_points',
                                    'format'=>'html',
                                    'label'=>'Total Points',
                                    'value'=> function($data){
                                        return Points::totalPoint($data->user->id);
                                    }
                                ],
                                [
                                    'attribute'=>'status',
                                    'format'=>'html',
                                    'label'=>'Status',
                                    'value'=> function($data){
                                        if($data->status==Karigar::STATUS_ACTIVE){

                                            return '<a class="badge badge-success m-2" href="#">Active</a>';
                                        }else if($data->status==Karigar::STATUS_INACTIVE){
                                            return '<a class="badge badge-danger m-2" href="#">Inactive</a>';
                                        }
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'header' => "Action",
                                    'template' => ' {update} {delete} {inactive} {active}',
                                    'buttons'=>[
                                        'update'=>function($url,$model,$key){
                                            return Html::a('<i class="text-20 i-Pen-3"></i>',Url::to(['karigar/update','id'=>$model->id]),[
                                                'title'=>'update',
                                            ]);
                                        },
                                        'delete'=>function($url,$model,$key){
                                            return Html::a('<i class="text-20 i-Delete-File"></i>',Url::to(['karigar/delete','id'=>$model->id]),[
                                                'title'=>'delete',
                                                'data'=>[
                                                    'confirm'=>'Delete confirm?',
                                                    'method'=>'post',
                                                ]
                                            ]);
                                        },
                                        'active'=> function($url,$model,$key){
                                            if(($model->status==Karigar::STATUS_INACTIVE) && (in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN]))){
                                                return Html::a('<i class="text-20 i-Add-User"></i>',Url::to(['karigar/change-status','id'=>$model->id]),[
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
                                            if(($model->status==Karigar::STATUS_ACTIVE) && (in_array(Yii::$app->user->identity->role_id,[User::SUPER_ADMIN]))){
                                                return Html::a('<i class="text-20 i-Remove-User"></i>',Url::to(['karigar/change-status','id'=>$model->id]),[
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