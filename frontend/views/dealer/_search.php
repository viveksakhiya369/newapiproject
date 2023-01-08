<?php

use common\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php
         $form = ActiveForm::begin([
             'id' => 'search_form',
             'action' => ['index'],
             'method' => 'get',
             'options' => ['data-pjax' => false],
            ]);
            ?>
            <div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <?= $form->field($searchmodel,'search',['template'=>"{input}"])->textInput(['class' => 'form-control','placeholder'=>'Search']) ?>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
    <?php echo Html::submitButton("Search", ['class' => 'btn btn-primary', 'id' => 'submit-dealer']); ?>
    </div>
    <?php if(in_array(Yii::$app->user->identity->role_id,[User::DISTRIBUTOR])) { ?>
        <div class="col-lg-3 col-md-6 col-sm-6 ml-0">
        <?php echo Html::a("Create",['dealer/create'], ['class' => 'btn btn-primary', 'id' => 'submit-dealer']); ?>
        </div>
    <?php } ?>
   
</div>
    <?php ActiveForm::end(); ?>