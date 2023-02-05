<?php

use common\models\City;
use common\models\CommonHelpers;
use common\models\Distributor;
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
                            'dataProvider'=>$searchdata,
                            'responsiveWrap' => false,
                            'layout' => "{items}\n<div class='float-left'>{summary}</div>\n<div class='float-right'>{pager}</div>",
                            'columns'=>[
                                [
                                    'class'=>'yii\grid\SerialColumn',
                                    'header'=>'sr.no'
                                ],
                                [
                                    'attribute'=>'item_group',
                                    'format'=>'html',
                                    'label'=>'Item Group',
                                    'value'=> function($data){
                                        return $data->item_group;
                                    }
                                ],
                                [
                                    'attribute'=>'item_name',
                                    'format'=>'html',
                                    'label'=>'Item Name',
                                    'value'=> function($data){
                                        return $data->item_name;
                                    }
                                ],
                                [
                                    'attribute'=>'size',
                                    'format'=>'html',
                                    'label'=>'Size',
                                    'value'=> function($data){
                                        return $data->size;
                                    }
                                ],
                                [
                                    'attribute'=>'pack',
                                    'format'=>'html',
                                    'label'=>'Pack',
                                    'value'=> function($data){
                                        return $data->pack;
                                    }
                                ],
                                [
                                    'attribute'=>'unit',
                                    'format'=>'html',
                                    'label'=>'Unit',
                                    'value'=> function($data){
                                        return $data->unit;
                                    }
                                ],
                                [
                                    'attribute'=>'point',
                                    'format'=>'html',
                                    'label'=>'Point',
                                    'value'=> function($data){
                                        return $data->point;
                                    }
                                ],
                                [
                                    'attribute'=>'hsn',
                                    'format'=>'html',
                                    'label'=>'Hsn',
                                    'value'=> function($data){
                                        return $data->hsn;
                                    }
                                ],
                                [
                                    'attribute'=>'barcode',
                                    'format'=>'html',
                                    'label'=>'Barcode',
                                    'value'=> function($data){
                                        return $data->barcode;
                                    }
                                ],
                                // [
                                //     'attribute'=>'tax',
                                //     'format'=>'html',
                                //     'label'=>'Tax',
                                //     'value'=> function($data){
                                //         return $data->taxName->name.'-'.$data->taxName->percentage.'%';
                                //     }
                                // ],
                                [
                                    'attribute'=>'purchase_rate',
                                    'format'=>'html',
                                    'label'=>'Purchase Rate',
                                    'value'=> function($data){
                                        return $data->purchase_rate;
                                    }
                                ],
                                [
                                    'attribute'=>'wholesale_rate',
                                    'format'=>'html',
                                    'label'=>'Distributor Rate',
                                    'value'=> function($data){
                                        return $data->wholesale_rate;
                                    }
                                ],
                                [
                                    'attribute'=>'dealer_rate',
                                    'format'=>'html',
                                    'label'=>'Dealer Rate',
                                    'value'=> function($data){
                                        return $data->dealer_rate;
                                    }
                                ],
                                [
                                    'attribute'=>'mrp',
                                    'format'=>'html',
                                    'label'=>'Mrp',
                                    'value'=> function($data){
                                        return $data->mrp;
                                    }
                                ],
                                [
                                    'attribute'=>'discount',
                                    'format'=>'html',
                                    'label'=>'Discount',
                                    'value'=> function($data){
                                        return $data->discount;
                                    }
                                ],
                                [
                                    'attribute'=>'minimum_stock',
                                    'format'=>'html',
                                    'label'=>'Minimum Stock',
                                    'value'=> function($data){
                                        return $data->minimum_stock;
                                    }
                                ],
                                [
                                    'attribute'=>'order_level',
                                    'format'=>'html',
                                    'label'=>'Order Level',
                                    'value'=> function($data){
                                        return $data->order_level;
                                    }
                                ],
                                [
                                    'attribute'=>'personal_code',
                                    'format'=>'html',
                                    'label'=>'Personal Code',
                                    'value'=> function($data){
                                        return $data->personal_code;
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
                                    'template' => ' {update} {delete} {inactive} {active}',
                                    'buttons'=>[
                                        'update'=>function($url,$model,$key){
                                            return Html::a('<i class="text-20 i-Pen-3"></i>',Url::to(['product/update','id'=>$model->id]),[
                                                'title'=>'update',
                                            ]);
                                        },
                                        'delete'=>function($url,$model,$key){
                                            return Html::a('<i class="text-20 i-Delete-File"></i>',Url::to(['product/delete','id'=>$model->id]),[
                                                'title'=>'delete',
                                                'data'=>[
                                                    'confirm'=>'Delete confirm?',
                                                    'method'=>'post',
                                                ]
                                            ]);
                                        },
                                        'active'=> function($url,$model,$key){
                                            if($model->status==Distributor::STATUS_INACTIVE){
                                                return Html::a('<i class="text-20 i-Add-User"></i>',Url::to(['product/change-status','id'=>$model->id]),[
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
                                                return Html::a('<i class="text-20 i-Remove-User"></i>',Url::to(['product/change-status','id'=>$model->id]),[
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