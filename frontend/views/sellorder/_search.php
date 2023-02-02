<?php

use common\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php
         $form = ActiveForm::begin([
             'id' => 'search_form',
             'method' => 'post',
             'options' => ['data-pjax' => false],
            ]);
            ?>
            <div class="row">
    <div class="col">
        <?= $form->field($searchmodel,'search',['template'=>"{input}"])->textInput(['class' => 'form-control','placeholder'=>'Search']) ?>
    </div>
    <div class="col">
    <?php echo Html::submitButton("Search", ['class' => 'btn btn-primary', 'id' => 'submit-dealer']); ?>
    </div>
    <?php if(in_array(Yii::$app->user->identity->role_id,[User::DISTRIBUTOR,User::DEALER,User::SALES_PERSON]) && Yii::$app->request->get('sell_order')) { ?>
        <div class="col">
        <?php echo Html::a("Create",['sellorder/create','sell_order'=>true], ['class' => 'btn btn-primary', 'id' => 'submit-dealer']); ?>
        </div>
    <?php } ?>
   
</div>
    <?php ActiveForm::end(); ?>