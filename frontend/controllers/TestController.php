<?php
 namespace frontend\controllers;

use common\models\CommonHelpers;
use Yii;
use yii\web\Controller;

 class TestController extends Controller{
    
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!CommonHelpers::CheckLogin()) {
                $this->redirect(Yii::$app->getUrlManager()->createUrl('site/login'));
                return false;
            }
                $this->enableCsrfValidation = false;
                return true;
        }
        return false;
    }

    

 }

?>