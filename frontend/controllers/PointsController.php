<?php

namespace frontend\controllers;

use common\models\CommonHelpers;
use common\models\PendingOrdersSearch;
use common\models\PointsSearch;
use Yii;
use yii\web\Controller;

class PointsController extends Controller{

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
        $searchmodel=new PointsSearch();
        $data=$searchmodel->search(Yii::$app->request->queryParams);
        return $this->render('index',[
            'searchmodel'=>$searchmodel,
            'data'=>$data
        ]);
    }
}


?>