<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col d-flex justify-content-center">
<div class="card">
    <div class="card-body"> 
            <div>
                <div class="auth-logo text-center mb-4"><img style="width: 250px; height: 100px;" src="<?= Yii::$app->request->baseUrl?>/images/logo.jpg" alt=""></div>
                    <h1 class="mb-3 text-18"><?= Html::encode($this->title) ?></h1>

                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                    <div class="form-group">
                        <?= $form->field($model, 'mobile_number')->textInput(['autofocus' => true,'class'=>'form-control form-control-rounded']) ?>
                    </div>
                        
                    <div class="form-group">
                        <?= $form->field($model, 'password')->passwordInput(['class'=>'form-control form-control-rounded']) ?>
                    </div>

                        <div class="form-group">
                            <?= Html::submitButton('Login', ['class' => 'btn btn-rounded btn-primary btn-block mt-2', 'name' => 'login-button']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                    <div class="mt-3 text-center"><a class="text-muted" href="<?= Url::toRoute(['site/forgot-password']) ?>">
                                <u>Forgot Password?</u></a></div>
                    </div>
            </div>
        </div>
</div>