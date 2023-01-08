<?php

namespace frontend\controllers;

use common\models\User;
use common\models\CommonHelpers;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
/*
* User controller
*/
class UserController extends Controller{

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



    public function actionIndex(){
        $model= new User();
        $dataProvider=$this->usersearch(Yii::$app->request->queryParams);

        return $this->render('index',[
            'model'=>$model,
            'dataProvider'=>$dataProvider,
        ]);

    }

    public function usersearch($params){
        
        $query=User::find();

        $dataProvider= new ActiveDataProvider([
            'query'=>$query,
        ]);

        return $dataProvider;
    }


}
?>