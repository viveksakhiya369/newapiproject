<?php

use common\models\CommonHelpers;
use yii\grid\GridView;
use common\models\City;

?>
<div class="main-content">
                <div class="breadcrumb">
                    <h1 class="mr-2"><?= CommonHelpers::getTitle($this->context->id,$this->context->action->id)?></h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                    <!-- ICON BG-->
                
                <?=   $this->render('_form',['model'=>$model]) ?>
</div>