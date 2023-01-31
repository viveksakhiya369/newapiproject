<?php
use yii\helpers\Html;
use common\models\CommonHelpers;
use common\models\User;

?>
<div class="main-header">
    <img src="<?= Yii::$app->request->baseUrl?>/images/logo.jpg" style="width: 220px; height: 70px;" alt="">
            <!-- <div class="logo">
            </div> -->
            <div class="menu-toggle">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="d-flex align-items-center">
            </div>
            <div style="margin: auto"></div>
            <div class="header-part-right">
                <!-- Full screen toggle -->
                <i class="i-Full-Screen header-icon d-none d-sm-inline-block" data-fullscreen></i>
                <!-- Notificaiton -->
                <div class="dropdown">
                </div>
                <!-- Notificaiton End -->
                <!-- User avatar dropdown -->
                <div class="dropdown">
                    <div class="user col align-self-end">
                        <img src="<?= Yii::$app->request->baseUrl?>/dist-assets/images/faces/1.jpg" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            <div class="dropdown-header">
                                <i class="i-Lock-User mr-1"></i><b style="font-size:1rem"><?php echo (CommonHelpers::CheckLogin()) ?  Yii::$app->user->identity->username." (".User::ROLE_ARR[Yii::$app->user->identity->role_id].")" : '' ?></b>
                            </div>
                            <?= Html::a('<span class="dropdown-item">Change Password</span>', ['site/reset-password'], ['data' => ['method' => 'post']]) ?>
                            <a class="dropdown-item">Billing history</a>
                            <?= Html::a('<span class="dropdown-item">Logout</span>', ['site/logout'], ['data' => ['method' => 'post']]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>