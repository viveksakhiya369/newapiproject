<?php

use common\models\CommonHelpers;
// use yii\grid\GridView;
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
                <div class="row">
                    <!-- ICON BG-->
                    <!-- <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center"><i class="i-Add-User"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">New Leads</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">205</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center"><i class="i-Financial"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">Sales</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">$4021</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center"><i class="i-Checkout-Basket"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">Orders</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">80</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center"><i class="i-Money-2"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">Expense</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">$1200</p>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="row">   
                    <?php
                        echo GridView::widget([
                            'dataProvider'=>$dataProvider,
                            'layout' => "{items}\n<div class='float-left'>{summary}</div>\n<div class='float-right'>{pager}</div>",
                            'columns'=>[
                                [
                                    'class'=>'yii\grid\SerialColumn',
                                    'header'=>'sr.no'
                                ],
                                [
                                    'attribute'=>'username',
                                    'format'=>'html',
                                    'label'=>'Username',
                                    'value'=> function($data){
                                        return $data->username;
                                    }
                                ],
                                [
                                    'attribute'=>'mobile_num',
                                    'format'=>'html',
                                    'label'=>'Mobile Number',
                                    'value'=> function($data){
                                        return $data->mobile_num;
                                    }
                                ],
                                [
                                    'attribute'=>'email',
                                    'format'=>'html',
                                    'label'=>'Email',
                                    'value'=> function($data){
                                        return $data->email;
                                    }
                                ],
                                [
                                    'attribute'=>'role_id',
                                    'format'=>'html',
                                    'label'=>'Role',
                                    'value'=> function($data){
                                        return $data->role_id;
                                    }
                                ],
                                [
                                    'attribute'=>'status',
                                    'format'=>'html',
                                    'label'=>'Status',
                                    'value'=> function($data){
                                        return $data->status;
                                    }
                                ]
                            ]
                        ])
                    ?>
                </div>
                
            </div>